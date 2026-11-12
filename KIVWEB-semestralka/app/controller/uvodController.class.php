<?php
/*
 * zeptá se modelu na data a předá je šabloně
 */
namespace App\Controller;

use App\Model\ZvireModel;

class UvodController{
    public function uvod(): void{
        $pavilony = (new ZvireModel())->pavilonyAZvirata();
        $user = $_SESSION['user'] ?? null;
        require ROOT . 'app/view/uvod.php';
    }
}
