<?php
namespace App\Controller;

class CitoyenController
{
   
     private static ?CitoyenController $citoyenController = null;
    // Autres méthodes spécifiques au citoyen...

    public function getInstance(){
        if (self::$citoyenController === null) {
            self::$citoyenController = new CitoyenController();
        }
        return self::$citoyenController;
    }
    private function __construct()
    {
        
    }
    // Autres méthodes spécifiques au citoyen...
}