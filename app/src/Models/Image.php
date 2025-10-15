<?php

namespace Models;

class Image
{
    public int $id;
    public int $user_id;
    public string $filename;
    public string $created_at;
    private ?object $user;

    public function __construct(private \Core\Model $usersModel) {}

    public function getAuthor()
    {
        if (empty($this->user)) {
            $this->user = $this->usersModel->find('id', $this->user_id)[0];
        }

        return $this->user;
    }
}
