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
    | OPTIMIZED VERSION with compression and organized storage structure
    |
    */

    'photos' => [
        'max_count' => 5,
        'max_size' => 3 * 1024 * 1024, // 3MB in bytes (increased from 2MB to allow quality photos)
        'max_dimensions' => [3000, 3000], // Maximum width and height
        'allowed_types' => ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'],
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'storage_path' => 'incidents', // Base path - will be organized by municipality/year/month

        'validation' => [
            'required' => false,
            'max' => 5,
            'mimes' => 'jpeg,png,jpg,gif,webp',
            'max_size' => '3072', // in KB (3MB)
        ],

        // Compression settings (using GD library)
        'compress' => [
            'enabled' => true,
            'quality' => 75, // JPEG quality (0-100)
            'max_width' => 1920, // Resize to max 1920px width
            'max_height' => 1080, // Resize to max 1080px height
            'format' => 'jpg', // Convert all images to JPG for consistency
            'keep_original' => false, // Set to true to keep original files (uses more storage)
        ],

        // Thumbnail generation
        'thumbnails' => [
            'enabled' => true,
            'sizes' => [
                'small' => [150, 150], // For icons/avatars
                'medium' => [300, 300], // For listing pages
            ],
        ],

        // Legacy support
        'processing' => [
            'create_thumbnails' => true,
            'thumbnail_sizes' => [
                'small' => [150, 150],
                'medium' => [300, 300],
            ],
            'optimize' => true,
            'quality' => 75,
        ],
    ],

    'videos' => [
        'max_count' => 2,
        'max_size' => 20 * 1024 * 1024, // 20MB in bytes (increased from 10MB)
        'max_duration' => 30, // Maximum duration in seconds
        'max_resolution' => '1920x1080', // Maximum resolution
        'allowed_types' => ['video/mp4', 'video/webm', 'video/quicktime'],
        'allowed_extensions' => ['mp4', 'webm', 'mov'],
        'storage_path' => 'incidents', // Base path - will be organized by municipality/year/month

        'validation' => [
            'required' => false,
            'max' => 2,
            'mimes' => 'mp4,webm,mov',
            'max_size' => '20480', // in KB (20MB)
        ],

        // Video compression options (requires FFmpeg)
        'compress' => [
            'enabled' => false, // Set to true when FFmpeg is installed
            'codec' => 'libx264',
            'bitrate' => '1M', // Video bitrate
            'audio_bitrate' => '128k', // Audio bitrate
            'format' => 'mp4', // Output format
            'keep_original' => false,
        ],

        // Video processing options
        'processing' => [
            'create_thumbnails' => true,
            'thumbnail_time' => 1, // seconds into video
            'compress' => false, // Will be enabled when FFmpeg is installed
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

        // Organized path structure
        // Pattern: incidents/{municipality}/{year}/{month}/{incident_number}/{type}/{variant}
        // Example: incidents/valencia/2025/01/INC-2025-001/photos/compressed/photo_1.jpg
        'path_pattern' => '{municipality}/{year}/{month}/{incident_number}/{type}',
    ],

    /*
    |--------------------------------------------------------------------------
    | Error Messages
    |--------------------------------------------------------------------------
    */
    'messages' => [
        'photo' => [
            'max_count' => 'You can only upload up to :max photos.',
            'invalid_type' => 'Invalid file type: :filename. Only JPG, PNG, GIF, and WebP are allowed.',
            'too_large' => 'File too large: :filename (:size). Maximum size is :max.',
            'upload_failed' => 'Failed to upload photo: :filename',
            'dimensions_exceeded' => 'Photo dimensions too large: :filename. Maximum allowed is :max.',
        ],
        'video' => [
            'max_count' => 'You can only upload up to :max videos.',
            'invalid_type' => 'Invalid file type: :filename. Only MP4, WebM, and MOV are allowed.',
            'too_large' => 'File too large: :filename (:size). Maximum size is :max.',
            'upload_failed' => 'Failed to upload video: :filename',
            'duration_exceeded' => 'Video too long: :filename. Maximum duration is :max seconds.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | System Information
    |--------------------------------------------------------------------------
    */
    'system' => [
        'gd_enabled' => extension_loaded('gd'),
        'imagick_enabled' => extension_loaded('imagick'),
        'ffmpeg_available' => false, // Set to true after installing FFmpeg
    ],
];
