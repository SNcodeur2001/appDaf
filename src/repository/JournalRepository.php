<?php
namespace App\Repository;

use App\Core\Abstract\AbstractRepository;
use pdo;

class JournalRepository extends AbstractRepository
{
    protected \PDO $pdo;
    private static ?JournalRepository $journalRepository = null;

    public function getInstance(){
        if (self::$journalRepository === null) {
            self::$journalRepository = new JournalRepository();
        }
        return self::$journalRepository;
    }
    private function __construct()
    {
        parent::__construct();

        
    }
     public function selectAll(){}
     public function insert(){}
     public function update(){}
     public function delete(){}
     public function selectById(){}
     public function selectBy(Array $filtre){}


}