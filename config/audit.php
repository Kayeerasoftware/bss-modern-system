<?php

return [
    // Disable GET audit logging by default to keep production request latency low.
    'log_get_requests' => env('AUDIT_LOG_GET_REQUESTS', false),

    // Queue audit writes when a queue worker is available.
    'queue_enabled' => env('AUDIT_QUEUE_ENABLED', true),
];
