<?php

namespace App\Controller;

use App\Core\BaseController;
use App\Model\ProblemModel;
use App\Model\ZvireModel;

class ZvireController extends BaseController
{
    public function detail(): void
    {
        $this->vyzadatRoli(['superadmin', 'admin', 'zamestnanec', 'brigadnik']);
        $user = $this->user();

        $id = (int) ($_GET['id'] ?? 0);
        $zvire = (new ZvireModel())->detail($id);
        if ($zvire === null) {
            http_response_code(404);
            exit('Zvíře nenalezeno.');
        }

        $problemy = new ProblemModel();

        $uspech = $_SESSION['flash'] ?? '';
        $chybaAkce = $_SESSION['flash_chyba'] ?? '';
        unset($_SESSION['flash'], $_SESSION['flash_chyba']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->overCsrf();

            switch ($_POST['akce'] ?? '') {
                case 'pridat_problem':
                    $typ = (int) ($_POST['id_typ_problemu'] ?? 0);
                    $nazevTypu = $problemy->nazevTypu($typ);
                    if ($nazevTypu === null) {
                        $this->presmerujNaDetail($id, null, 'Vyberte typ problému ze seznamu.');
                    }
                    // U typu „Ostatní“ je popis povinný, u ostatních typů se ignoruje.
                    $popis = null;
                    if ($nazevTypu === 'Ostatní') {
                        $popis = trim((string) ($_POST['popis'] ?? ''));
                        if ($popis === '' || mb_strlen($popis) > 255) {
                            $this->presmerujNaDetail($id, null, 'U typu „Ostatní“ popište problém (1 až 255 znaků).');
                        }
                    }
                    if ($problemy->existujeOtevreny($id, $typ, $popis)) {
                        $this->presmerujNaDetail($id, null, 'Tento problém už je u zvířete nahlášen a zatím není vyřešený.');
                    }
                    $problemy->pridat($id, $typ, (int) $user['id'], $popis);
                    $this->presmerujNaDetail($id, 'Problém byl nahlášen.');
                    break;

                case 'vyresit_problem':
                    $this->vyzadatRoli(['superadmin', 'admin', 'zamestnanec']);   // brigádník problém jen nahlásí
                    $ok = $problemy->vyresit((int) ($_POST['id_problemu'] ?? 0), $id);
                    $this->presmerujNaDetail($id, $ok ? 'Problém byl označen jako vyřešený.' : null, $ok ? null : 'Problém se nepodařilo označit jako vyřešený.');
                    break;

                default:
                    http_response_code(400);
                    exit('Neznámá akce');
            }
        }

        $typyProblemu = $problemy->typy();
        $otevrene = $problemy->otevrene($id);
        $vyresene = $problemy->vyresene($id);

        $muzeVyresit = $user['role'] !== 'brigadnik';

        $csrf = $this->csrfToken();
        [$zpetUrl, $zpetText, $aktivni] = $this->kamZpet();
        require ROOT . 'app/view/zvire.php';
    }

    /** Stránky, ze kterých se na detail chodí, a text odkazu „zpět“ pro každou z nich. */
    private const ODKUD = [
        'admin'      => ['Zpět na správu péče', 'sprava'],
        'zvirata'    => ['Zpět na zvířata', 'zvirata'],
        'dnes'       => ['Zpět na dnešní přehled', 'dnes'],
        'statistiky' => ['Zpět na statistiky', 'statistiky'],
    ];

    private function kamZpet(): array
    {
        $vychozi = ['index.php', 'Zpět na přehled', 'prehled'];

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $ref = parse_url((string) ($_SERVER['HTTP_REFERER'] ?? ''));
            $mojeHost = $_SERVER['HTTP_HOST'] ?? '';
            $hostRef = ($ref['host'] ?? '') . (isset($ref['port']) ? ':' . $ref['port'] : '');

            if ($ref && $hostRef === $mojeHost) {
                parse_str($ref['query'] ?? '', $q);
                $stranka = is_string($q['page'] ?? null) ? $q['page'] : '';
                if (isset(self::ODKUD[$stranka])) {
                    $_SESSION['zpet_zvire'] = 'index.php?' . http_build_query($q);
                } elseif ($stranka !== 'zvire') {      // přišel odjinud (např. z přehledu): vrací se na přehled
                    unset($_SESSION['zpet_zvire']);
                }
            } else {
                unset($_SESSION['zpet_zvire']);
            }
        }

