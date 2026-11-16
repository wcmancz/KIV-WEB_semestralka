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

        $role = $zamModel->role();
        if($user['role'] !== 'superadmin') {
            $role = array_values(array_filter($role, fn(array $r) => $r['role_zam'] !== 'superadmin'));
        }
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

            if ($akce === 'novy_zamestnanec') {
                $oldZam = [
                    'jmeno'     => trim((string) ($_POST['jmeno'] ?? '')),
                    'prijmeni'  => trim((string) ($_POST['prijmeni'] ?? '')),
                    'vek'       => trim((string) ($_POST['vek'] ?? '')),
                    'id_role'   => (int) ($_POST['id_role'] ?? ''),
                    'login'     => trim((string) ($_POST['login'] ?? '')),
                ];
                $heslo = (string) ($_POST['heslo'] ?? '');
                $heslo2 = (string) ($_POST['heslo2'] ?? '');
                $chybyZam = $this->ZkontrolujZamestnance($oldZam, $heslo, $heslo2, $role, $zamModel);

                if(!$chybyZam) {
                    try {
                        $zamModel->vytvor(
                            $oldZam['jmeno'],
                            $oldZam['prijmeni'],
                            (int) $oldZam['vek'],
                            $oldZam['id_role'],
                            $oldZam['login'],
                            password_hash($heslo, PASSWORD_DEFAULT)
                        );
                        $_SESSION['flash'] = 'Zaměstnanec ' . $oldZam['jmeno'] . ' ' . $oldZam['prijmeni'] .
                            ' byl vytvořen (přihlášení: ' . $oldZam['login'] . ')';
                        header('Location: index.php?page=pridat');
                        exit;
                    } catch (\PDOException $e) {
                        if ($e->getCode() !== '23000') {
                            throw $e;
                        }
                        $chybyZam['login'] = 'Toto přihlašovací jméno je už obsazené';
                    }
                }
            } elseif ($akce === 'nove_zvire') {
                $oldZvire = [
                    'jmeno' => trim((string) $_POST['jmeno_zvirete'] ?? ''),
                    'pavilon' => (int) ($_POST['pavilon'] ?? 0),
                    'potrava' => (int) ($_POST['potrava'] ?? 0),
                ];

                $chybyZvire = $this->zkontrolujZvire($oldZvire, $pavilony, $potravy);

                if (!$chybyZvire) {
                    $zvireModel->pridat(
                        $oldZvire['jmeno'],
                        $oldZvire['pavilon'],
                        $oldZvire['potrava'] > 0 ? $oldZvire['potrava'] : null,
                    );

                    $_SESSION['flash'] = 'Zvíře "' . $oldZvire['jmeno'] . '" bylo přidáno';
                    header('Location: index.php?page=pridat');
                    exit;
                }
            } else {
                http_response_code(400);
                exit('Neznámá akce.');
            }
        }

        $csrf = $this->csrfToken();

        require ROOT . 'app/view/admin/pridat.php';
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
        } elseif (mb_strlen($heslo) > 72) {
            $chyby['heslo'] = 'Heslo je příliš dlouhé (nejvýše 72).';
        } elseif ($heslo !== $heslo2) {
            $chyby['heslo2'] = 'Hesla se neshodují.';
        }
        return $chyby;
    }

    private function zkontrolujZvire(array $d, array $pavilony, array $potravy): array {
        $chyby = [];

        if($d['jmeno'] === '' || mb_strlen($d['jmeno']) > 50) {
            $chyby['jmeno'] = 'Název zvířete musí mít 1 až 100 znaků.';
        }

        $idPavilonu = array_map('intval', array_column($pavilony, 'ID_pavilon'));
        if (!in_array($d['pavilon'], $idPavilonu, true)) {
            $chyby['pavilon'] = 'Vyberte pavilon.';
        }

        $idPotrav = array_map('intval', array_column($potravy, 'ID_potrava'));
        if (!in_array($d['potrava'], $idPotrav, true)) {
            $chyby['potrava'] = 'Vybraná potrava neexistuje.';
        }
        return $chyby;
    }

}