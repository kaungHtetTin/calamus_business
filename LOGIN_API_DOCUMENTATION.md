# Partner Login API Documentation

## Overview

The Partner Login API (`/api/login_partner.php`) provides comprehensive authentication and session management for partners in the affiliate system.

## Base URL

```
http://localhost/business/api/login_partner.php
```

## Authentication

All endpoints require proper session tokens for authenticated operations. Session tokens are returned upon successful login and must be included in subsequent requests.

## Endpoints

### 1. Login

**POST** `/api/login_partner.php?endpoint=login`

Authenticates a partner and creates a session.

#### Request Body:

```json
{
  "email": "partner@example.com",
  "password": "password123",
  "remember": false
}
```

#### Response (Success):

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

#### Response (Error):

```json
{
  "success": false,
  "message": "Invalid credentials"
}
```

### 2. Logout

**POST** `/api/login_partner.php?endpoint=logout`

Terminates the current session.

#### Request Body:

```json
{
  "session_token": "abc123..."
}
```

#### Response:

```json
{
  "success": true,
  "message": "Logged out successfully",
  "redirect_url": "../partner_login.php"
}
```

### 3. Validate Session

**POST** `/api/login_partner.php?endpoint=validate_session`

Validates if a session token is still active.

#### Request Body:

```json
{
  "session_token": "abc123..."
}
```

#### Response (Valid):

```json
{
  "success": true,
  "partner": {
    "id": 1,
    "email": "partner@example.com",
    "contact_name": "John Doe",
    "company_name": "ABC Corp",
    "status": "active",
    "commission_rate": "10.00"
  }
}
```

#### Response (Invalid):

```json
{
  "success": false,
  "message": "Invalid or expired session"
}
```

### 4. Forgot Password

**POST** `/api/login_partner.php?endpoint=forgot_password`

Sends a password reset email to the partner.

#### Request Body:

```json
{
  "email": "partner@example.com"
}
```

#### Response:

```json
{
  "success": true,
  "message": "Password reset link sent to your email"
}
```

### 5. Reset Password

**POST** `/api/login_partner.php?endpoint=reset_password`

Resets password using a reset token.

#### Request Body:

```json
{
  "token": "reset_token_here",
  "new_password": "newpassword123",
  "confirm_password": "newpassword123"
}
```

#### Response:

```json
{
  "success": true,
  "message": "Password reset successfully"
}
```

### 6. Change Password

**POST** `/api/login_partner.php?endpoint=change_password`

Changes password for authenticated user.

#### Request Body:

```json
{
  "session_token": "abc123...",
  "current_password": "oldpassword123",
  "new_password": "newpassword123",
  "confirm_password": "newpassword123"
}
```

#### Response:

```json
{
  "success": true,
  "message": "Password changed successfully"
}
```

### 7. Get Partner Info

**POST** `/api/login_partner.php?endpoint=get_partner_info`

Retrieves partner information for authenticated user.

#### Request Body:

```json
{
  "session_token": "abc123..."
}
```

#### Response:

```json
{
  "success": true,
  "partner": {
    "id": 1,
    "company_name": "ABC Corp",
    "contact_name": "John Doe",
    "email": "partner@example.com",
    "phone": "+1234567890",
    "website": "https://abc-corp.com",
    "description": "Marketing agency",
    "commission_rate": "10.00",
    "code_prefix": "ABC",
    "payment_method": "bank_transfer",
    "status": "active",
    "email_verified": 1,
    "created_at": "2024-01-15 10:30:00",
    "last_login": "2024-01-15 14:30:00"
  }
}
```

### 8. Update Profile

**POST** `/api/login_partner.php?endpoint=update_profile`

Updates partner profile information.

#### Request Body:

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

#### Response:

```json
{
  "success": true,
  "message": "Partner information updated successfully"
}
```

## Error Handling

### Common Error Responses:

#### Validation Errors:

```json
{
  "success": false,
  "message": "Email and password are required"
}
```

#### Authentication Errors:

```json
{
  "success": false,
  "message": "Invalid credentials"
}
```

#### Session Errors:

```json
{
  "success": false,
  "message": "Invalid session"
}
```

#### Method Not Allowed:

```json
{
  "success": false,
  "message": "Method not allowed"
}
```

## Security Features

### 1. Session Management

- Sessions expire after 7 days (30 days if "remember me" is checked)
- Session tokens are cryptographically secure
- Sessions are validated on each request

### 2. Password Security

- Passwords are hashed using PHP's `password_hash()`
- Password strength validation (minimum 8 characters)
- Password confirmation required for changes

### 3. Input Validation

- Email format validation
- Required field validation
- SQL injection prevention through prepared statements

### 4. Logging

- Login attempts are logged to `logs/login.log`
- Failed login attempts are tracked
- Email sending attempts are logged

## Usage Examples

### JavaScript/AJAX Example:

```javascript
// Login
fetch("api/login_partner.php?endpoint=login", {
  method: "POST",
  headers: {
    "Content-Type": "application/json",
  },
  body: JSON.stringify({
    email: "partner@example.com",
    password: "password123",
    remember: true,
  }),
})
  .then((response) => response.json())
  .then((data) => {
    if (data.success) {
      // Store session token
      localStorage.setItem("session_token", data.session_token);
      // Redirect to dashboard
      window.location.href = data.redirect_url;
    } else {
      alert("Login failed: " + data.message);
    }
  });
```

### PHP Example:

```php
// Login
$loginData = [
    'email' => 'partner@example.com',
    'password' => 'password123',
    'remember' => false
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/business/api/login_partner.php?endpoint=login');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
if ($result['success']) {
    // Login successful
    $sessionToken = $result['session_token'];
    $partner = $result['partner'];
}
```

## Testing

### Test Page

Visit `http://localhost/business/test_login_api.php` to test the API endpoints interactively.

### Test Credentials

Use the partner registration system to create test accounts, or insert test data directly into the database.

## Database Schema

### Required Tables:

- `partners` - Partner information
- `partner_sessions` - Session management
- `logs/login.log` - Login attempt logs

## CORS Support

The API includes CORS headers for cross-origin requests:

```php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
```

## Status Codes

- **200** - Success
- **400** - Bad Request (validation errors)
- **401** - Unauthorized (authentication errors)
- **405** - Method Not Allowed
- **500** - Internal Server Error

## Rate Limiting

Consider implementing rate limiting for login attempts to prevent brute force attacks.

## Next Steps

1. Test the API using the provided test page
2. Integrate with your frontend applications
3. Implement additional security measures as needed
4. Set up proper email configuration for password reset functionality