        $ulozeno = $_SESSION['zpet_zvire'] ?? '';
        if (is_string($ulozeno) && $ulozeno !== '') {
            parse_str((string) parse_url($ulozeno, PHP_URL_QUERY), $q);
            $stranka = is_string($q['page'] ?? null) ? $q['page'] : '';
            if (isset(self::ODKUD[$stranka])) {
                return [$ulozeno, self::ODKUD[$stranka][0], self::ODKUD[$stranka][1]];
            }
        }
        return $vychozi;
    }

    private function presmerujNaDetail(int $id, ?string $uspech = null, ?string $chyba = null): void
    {
        if ($uspech !== null) {
            $_SESSION['flash'] = $uspech;
        }
        if ($chyba !== null) {
            $_SESSION['flash_chyba'] = $chyba;
        }
        header('Location: index.php?page=zvire&id=' . $id);
        exit;
    }

    public function sprava(): void
    {
        $this->vyzadatRoli(['superadmin', 'admin', 'zamestnanec']);
        $user = $this->user();
        $model = new ZvireModel();

        $pavilony = $model->pavilonySPoctem();
        $potravy = $model->potravy();

        // Hledání a filtr pavilonu ze adresy (zachovají se po každé akci).
        $q = $_GET['q'] ?? '';
        $q = is_string($q) ? mb_substr(trim($q), 0, 100) : '';
        $idPavilonuFiltr = (int) (is_string($_GET['pavilon'] ?? null) ? $_GET['pavilon'] : 0);
        if (!in_array($idPavilonuFiltr, array_map('intval', array_column($pavilony, 'ID_pavilon')), true)) {
            $idPavilonuFiltr = 0;
        }

        $chyby = [];
        $old = ['jmeno' => '', 'pavilon' => 0, 'potrava' => 0];
        $upravovane = 0;     // ID zvířete, které se právě upravuje (0 = formulář přidává nové)

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->overCsrf();
            $akce = $_POST['akce'] ?? '';

            if ($akce === 'smazat_zvire') {
                $this->odeber($model, (int) ($_POST['id'] ?? 0), $q, $idPavilonuFiltr);
            } elseif ($akce === 'nove_zvire' || $akce === 'upravit_zvire') {
                $upravovane = $akce === 'upravit_zvire' ? (int) ($_POST['id'] ?? 0) : 0;
                $old = [
                    'jmeno'   => trim((string) ($_POST['jmeno_zvirete'] ?? '')),
                    'pavilon' => (int) ($_POST['pavilon'] ?? 0),
                    'potrava' => (int) ($_POST['potrava'] ?? 0),     // 0 = neuvedeno
                ];
                $chyby = $this->zkontroluj($old, $pavilony, $potravy);
                if ($akce === 'upravit_zvire' && $model->proUpravu($upravovane) === null) {
                    $chyby['id'] = 'Upravované zvíře už neexistuje.';
                }

                if (!$chyby) {
                    $potrava = $old['potrava'] > 0 ? $old['potrava'] : null;
                    if ($upravovane > 0) {
                        $model->upravit($upravovane, $old['jmeno'], $old['pavilon'], $potrava);
                        $this->presmerujNaSpravu('Zvíře „' . $old['jmeno'] . '“ bylo upraveno.', null, $q, $idPavilonuFiltr);
                    }
                    $model->pridat($old['jmeno'], $old['pavilon'], $potrava);
                    $this->presmerujNaSpravu('Zvíře „' . $old['jmeno'] . '“ bylo přidáno.', null, $q, $idPavilonuFiltr);
                }
            } else {
                http_response_code(400);
                exit('Neznámá akce');
            }
        } elseif (isset($_GET['upravit'])) {
            // Odkaz „Upravit“ v tabulce: formulář se předvyplní údaji zvířete.
            $zvire = $model->proUpravu((int) (is_string($_GET['upravit']) ? $_GET['upravit'] : 0));
            if ($zvire !== null) {
                $upravovane = (int) $zvire['id_zvirata'];
                $old = ['jmeno' => $zvire['jmeno_zvirete'], 'pavilon' => (int) $zvire['pavilon'], 'potrava' => (int) $zvire['potrava']];
            }
        }

        $uspech = $_SESSION['flash'] ?? '';
        $chybaAkce = $_SESSION['flash_chyba'] ?? '';
        unset($_SESSION['flash'], $_SESSION['flash_chyba']);

        $seznam = $model->hledat($q, $idPavilonuFiltr);
        foreach ($seznam as &$z) {
            $z['duvod'] = $this->nelzeOdebrat($z);
        }
        unset($z);

        // Adresa pro formuláře a odkazy (zachová hledání a filtr).
        $filtrParams = array_filter(['q' => $q, 'pavilon' => $idPavilonuFiltr ?: ''], fn($v) => $v !== '');
        $akceUrl = 'index.php?' . http_build_query(['page' => 'zvirata'] + $filtrParams);
        $jeFiltr = $filtrParams !== [];
        $csrf = $this->csrfToken();

        require ROOT . 'app/view/admin/zvirata.php';
    }

    private function odeber(ZvireModel $model, int $id, string $q, int $pavilon): void
    {
        $cil = $model->podleId($id);
        $chyba = $cil === null ? 'Zvíře neexistuje.' : $this->nelzeOdebrat($cil);

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
        $this->presmerujNaSpravu($chyba === null ? 'Zvíře „' . $cil['jmeno_zvirete'] . '“ bylo odebráno.' : null, $chyba, $q, $pavilon);
    }

    private function nelzeOdebrat(array $z): ?string
    {
        if ((int) $z['pocet_pece'] > 0) {
            return 'Má přidělenou péči (' . (int) $z['pocet_pece'] . '). Nejdřív ji smažte ve správě péče.';
        }
        return null;
    }

    private function presmerujNaSpravu(?string $uspech, ?string $chyba, string $q, int $pavilon): void
    {
        if ($chyba === null) {
            $_SESSION['flash'] = (string) $uspech;
        } else {
            $_SESSION['flash_chyba'] = $chyba;
        }
        $params = ['page' => 'zvirata'];
        if ($q !== '') { $params['q'] = $q; }
        if ($pavilon > 0) { $params['pavilon'] = $pavilon; }
        header('Location: index.php?' . http_build_query($params));
        exit;
    }

    private function zkontroluj(array $d, array $pavilony, array $potravy): array
    {
        $chyby = [];

        if ($d['jmeno'] === '' || mb_strlen($d['jmeno']) > 50) {
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