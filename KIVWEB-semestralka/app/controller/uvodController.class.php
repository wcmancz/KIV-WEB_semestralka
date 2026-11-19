<?php
/*
 * zeptá se modelu na data a předá je šabloně
 */
namespace App\Controller;

use App\Model\ZvireModel;
use App\Model\PeceModel;
use App\Model\ProblemModel;
use App\Core\BaseController;

class UvodController extends BaseController{
    public function uvod(): void{
        $pavilony = (new ZvireModel())->pavilonyAZvirata();
        $user = $_SESSION['user'] ?? null;
        $sluzby = [];
        $problemy = [];
        if ($this->jeZamestnanec()){
            $sluzby = (new PeceModel())->sluzbyZamestnance($user['id']);
            $problemy = (new ProblemModel())->poctyOtevrenych();   // [id_zvirata => počet neřešených problémů]
        }
        require ROOT . 'app/view/uvod.php';
    }
}