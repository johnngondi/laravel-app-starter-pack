<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Upload Disk
    |--------------------------------------------------------------------------
    |
    | The filesystem disk uploads are written to. Defaults to the private
    | "local" disk — the bytes are served through the uuid-keyed preview and
    | download routes rather than being directly web-accessible.
    |
    */

    'disk' => env('UPLOADS_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Maximum Upload Size
    |--------------------------------------------------------------------------
    |
    | The largest file (in kilobytes) accepted by the upload endpoint.
    |
    */

    'max_size' => (int) env('UPLOADS_MAX_SIZE', 25600), // 25 MB

];
