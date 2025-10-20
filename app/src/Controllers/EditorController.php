<?php

namespace Controllers;

use Core\Model;

class EditorController
{
    public function __construct(
        private \Core\Authentication $authentication,
        private Model $imagesModel,
        private Model $usersModel
    ) {}

    public function index()
    {
        if (!$this->authentication->isLoggedIn()) {
            header('Location: /auth/login');
            exit();
        }

        $user = $this->authentication->getUser();

        // Get user's images
        $userImages = $this->imagesModel->findByColumn('user_id', $user->id, 'created_at DESC');

        // Get available overlays
        $overlaysPath = __DIR__ . '/../../public/overlays/';
        $overlays = [];

        if (is_dir($overlaysPath)) {
            $files = scandir($overlaysPath);
            foreach ($files as $file) {
                if (
                    $file !== '.' && $file !== '..' &&
                    (pathinfo($file, PATHINFO_EXTENSION) === 'png')
                ) {
                    $overlays[] = $file;
                }
            }
        }

        return [
            'view' => 'editor/index.php',
            'title' => 'Photo Editor',
            'variables' => [
                'userImages' => $userImages,
                'overlays' => $overlays
            ]
        ];
    }

    public function captureSubmit()
    {
        if (!$this->authentication->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }

        $user = $this->authentication->getUser();

        // Get POST data
        $imageData = $_POST['image'] ?? '';
        $overlayFile = $_POST['overlay'] ?? '';

        if (empty($imageData)) {
            http_response_code(400);
            echo json_encode(['error' => 'No image data provided']);
            exit();
        }

        if (empty($overlayFile)) {
            http_response_code(400);
            echo json_encode(['error' => 'No overlay selected']);
            exit();
        }

        try {
            // Decode base64 image
            $imageData = str_replace('data:image/png;base64,', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            $decodedImage = base64_decode($imageData);

            if ($decodedImage === false) {
                throw new \Exception('Failed to decode image');
            }

            // Create image from string
            $baseImage = imagecreatefromstring($decodedImage);
            if ($baseImage === false) {
                throw new \Exception('Failed to create image from string');
            }

            // Load overlay
            $overlayPath = __DIR__ . '/../../public/overlays/' . basename($overlayFile);
            if (!file_exists($overlayPath)) {
                throw new \Exception('Overlay not found');
            }

            $overlay = imagecreatefrompng($overlayPath);
            if ($overlay === false) {
                throw new \Exception('Failed to load overlay');
            }

            // Get dimensions
            $baseWidth = imagesx($baseImage);
            $baseHeight = imagesy($baseImage);
            $overlayWidth = imagesx($overlay);
            $overlayHeight = imagesy($overlay);

            // Resize overlay to match base image
            $resizedOverlay = imagecreatetruecolor($baseWidth, $baseHeight);
            imagealphablending($resizedOverlay, false);
            imagesavealpha($resizedOverlay, true);

            imagecopyresampled(
                $resizedOverlay,
                $overlay,
                0,
                0,
                0,
                0,
                $baseWidth,
                $baseHeight,
                $overlayWidth,
                $overlayHeight
            );

            // Merge images
            imagealphablending($baseImage, true);
            imagecopy($baseImage, $resizedOverlay, 0, 0, 0, 0, $baseWidth, $baseHeight);

            // Generate unique filename
            $filename = uniqid('img_', true) . '.png';
            $uploadPath = __DIR__ . '/../../public/uploads/';

            // Create uploads directory if it doesn't exist
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $filepath = $uploadPath . $filename;

            // Save merged image
            if (!imagepng($baseImage, $filepath)) {
                throw new \Exception('Failed to save image');
            }

            // Save to database
            $this->imagesModel->save([
                'user_id' => $user->id,
                'filename' => $filename
            ]);

            // Clean up
            imagedestroy($baseImage);
            imagedestroy($overlay);
            imagedestroy($resizedOverlay);

            echo json_encode([
                'success' => true,
                'filename' => $filename,
                'message' => 'Image saved successfully'
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit();
    }

    public function delete(string $imageId)
    {
        if (!$this->authentication->isLoggedIn()) {
            header('Location: /auth/login');
            exit();
        }

        $user = $this->authentication->getUser();
        $image = $this->imagesModel->findById($imageId);

        if (!$image) {
            $_SESSION['error'] = 'Image not found';
            header('Location: /editor');
            exit();
        }

        // Check if user owns this image
        if ($image->user_id !== $user->id) {
            $_SESSION['error'] = 'You can only delete your own images';
            header('Location: /editor');
            exit();
        }

        // Delete file from filesystem
        $filepath = __DIR__ . '/../../public/uploads/' . $image->filename;
        if (file_exists($filepath)) {
            unlink($filepath);
        }

        // Delete from database
        $this->imagesModel->delete('id', $imageId);

        $_SESSION['success'] = 'Image deleted successfully';
        header('Location: /editor');
        exit();
    }
}
