# Admin Dashboard - Calamus Education Partner Portal

## Overview

This admin dashboard allows administrators to manage partners in the Calamus Education Partner Portal.

## Access

- **URL**: `/business/admin/login.php`
- **Username**: `calamuseducation@gmail.com`
- **Password**: `@$calamus5241$@`

## Features

### 1. Partner Management

- View all partners with details
- See partner statistics (total, active, verified, new this month)
- View partner information including:
  - Partner ID
  - Company Name
  - Contact Name
  - Email
  - Status (Active/Inactive/Suspended)
  - Verification Status
  - Join Date
  - Last Login
- Delete partners (with confirmation)

### 2. Statistics Dashboard

- Total Partners count
- Active Partners count
- Verified Partners count
- New Partners this month

### 3. Admin Features

- Login/Logout functionality
- Session management
- Partner deletion with data cleanup
- Refresh function to reload partner data

## File Structure

```
admin/
├── index.php          # Main admin dashboard
├── login.php          # Admin login page
├── logout.php         # Admin logout handler
└── README.md          # This file

classes/
└── admin_auth.php     # Admin authentication and management logic

api/
├── admin_login.php           # Admin login API
├── admin_delete_partner.php  # Delete partner API
└── admin_get_partners.php    # Get partners list API
```

## Security

- Uses session-based authentication
- Fixed credentials for admin access
- All admin pages check for authentication
- API endpoints validate admin session

## Theme

- Google-style black/white theme
- Minimalist design
- Responsive layout
- Consistent with main portal design

## Partner Status Values

- `active`: Partner can access the portal
- `inactive`: Partner account is disabled
- `suspended`: Partner account is suspended

## API Endpoints

### Admin Login

- **URL**: `/api/admin_login.php`
- **Method**: POST
- **Body**: `{ "username": "...", "password": "..." }`

### Get Partners

- **URL**: `/api/admin_get_partners.php`
- **Method**: GET
- **Params**: `page`, `limit`

### Delete Partner

- **URL**: `/api/admin_delete_partner.php`
- **Method**: POST
- **Body**: `{ "partner_id": "..." }`
- **Action**: Deletes partner and all related data

## Usage

1. Navigate to `/business/admin/login.php`
2. Enter admin credentials
3. View partner statistics on the dashboard
4. Browse the partners table
5. Use action buttons to view, edit, or delete partners

## Notes

- Partner deletion removes all associated data (sessions, profile images)
- All times are displayed in the database timezone
- Statistics update in real-time when the page is refreshed
