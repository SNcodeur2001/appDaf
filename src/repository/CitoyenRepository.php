<?php
namespace App\Repository;

use App\Core\Abstract\Database;
use Pdo;
use App\Entity\Citoyen;
class CitoyenRepository
{
   
    private static ?CitoyenRepository $citoyenRepository = null;
    private PDO $pdo;
    public static function getInstance(){
        if (self::$citoyenRepository === null) {
            self::$citoyenRepository = new CitoyenRepository();
        }
        return self::$citoyenRepository;
    }
    private function __construct()
    {
     $this->pdo = Database::getConnection();

    }

   public function fincByCNi(int $cni){
        $query  = "SELECT * FROM citoyen WHERE cni = :cni";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['cni'=> $cni]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result){
            return Citoyen::toObject($result);
        }
        return null;
       }
}
