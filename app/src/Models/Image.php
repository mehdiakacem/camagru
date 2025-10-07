<?php

namespace Models;

class Image
{
    public int $id;
    public string $image_path;
    public ?string $thumbnail_path;
    public string $created_at;
    private ?object $author;

    public function __construct(
        // private ?\Ninja\DatabaseTable $authorsTable,
        // private ?\Ninja\DatabaseTable $jokeCategoriesTable
    ) {}

    // public function getAuthor()
    // {
    //     if (empty($this->author)) {
    //         $this->author = $this->authorsTable->find('id', $this->authorId)[0];
    //     }

    //     return $this->author;
    // }

    // public function addCategory($categoryId)
    // {
    //     $jokeCat = ['jokeId' => $this->id, 'categoryId' => $categoryId];

    //     $this->jokeCategoriesTable->save($jokeCat);
    // }

    // public function hasCategory($categoryId)
    // {
    //     $jokeCategories = $this->jokeCategoriesTable->find('jokeId', $this->id);

    //     foreach ($jokeCategories as $jokeCategory) {
    //         if ($jokeCategory->categoryId == $categoryId) {
    //             return true;
    //         }
    //     }
    // }

    // public function clearCategories()
    // {
    //     $this->jokeCategoriesTable->delete('jokeId', $this->id);
    // }
}
