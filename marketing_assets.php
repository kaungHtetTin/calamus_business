<?php
$pageTitle = 'Marketing Assets';
include 'layout/header.php';

// Define the marketing assets directory
$assetsDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'marketing_assets';

// Function to get all files from a directory
function getFilesFromDir($dir) {
    $files = [];
    if (is_dir($dir)) {
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item != '.' && $item != '..') {
                $filePath = $dir . DIRECTORY_SEPARATOR . $item;
                if (is_file($filePath)) {
                    // Convert file path to web-accessible path
                    $webPath = str_replace(__DIR__ . DIRECTORY_SEPARATOR, '', $filePath);
                    $webPath = str_replace('\\', '/', $webPath); // Convert Windows path to web path
                    
                    $files[] = [
                        'name' => $item,
                        'path' => $webPath,
                        'size' => filesize($filePath),
                        'modified' => filemtime($filePath)
                    ];
                }
            }
        }
        // Sort files by name
        usort($files, function($a, $b) {
            return strnatcasecmp($a['name'], $b['name']);
        });
    }
    return $files;
}

// Get all assets organized by language and type
$assets = [
    'english' => [
        'image' => [],
        'video' => []
    ],
    'korea' => [
        'image' => [],
        'video' => []
    ]
];

// Scan English assets
$englishImageDir = $assetsDir . DIRECTORY_SEPARATOR . 'english' . DIRECTORY_SEPARATOR . 'image';
$englishVideoDir = $assetsDir . DIRECTORY_SEPARATOR . 'english' . DIRECTORY_SEPARATOR . 'video';
$assets['english']['image'] = getFilesFromDir($englishImageDir);
$assets['english']['video'] = getFilesFromDir($englishVideoDir);

// Scan Korea assets
$koreaImageDir = $assetsDir . DIRECTORY_SEPARATOR . 'korea' . DIRECTORY_SEPARATOR . 'image';
$koreaVideoDir = $assetsDir . DIRECTORY_SEPARATOR . 'korea' . DIRECTORY_SEPARATOR . 'video';
$assets['korea']['image'] = getFilesFromDir($koreaImageDir);
$assets['korea']['video'] = getFilesFromDir($koreaVideoDir);

// Helper function to format file size
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

// Helper function to get file extension icon
function getFileIcon($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
    $videoExts = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'];
    
    if (in_array($ext, $imageExts)) {
        return 'fa-image';
    } elseif (in_array($ext, $videoExts)) {
        return 'fa-video';
    } else {
        return 'fa-file';
    }
}
?>

