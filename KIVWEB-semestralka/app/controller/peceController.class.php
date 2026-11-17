<?php

namespace App\Controller;

use App\Core\BaseController;
use App\Model\PeceModel;
use App\Model\ZamestnanecModel;
use App\Model\ZvireModel;

class PeceController extends BaseController {

    public function sprava(): void {
        $this->vyzadatRoli(['superadmin', 'admin', 'zamestnanec', 'brigadnik']);
        $user = $this->user();
        $jeAdmin = in_array($user['role'], ['admin', 'superadmin'], true);

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->vyzadatAdmina();
        }

        $pece = new PeceModel();

        $zamestnanci = $jeAdmin ? (new ZamestnanecModel())->vseSPoctem() : [];
        $pavilony = $jeAdmin ? (new ZvireModel())->pavilonyAZvirata() : [];
        $typy = $jeAdmin ? $pece->typy(): [];

        $dnes = date("Y-m-d");

        $chyby = [];

        $old = ['zamestnanec' => 0, 'zvirata' => [], 'typ' => 0, 'od' => $dnes, 'do' => ''];

        $uspech = $_SESSION['flash'] ?? '';
        $chybaAkce = $_SESSION['flash_chyba'] ?? '';
        unset($_SESSION['flash'], $_SESSION['flash_chyba']);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->overCsrf();
            $akce = $_POST['akce'] ?? '';

            if ($akce === 'pridat') {
                $zvirataPost = array_filter((array) ($_POST['id_zvirata'] ?? []), 'is_scalar');

                $old = [
                    'zamestnanec' => (int) ($_POST['id_zamestnance'] ?? 0),
                    'zvirata' => array_values(array_unique(array_map('intval', $zvirataPost))),
                    'typ' => (int) ($_POST['typ_pece'] ?? 0),
                    'od' => trim($_POST['datum_od'] ?? ''),
                    'do' => trim($_POST['datum_do'] ?? ''),
                ];

                $chyby = $this->zkontrolujPrideleni($old, $zamestnanci, $pavilony, $typy);

                if (!$chyby) {
                    $pece->pridat($old['zamestnanec'], $old['zvirata'], $old['typ'], $old['od'], $old['do'] === '' ? null : $old['do']);
                    $pocet = count($old['zvirata']);
                    $_SESSION['flash'] = 'Péče byla přidělena (počet záznamů: ' . $pocet . ').';
                    header('Location: index.php?page=admin');
                    exit;
                }
            } elseif ($akce === 'smazat') {
                $ok = $pece->smazat((int) ($_POST['id_pece'] ?? 0));
                $this->zpravaPoAkci($ok, 'Záznam byl smazán.', 'Záznam se nepodařilo smazat.');
            } elseif ($akce === 'ukoncit'){
                $ok = $pece->ukoncit((int) ($_POST['id_pece'] ?? 0));
                $this->zpravaPoAkci($ok, 'Péče byla ukončena', 'Péči se nepodařilo ukončit');
            } else {
                http_response_code(400);
                exit('Neznámá akce');
            }
        }

        $csrf = $this->csrfToken();
        $zaznamy = $jeAdmin ? $pece->vse(): $pece->vse((int) $user['id']);

        require ROOT . 'app/view/admin/pece.php';
    }

    private function zpravaPoAkci(bool $ok, string $uspech, string $chyba): void {
        if($ok) {
            $_SESSION['flash'] = $uspech;
        } else {
            $_SESSION['flash_chyba'] = $chyba;
        }
        header('Location: index.php?page=admin');
        exit;
    }

    private function zkontrolujPrideleni(array $d, array $zamestnanci, array $pavilony, array $typy): array {
        $chyby = [];

        $idZamestnancu = array_map('intval', array_column($zamestnanci, 'ID_zamestnance'));
        $idTypu = array_map('intval', array_column($typy, 'ID_typ_pece'));
        $idZvirat = [];

        foreach ($pavilony as $pav) {
            foreach ($pav['zvirata'] ?? [] as $zv) {
                $idZvirat[] = (int)$zv['id_zvirata'];
            }
        }

        if(!in_array($d['zamestnanec'], $idZamestnancu, true)) {
            $chyby['zamestnanec'] = 'Vyberte zaměstnance.';
        }

        if(!$d['zvirata']) {
            $chyby['zvirata'] = 'Vyberte alespoň jedno zvíře.';
        } else {
            foreach ($d['zvirata'] as $idZvirete) {
                if(!in_array($idZvirete, $idZvirat, true)) {
                    $chyby['zvirata'] = 'Vybrané zvíře neexistuje.';
                    break;
                }
            }
        }

        if (!in_array($d['typ'], $idTypu, true)) {
            $chyby['typ'] = 'Vyberte druh péče.';
        }

        $odOk = $this->platneDatum($d['od']);
        if (!$odOk) {
            $chyby['od'] = 'Zadejte platné datum začátku.';
        }

        if($d['do'] !== '') {
            if(!$this->platneDatum($d['do'])) {
                $chyby['do'] = 'Zadejte platné datum konce.';
            } elseif ($odOk && $d['do'] < $d['od']) {
                $chyby['do'] = 'Konec péče nesmí být před jejím začátkem';
            }
        }

        return $chyby;
    }

    private function platneDatum(string $text): bool {
        $datum = \DateTime::createFromFormat('Y-m-d', $text);
        return $datum !== false && $datum->format('Y-m-d') === $text;
    }
}