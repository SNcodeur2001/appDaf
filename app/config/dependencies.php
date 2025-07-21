<?php
use App\Core\Router;
use App\Core\Session;
use App\Core\Abstract\Database;
use App\Core\Validator;
use App\Service\CitoyenService;
use App\Service\JournalService;
use App\Controller\CitoyenController;
use App\Controller\JournalController;
use App\Repository\CitoyenRepository;
use App\Repository\JournalRepository;


return [
    "core" => [
        "router" => fn() => new Router(),
        "database" => fn() => Database::getConnection(),
        "session" => fn() => Session::getInstance(),
    ],

    "services" => [
        "citoyenService" => fn() => new CitoyenService(),
        "journalService" => fn() => new JournalService(),
    ],

    "repositories" => [
        "citoyenRepository" => fn() => new CitoyenRepository(),
        "journalRepository" => fn() => new JournalRepository(),
    ],

    "controllers" => [
        "citoyenController" => fn() => new CitoyenController(),
        "journalController" => fn() => new JournalController(),
    ]
];
