<?php

use Controllers\AuthController;
use Controllers\ProfileController;
use Controllers\GalleryController;

class CamagruWebsite implements \Core\Website
{
    private \Core\Authentication $authentication;
    private ?\Core\Model $usersModel;
    private ?\Core\Model $imagesModel;
    // private ?\Ninja\DatabaseTable $categoriesTable;
    // private ?\Ninja\DatabaseTable $jokeCategoriesTable;

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
            'gallery' => new GalleryController(),
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
