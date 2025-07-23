<?php
require __DIR__ . '/vendor/autoload.php';
require 'app/config/env.php';
require 'app/core/abstract/Database.php';
try {
    $pdo = \App\Core\Abstract\Database::getConnection();
    echo 'Connexion OK';
} catch (Exception $e) {
    echo $e->getMessage();
}
