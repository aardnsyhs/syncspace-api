<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Personal Workspace Name
    |--------------------------------------------------------------------------
    |
    | When a new user registers (or logs in for the first time), a personal
    | workspace is automatically created for them. This value sets the default
    | name for that workspace.
    |
    | You can also set this via the APP_DEFAULT_WORKSPACE_NAME environment
    | variable to customise it per deployment without touching code.
    |
    */
    'default_workspace_name' => env('APP_DEFAULT_WORKSPACE_NAME', 'Personal Workspace'),

    /*
    |--------------------------------------------------------------------------
    | System User
    |--------------------------------------------------------------------------
    |
    | The system user is used to own global board templates and perform
    | automated actions. Set these values in your .env file.
    |
    */
    'system_user_email' => env('SYSTEM_USER_EMAIL', 'system@example.com'),
    'system_user_name'  => env('SYSTEM_USER_NAME', 'System'),

];
