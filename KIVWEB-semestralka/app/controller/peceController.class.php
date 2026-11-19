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

        // Hledání a filtry jsou jen pro admina (zaměstnanec vidí vždy jen svoje péče bez filtru).
        $filtr = ['q' => '', 'typ' => 0, 'stav' => ''];
        if ($jeAdmin) {
            $filtr['q'] = mb_substr(trim($this->get('q')), 0, 100);
            $typFiltr = (int) ($_GET['typ_filtr'] ?? 0);
            $filtr['typ'] = in_array($typFiltr, array_map('intval', array_column($typy, 'ID_typ_pece')), true) ? $typFiltr : 0;
            $stavFiltr = $this->get('stav');
            $filtr['stav'] = isset(PeceModel::STAVY[$stavFiltr]) ? $stavFiltr : '';
        }
        $jeFiltr = $filtr['q'] !== '' || $filtr['typ'] !== 0 || $filtr['stav'] !== '';
        $stavy = PeceModel::STAVY;

        // Řazení podle kliknutého sloupce (sloupec zaměstnance řadí jen admin, který ho vidí).
        $sloupce = ['zvire' => 'Zvíře', 'pece' => 'Péče', 'obdobi' => 'Období', 'stav' => 'Stav'];
        if ($jeAdmin) {
            $sloupce = ['zamestnanec' => 'Zaměstnanec'] + $sloupce;
        }
        $razeni = $this->get('razeni');
        if (!isset($sloupce[$razeni])) {
            $razeni = '';
        }
        $smer = $this->get('smer') === 'desc' ? 'desc' : 'asc';

        $stranaPozadovana = max(1, (int) ($_GET['strana'] ?? 1));

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
                $this->zpravaPoAkci($ok, 'Záznam byl smazán.', 'Záznam se nepodařilo smazat.', $this->adresa($filtr, $razeni, $smer, $stranaPozadovana, false));
            } elseif ($akce === 'ukoncit'){
                $ok = $pece->ukoncit((int) ($_POST['id_pece'] ?? 0));
                $this->zpravaPoAkci($ok, 'Péče byla ukončena', 'Péči se nepodařilo ukončit', $this->adresa($filtr, $razeni, $smer, $stranaPozadovana, false));
            } else {
                http_response_code(400);
                exit('Neznámá akce');
            }
        }

        $bezPece = $jeAdmin ? $pece->zvirataBezPece() : [];      // zvířata bez dnešní péče (upozornění pro admina)
        $csrf = $this->csrfToken();

        // Stránkování: 10 záznamů na stránku.
        $naStranu = 10;
        $idZam = $jeAdmin ? null : (int) $user['id'];
        $celkem = $pece->pocet($filtr, $idZam);
        $stran = max(1, (int) ceil($celkem / $naStranu));
        $strana = min($stranaPozadovana, $stran);
        $zaznamy = $this->pripravRadky(
            $pece->hledat($filtr, $naStranu, ($strana - 1) * $naStranu, $idZam, $razeni, $smer),
            $dnes
        );

        $adresa = fn(int $cisloStrany, ?string $r = null, ?string $s = null): string
        => $this->adresa($filtr, $r ?? $razeni, $s ?? $smer, $cisloStrany);

        // Hlavičky tabulky: kliknutí řadí vzestupně, další kliknutí na stejný sloupec sestupně.
        $hlavicky = [];
        foreach ($sloupce as $klic => $text) {
            $razenoTed = $razeni === $klic;
            $dalsiSmer = ($razenoTed && $smer === 'asc') ? 'desc' : 'asc';
            $hlavicky[$klic] = [
                'text'  => $text,
                'url'   => $adresa(1, $klic, $dalsiSmer),
                'ikona' => $razenoTed ? ($smer === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort',
                'aria'  => $razenoTed ? ($smer === 'asc' ? 'ascending' : 'descending') : 'none',
                'aktivni' => $razenoTed,
            ];
        }

        // Podklady pro stránkování (view jen vypisuje): první, poslední a okolí aktuální stránky, mezery jako „…“.
        $od = $celkem > 0 ? ($strana - 1) * $naStranu + 1 : 0;
        $do = min($strana * $naStranu, $celkem);
        $stranky = [];
        $minule = 0;
        for ($i = 1; $i <= $stran; $i++) {
            if ($i === 1 || $i === $stran || abs($i - $strana) <= 2) {
                if ($i - $minule > 1) {
                    $stranky[] = ['cislo' => null, 'url' => '', 'aktualni' => false];
                }
                $stranky[] = ['cislo' => $i, 'url' => $adresa($i), 'aktualni' => $i === $strana];
                $minule = $i;
            }
        }
        $akceUrl = $this->adresa($filtr, $razeni, $smer, $strana, false);   // formuláře v tabulce se vrací na tento pohled
        $zrusitFiltr = $this->adresa(['q' => '', 'typ' => 0, 'stav' => ''], $razeni, $smer, 1);
        $predchozi = $strana > 1 ? $adresa($strana - 1) : null;
        $dalsi = $strana < $stran ? $adresa($strana + 1) : null;

        require ROOT . 'app/view/admin/pece.php';
    }

    private function zpravaPoAkci(bool $ok, string $uspech, string $chyba, string $adresa): void {
        if($ok) {
            $_SESSION['flash'] = $uspech;
        } else {
            $_SESSION['flash_chyba'] = $chyba;
        }
        header('Location: ' . $adresa);   // zpět na stejný filtr, řazení a stránku
        exit;
    }

    /** Textový parametr z adresy; pole a jiné nesmysly (?q[]=x) se změní na prázdný text. */
    private function get(string $klic): string {
        $hodnota = $_GET[$klic] ?? '';
        return is_string($hodnota) ? $hodnota : '';
    }

    /** Adresa stránky se zachovaným filtrem a řazením. #pridelena-pece vrátí čtenáře rovnou k tabulce. */
    private function adresa(array $filtr, string $razeni, string $smer, int $strana, bool $kotva = true): string {
        $params = ['page' => 'admin'];
        if ($filtr['q'] !== '')    { $params['q'] = $filtr['q']; }
        if ($filtr['typ'] !== 0)   { $params['typ_filtr'] = $filtr['typ']; }
        if ($filtr['stav'] !== '') { $params['stav'] = $filtr['stav']; }
        if ($razeni !== '') {
            $params['razeni'] = $razeni;
            $params['smer'] = $smer;
        }
        if ($strana > 1) { $params['strana'] = $strana; }
        return 'index.php?' . http_build_query($params) . ($kotva ? '#pridelena-pece' : '');
    }

    /** Doplní k řádkům hotové texty a stavy, aby je view jen vypsalo. */
    private function pripravRadky(array $radky, string $dnes): array {
        $barvaTypu = [1 => 'warning', 4 => 'warning', 2 => 'success', 6 => 'success', 3 => 'secondary', 5 => 'secondary'];
        $cz = fn(?string $d): string => $d ? date('j. n. Y', strtotime($d)) : '';

        foreach ($radky as &$z) {
            // Stav záznamu podle dnešního data (texty ve tvaru RRRR-MM-DD se dají porovnat přímo).
            $planovana = $z['datum_od'] !== null && $z['datum_od'] > $dnes;     // ještě nezačala
            $ukoncena  = $z['datum_do'] !== null && $z['datum_do'] < $dnes;     // už skončila
            $konciDnes = $z['datum_do'] !== null && $z['datum_do'] === $dnes;   // dnes je poslední den

            if ($ukoncena) {
                [$z['stav_text'], $z['stav_barva']] = ['Ukončená', 'secondary'];
            } elseif ($planovana) {
                [$z['stav_text'], $z['stav_barva']] = ['Naplánovaná', 'info'];
            } elseif ($konciDnes) {
                [$z['stav_text'], $z['stav_barva']] = ['Končí dnes', 'warning'];
            } else {
                [$z['stav_text'], $z['stav_barva']] = ['Aktivní', 'success'];
            }

            $z['ukoncena']     = $ukoncena;
            $z['muze_ukoncit'] = !$planovana && !$ukoncena && !$konciDnes;     // běží a nemá konec
            $z['zamestnanec']  = $z['jmeno'] . ' ' . $z['prijmeni'];
            $z['inicialy']     = mb_strtoupper(mb_substr($z['jmeno'], 0, 1) . mb_substr($z['prijmeni'], 0, 1));
            $z['typ_barva']    = $barvaTypu[(int) $z['typ_pece']] ?? 'secondary';
            $z['od_cz']        = $cz($z['datum_od']);
            $z['do_cz']        = $z['datum_do'] ? $cz($z['datum_do']) : 'bez konce';
        }
        unset($z);

        return $radky;
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