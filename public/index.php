<?php
date_default_timezone_set('Europe/Paris');
require_once __DIR__.'/../vendor/autoload.php';
$app = new Silex\Application();
require_once __DIR__.'/../app/services.php';
require_once __DIR__.'/../app/routes.php';
require_once __DIR__.'/../app/controleurs.php';
$app->run();

