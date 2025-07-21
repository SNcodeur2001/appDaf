<?php
namespace App\Controller;

class JournalController
{
    private static ?JournalController $journalController = null;
    // Autres méthodes spécifiques au citoyen...

    public function getInstance(){
        if (self::$journalController === null) {
            self::$journalController = new JournalController();
        }
        return self::$journalController;
    }
    private function __construct()
    {
        
    }

    // Autres méthodes spécifiques au citoyen...
}