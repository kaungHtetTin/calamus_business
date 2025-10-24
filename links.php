<?php
$pageTitle = 'Affiliate Links';
include 'layout/header.php';

// Get affiliate links data
$affiliateLinks = $dashboard->getAffiliateLinks($currentPartner['id']);
?>

<!-- Affiliate Links Section -->
<div class="content-section">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-link me-2"></i>Affiliate Links</h2>
        <button class="btn btn-primary" onclick="showCreateLinkModal()">
            <i class="fas fa-plus me-2"></i>Create New Link
        </button>
    </div>
    
    <div id="affiliate-links-list">
        <?php foreach ($affiliateLinks as $link): ?>
        <div class="affiliate-link">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1"><?php echo htmlspecialchars($link['campaign_name']); ?></h6>
                    <small class="text-muted">Code: <?php echo htmlspecialchars($link['link_code']); ?></small>
                    <br>
                    <small class="text-muted">Clicks: <?php echo $link['clicks']; ?> | Conversions: <?php echo $link['conversions']; ?></small>
                </div>
                <div class="text-end">
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" value="http://localhost/business/affiliate.php?ref=<?php echo $link['link_code']; ?>" readonly>
                        <button class="btn btn-outline-secondary copy-btn" onclick="copyToClipboard(this.previousElementSibling)">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <small class="text-success">Earnings: $<?php echo number_format($link['commission_earned'], 2); ?></small>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Create Link Modal -->
<div class="modal fade" id="createLinkModal" tabindex="-1" aria-labelledby="createLinkModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createLinkModalLabel">Create New Affiliate Link</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createLinkForm">
                    <div class="mb-3">
                        <label for="campaign_name" class="form-label">Campaign Name</label>
                        <input type="text" class="form-control" id="campaign_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="target_course" class="form-label">Target Course (Optional)</label>
                        <select class="form-select" id="target_course">
                            <option value="">All Courses</option>
                            <!-- Add course options here -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="target_major" class="form-label">Target Major (Optional)</label>
                        <input type="text" class="form-control" id="target_major" placeholder="e.g., Computer Science">
                    </div>
                    <div class="mb-3">
                        <label for="custom_url" class="form-label">Custom URL (Optional)</label>
                        <input type="url" class="form-control" id="custom_url" placeholder="https://example.com">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="createAffiliateLink()">Create Link</button>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
