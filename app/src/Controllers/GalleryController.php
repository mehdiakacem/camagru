<?php

namespace Controllers;

class GalleryController
{
    public function __construct() {}

    public function index()
    {
        return [
            'view' => 'gallery.php',
            'title' => 'Gallery'
        ];
    }
}
