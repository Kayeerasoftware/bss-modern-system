<?php

return [
    // Log GET requests to keep full user activity history.
    'log_get_requests' => env('AUDIT_LOG_GET_REQUESTS', true),

    // Write synchronously by default so logs are never lost when queue workers are down.
    'queue_enabled' => env('AUDIT_QUEUE_ENABLED', false),
];
