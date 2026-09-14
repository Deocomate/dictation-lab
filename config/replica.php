<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Excluded tables
    |--------------------------------------------------------------------------
    |
    | Framework bookkeeping and per-environment/ephemeral state that should
    | never be copied between local and the server by `replica:export` /
    | `replica:import`.
    */

    'excluded_tables' => [
        'migrations',
        'sessions',
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
        'failed_jobs',
        'password_reset_tokens',
    ],

    /*
    |--------------------------------------------------------------------------
    | Images disk
    |--------------------------------------------------------------------------
    |
    | Disk holding the uploaded article/lesson images (see FileUploadService).
    | Its whole contents are bundled into / merged from the replica archive.
    */

    'images_disk' => 'public',

    /*
    |--------------------------------------------------------------------------
    | Storage paths
    |--------------------------------------------------------------------------
    */

    'export_path' => storage_path('app/private/replica-exports'),
    'backup_path' => storage_path('app/private/replica-backups'),

    /*
    |--------------------------------------------------------------------------
    | Row chunk size
    |--------------------------------------------------------------------------
    |
    | Kept conservative so chunk_size * column_count stays well under SQLite's
    | bound-parameter limit when importing into a local sqlite database.
    */

    'chunk_size' => 50,

];
