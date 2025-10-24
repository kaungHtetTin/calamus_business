<?php
/**
 * API Index - Available Endpoints
 * 
 * This file provides information about all available API endpoints.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$endpoints = [
    'authentication' => [
        'login' => [
            'url' => '/api/login_partner.php?endpoint=login',
            'method' => 'POST',
            'description' => 'Partner login',
            'parameters' => ['email', 'password', 'remember']
        ],
        'logout' => [
            'url' => '/api/login_partner.php?endpoint=logout',
            'method' => 'POST',
            'description' => 'Partner logout',
            'parameters' => ['session_token']
        ],
        'validate_session' => [
            'url' => '/api/validate_session.php',
            'method' => 'POST',
            'description' => 'Validate session token',
            'parameters' => ['session_token']
        ],
        'forgot_password' => [
            'url' => '/api/login_partner.php?endpoint=forgot_password',
            'method' => 'POST',
            'description' => 'Request password reset',
            'parameters' => ['email']
        ],
        'reset_password' => [
            'url' => '/api/login_partner.php?endpoint=reset_password',
            'method' => 'POST',
            'description' => 'Reset password with token',
            'parameters' => ['token', 'new_password', 'confirm_password']
        ],
        'change_password' => [
            'url' => '/api/login_partner.php?endpoint=change_password',
            'method' => 'POST',
            'description' => 'Change password for authenticated user',
            'parameters' => ['session_token', 'current_password', 'new_password', 'confirm_password']
        ]
    ],
    'registration' => [
        'register' => [
            'url' => '/api/register_partner.php?endpoint=register',
            'method' => 'POST',
            'description' => 'Register new partner',
            'parameters' => ['company_name', 'contact_name', 'email', 'phone', 'password', 'commission_rate', 'code_prefix']
        ],
        'verify_email' => [
            'url' => '/api/register_partner.php?endpoint=verify_email',
            'method' => 'POST',
            'description' => 'Verify email with code',
            'parameters' => ['email', 'verification_code']
        ],
        'resend_verification' => [
            'url' => '/api/register_partner.php?endpoint=resend_verification',
            'method' => 'POST',
            'description' => 'Resend verification email',
            'parameters' => ['email']
        ],
        'check_email' => [
            'url' => '/api/register_partner.php?endpoint=check_email',
            'method' => 'POST',
            'description' => 'Check if email is available',
            'parameters' => ['email']
        ],
        'check_code_prefix' => [
            'url' => '/api/register_partner.php?endpoint=check_code_prefix',
            'method' => 'POST',
            'description' => 'Check if code prefix is available',
            'parameters' => ['code_prefix']
        ]
    ],
    'profile_management' => [
        'get_partner_info' => [
            'url' => '/api/login_partner.php?endpoint=get_partner_info',
            'method' => 'POST',
            'description' => 'Get partner information',
            'parameters' => ['session_token']
        ],
        'update_profile' => [
            'url' => '/api/login_partner.php?endpoint=update_profile',
            'method' => 'POST',
            'description' => 'Update partner profile',
            'parameters' => ['session_token', 'update_data']
        ]
    ],
    'promotion_codes' => [
        'validate_code' => [
            'url' => '/api/code_validation.php?endpoint=validate_code',
            'method' => 'POST',
            'description' => 'Validate promotion code',
            'parameters' => ['code']
        ],
        'use_code' => [
            'url' => '/api/code_validation.php?endpoint=use_code',
            'method' => 'POST',
            'description' => 'Use promotion code',
            'parameters' => ['code', 'learner_phone']
        ],
        'process_vip_with_code' => [
            'url' => '/api/code_validation.php?endpoint=process_vip_with_code',
            'method' => 'POST',
            'description' => 'Process VIP subscription with promotion code',
            'parameters' => ['code', 'learner_phone', 'course_id', 'amount']
        ],
        'process_package_with_code' => [
            'url' => '/api/code_validation.php?endpoint=process_package_with_code',
            'method' => 'POST',
            'description' => 'Process package purchase with promotion code',
            'parameters' => ['code', 'learner_phone', 'package_id', 'amount']
        ]
    ],
    'promotion_management' => [
        'generate_code' => [
            'url' => '/api/promotion_codes.php?endpoint=generate_code',
            'method' => 'POST',
            'description' => 'Generate new promotion code',
            'parameters' => ['session_token', 'code_type', 'target_course_id', 'target_package_id', 'client_name', 'expires_at']
        ],
        'get_codes' => [
            'url' => '/api/promotion_codes.php?endpoint=get_codes',
            'method' => 'POST',
            'description' => 'Get partner promotion codes',
            'parameters' => ['session_token']
        ],
        'cancel_code' => [
            'url' => '/api/promotion_codes.php?endpoint=cancel_code',
            'method' => 'POST',
            'description' => 'Cancel promotion code',
            'parameters' => ['session_token', 'code_id']
        ]
    ]
];

$response = [
    'success' => true,
    'message' => 'Affiliate System API Endpoints',
    'version' => '1.0.0',
    'base_url' => 'http://localhost/business',
    'endpoints' => $endpoints,
    'usage_examples' => [
        'login' => [
            'url' => 'http://localhost/business/api/login_partner.php?endpoint=login',
            'method' => 'POST',
            'body' => [
                'email' => 'partner@example.com',
                'password' => 'password123',
                'remember' => false
            ]
        ],
        'validate_session' => [
            'url' => 'http://localhost/business/api/validate_session.php',
            'method' => 'POST',
            'body' => [
                'session_token' => 'your_session_token_here'
            ]
        ],
        'register' => [
            'url' => 'http://localhost/business/api/register_partner.php?endpoint=register',
            'method' => 'POST',
            'body' => [
                'company_name' => 'ABC Corp',
                'contact_name' => 'John Doe',
                'email' => 'john@abc.com',
                'phone' => '+1234567890',
                'password' => 'password123',
                'commission_rate' => 10,
                'code_prefix' => 'ABC'
            ]
        ]
    ],
    'testing' => [
        'test_login_api' => 'http://localhost/business/test_login_api.php',
        'test_autoloader' => 'http://localhost/business/test_autoloader.php',
        'verify_autoloader' => 'http://localhost/business/verify_autoloader.php'
    ]
];

echo json_encode($response, JSON_PRETTY_PRINT);
?>