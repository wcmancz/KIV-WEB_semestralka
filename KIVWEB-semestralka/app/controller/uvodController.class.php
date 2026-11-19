<?php

namespace App\Controller;

use App\Model\ZvireModel;
use App\Model\PeceModel;
use App\Model\ProblemModel;
use App\Model\RezervaceModel;
use App\Core\BaseController;

class UvodController extends BaseController{
    public function uvod(): void{
        $pavilony = (new ZvireModel())->pavilonyAZvirata();
        $user = $_SESSION['user'] ?? null;
        $sluzby = [];
        $problemy = [];
        $jeNavstevnik = $user !== null && !$this->jeZamestnanec();
        $nejblizsi = null;
        if ($jeNavstevnik) {
            $nejblizsi = (new RezervaceModel())->nejblizsi((int) $user['id']);
            if ($nejblizsi !== null) {
                $nejblizsi['datum_cz'] = date('j. n. Y', strtotime($nejblizsi['datum']));
                $nejblizsi['cas_text'] = substr($nejblizsi['cas'], 0, 5);
            }
        }
        if ($this->jeZamestnanec()){
            $sluzby = (new PeceModel())->sluzbyZamestnance($user['id']);
            $problemy = (new ProblemModel())->poctyOtevrenych();
        }
        require ROOT . 'app/view/uvod.php';
    }
}