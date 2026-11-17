<?php

namespace App\Controller;

use App\Core\BaseController;
use App\Model\ZamestnanecModel;
use App\Model\ZvireModel;

class PridatController extends BaseController {

    public function pridat(): void {
        $this->vyzadatAdmina();

        $user = $this->user();
        $zamModel = new ZamestnanecModel();
        $zvireModel = new ZvireModel();

        $role = $this->povoleneRole($zamModel, $user);
        $pavilony = $zvireModel->pavilonySPoctem();
        $potravy = $zvireModel->potravy();

        $chybyZam = [];
        $chybyZvire = [];
        $oldZam = ['jmeno' => '', 'prijmeni' => '', 'vek' => '', 'id_role' => 0, 'login' => ''];
        $oldZvire = ['jmeno' => '', 'pavilon' => 0, 'potrava' => 0];

        $uspech = $_SESSION['flash'] ?? '';
        $chybaAkce = $_SESSION['flash_chyba'] ?? '';
        unset($_SESSION['flash'], $_SESSION['flash_chyba']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->overCsrf();
            $akce = $_POST['akce'] ?? '';

            if($akce === 'novy_zamestnanec'){
                [$oldZam, $chybyZam] = $this->zpracujZamestnance($zamModel, $role);
            } elseif ($akce === 'nove_zvire') {
                [$oldZvire, $chybyZvire] = $this->zpracujZvire($zvireModel, $pavilony, $potravy);
            } else {
                http_response_code(400);
                exit('Neznámá akce.');
            }
        }

        $seznamZam = $zamModel->seznam();
        foreach ($seznamZam as &$zam) {
            $zam['duvod'] = $this->nelzeOdebratZamestnance($zam, $user);
        }
        unset($zam);

        $seznamZvirat = $zvireModel->seznam();
        foreach ($seznamZvirat as &$zvi) {
            $zvi['duvod'] = $this->nelzeOdebratZvire($zvi);
        }
        unset($zvi);

        $csrf = $this->csrfToken();

        require ROOT . 'app/view/admin/pridat.php';
    }

    private function zpracujZamestnance(ZamestnanecModel $model, array $role): array
    {
        $old = [
            'jmeno'    => trim((string) ($_POST['jmeno'] ?? '')),
            'prijmeni' => trim((string) ($_POST['prijmeni'] ?? '')),
            'vek'      => trim((string) ($_POST['vek'] ?? '')),
            'id_role'  => (int) ($_POST['id_role'] ?? 0),
            'login'    => trim((string) ($_POST['login'] ?? '')),
        ];
        $heslo  = (string) ($_POST['heslo'] ?? '');
        $heslo2 = (string) ($_POST['heslo2'] ?? '');

        $chyby = $this->zkontrolujZamestnance($old, $heslo, $heslo2, $role, $model);

        if (!$chyby) {
            try {
                $model->vytvor(
                    $old['jmeno'],
                    $old['prijmeni'],
                    (int) $old['vek'],
                    $old['id_role'],
                    $old['login'],
                    password_hash($heslo, PASSWORD_DEFAULT)
                );
                $this->presmeruj(null, 'Zaměstnanec ' . $old['jmeno'] . ' ' . $old['prijmeni']
                    . ' byl vytvořen (přihlášení: ' . $old['login'] . ').', 'pridat');
            } catch (\PDOException $e) {
                if ($e->getCode() !== '23000') {
                    throw $e;
                }
                $chyby['login'] = 'Toto přihlašovací jméno je už obsazené.';
            }
        }
        return [$old, $chyby];
    }

    private function zpracujZvire(ZvireModel $model, array $pavilony, array $potravy): array
    {
        $old = [
            'jmeno'   => trim((string) ($_POST['jmeno_zvirete'] ?? '')),
            'pavilon' => (int) ($_POST['pavilon'] ?? 0),
            'potrava' => (int) ($_POST['potrava'] ?? 0),     // 0 = neuvedeno
        ];

        $chyby = $this->zkontrolujZvire($old, $pavilony, $potravy);

        if (!$chyby) {
            $model->pridat($old['jmeno'], $old['pavilon'], $old['potrava'] > 0 ? $old['potrava'] : null);
            $this->presmeruj(null, 'Zvíře „' . $old['jmeno'] . '“ bylo přidáno.', 'pridat');
        }
        return [$old, $chyby];
    }

    public function vymazat(): void {
        $this->vyzadatAdmina();

        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header('Location: index.php?page=pridat');
            exit;
        }
        $this->overCsrf();

        $user = $this->user();
        $id = (int) ($_POST['id'] ?? 0);

