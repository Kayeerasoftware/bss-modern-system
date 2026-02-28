<?php

return [
    // Log GET requests to keep full user activity history.
    'log_get_requests' => env('AUDIT_LOG_GET_REQUESTS', false),

    // Queue audit writes when an async queue backend is configured.
    'queue_enabled' => env('AUDIT_QUEUE_ENABLED', true),

    // Capture model created/updated/deleted events globally.
    'model_events_enabled' => env('AUDIT_MODEL_EVENTS_ENABLED', false),
];
