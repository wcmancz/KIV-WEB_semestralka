<?php
/*
 * zeptá se modelu na data a předá je šabloně
 */
namespace App\Controller;

use App\Model\ZvireModel;
use App\Model\PeceModel;

class UvodController{
    public function uvod(): void{
        $pavilony = (new ZvireModel())->pavilonyAZvirata();
        $user = $_SESSION['user'] ?? null;
        $sluzby = [];
        if ($user && $user['typ'] === 'zamestnanec'){
            $sluzby = (new PeceModel())->sluzbyZamestnance($user['id']);
        }
        require ROOT . 'app/view/uvod.php';
    }
}
