<?php
/*
 * Správa uživatelů (jen admin a superadmin): vytvoření zaměstnance, změna role a smazání účtu.
 * Pravidla (server je vynucuje vždy, tlačítka ve view jsou jen pohodlí):
 *   - nikdo nemůže upravit sám sebe a superadmina nelze upravit vůbec,
 *   - admina smí upravit jen superadmin,
 *   - admin přiděluje role zaměstnanec, brigádník a návštěvník; superadmin přidělí i roli admin.
 */
namespace App\Controller;

use App\Core\BaseController;
use App\Model\UzivatelModel;
use App\Model\ZamestnanecModel;

class UzivateleController extends BaseController
{
    public function sprava(): void
    {
        $this->vyzadatAdmina();
        $user = $this->user();
        $model = new UzivatelModel();

        $role = $this->prideleneRole($model->role(), $user);
        $roleNovy = array_values(array_filter($role, fn(array $r) => $r['role_zam'] !== 'navstevnik'));   // zaměstnanec = jakákoli role kromě návštěvníka

        $chybyZam = [];
        $oldZam = ['jmeno' => '', 'prijmeni' => '', 'vek' => '', 'id_role' => 0, 'email' => '', 'login' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->overCsrf();
            if (($_POST['akce'] ?? '') === 'novy_zamestnanec') {
                [$oldZam, $chybyZam] = $this->zpracujZamestnance($roleNovy);   // při úspěchu přesměruje
            } else {
                $this->zpracuj($model, $user, $role);
            }
        }

        $uspech = $_SESSION['flash'] ?? '';
        $chybaAkce = $_SESSION['flash_chyba'] ?? '';
        unset($_SESSION['flash'], $_SESSION['flash_chyba']);

        $q = $_GET['q'] ?? '';
        $q = is_string($q) ? mb_substr(trim($q), 0, 100) : '';

        $uzivatele = $model->hledat($q);
        foreach ($uzivatele as &$u) {
            $u['duvod'] = $this->nelzeSpravovat($u, $user);                                            // proč nejde upravit nic
            $u['duvod_smazat'] = $u['duvod'] ?? ((int) $u['pocet_pece'] > 0
                ? 'Má přidělenou péči (' . (int) $u['pocet_pece'] . '). Nejdřív ji smažte ve správě péče.' : null);
        }
        unset($u);

        $popisRole = [
            'admin'       => 'spravuje péči, zvířata a uživatele',
            'zamestnanec' => 'stará se o zvířata',
            'brigadnik'   => 'dočasná výpomoc',
        ];
        $akceUrl = 'index.php?page=uzivatele' . ($q !== '' ? '&q=' . urlencode($q) : '');   // formuláře zachovají hledání
        $csrf = $this->csrfToken();
        $jeSuperadmin = $user['role'] === 'superadmin';

        require ROOT . 'app/view/admin/uzivatele.php';
    }

    private function zpracuj(UzivatelModel $model, array $user, array $role): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        $cil = $model->podleId($id);
        $akce = $_POST['akce'] ?? '';

        if (!in_array($akce, ['zmenit_roli', 'smazat'], true)) {
            http_response_code(400);
            exit('Neznámá akce');
        }

        $chyba = $cil === null ? 'Uživatel neexistuje.' : $this->nelzeSpravovat($cil, $user);
        $uspech = '';

        if ($chyba === null) {
            $jmeno = trim($cil['jmeno'] . ' ' . ($cil['prijmeni'] ?? ''));

            if ($akce === 'zmenit_roli') {
                $novaRole = (int) ($_POST['id_role'] ?? 0);
                $nazev = array_column($role, 'role_zam', 'id_role')[$novaRole] ?? null;
                if ($nazev === null) {
                    $chyba = 'Tuto roli nelze přidělit.';
                } elseif ($nazev === 'navstevnik' && (int) $cil['pocet_pece'] > 0) {
                    $chyba = 'Má přidělenou péči (' . (int) $cil['pocet_pece'] . '). Nejdřív ji smažte ve správě péče.';
                } else {
                    $model->zmenRoli($id, $novaRole);
                    $uspech = $jmeno . ' má nově roli ' . $nazev . '.';
                }
            } else {   // smazat
                if ((int) $cil['pocet_pece'] > 0) {
                    $chyba = 'Má přidělenou péči (' . (int) $cil['pocet_pece'] . '). Nejdřív ji smažte ve správě péče.';
                } else {
                    try {
                        $model->smazat($id);
                        $uspech = 'Účet ' . $jmeno . ' byl smazán.';
                    } catch (\PDOException $e) {
                        if ($e->getCode() !== '23000') {
                            throw $e;
                        }
                        $chyba = 'Uživatel má navázané záznamy, smazat ho nelze.';
                    }
                }
            }
        }

