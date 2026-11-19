<?php

namespace App\Controller;

use App\Core\BaseController;
use App\Model\StatistikyModel;

class StatistikyController extends BaseController
{
    public function statistiky(): void
    {
        $this->vyzadatAdmina();
        $user = $this->user();

        $model = new StatistikyModel();
        $souhrn = $model->souhrn();
        $zamestnanci = $model->zamestnanci();
        $zvirata = $model->nejpecovanejsi(10);
        $bezPece = $model->bezDnesniPece();
        $druhy = $model->podleDruhu();
        $pavilony = $model->podlePavilonu();

        // Délka sloupců v grafech: nejvyšší hodnota = 100 % (view jen vypisuje).
        $max = fn(array $radky, string $klic): int => max(1, ...array_map(fn(array $r) => (int) $r[$klic], $radky ?: [[$klic => 1]]));
        $maxDruh = $max($druhy, 'celkem');
        $maxZvire = $max($zvirata, 'zaznamu');
        $maxZam = $max($zamestnanci, 'pocet_zaznamu');

        require ROOT . 'app/view/admin/statistiky.php';
    }
}