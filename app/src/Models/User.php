<?php

namespace Models;

class User
{
    const EDIT_JOKE = 1;
    const DELETE_JOKE = 2;
    const LIST_CATEGORIES = 4;
    const EDIT_CATEGORY = 8;
    const DELETE_CATEGORY = 16;
    const EDIT_USER_ACCESS = 32;

    public int $id;
    public string $name;
    public string $email;
    public string $password;
    public bool $is_verified;

    public ?int $permissions;

    public function __construct(private \Core\Model $imagesModel) {}

    public function getJokes()
    {
        return $this->imagesModel->find('authorId', $this->id);
    }

    public function addJoke($joke)
    {

        $joke['authorId'] = $this->id;

        return $this->imagesModel->save($joke);
    }

    public function hasPermission(int $permission)
    {
        return $this->permissions & $permission;
    }
}
