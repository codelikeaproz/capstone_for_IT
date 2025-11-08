<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Media Upload Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains all configuration for media uploads including
    | validation rules, storage paths, and processing options.
    |
    */

    'photos' => [
        'max_count' => 5,
        'max_size' => 2 * 1024 * 1024, // 2MB in bytes
        'allowed_types' => ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'],
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif'],
        'storage_path' => 'incident_photos',

        'validation' => [
            'required' => false,
            'max' => 5,
            'mimes' => 'jpeg,png,jpg,gif',
            'max_size' => '2048', // in KB
        ],

        // Image processing options
        'processing' => [
            'create_thumbnails' => true,
            'thumbnail_sizes' => [
                'small' => [150, 150],
                'medium' => [300, 300],
                'large' => [800, 800],
            ],
            'optimize' => true,
            'quality' => 85,
        ],
    ],

    'videos' => [
        'max_count' => 2,
        'max_size' => 10 * 1024 * 1024, // 10MB in bytes
        'allowed_types' => ['video/mp4', 'video/webm', 'video/quicktime'],
        'allowed_extensions' => ['mp4', 'webm', 'mov'],
        'storage_path' => 'incident_videos',

        'validation' => [
            'required' => false,
            'max' => 2,
            'mimes' => 'mp4,webm,mov',
            'max_size' => '10240', // in KB
        ],

        // Video processing options
        'processing' => [
            'create_thumbnails' => true,
            'thumbnail_time' => 1, // seconds into video
            'compress' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Configuration
    |--------------------------------------------------------------------------
    */
    'storage' => [
        'disk' => 'public',
        'visibility' => 'public',
        'delete_on_update' => false, // Keep old files when updating
        'delete_on_destroy' => true, // Delete files when incident is deleted
    ],

    /*
    |--------------------------------------------------------------------------
    | Error Messages
    |--------------------------------------------------------------------------
    */
    'messages' => [
        'photo' => [
            'max_count' => 'You can only upload up to :max photos.',
            'invalid_type' => 'Invalid file type: :filename. Only JPG, PNG, and GIF are allowed.',
            'too_large' => 'File too large: :filename (:size). Maximum size is :max.',
            'upload_failed' => 'Failed to upload photo: :filename',
        ],
        'video' => [
            'max_count' => 'You can only upload up to :max videos.',
            'invalid_type' => 'Invalid file type: :filename. Only MP4, WebM, and MOV are allowed.',
            'too_large' => 'File too large: :filename (:size). Maximum size is :max.',
            'upload_failed' => 'Failed to upload video: :filename',
        ],
    ],
];