        switch ($_POST['akce'] ?? '') {
            case 'smazat_zamestnance':
                $this->odeberZamestnance($id, $user);
                break;
            case 'smazat_zvire':
                $this->odeberZvire($id);
                break;
            default:
                http_response_code(400);
                exit('Neznámá akce');
        }
    }

    private function odeberZamestnance(int $id, array $user): void
    {
        $model = new ZamestnanecModel();
        $cil   = $model->podleId($id);
        $chyba = $cil === null ? 'Zaměstnanec neexistuje.' : $this->nelzeOdebratZamestnance($cil, $user);

        if ($chyba === null) {
            try {
                $chyba = $model->smazat($id) ? null : 'Zaměstnance se nepodařilo odebrat.';
            } catch (\PDOException $e) {
                if ($e->getCode() !== '23000') {
                    throw $e;
                }
                $chyba = 'Zaměstnanec má navázané záznamy (péče), odebrat ho nelze.';
            }
        }
        $this->presmeruj($chyba, $cil ? 'Zaměstnanec ' . $cil['jmeno'] . ' ' . $cil['prijmeni'] . ' byl odebrán.' : '', 'pridat');
    }

    private function odeberZvire(int $id): void
    {
        $model = new ZvireModel();
        $cil   = $model->podleId($id);
        $chyba = $cil === null ? 'Zvíře neexistuje.' : $this->nelzeOdebratZvire($cil);

        if ($chyba === null) {
            try {
                $chyba = $model->smazat($id) ? null : 'Zvíře se nepodařilo odebrat.';
            } catch (\PDOException $e) {
                if ($e->getCode() !== '23000') {
                    throw $e;
                }
                $chyba = 'Zvíře má navázané záznamy (péče), odebrat ho nelze.';
            }
        }
        $this->presmeruj($chyba, $cil ? 'Zvíře „' . $cil['jmeno_zvirete'] . '“ bylo odebráno.' : '', 'pridat');
    }

    private function nelzeOdebratZamestnance(array $z, array $user): ?string
    {
        if ((int) $z['ID_zamestnance'] === (int) $user['id']) {
            return 'Nemůžete odebrat sami sebe.';
        }
        if (in_array($z['role_zam'], ['admin', 'superadmin'], true) && $user['role'] !== 'superadmin') {
            return 'Admina a superadmina smí odebrat jen superadmin.';
        }
        if ((int) $z['pocet_pece'] > 0) {
            return 'Má přidělenou péči (' . (int) $z['pocet_pece'] . '). Nejdřív ji smažte ve správě péče.';
        }
        return null;
    }

    private function nelzeOdebratZvire(array $z): ?string
    {
        if ((int) $z['pocet_pece'] > 0) {
            return 'Má přidělenou péči (' . (int) $z['pocet_pece'] . '). Nejdřív ji smažte ve správě péče.';
        }
        return null;
    }

    private function povoleneRole(ZamestnanecModel $model, array $user): array
    {
        $role = $model->role();
        if ($user['role'] !== 'superadmin') {
            $role = array_values(array_filter(
                $role,
                fn(array $r) => !in_array($r['role_zam'], ['superadmin', 'admin'], true)
            ));
        }
        return $role;
    }
    private function presmeruj(?string $chyba, string $uspech, string $stranka): void
    {
        if ($chyba === null) {
            $_SESSION['flash'] = $uspech;
        } else {
            $_SESSION['flash_chyba'] = $chyba;
        }
        header('Location: index.php?page=' . $stranka);
        exit;
    }


    private function ZkontrolujZamestnance(array $d, string $heslo, string $heslo2, array $role, ZamestnanecModel $model): array
    {
        $chyby = [];

        if($d['jmeno'] === '' || mb_strlen($d['jmeno']) > 50) {
            $chyby['jmeno'] = 'Jméno musí mít 1 až 50 znaků';
        }
        if ($d['prijmeni'] === '' || mb_strlen($d['prijmeni']) > 50) {
            $chyby['prijmeni'] = 'Příjmení musí mít 1 až 50 znaků';
        }

        $vek = filter_var($d['vek'], FILTER_VALIDATE_INT);
        if($vek === false || $vek < 15 || $vek > 99) {
            $chyby['vek'] = 'Věk musí být celé číslo od 15 do 99.';
        }

        $idRoli = array_map('intval', array_column($role, 'id_role'));
        if (!in_array($d['id_role'], $idRoli, true)) {
            $chyby['id_role'] = 'Vyberte roli.';
        }

        if(!preg_match('/^[A-Za-z0-9._-]{3,50}$/', $d['login'])) {
            $chyby['login'] = 'Přihlašovací jméno má mít 3 až 50 znaků (písmena bez diakritiky, čísla, tečka, pomlčka a podtržítko).';
        } elseif ($model->loginExistuje($d['login'])) {
            $chyby['login'] = 'Toto přihlašovací jméno je již zabrané.';
        }

        if (mb_strlen($heslo) < 8) {
            $chyby['heslo'] = 'Heslo musí mít alespoň 8 znaků.';
        } elseif (strlen($heslo) > 72) {
            $chyby['heslo'] = 'Heslo je příliš dlouhé (nejvýše 72).';
        } elseif ($heslo !== $heslo2) {
            $chyby['heslo2'] = 'Hesla se neshodují.';
        }
        return $chyby;
    }

    private function zkontrolujZvire(array $d, array $pavilony, array $potravy): array {
        $chyby = [];

        if($d['jmeno'] === '' || mb_strlen($d['jmeno']) > 50) {
            $chyby['jmeno'] = 'Název zvířete musí mít 1 až 50 znaků.';
        }

        $idPavilonu = array_map('intval', array_column($pavilony, 'ID_pavilon'));
        if (!in_array($d['pavilon'], $idPavilonu, true)) {
            $chyby['pavilon'] = 'Vyberte pavilon.';
        }

        $idPotrav = array_map('intval', array_column($potravy, 'ID_potrava'));
        if ($d['potrava'] !== 0 && !in_array($d['potrava'], $idPotrav, true)) {
            $chyby['potrava'] = 'Vybraná potrava neexistuje.';
        }
        return $chyby;
    }

}