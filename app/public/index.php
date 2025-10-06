<?php

include __DIR__ . '/../includes/autoload.php';

$uri = strtok(ltrim($_SERVER['REQUEST_URI'], '/'), '?');

echo $uri;

$camagruWebsite = new CamagruWebsite;
$entryPoint = new \Core\EntryPoint($camagruWebsite);
$entryPoint->run($uri, $_SERVER['REQUEST_METHOD']);

