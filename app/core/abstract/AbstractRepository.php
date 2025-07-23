<?php

namespace App\Core\Abstract;
use App\Core\Abstract\Database;

use PDO;
use PDOException;

abstract class AbstractRepository extends Singleton
{
    protected PDO $pdo;


    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    
}
