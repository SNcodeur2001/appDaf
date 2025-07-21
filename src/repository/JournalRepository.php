<?php
namespace App\Repository;

class JournalRepository
{
    private static ?JournalRepository $journalRepository = null;
    // Autres méthodes spécifiques au citoyen...

    public function getInstance(){
        if (self::$journalRepository === null) {
            self::$journalRepository = new JournalRepository();
        }
        return self::$journalRepository;
    }
    private function __construct()
    {
        
    }

    // Autres méthodes spécifiques au citoyen...
}