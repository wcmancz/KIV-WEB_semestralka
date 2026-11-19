<?php
/*
 * Dnešní péče: přehled všech péčí, které platí dnes, seřazený podle pavilonů.
 * Vidí ho všichni zaměstnanci včetně brigádníka (ten podle něj ví, co se dnes v zoo dělá). Stránka se sama aktualizuje.
 */
namespace App\Controller;

use App\Core\BaseController;
use App\Model\PeceModel;

class DnesController extends BaseController
{
    public function dnes(): void
    {
        $this->vyzadatRoli(['superadmin', 'admin', 'zamestnanec', 'brigadnik']);
        $user = $this->user();

        $dnes = date('Y-m-d');
        $cz = fn(?string $d): string => $d ? date('j. n. Y', strtotime($d)) : '';

        // Seskupení podle pavilonu; hotové texty a příznaky, aby view jen vypisovalo.
        $pavilony = [];
        $celkem = 0;
        $moje = 0;
        foreach ((new PeceModel())->dnes() as $r) {
            $jeMoje = (int) $r['ID_zamestnance'] === (int) $user['id'];
            $r['moje'] = $jeMoje;
            $r['zacina_dnes'] = $r['datum_od'] === $dnes;
            $r['konci_dnes'] = $r['datum_do'] === $dnes;
            $r['zamestnanec'] = trim($r['jmeno'] . ' ' . $r['prijmeni']);
            $r['do_text'] = $r['datum_do'] ? 'do ' . $cz($r['datum_do']) : 'bez konce';

            $pavilony[$r['jmeno_pavilonu'] ?? 'Bez pavilonu'][] = $r;
            $celkem++;
            $moje += $jeMoje ? 1 : 0;
        }

        $aktualizovano = date('H:i:s');
        $datum = $cz($dnes);

        require ROOT . 'app/view/dnes.php';
    }
}