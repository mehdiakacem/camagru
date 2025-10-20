// Editor State
let stream = null;
let selectedOverlay = null;
let uploadedFile = null;

// DOM Elements
const webcamVideo = document.getElementById("webcam-video");
const webcamCanvas = document.getElementById("webcam-canvas");
const overlayPreview = document.getElementById("overlay-preview");
const uploadOverlayPreview = document.getElementById("upload-overlay-preview");
const startCameraBtn = document.getElementById("start-camera");
const stopCameraBtn = document.getElementById("stop-camera");
const captureBtn = document.getElementById("capture-btn");
const fileInput = document.getElementById("file-input");
const browseBtn = document.getElementById("browse-btn");
const uploadArea = document.getElementById("upload-area");
const uploadPreviewContainer = document.getElementById(
  "upload-preview-container"
);
const uploadPreview = document.getElementById("upload-preview");
const uploadSubmitBtn = document.getElementById("upload-submit-btn");
const cancelUploadBtn = document.getElementById("cancel-upload");

// Tab Switching
document.querySelectorAll(".tab-btn").forEach((btn) => {
  btn.addEventListener("click", () => {
    const tabName = btn.dataset.tab;

    // Update tab buttons
    document
      .querySelectorAll(".tab-btn")
      .forEach((b) => b.classList.remove("active"));
    btn.classList.add("active");

    // Update tab content
    document.querySelectorAll(".tab-content").forEach((content) => {
      content.classList.remove("active");
    });
    document.getElementById(tabName + "-tab").classList.add("active");

    // Stop camera when switching away from webcam tab
    if (tabName !== "webcam" && stream) {
      stopCamera();
    }
  });
});

// Overlay Selection
document.querySelectorAll(".overlay-item").forEach((item) => {
  item.addEventListener("click", () => {
    // Remove previous selection
    document
      .querySelectorAll(".overlay-item")
      .forEach((i) => i.classList.remove("selected"));

    // Select current
    item.classList.add("selected");
    selectedOverlay = item.dataset.overlay;

    // Update overlay preview
    const overlayPath = "/overlays/" + selectedOverlay;
    overlayPreview.src = overlayPath;
    uploadOverlayPreview.src = overlayPath;
    overlayPreview.style.display = "block";
    uploadOverlayPreview.style.display = "block";

    // Enable capture button if camera is active
    if (stream) {
      captureBtn.disabled = false;
    }

    // Enable upload submit if file is uploaded
    if (uploadedFile) {
      uploadSubmitBtn.disabled = false;
    }
  });
});

// Webcam Functions
startCameraBtn.addEventListener("click", async () => {
  try {
    stream = await navigator.mediaDevices.getUserMedia({
      video: {
        width: { ideal: 640 },
        height: { ideal: 480 },
      },
    });

    webcamVideo.srcObject = stream;
    webcamVideo.style.display = "block";

    startCameraBtn.style.display = "none";
    stopCameraBtn.style.display = "inline-block";

    if (selectedOverlay) {
      captureBtn.disabled = false;
    }
  } catch (err) {
    alert("Error accessing camera: " + err.message);
  }
});

stopCameraBtn.addEventListener("click", () => {
  stopCamera();
});

function stopCamera() {
  if (stream) {
    stream.getTracks().forEach((track) => track.stop());
    stream = null;
    webcamVideo.srcObject = null;
    webcamVideo.style.display = "none";

    startCameraBtn.style.display = "inline-block";
    stopCameraBtn.style.display = "none";
    captureBtn.disabled = true;
  }
}

