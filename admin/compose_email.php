<?php
/**
 * Admin - Compose Email to Partners
 * Sends emails using the general_action template
 */

require_once '../classes/admin_auth.php';
require_once '../email_config.php';

$adminAuth = new AdminAuth();

// Check if admin is logged in
if (!$adminAuth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$pageTitle = 'Compose Email';
$isAdmin = true;
$currentPage = 'compose_email';

// Defaults from query params (for deep-linking)
$defaultAudience = isset($_GET['audience']) && in_array($_GET['audience'], ['all','verified','active','specific']) ? $_GET['audience'] : 'all';
$defaultSpecificEmail = isset($_GET['email']) ? trim($_GET['email']) : '';

$successMessage = '';
$errorMessage = '';
$details = [];

// Handle submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $audience = $_POST['audience'] ?? 'all';
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $customEmails = trim($_POST['custom_emails'] ?? '');
    $specificEmail = trim($_POST['specific_email'] ?? '');

    if ($subject === '' || $message === '') {
        $errorMessage = 'Subject and message are required.';
    } else {
        $db = new Database();

        // Build recipient list
        $recipients = [];

        if ($audience === 'all' || $audience === 'verified' || $audience === 'active') {
            $where = '1=1';
            if ($audience === 'verified') {
                $where .= " AND email_verified = 1";
            }
            if ($audience === 'active') {
                $where .= " AND status = 'active'";
            }
            $rows = $db->read("SELECT id, email, contact_name FROM partners WHERE $where");
            if ($rows) {
                foreach ($rows as $row) {
                    if (!empty($row['email'])) {
                        $recipients[] = [
                            'email' => $row['email'],
                            'name' => $row['contact_name'] ?: 'Partner'
                        ];
                    }
                }
            }
        } elseif ($audience === 'specific') {
            if ($specificEmail !== '' && filter_var($specificEmail, FILTER_VALIDATE_EMAIL)) {
                $recipients[] = [
                    'email' => $specificEmail,
                    'name' => 'Partner'
                ];
            }
        }

        // Append custom emails if provided
        if (!empty($customEmails)) {
            $emails = preg_split('/[,\n;]+/', $customEmails);
            foreach ($emails as $e) {
                $e = trim($e);
                if ($e !== '' && filter_var($e, FILTER_VALIDATE_EMAIL)) {
                    $recipients[] = [
                        'email' => $e,
                        'name' => 'Partner'
                    ];
                }
            }
        }

        // De-duplicate by email
        $unique = [];
        $finalRecipients = [];
        foreach ($recipients as $r) {
            $key = strtolower($r['email']);
            if (!isset($unique[$key])) {
                $unique[$key] = true;
                $finalRecipients[] = $r;
            }
        }

        if (empty($finalRecipients)) {
            $errorMessage = 'No recipients found for the selected audience.';
        } else {
            $sentCount = 0;
            $failCount = 0;

            foreach ($finalRecipients as $r) {
                $variables = [
                    'partner_name' => $r['name'],
                    'message' => nl2br(htmlspecialchars($message))
                ];
                $template = getEmailTemplate('general_action', $variables);
                if (!$template) {
                    $template = "<div style='font-family: Arial, sans-serif;'>"
                              . "<p>Dear " . htmlspecialchars($r['name']) . ",</p>"
                              . "<p>" . nl2br(htmlspecialchars($message)) . "</p>"
                              . "<p>Regards,<br>Calamus Education</p>"
                              . "</div>";
                }

                $ok = sendEmail($r['email'], $subject, $template, 'general_action');
                if ($ok) {
                    $sentCount++;
                } else {
                    $failCount++;
                    $details[] = 'Failed: ' . htmlspecialchars($r['email']);
                }
            }

            $successMessage = "Emails sent: $sentCount. Failures: $failCount.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Calamus Education Partner Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/app.css">
    <link rel="stylesheet" href="css/app.css">
    <link rel="icon" href="../logo.png" type="image/x-icon">
</head>
<body>
    <?php include 'layout/admin_header.php'; ?>
    <?php include 'layout/admin_sidebar.php'; ?>

    <div class="container-fluid" style="padding: 24px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">Compose Email</h2>
            <a href="partners.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Partners</a>
        </div>

        <?php if (!empty($successMessage)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($successMessage); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($errorMessage); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($details)): ?>
            <div class="card mb-3">
                <div class="card-header"><strong>Details</strong></div>
                <div class="card-body">
                    <ul class="mb-0">
                        <?php foreach ($details as $line): ?>
                            <li><?php echo $line; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header"><strong>New Email</strong></div>
            <div class="card-body">
                <form method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Audience</label>
                            <select name="audience" id="audience" class="form-select" onchange="toggleSpecificPartner()">
                                <option value="all" <?php echo $defaultAudience==='all'?'selected':''; ?>>All partners</option>
                                <option value="verified" <?php echo $defaultAudience==='verified'?'selected':''; ?>>Verified partners (email verified)</option>
                                <option value="active" <?php echo $defaultAudience==='active'?'selected':''; ?>>Active partners (status = active)</option>
                                <option value="specific" <?php echo $defaultAudience==='specific'?'selected':''; ?>>Specific partner</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Additional recipients (comma/newline separated)</label>
                            <textarea name="custom_emails" class="form-control" rows="1" placeholder="optional@example.com"></textarea>
                        </div>
                    </div>

                    <div class="row mb-3" id="specificPartnerRow" style="display:none;">
                        <div class="col-md-6">
                            <label class="form-label">Specific Partner Email</label>
                            <input type="email" name="specific_email" class="form-control" placeholder="partner@example.com" value="<?php echo htmlspecialchars($defaultSpecificEmail); ?>">
                            <div class="form-text">Enter a single partner email address.</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Subject <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control" rows="8" placeholder="Write your announcement or update here..." required></textarea>
                        <div class="form-text">This will use the general email template.</div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Send Email
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSpecificPartner() {
            var audience = document.getElementById('audience').value;
            var row = document.getElementById('specificPartnerRow');
            row.style.display = audience === 'specific' ? 'block' : 'none';
        }
        // Initialize on load in case of back navigation
        document.addEventListener('DOMContentLoaded', toggleSpecificPartner);
    </script>
</body>
</html>


