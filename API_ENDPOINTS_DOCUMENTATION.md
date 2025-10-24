# API Endpoints Documentation

## Overview

The Affiliate System provides a comprehensive REST API for partner management, authentication, and promotion code handling.

## Base URL

```
http://localhost/business/api/
```

## Available Endpoints

### 1. Authentication Endpoints

#### Login

- **URL**: `/api/login_partner.php?endpoint=login`
- **Method**: `POST`
- **Description**: Partner login
- **Parameters**:
  ```json
  {
    "email": "partner@example.com",
    "password": "password123",
    "remember": false
  }
  ```
- **Response**:
  ```json
  {
    "success": true,
    "message": "Login successful",
    "session_token": "abc123...",
    "partner": {
      "id": 1,
      "email": "partner@example.com",
      "contact_name": "John Doe",
      "company_name": "ABC Corp"
    },
    "redirect_url": "../index.php"
  }
  ```

#### Logout

- **URL**: `/api/login_partner.php?endpoint=logout`
- **Method**: `POST`
- **Parameters**:
  ```json
  {
    "session_token": "abc123..."
  }
  ```

#### Validate Session

- **URL**: `/api/validate_session.php` ✅ **NEW ENDPOINT**
- **Method**: `POST`
- **Description**: Validate session token
- **Parameters**:
  ```json
  {
    "session_token": "abc123..."
  }
  ```
- **Alternative URL**: `/api/login_partner.php?endpoint=validate_session`

#### Forgot Password

- **URL**: `/api/login_partner.php?endpoint=forgot_password`
- **Method**: `POST`
- **Parameters**:
  ```json
  {
    "email": "partner@example.com"
  }
  ```

#### Reset Password

- **URL**: `/api/login_partner.php?endpoint=reset_password`
- **Method**: `POST`
- **Parameters**:
  ```json
  {
    "token": "reset_token_here",
    "new_password": "newpassword123",
    "confirm_password": "newpassword123"
  }
  ```

#### Change Password

- **URL**: `/api/login_partner.php?endpoint=change_password`
- **Method**: `POST`
- **Parameters**:
  ```json
  {
    "session_token": "abc123...",
    "current_password": "oldpassword123",
    "new_password": "newpassword123",
    "confirm_password": "newpassword123"
  }
  ```

### 2. Registration Endpoints

#### Register Partner

- **URL**: `/api/register_partner.php?endpoint=register`
- **Method**: `POST`
- **Parameters**:
  ```json
  {
    "company_name": "ABC Corp",
    "contact_name": "John Doe",
    "email": "john@abc.com",
    "phone": "+1234567890",
    "password": "password123",
    "commission_rate": 10,
    "code_prefix": "ABC"
  }
  ```

#### Verify Email

- **URL**: `/api/register_partner.php?endpoint=verify_email`
- **Method**: `POST`
- **Parameters**:
  ```json
  {
    "email": "john@abc.com",
    "verification_code": "123456"
  }
  ```

#### Resend Verification

- **URL**: `/api/register_partner.php?endpoint=resend_verification`
- **Method**: `POST`
- **Parameters**:
  ```json
  {
    "email": "john@abc.com"
  }
  ```

#### Check Email Availability

- **URL**: `/api/register_partner.php?endpoint=check_email`
- **Method**: `POST`
- **Parameters**:
  ```json
  {
    "email": "john@abc.com"
  }
  ```

#### Check Code Prefix Availability

- **URL**: `/api/register_partner.php?endpoint=check_code_prefix`
- **Method**: `POST`
- **Parameters**:
  ```json
  {
    "code_prefix": "ABC"
  }
  ```

### 3. Profile Management

#### Get Partner Info

- **URL**: `/api/login_partner.php?endpoint=get_partner_info`
- **Method**: `POST`
- **Parameters**:
  ```json
  {
    "session_token": "abc123..."
  }
  ```

#### Update Profile

- **URL**: `/api/login_partner.php?endpoint=update_profile`
- **Method**: `POST`
- **Parameters**:
  ```json
  {
    "session_token": "abc123...",
    "update_data": {
      "company_name": "New Company Name",
      "contact_name": "Jane Doe",
      "phone": "+0987654321",
      "website": "https://new-website.com",
      "description": "Updated description"
    }
  }
  ```

