<?php

namespace App\Http\Controllers;

use App\Events\CardCreated;
use App\Events\CardDeleted;
use App\Events\CardMoved;
use App\Events\CardUpdated;
use App\Events\UserNotification;
use App\Http\Requests\Card\MoveCardRequest;
use App\Http\Requests\Card\StoreCardRequest;
use App\Http\Requests\Card\UpdateCardRequest;
use App\Http\Resources\CardResource;
use App\Models\Card;
use App\Models\CardTransition;
use App\Models\Column;
use App\Services\ActivityService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CardController extends Controller
{
  public function __construct(
    private ActivityService $activityService,
    private NotificationService $notificationService
  ) {
  }

  public function store(StoreCardRequest $request, Column $column): JsonResponse
  {
    $this->authorize('create', [Card::class, $column]);

    $maxPosition = $column->cards()->max('position') ?? -1;

    $card = $column->cards()->create([
      ...$request->validated(),
      'position' => $maxPosition + 1,
    ]);

    $boardId = $column->board_id;

    CardTransition::create([
      'card_id' => $card->id,
      'from_column_id' => null,
      'to_column_id' => $column->id,
      'user_id' => $request->user()->id,
      'transitioned_at' => now(),
    ]);

    $this->activityService->logCardCreated($card, $request->user());
    broadcast(new CardCreated($card, $boardId))->toOthers();

    return response()->json([
      'data' => new CardResource($card->load(['assignee', 'labels'])),
    ], 201);
  }

  public function show(Request $request, Card $card): JsonResponse
  {
    $this->authorize('view', $card);

    $card->load(['assignee', 'labels', 'comments.user']);

    return response()->json([
      'data' => new CardResource($card),
    ]);
  }

  public function update(UpdateCardRequest $request, Card $card): JsonResponse
  {
    $this->authorize('update', $card);

    $oldAssigneeId = $card->assignee_id;
    $changes = $request->validated();

    $card->update($changes);

    $boardId = $card->column->board_id;

    $this->activityService->logCardUpdated($card, $request->user(), array_keys($changes));

    if (isset($changes['assignee_id']) && $changes['assignee_id'] !== $oldAssigneeId) {
      $this->activityService->logCardAssigned($card, $request->user(), $card->assignee);

      if ($card->assignee_id && $card->assignee_id !== $request->user()->id) {
        
        $this->notificationService->notifyCardAssigned($card, $card->assignee, $request->user());

        broadcast(new UserNotification(
          userId: $card->assignee_id,
          type: 'card_assigned',
          title: 'You were assigned to a card',
          message: "{$request->user()->name} assigned you to \"{$card->title}\"",
          data: [
            'card_id' => $card->id,
            'board_id' => $boardId,
          ]
        ));
      }
    }

    broadcast(new CardUpdated($card, $boardId))->toOthers();

    return response()->json([
      'data' => new CardResource($card->load(['assignee', 'labels'])),
    ]);
  }

  public function destroy(Request $request, Card $card): JsonResponse
  {
    $this->authorize('delete', $card);

    $column = $card->column;
    $board = $column->board;
    $boardId = $board->id;
    $columnId = $column->id;
    $cardId = $card->id;
    $cardTitle = $card->title;
    $columnName = $column->name;
    $position = $card->position;

    $card->delete();

    $column->cards()
      ->where('position', '>', $position)
      ->decrement('position');

    $this->activityService->logCardDeleted($board, $request->user(), $cardTitle, $columnName);
    broadcast(new CardDeleted($boardId, $columnId, $cardId))->toOthers();

    return response()->json(null, 204);
  }

  public function move(MoveCardRequest $request, Card $card): JsonResponse
  {
    $this->authorize('move', $card);

    $newColumnId = $request->column_id;
    $newPosition = $request->position;
    $oldColumnId = $card->column_id;
    $oldPosition = $card->position;
    $oldColumnName = $card->column->name;
    $boardId = $card->column->board_id;

    $wipExceeded = false;
    $newColumn = null;
    if ($newColumnId !== $oldColumnId) {
      $newColumn = Column::find($newColumnId);
      if ($newColumn && $newColumn->wouldExceedWip()) {
        $wipExceeded = true;
        
      }
    }

    DB::transaction(function () use ($card, $newColumnId, $newPosition, $oldColumnId, $oldPosition) {
      if ($newColumnId === $oldColumnId) {
        if ($newPosition > $oldPosition) {
          Card::where('column_id', $oldColumnId)
            ->whereBetween('position', [$oldPosition + 1, $newPosition])
            ->decrement('position');
        } else {
          Card::where('column_id', $oldColumnId)
            ->whereBetween('position', [$newPosition, $oldPosition - 1])
            ->increment('position');
        }
      } else {
        Card::where('column_id', $oldColumnId)
          ->where('position', '>', $oldPosition)
          ->decrement('position');

        Card::where('column_id', $newColumnId)
          ->where('position', '>=', $newPosition)
          ->increment('position');
      }

      $card->update([
        'column_id' => $newColumnId,
        'position' => $newPosition,
      ]);
    });

    if ($newColumnId !== $oldColumnId) {
      CardTransition::create([
        'card_id' => $card->id,
        'from_column_id' => $oldColumnId,
        'to_column_id' => $newColumnId,
        'user_id' => $request->user()->id,
        'transitioned_at' => now(),
      ]);
    }

    $card->refresh();
    $newColumnName = $card->column->name;

    if ($newColumnId !== $oldColumnId) {
      $this->activityService->logCardMoved($card, $request->user(), $oldColumnName, $newColumnName);

      $this->notificationService->notifyCardMoved($card, $oldColumnName, $newColumnName, $request->user());
    }

    broadcast(new CardMoved(
      $card,
      $boardId,
      $oldColumnId,
      $newColumnId,
      $newPosition
    ))->toOthers();

    $response = [
      'data' => new CardResource($card->load(['assignee', 'labels'])),
    ];

    if ($wipExceeded && $newColumn) {
      $response['wip_warning'] = [
        'exceeded' => true,
        'column_name' => $newColumn->name,
        'limit' => $newColumn->wip_limit,
        'count' => $newColumn->cards()->count(),
      ];
    }

    return response()->json($response);
  }
}
