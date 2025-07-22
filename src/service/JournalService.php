<?php
namespace App\Service;

class JournalService
{
   
 private static ?JournalService $journalService = null;
    // Autres méthodes spécifiques au citoyen...

    public function getInstance(){
        if (self::$journalService === null) {
            self::$journalService = new JournalService();
        }
        return self::$journalService;
    }
    private function __construct()
    {
        
    }
}