### 4. Promotion Code Management

#### Validate Code

- **URL**: `/api/code_validation.php?endpoint=validate_code`
- **Method**: `POST`
- **Parameters**:
  ```json
  {
    "code": "ABC-VIP-001-1234"
  }
  ```

#### Use Code

- **URL**: `/api/code_validation.php?endpoint=use_code`
- **Method**: `POST`
- **Parameters**:
  ```json
  {
    "code": "ABC-VIP-001-1234",
    "learner_phone": "1234567890"
  }
  ```

#### Process VIP with Code

- **URL**: `/api/code_validation.php?endpoint=process_vip_with_code`
- **Method**: `POST`
- **Parameters**:
  ```json
  {
    "code": "ABC-VIP-001-1234",
    "learner_phone": "1234567890",
    "course_id": 1,
    "amount": 99
  }
  ```

#### Process Package with Code

- **URL**: `/api/code_validation.php?endpoint=process_package_with_code`
- **Method**: `POST`
- **Parameters**:
  ```json
  {
    "code": "ABC-PKG-002-5678",
    "learner_phone": "1234567890",
    "package_id": 1,
    "amount": 299
  }
  ```

### 5. Promotion Code Generation

#### Generate Code

- **URL**: `/api/promotion_codes.php?endpoint=generate_code`
- **Method**: `POST`
- **Parameters**:
  ```json
  {
    "session_token": "abc123...",
    "code_type": "vip_subscription",
    "target_course_id": 1,
    "target_package_id": null,
    "client_name": "John Doe",
    "expires_at": "2024-12-31 23:59:59"
  }
  ```

#### Get Codes

- **URL**: `/api/promotion_codes.php?endpoint=get_codes`
- **Method**: `POST`
- **Parameters**:
  ```json
  {
    "session_token": "abc123..."
  }
  ```

#### Cancel Code

- **URL**: `/api/promotion_codes.php?endpoint=cancel_code`
- **Method**: `POST`
- **Parameters**:
  ```json
  {
    "session_token": "abc123...",
    "code_id": 123
  }
  ```

## Testing Endpoints

### Test Pages

- **Login API Test**: `http://localhost/business/test_login_api.php`
- **Session Validation Test**: `http://localhost/business/test_validate_session.php` ✅ **NEW**
- **Autoloader Test**: `http://localhost/business/test_autoloader.php`
- **Email Test**: `http://localhost/business/test_email.php`

### API Index

- **All Endpoints**: `http://localhost/business/api/index.php` ✅ **NEW**

## Error Handling

### Common Error Responses

```json
{
  "success": false,
  "message": "Error description"
}
```

### HTTP Status Codes

- **200** - Success
- **400** - Bad Request (validation errors)
- **401** - Unauthorized (authentication errors)
- **405** - Method Not Allowed
- **500** - Internal Server Error

## CORS Support

All API endpoints include CORS headers for cross-origin requests:

```php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
```

## Usage Examples

### JavaScript/AJAX

```javascript
// Validate session
fetch("api/validate_session.php", {
  method: "POST",
  headers: {
    "Content-Type": "application/json",
  },
  body: JSON.stringify({
    session_token: "your_session_token",
  }),
})
  .then((response) => response.json())
  .then((data) => {
    if (data.success) {
      console.log("Partner:", data.partner);
    } else {
      console.log("Error:", data.message);
    }
  });
```

### PHP cURL

```php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/business/api/validate_session.php');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['session_token' => 'your_token']));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
```

## Security Features

### Session Management

- Secure session tokens (cryptographically generated)
- Session expiry (7 days default, 30 days with "remember me")
- Session validation on each request

### Input Validation

- Email format validation
- Required field validation
- SQL injection prevention
- XSS protection

### Password Security

- Password hashing using PHP's `password_hash()`
- Password strength validation (minimum 8 characters)
- Password confirmation required for changes

## Status: All Endpoints Working ✅

The API is fully functional with all endpoints tested and working correctly. The new `validate_session.php` endpoint provides easy access to session validation functionality.
