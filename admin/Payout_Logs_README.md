# Payout Logs Feature - Admin Dashboard

## Overview

The Payout Logs section allows admins to view, filter, and process payouts for partners grouped by partner ID.

## Features

### 1. **Grouped Partner Data**

- Groups all `partner_earnings` records by `partner_id`
- Sums the total `amount_received` for each partner
- Counts the number of transactions per partner

### 2. **Advanced Filtering**

- **Status Filter**: Filter by "All Status", "Pending", or "Paid"
- **Date Range Filter**: Filter by start date and/or end date
- Filters persist across pagination via URL parameters

### 3. **Intelligent Sorting**

- Orders by status: Pending first, then Paid
- Within each status, orders by total amount (descending)
- Most important payouts appear at the top

### 4. **Pagination**

- Shows 20 payout logs per page
- Displays page numbers with ellipsis for large page counts
- Shows "Showing X to Y of Z logs" counter
- All filters and pagination states persist in URL

### 5. **Process Payout Action**

- Only pending payouts show "Process Payout" button
- Button confirms action with partner name and amount
- Updates all pending earnings for that partner to "paid" status
- Redirects back to payout logs with success/error message

### 6. **Statistics Dashboard**

- **Total Payout**: Sum of all grouped payout amounts
- **Total Partners**: Number of unique partners with earnings
- **Pending Payout**: Total amount that needs to be paid out
- **Paid Amount**: Total amount already paid out

## File Structure

```
admin/
├── payout_logs.php              # Main payout logs page
├── process_payout.php           # Payout handler
├── earning_logs.php             # Detailed earnings view
├── index.php                    # Dashboard
├── partners.php                 # Partner management
└── view_partner.php             # Partner details

classes/
└── admin_auth.php               # Backend methods:
                                 # - getPayoutLogs()
                                 # - getPayoutLogsStatistics()
                                 # - processPayout()

layout/
├── admin_header.php             # Reusable header
└── admin_sidebar.php            # Navigation with active states
```

## How It Works

### 1. **Data Retrieval**

```php
// Groups earnings by partner and sums amounts
$logsData = $adminAuth->getPayoutLogs($page, $limit, $status, $startDate, $endDate);
```

### 2. **Sorting Logic**

```sql
ORDER BY
    CASE
        WHEN status = 'pending' THEN 0
        WHEN status = 'paid' THEN 1
        ELSE 2
    END,
    total_amount DESC
```

### 3. **Payout Processing**

```php
// When admin clicks "Process Payout"
$result = $adminAuth->processPayout($partnerId);
// Updates all pending earnings for that partner to paid
```

## User Interface

### Statistics Cards

- Clean cards showing key metrics
- Hover effects for better UX
- Responsive grid layout

### Filter Section

- Three filter inputs: Status, Start Date, End Date
- "Apply Filters" button to submit
- "Clear Filters" link appears when filters are active

### Table Display

- Shows partner information, total amount, transaction count
- Status badges (orange for pending, green for paid)
- "Process Payout" button only for pending payouts
- Empty state with icon when no data

### Pagination

- Previous/Next buttons (disabled at boundaries)
- Page number links
- Ellipsis (...) for page jumps
- All filters preserved in pagination links

## Navigation

The Payout Logs menu item has been added to:

- Sidebar navigation
- Active state highlighting
- Consistent icon (credit card)

## Security

- Admin authentication required
- Session validation
- SQL injection protection via prepared queries
- Confirmation dialog before processing payout

## Usage Flow

1. Admin navigates to **Payout Logs**
2. Views statistics and filters data (optional)
3. Finds pending payouts (sorted to top)
4. Clicks "Process Payout" for a partner
5. Confirms the action
6. All pending earnings for that partner are marked as "paid"
7. Page redirects with success message
8. Updated data is displayed

## Example Data Display

```
Partner: John Doe (Company: ABC Inc.)
Total Amount: 150,000 MMK
Transactions: 15
Status: Pending
[Process Payout Button]

Partner: Jane Smith (Company: XYZ Ltd.)
Total Amount: 125,000 MMK
Transactions: 10
Status: Paid
[Already Paid]
```

This feature provides a complete payout management system for admins!
