<?php

namespace Controllers;

class GalleryController
{
    public function __construct(private \Core\Model $imagesModel) {}

    public function index()
    {
        $images = $this->imagesModel->findAll();
        return [
            'view' => 'gallery.php',
            'title' => 'Gallery',
            'variables' => [
                'images' => $images,
            ]
        ];
    }
}
