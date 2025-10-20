<?php

namespace Controllers;

use Core\Model;

class GalleryController
{
    private const ITEMS_PER_PAGE = 5;

    public function __construct(
        private \Core\Authentication $authentication,
        private Model $imagesModel,
        private Model $usersModel,
        private Model $likesModel,
        private Model $commentsModel,
    ) {}

    public function index()
    {
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page'] > 1000 ? 1000 : (int)$_GET['page']) : 1;
        $offset = ($page - 1) * self::ITEMS_PER_PAGE;

        // Get total count for pagination
        $totalImages = $this->imagesModel->total();
        $totalPages = ceil($totalImages / self::ITEMS_PER_PAGE);

        // Get images with pagination
        $images = $this->imagesModel->findAll(
            'created_at DESC',
            self::ITEMS_PER_PAGE,
            $offset
        );

        // Enrich images with user info, likes, and comments
        $enrichedImages = [];
        $currentUser = $this->authentication->getUser();

        foreach ($images as $image) {
            $user = $this->usersModel->findById($image->user_id);

            // Count likes
            $likesCount = $this->countLikes($image->id);

            // Check if current user has liked
            $userHasLiked = false;
            if ($currentUser) {
                $userHasLiked = $this->hasUserLiked($image->id, $currentUser->id);
            }

            // Get comments with user info
            $comments = $this->getCommentsForImage($image->id);

            $enrichedImages[] = [
                'image' => $image,
                'user' => $user,
                'likes_count' => $likesCount,
                'user_has_liked' => $userHasLiked,
                'comments' => $comments
            ];
        }

        return [
            'view' => 'gallery/index.php',
            'title' => 'Gallery',
            'variables' => [
                'images' => $enrichedImages,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'isLoggedIn' => $this->authentication->isLoggedIn()
            ]
        ];
    }

    public function likeSubmit(string $imageId)
    {
        if (!$this->authentication->isLoggedIn()) {
            header('Location: /auth/login');
            exit();
        }

        $user = $this->authentication->getUser();

        // Check if already liked
        if ($this->hasUserLiked($imageId, $user->id)) {
            // Unlike
            $this->likesModel->deleteWhere([
                'image_id' => $imageId,
                'user_id' => $user->id
            ]);
        } else {
            // Like
            $this->likesModel->save([
                'image_id' => $imageId,
                'user_id' => $user->id
            ]);
        }

        // Redirect back to gallery
        $referer = $_SERVER['HTTP_REFERER'] ?? '/gallery';
        header('Location: ' . $referer);
        exit();
    }

    public function commentSubmit(string $imageId)
    {
        if (!$this->authentication->isLoggedIn()) {
            header('Location: /auth/login');
            exit();
        }

        $user = $this->authentication->getUser();
        $comment = trim($_POST['comment'] ?? '');

        if (empty($comment)) {
            $_SESSION['comment_error'] = 'Comment cannot be empty';
            header('Location: /gallery?page=' . ($_GET['page'] ?? 1));
            exit();
        }

        // Save comment
        $this->commentsModel->save([
            'image_id' => $imageId,
            'user_id' => $user->id,
            'comment' => $comment
        ]);

        // Send notification to image owner
        $image = $this->imagesModel->findById($imageId);
        if ($image) {
            $imageOwner = $this->usersModel->findById($image->user_id);

            // Only send if not commenting on own image and owner has notifications enabled
            if (
                $imageOwner &&
                $imageOwner->id !== $user->id &&
                ($imageOwner->email_notifications ?? true)
            ) {
                $this->sendCommentNotification($imageOwner, $user, $image);
            }
        }

        // Redirect back to gallery
        $referer = $_SERVER['HTTP_REFERER'] ?? '/gallery';
        header('Location: ' . $referer);
        exit();
    }

    private function countLikes(int $imageId): int
    {
        return count($this->likesModel->findByColumn('image_id', $imageId));
    }

    private function hasUserLiked(int $imageId, int $userId): bool
    {
        $likes = $this->likesModel->findMultiple([
            'image_id' => $imageId,
            'user_id' => $userId
        ]);
        return !empty($likes);
    }

    private function getCommentsForImage(int $imageId): array
    {
        $comments = $this->commentsModel->findByColumn(
            'image_id',
            $imageId,
            'created_at DESC'
        );

        $enrichedComments = [];
        foreach ($comments as $comment) {
            $user = $this->usersModel->findById($comment->user_id);
            $enrichedComments[] = [
                'comment' => $comment,
                'user' => $user
            ];
        }

        return $enrichedComments;
    }

    private function sendCommentNotification($imageOwner, $commenter, $image)
    {
        $base_url = $_ENV['APP_URL'] ?? 'http://localhost';
        $gallery_link = $base_url . "/gallery";

        $to = $imageOwner->email;
        $subject = "New comment on your Camagru image";
        $message = "
            <html>
            <head>
                <title>New Comment</title>
            </head>
            <body>
                <h2>New Comment on Your Image</h2>
                <p>Hi {$imageOwner->name},</p>
                <p><strong>{$commenter->name}</strong> commented on your image.</p>
                <p><a href='{$gallery_link}'>View in Gallery</a></p>
                <p>If you don't want to receive these notifications, you can disable them in your profile settings.</p>
            </body>
            </html>
        ";

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
        $headers .= "From: Camagru <noreply@camagru.com>" . "\r\n";

        mail($to, $subject, $message, $headers);
    }
}
