<?php

namespace Controllers;

class ImagesController
{

    public function __construct(private \Core\Model $imagesModel) {}

    // public function home()
    // {
    //     $title = 'Internet Joke Database';

    //     return [
    //         'template' => 'home.html.php',
    //         'title' => $title,
    //         'variables' => []
    //     ];
    // }

    // public function deleteSubmit()
    // {

    //     $author = $this->authentication->getUser();

    //     $joke = $this->jokesTable->find('id', $_POST['id'])[0];

    //     if ($joke->authorId != $author->id && !$author->hasPermission(\Ijdb\Entity\Author::DELETE_JOKE)) {
    //         return;
    //     }
    //     $this->jokesTable->delete('id', $_POST['id']);

    //     header('location: /joke/list');
    // }

    public function list(?int $page = 0)
    {
        $offset = ($page - 1) * 10;


        $images = $this->imagesModel->findAll();
        $totalImages = $this->imagesModel->total();


        // $user = $this->authentication->getUser();

        return [
            'view' => '/images/list.php',
            'title' => 'Gallery',
            'variables' => [
                'totalImages' => $totalImages,
                'images' => $images,
                // 'user' => $user,
                'currentPage' => $page,
            ]
        ];
    }

    // public function editSubmit()
    // {
    //     $author = $this->authentication->getUser();

    //     if (!empty($id)) {
    //         $joke = $this->jokesTable->find('id', $id)[0];

    //         if ($joke->authorId != $author->id) {
    //             return;
    //         }
    //     }

    //     $joke = $_POST['joke'];
    //     $joke['jokedate'] = new \DateTime();

    //     $jokeEntity = $author->addJoke($joke);

    //     $jokeEntity->clearCategories();

    //     foreach ($_POST['category'] as $categoryId) {
    //         $jokeEntity->addCategory($categoryId);
    //     }

    //     header('location: /joke/list');
    // }

    // public function edit($id = null)
    // {
    //     if (isset($id)) {
    //         $joke = $this->jokesTable->find('id', $id)[0] ?? null;
    //     } else {
    //         $joke = null;
    //     }

    //     $title = 'Edit joke';

    //     $author = $this->authentication->getUser();
    //     $categories = $this->categoriesTable->findAll();

    //     return [
    //         'template' => 'editjoke.html.php',
    //         'title' => $title,
    //         'variables' => [
    //             'joke' => $joke,
    //             'user' => $author,
    //             'categories' => $categories
    //         ]
    //     ];
    // }
}
