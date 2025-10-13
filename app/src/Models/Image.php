<?php

namespace Models;

class Image
{
    public int $id;
    public int $user_id;
    public string $filename;
    public string $created_at;

    public function __construct() {}
}