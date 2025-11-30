<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Card;
use App\Services\ActivityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
  public function __construct(
    private ActivityService $activityService
  ) {
  }

  public function index(Card $card): JsonResponse
  {
    $board = $card->column->board;
    $this->authorize('view', $board);

    $attachments = $card->attachments()->with('uploader:id,name,avatar_url')->get();

    return response()->json(['data' => $attachments]);
  }

  public function store(Request $request, Card $card): JsonResponse
  {
    $board = $card->column->board;
    $this->authorize('editContent', $board);

    $validated = $request->validate([
      'file' => 'required_without:url|file|max:10240', 
      'url' => 'required_without:file|url|max:2048',
      'file_name' => 'required_with:url|string|max:255',
    ]);

    $user = $request->user();

    if ($request->hasFile('file')) {
      
      $file = $request->file('file');
      $path = $file->store('attachments/' . $card->id, 'public');

      $attachment = $card->attachments()->create([
        'file_name' => $file->getClientOriginalName(),
        'file_path' => $path,
        'file_size' => $file->getSize(),
        'mime_type' => $file->getMimeType(),
        'is_external' => false,
        'uploaded_by' => $user->id,
      ]);
    } else {
      
      $attachment = $card->attachments()->create([
        'file_name' => $validated['file_name'],
        'file_path' => $validated['url'],
        'file_size' => null,
        'mime_type' => null,
        'is_external' => true,
        'uploaded_by' => $user->id,
      ]);
    }

    $this->activityService->logAttachmentAdded($card, $user, $attachment);

    $attachment->load('uploader:id,name,avatar_url');

    return response()->json(['data' => $attachment], 201);
  }

  public function destroy(Request $request, Attachment $attachment): JsonResponse
  {
    $card = $attachment->card;
    $board = $card->column->board;
    $this->authorize('editContent', $board);

    $fileName = $attachment->file_name;

    if (!$attachment->is_external && $attachment->file_path) {
      Storage::disk('public')->delete($attachment->file_path);
    }

    $attachment->delete();

    $this->activityService->logAttachmentRemoved($card, $request->user(), $fileName);

    return response()->json(null, 204);
  }
}