captureBtn.addEventListener("click", async () => {
  if (!selectedOverlay) {
    alert("Please select an overlay first");
    return;
  }

  // Set canvas size to match video
  webcamCanvas.width = webcamVideo.videoWidth;
  webcamCanvas.height = webcamVideo.videoHeight;

  // Draw video frame to canvas
  const ctx = webcamCanvas.getContext("2d");
  ctx.drawImage(webcamVideo, 0, 0);

  // Get base64 image data
  const imageData = webcamCanvas.toDataURL("image/png");

  // Send to server
  captureBtn.disabled = true;
  captureBtn.textContent = "Processing...";

  try {
    const formData = new FormData();
    formData.append("image", imageData);
    formData.append("overlay", selectedOverlay);

    const response = await fetch("/editor/capture", {
      method: "POST",
      body: formData,
    });

    const result = await response.json();

    if (result.success) {
      alert("Image saved successfully!");
      location.reload();
    } else {
      alert("Error: " + result.error);
    }
  } catch (err) {
    alert("Error saving image: " + err.message);
  } finally {
    captureBtn.disabled = selectedOverlay ? false : true;
    captureBtn.textContent = "📸 Capture Photo";
  }
});

// Upload Functions
browseBtn.addEventListener("click", () => {
  fileInput.click();
});

uploadArea.addEventListener("click", () => {
  fileInput.click();
});

// Drag and Drop
uploadArea.addEventListener("dragover", (e) => {
  e.preventDefault();
  uploadArea.classList.add("dragover");
});

uploadArea.addEventListener("dragleave", () => {
  uploadArea.classList.remove("dragover");
});

uploadArea.addEventListener("drop", (e) => {
  e.preventDefault();
  uploadArea.classList.remove("dragover");

  const files = e.dataTransfer.files;
  if (files.length > 0) {
    handleFileSelect(files[0]);
  }
});

fileInput.addEventListener("change", (e) => {
  if (e.target.files.length > 0) {
    handleFileSelect(e.target.files[0]);
  }
});

function handleFileSelect(file) {
  // Validate file type
  if (!file.type.match("image.*")) {
    alert("Please select an image file");
    return;
  }

  // Validate file size (5MB)
  if (file.size > 2 * 1024 * 1024) {
    alert("File size must be less than 2MB");
    return;
  }

  uploadedFile = file;

  // Show preview
  const reader = new FileReader();
  reader.onload = (e) => {
    uploadPreview.src = e.target.result;
    uploadArea.style.display = "none";
    uploadPreviewContainer.style.display = "block";

    if (selectedOverlay) {
      uploadSubmitBtn.disabled = false;
    }
  };
  reader.readAsDataURL(file);
}

cancelUploadBtn.addEventListener("click", () => {
  uploadedFile = null;
  uploadPreview.src = "";
  uploadArea.style.display = "block";
  uploadPreviewContainer.style.display = "none";
  fileInput.value = "";
  uploadSubmitBtn.disabled = true;
});

uploadSubmitBtn.addEventListener("click", async () => {
  if (!selectedOverlay) {
    alert("Please select an overlay first");
    return;
  }

  if (!uploadedFile) {
    alert("Please upload an image first");
    return;
  }

  uploadSubmitBtn.disabled = true;
  uploadSubmitBtn.textContent = "Processing...";

  try {
    const formData = new FormData();
    formData.append("image", uploadedFile);
    formData.append("overlay", selectedOverlay);

    const response = await fetch("/editor/upload", {
      method: "POST",
      body: formData,
    });

    const result = await response.json();

    if (result.success) {
      alert("Image saved successfully!");
      location.reload();
    } else {
      alert("Error: " + result.error);
    }
  } catch (err) {
    alert("Error saving image: " + err.message);
  } finally {
    uploadSubmitBtn.disabled = false;
    uploadSubmitBtn.textContent = "Save Image";
  }
});

// Delete Image
function confirmDelete(imageId) {
  if (confirm("Are you sure you want to delete this image?")) {
    window.location.href = "/editor/delete/" + imageId;
  }
}

// Cleanup on page unload
window.addEventListener("beforeunload", () => {
  if (stream) {
    stream.getTracks().forEach((track) => track.stop());
  }
});
