<?php

class CamagruWebsite implements \Core\Website
{
    private \Core\Authentication $authentication;
    private ?\Core\Model $authorsTable;
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
            // $query = $pdo->query('SHOW VARIABLES LIKE "version"');
            // $row = $query->fetch();
            // echo '✅ Connected successfully<br>';
            // echo 'MySQL version: ' . htmlspecialchars($row['Value']);
        } catch (PDOException $e) {
            echo '❌ Connection failed: ' . htmlspecialchars($e->getMessage());
        }

        $this->authorsTable = new \Core\Model(
            $pdo,
            'author',
            'id',
            '\Ijdb\Entity\Author',
            // [&$this->jokesTable]
        );
        $this->authentication = new \Core\Authentication($this->authorsTable, 'email', 'password');

        $this->imagesModel = new \Core\Model(
            $pdo,
            'images',
            'id',
            '\Models\Images',
            []
        );
        // $this->categoriesTable = new \Ninja\DatabaseTable(
        //     $pdo,
        //     'category',
        //     'id',
        //     '\Ijdb\Entity\Category',
        //     [&$this->jokesTable, &$this->jokeCategoriesTable]
        // );
        // $this->jokeCategoriesTable = new \Ninja\DatabaseTable($pdo, 'joke_category', 'categoryId');
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
            'images' => new Controllers\ImagesController(
                $this->imagesModel,
            ),
            'auth' => new Controllers\AuthController($this->authentication),
            // 'author' => new \Ijdb\Controllers\Author($this->authorsTable),
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