        if ($chyba !== null) {
            $_SESSION['flash_chyba'] = $chyba;
        } else {
            $_SESSION['flash'] = $uspech;
        }

        // Zpět na stejný výpis (hledání zůstane).
        $q = $_GET['q'] ?? '';
        header('Location: index.php?page=uzivatele' . (is_string($q) && $q !== '' ? '&q=' . urlencode($q) : ''));
        exit;
    }

    /** Důvod, proč uživatele nejde spravovat, nebo null. */
    private function nelzeSpravovat(array $cil, array $user): ?string
    {
        if ((int) $cil['id_uzivatele'] === (int) $user['id']) {
            return 'Nemůžete upravit sami sebe.';
        }
        if ($cil['role_zam'] === 'superadmin') {
            return 'Superadmina nelze upravit.';
        }
        if ($cil['role_zam'] === 'admin' && $user['role'] !== 'superadmin') {
            return 'Admina smí upravit jen superadmin.';
        }
        return null;
    }

    /** Role, které smí přihlášený přidělit: nikdo nepřiděluje superadmina, admina jen superadmin. */
    private function prideleneRole(array $vsechny, array $user): array
    {
        $zakazane = $user['role'] === 'superadmin' ? ['superadmin'] : ['superadmin', 'admin'];
        return array_values(array_filter($vsechny, fn(array $r) => !in_array($r['role_zam'], $zakazane, true)));
    }

    private function zpracujZamestnance(array $role): array
    {
        $old = [
            'jmeno'    => trim((string) ($_POST['jmeno'] ?? '')),
            'prijmeni' => trim((string) ($_POST['prijmeni'] ?? '')),
            'vek'      => trim((string) ($_POST['vek'] ?? '')),
            'id_role'  => (int) ($_POST['id_role'] ?? 0),
            'email'    => trim((string) ($_POST['email'] ?? '')),
            'login'    => trim((string) ($_POST['login'] ?? '')),
        ];
        $heslo  = (string) ($_POST['heslo'] ?? '');
        $heslo2 = (string) ($_POST['heslo2'] ?? '');

        $model = new ZamestnanecModel();
        $chyby = $this->zkontrolujZamestnance($old, $heslo, $heslo2, $role);

        if (!$chyby) {
            try {
                $model->vytvor(
                    $old['jmeno'],
                    $old['prijmeni'],
                    (int) $old['vek'],
                    $old['id_role'],
                    $old['email'],
                    $old['login'],
                    password_hash($heslo, PASSWORD_DEFAULT)
                );
                $_SESSION['flash'] = 'Zaměstnanec ' . $old['jmeno'] . ' ' . $old['prijmeni'] . ' byl vytvořen (přihlášení: ' . $old['login'] . ').';
                header('Location: index.php?page=uzivatele');
                exit;
            } catch (\PDOException $e) {
                if ($e->getCode() !== '23000') {
                    throw $e;
                }
                $chyby['login'] = 'Toto přihlašovací jméno nebo e-mail je už obsazené.';
            }
        }
        return [$old, $chyby];
    }

    private function zkontrolujZamestnance(array $d, string $heslo, string $heslo2, array $role): array
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
        } elseif ((new UzivatelModel())->existujeLogin($d['login'])) {
            $chyby['login'] = 'Toto přihlašovací jméno je již zabrané.';
        }

        if ($d['email'] === '' || mb_strlen($d['email']) > 100 || !filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
            $chyby['email'] = 'Zadejte platný e-mail (nejvýše 100 znaků).';
        } elseif ((new UzivatelModel())->existujeEmail($d['email'])) {
            $chyby['email'] = 'Účet s tímto e-mailem už existuje.';
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
}