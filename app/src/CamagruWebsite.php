<?php

use Controllers\AuthController;
use Controllers\ProfileController;
use Controllers\GalleryController;
use Controllers\EditorController;

class CamagruWebsite implements \Core\Website
{
    private \Core\Authentication $authentication;
    private \Core\Model $usersModel;
    private \Core\Model $imagesModel;
    private \Core\Model $likesModel;
    private \Core\Model $commentsModel;

    public function __construct()
    {
        $host = getenv('DB_HOST');
        $dbname = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASS');

        try {
            $pdo = new PDO("mysql:dbname=$dbname;host=$host", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        } catch (PDOException $e) {
            echo '❌ Connection failed: ' . htmlspecialchars($e->getMessage());
        }

        $this->usersModel = new \Core\Model(
            $pdo,
            'users',
            'id',
            '\Models\User',
            []
        );
        $this->authentication = new \Core\Authentication($this->usersModel, 'name', 'password');
        $this->imagesModel = new \Core\Model(
            $pdo,
            'images',
            'id',
            '\Models\Image',
            [$this->usersModel]
        );

        $this->likesModel = new \Core\Model(
            $pdo,
            'likes',
            'id',
            '\stdClass',
            []
        );

        $this->commentsModel = new \Core\Model(
            $pdo,
            'comments',
            'id',
            '\stdClass',
            []
        );
    }

    public function getLayoutVariables(): array
    {
        return [
            'loggedIn' => $this->authentication->isLoggedIn()
        ];
    }

    public function getDefaultRoute(): string
    {
        return 'joke/home';
    }

    public function getController(string $controllerName): ?object
    {
        $controllers = [
            'auth' => new AuthController($this->authentication, $this->usersModel),
            'profile' => new ProfileController($this->authentication, $this->usersModel),
            'gallery' => new GalleryController(
                $this->authentication,
                $this->imagesModel,
                $this->usersModel,
                $this->likesModel,
                $this->commentsModel
            ),
            'editor' => new EditorController(
                $this->authentication,
                $this->imagesModel,
                $this->usersModel
            ),
        ];

        return $controllers[$controllerName] ?? null;
    }

    public function checkLogin(string $uri): ?string
    {
        // $restrictedPages = [
        //     'category/list' => \Ijdb\Entity\Author::LIST_CATEGORIES,
        //     'category/delete' => \Ijdb\Entity\Author::DELETE_CATEGORY,
        //     'category/edit' => \Ijdb\Entity\Author::EDIT_CATEGORY,
        //     'author/permissions' => \Ijdb\Entity\Author::EDIT_USER_ACCESS,
        //     'author/list' => \Ijdb\Entity\Author::EDIT_USER_ACCESS
        // ];

        // if (isset($restrictedPages[$uri])) {
        //     if (
        //         !$this->authentication->isLoggedIn()
        //         || !$this->authentication->getUser()->hasPermission($restrictedPages[$uri])
        //     ) {
        //         header('location: /login/login');
        //         exit();
        //     }
        // }

        return $uri;
    }
}