<div class="content-section">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-bullhorn me-2"></i>Marketing Assets</h2>
            <p class="text-muted mb-0">Browse and download marketing materials for your promotions</p>
        </div>
    </div>

    <!-- Language Tabs -->
    <ul class="nav nav-tabs mb-4" id="languageTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="english-tab" data-bs-toggle="tab" data-bs-target="#english" type="button" role="tab" aria-controls="english" aria-selected="true">
                <i class="fas fa-flag me-2"></i>English
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="korea-tab" data-bs-toggle="tab" data-bs-target="#korea" type="button" role="tab" aria-controls="korea" aria-selected="false">
                <i class="fas fa-flag me-2"></i>Korea
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="languageTabContent">
        <!-- English Tab -->
        <div class="tab-pane fade show active" id="english" role="tabpanel" aria-labelledby="english-tab">
            <!-- Images Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-image me-2"></i>Images
                        <span class="badge bg-secondary ms-2"><?php echo count($assets['english']['image']); ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($assets['english']['image'])): ?>
                    <div class="row g-3">
                        <?php foreach ($assets['english']['image'] as $file): ?>
                        <div class="col-md-3 col-sm-4 col-6">
                            <div class="card h-100 asset-card">
                                <div class="asset-preview position-relative">
                                    <?php if (in_array(strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                    <img src="<?php echo htmlspecialchars($file['path']); ?>" 
                                         class="card-img-top" 
                                         alt="<?php echo htmlspecialchars($file['name']); ?>"
                                         style="height: 200px; object-fit: cover; cursor: pointer;"
                                         onclick="openModal('<?php echo htmlspecialchars($file['path']); ?>', '<?php echo htmlspecialchars($file['name']); ?>')">
                                    <?php else: ?>
                                    <div class="d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                                        <i class="fas fa-file-image fa-3x text-muted"></i>
                                    </div>
                                    <?php endif; ?>
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <a href="<?php echo htmlspecialchars($file['path']); ?>" 
                                           download 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <h6 class="card-title small mb-1" style="font-size: 0.85rem;">
                                        <?php echo htmlspecialchars($file['name']); ?>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="fas fa-file me-1"></i><?php echo formatFileSize($file['size']); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-image fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No images available</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Videos Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-video me-2"></i>Videos
                        <span class="badge bg-secondary ms-2"><?php echo count($assets['english']['video']); ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($assets['english']['video'])): ?>
                    <div class="row g-3">
                        <?php foreach ($assets['english']['video'] as $file): ?>
                        <div class="col-md-4 col-sm-6 col-12">
                            <div class="card h-100 asset-card">
                                <div class="asset-preview position-relative">
                                    <video class="card-img-top" 
                                           style="width: 100%; height: 200px; object-fit: cover; background: #000; cursor: pointer; display: block;"
                                           onclick="this.paused ? this.play() : this.pause();"
                                           onmouseover="this.controls = true;"
                                           onmouseout="this.controls = false;">
                                        <source src="<?php echo htmlspecialchars($file['path']); ?>" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <a href="<?php echo htmlspecialchars($file['path']); ?>" 
                                           download 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <h6 class="card-title small mb-1" style="font-size: 0.85rem;">
                                        <?php echo htmlspecialchars($file['name']); ?>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="fas fa-file me-1"></i><?php echo formatFileSize($file['size']); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-video fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No videos available</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Korea Tab -->
        <div class="tab-pane fade" id="korea" role="tabpanel" aria-labelledby="korea-tab">
            <!-- Images Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-image me-2"></i>Images
                        <span class="badge bg-secondary ms-2"><?php echo count($assets['korea']['image']); ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($assets['korea']['image'])): ?>
                    <div class="row g-3">
                        <?php foreach ($assets['korea']['image'] as $file): ?>
                        <div class="col-md-3 col-sm-4 col-6">
                            <div class="card h-100 asset-card">
                                <div class="asset-preview position-relative">
                                    <?php if (in_array(strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                    <img src="<?php echo htmlspecialchars($file['path']); ?>" 
                                         class="card-img-top" 
                                         alt="<?php echo htmlspecialchars($file['name']); ?>"
                                         style="height: 200px; object-fit: cover; cursor: pointer;"
                                         onclick="openModal('<?php echo htmlspecialchars($file['path']); ?>', '<?php echo htmlspecialchars($file['name']); ?>')">
                                    <?php else: ?>
                                    <div class="d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                                        <i class="fas fa-file-image fa-3x text-muted"></i>
                                    </div>
                                    <?php endif; ?>
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <a href="<?php echo htmlspecialchars($file['path']); ?>" 
                                           download 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <h6 class="card-title small mb-1" style="font-size: 0.85rem;">
                                        <?php echo htmlspecialchars($file['name']); ?>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="fas fa-file me-1"></i><?php echo formatFileSize($file['size']); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-image fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No images available</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Videos Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-video me-2"></i>Videos
                        <span class="badge bg-secondary ms-2"><?php echo count($assets['korea']['video']); ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($assets['korea']['video'])): ?>
                    <div class="row g-3">
                        <?php foreach ($assets['korea']['video'] as $file): ?>
                        <div class="col-md-4 col-sm-6 col-12">
                            <div class="card h-100 asset-card">
                                <div class="asset-preview position-relative">
                                    <video class="card-img-top" 
                                           style="width: 100%; height: 200px; object-fit: cover; background: #000; cursor: pointer; display: block;"
                                           onclick="this.paused ? this.play() : this.pause();"
                                           onmouseover="this.controls = true;"
                                           onmouseout="this.controls = false;">
                                        <source src="<?php echo htmlspecialchars($file['path']); ?>" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <a href="<?php echo htmlspecialchars($file['path']); ?>" 
                                           download 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <h6 class="card-title small mb-1" style="font-size: 0.85rem;">
                                        <?php echo htmlspecialchars($file['name']); ?>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="fas fa-file me-1"></i><?php echo formatFileSize($file['size']); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-video fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No videos available</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="" class="img-fluid">
                <div class="mt-3">
                    <a id="modalDownloadBtn" href="" download class="btn btn-primary">
                        <i class="fas fa-download me-2"></i>Download
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.asset-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid #e0e0e0;
}

.asset-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.asset-preview {
    overflow: hidden;
}

.asset-preview img {
    transition: transform 0.3s;
}

.asset-card:hover .asset-preview img {
    transform: scale(1.05);
}
</style>

<script>
function openModal(imagePath, imageName) {
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    document.getElementById('modalImage').src = imagePath;
    document.getElementById('modalImage').alt = imageName;
    document.getElementById('imageModalLabel').textContent = imageName;
    document.getElementById('modalDownloadBtn').href = imagePath;
    document.getElementById('modalDownloadBtn').download = imageName;
    modal.show();
}
</script>

<?php include 'layout/footer.php'; ?>

