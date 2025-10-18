<link rel="stylesheet" href="/css/editor.css">

<div class="editor-container">
    <div class="editor-layout">
        <!-- Main Editor Section -->
        <div class="editor-main">
            <div class="editor-tabs">
                <button class="tab-btn active" data-tab="webcam">
                    Webcam
                </button>
                <button class="tab-btn" data-tab="upload">
                    Upload
                </button>
            </div>

            <!-- Webcam Tab -->
            <div id="webcam-tab" class="tab-content active">
                <div class="preview-container">
                    <video id="webcam-video" autoplay playsinline></video>
                    <canvas id="webcam-canvas"></canvas>
                    <img id="overlay-preview" class="overlay-layer" />
                </div>

                <div class="controls">
                    <button id="start-camera" class="btn btn-primary">
                        Start Camera
                    </button>
                    <button id="capture-btn" class="btn btn-success" disabled>
                        📸 Capture Photo
                    </button>
                    <button id="stop-camera" class="btn btn-secondary" style="display: none;">
                        Stop Camera
                    </button>
                </div>
            </div>

            <!-- Upload Tab -->
            <div id="upload-tab" class="tab-content">
                <div class="upload-area" id="upload-area">
                    <p>Drag and drop an image here or click to browse</p>
                    <input type="file" id="file-input" accept="image/*" style="display: none;">
                    <button class="btn btn-primary" id="browse-btn">Choose File</button>
                </div>

                <div id="upload-preview-container" style="display: none;">
                    <div class="preview-container">
                        <img id="upload-preview" />
                        <img id="upload-overlay-preview" class="overlay-layer" />
                    </div>
                    <div class="controls">
                        <button id="upload-submit-btn" class="btn btn-success" disabled>
                            Save Image
                        </button>
                        <button id="cancel-upload" class="btn btn-secondary">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>

            <!-- Overlay Selection -->
            <div class="overlay-section">
                <h3>Select Overlay</h3>
                <p class="help-text">Choose an overlay to apply to your image (required)</p>
                <div class="overlay-grid">
                    <?php if (empty($overlays)): ?>
                        <p class="no-overlays">No overlays available. Please add PNG images to /public/overlays/</p>
                    <?php else: ?>
                        <?php foreach ($overlays as $overlay): ?>
                            <div class="overlay-item" data-overlay="<?= htmlspecialchars($overlay) ?>">
                                <img src="/overlays/<?= htmlspecialchars($overlay) ?>"
                                    alt="<?= htmlspecialchars(pathinfo($overlay, PATHINFO_FILENAME)) ?>">
                                <span class="overlay-name"><?= htmlspecialchars(pathinfo($overlay, PATHINFO_FILENAME)) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/editor.js"></script>