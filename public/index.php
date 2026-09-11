<?php

declare(strict_types=1);

session_start();

require __DIR__ . '/../vendor/autoload.php';

$capsuleFactory = require __DIR__ . '/../config/database.php';
$capsuleFactory();

$containerFactory = require __DIR__ . '/../config/container.php';
$container = $containerFactory();

$routeur = require __DIR__ . '/../routes/index.php';
$routeur($container);