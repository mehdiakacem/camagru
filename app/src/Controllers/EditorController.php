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

}
