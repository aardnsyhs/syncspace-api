<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Board Columns
    |--------------------------------------------------------------------------
    |
    | When a new board is created without a template, these column names are
    | used to scaffold the initial board structure. Modify this list to change
    | the default columns for every new board created in your application.
    |
    */
    'default_columns' => [
        'To Do',
        'In Progress',
        'Review',
        'Done',
    ],

    /*
    |--------------------------------------------------------------------------
    | "Done" Column Detection Keywords
    |--------------------------------------------------------------------------
    |
    | These keywords are used to identify "completion" columns when calculating
    | dashboard statistics. A column whose name contains any of these strings
    | (case-insensitive) is treated as a terminal/done column.
    |
    | NOTE: This is a fallback for boards that do not use the `is_completed`
    | flag on cards. Prefer using `is_completed` for accurate tracking.
    |
    */
    'done_column_keywords' => [
        'done',
        'complete',
        'finished',
        'released',
        'deployed',
        'closed',
    ],

];
