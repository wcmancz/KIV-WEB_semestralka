<?php
/*
 * Rezervace komentovaných prohlídek.
 *   návštěvník – vytvoří rezervaci a vidí své rezervace i jejich stav (může zrušit čekající nebo schválenou),
 *   brigádník a vyšší role – rezervaci schválí a posouvá její stav (schválená → probíhá → dokončená),
 *   admin a superadmin – rezervaci navíc zamítnou (povinný důvod).
 */
namespace App\Controller;

use App\Core\BaseController;
use App\Model\RezervaceModel;

class RezervaceController extends BaseController
{
    private const BARVY = [
        RezervaceModel::CEKA      => 'warning',
        RezervaceModel::SCHVALENA => 'info',
        RezervaceModel::PROBIHA   => 'primary',
        RezervaceModel::DOKONCENA => 'success',
        RezervaceModel::ZAMITNUTA => 'danger',
    ];

    public function rezervace(): void
    {
        $this->vyzadatRoli(['superadmin', 'admin', 'zamestnanec', 'brigadnik', 'navstevnik']);
        $user = $this->user();

        if ($user['role'] === 'navstevnik') {
            $this->prohlidky($user);
        } else {
            $this->sprava($user);
        }
    }


    private function prohlidky(array $user): void
    {
        $model = new RezervaceModel();
        $chyby = [];
        $old = ['datum' => '', 'cas' => '', 'osob' => '2'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->overCsrf();
            $akce = $_POST['akce'] ?? '';

            if ($akce === 'zrusit') {
                $ok = $model->zrusitVlastni((int) ($_POST['id'] ?? 0), (int) $user['id']);
                $this->presmeruj($ok ? 'Rezervace byla zrušena.' : null, $ok ? null : 'Rezervaci už nejde zrušit (je zahájená, proběhla nebo neexistuje).');
            } elseif ($akce === 'vytvorit') {
                $old = [
                    'datum' => trim((string) ($_POST['datum'] ?? '')),
                    'cas'   => trim((string) ($_POST['cas'] ?? '')),
                    'osob'  => trim((string) ($_POST['osob'] ?? '')),
                ];
                $chyby = $this->zkontroluj($old, $model);

                if (!$chyby) {
                    if ($model->vytvor((int) $user['id'], $old['datum'], $old['cas'] . ':00', (int) $old['osob'])) {
                        $this->presmeruj('Rezervace byla odeslána a čeká na schválení.');
                    }
                    // Kontrola v INSERT neprošla: termín se zaplnil, nebo už stejný termín máte.
                    $chyby['termin'] = 'Na tento termín už nelze rezervovat (je zaplněný, nebo ho už máte rezervovaný).';
                }
            } else {
                http_response_code(400);
                exit('Neznámá akce');
            }
        }

        $uspech = $_SESSION['flash'] ?? '';
        $chybaAkce = $_SESSION['flash_chyba'] ?? '';
        unset($_SESSION['flash'], $_SESSION['flash_chyba']);

        $dnes = date('Y-m-d');
        $rezervace = $model->mojeRezervace((int) $user['id']);
        foreach ($rezervace as &$r) {
            $this->doplnZobrazeni($r);
            $r['muze_zrusit'] = in_array((int) $r['id_stav'], [RezervaceModel::CEKA, RezervaceModel::SCHVALENA], true) && $r['datum'] > $dnes;
        }
        unset($r);

        $sloty = RezervaceModel::SLOTY;
        $minDatum = date('Y-m-d', strtotime('+1 day'));
        $maxDatum = date('Y-m-d', strtotime('+' . RezervaceModel::DNI_DOPREDU . ' days'));
        $maxOsob = RezervaceModel::MAX_OSOB;
        $kapacita = RezervaceModel::KAPACITA;
        $csrf = $this->csrfToken();

        require ROOT . 'app/view/rezervaceNavstevnik.php';
    }

    private function zkontroluj(array $d, RezervaceModel $model): array
    {
        $chyby = [];

        $datum = \DateTime::createFromFormat('Y-m-d', $d['datum']);
        $min = date('Y-m-d', strtotime('+1 day'));
        $max = date('Y-m-d', strtotime('+' . RezervaceModel::DNI_DOPREDU . ' days'));
        if ($datum === false || $datum->format('Y-m-d') !== $d['datum']) {
            $chyby['datum'] = 'Zadejte platné datum.';
        } elseif ($d['datum'] < $min || $d['datum'] > $max) {
            $chyby['datum'] = 'Rezervovat lze od zítřka do ' . date('j. n. Y', strtotime($max)) . '.';
        }

        if (!in_array($d['cas'], RezervaceModel::SLOTY, true)) {
            $chyby['cas'] = 'Vyberte čas prohlídky.';
        }

        $osob = filter_var($d['osob'], FILTER_VALIDATE_INT);
        if ($osob === false || $osob < 1 || $osob > RezervaceModel::MAX_OSOB) {
            $chyby['osob'] = 'Počet osob musí být od 1 do ' . RezervaceModel::MAX_OSOB . '.';
        }

        if (!$chyby) {
            $volno = $model->volno($d['datum'], $d['cas'] . ':00');
            if ($osob > $volno) {
                $chyby['osob'] = $volno === 0
                    ? 'Tento termín je už zaplněný, vyberte jiný čas nebo den.'
                    : 'V tomto termínu zbývá už jen ' . $volno . ' míst.';
            }
        }
        return $chyby;
    }


    private function sprava(array $user): void
    {
        $jeSpravce = in_array($user['role'], ['zamestnanec', 'admin', 'superadmin'], true);   // brigádník rezervace zamítat nesmí
        $model = new RezervaceModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->overCsrf();
            $this->zpracujAkci($model, $user, $jeSpravce);
        }

        $stavy = $model->stavy();
        $obdobi = is_string($_GET['obdobi'] ?? null) ? $_GET['obdobi'] : 'nadchazejici';
        $obdobi = in_array($obdobi, ['nadchazejici', 'dnes', 'historie', 'vse'], true) ? $obdobi : 'nadchazejici';
        $stav = (int) (is_string($_GET['stav'] ?? null) ? $_GET['stav'] : 0);
        $stav = in_array($stav, array_map('intval', array_column($stavy, 'id_stav')), true) ? $stav : 0;

        $uspech = $_SESSION['flash'] ?? '';
        $chybaAkce = $_SESSION['flash_chyba'] ?? '';
        unset($_SESSION['flash'], $_SESSION['flash_chyba']);

        $dnes = date('Y-m-d');
        $rezervace = $model->hledat($obdobi, $stav);
        foreach ($rezervace as &$r) {
            $this->doplnZobrazeni($r);
            $id = (int) $r['id_stav'];
            $r['muze_schvalit'] = $id === RezervaceModel::CEKA;
            $r['muze_zahajit'] = $id === RezervaceModel::SCHVALENA && $r['datum'] <= $dnes;    // budoucí prohlídku zahájit nejde
            $r['muze_dokoncit'] = $id === RezervaceModel::PROBIHA;
            $r['muze_zamitnout'] = $jeSpravce && in_array($id, [RezervaceModel::CEKA, RezervaceModel::SCHVALENA], true);
        }
        unset($r);

        $obdobiMoznosti = ['nadchazejici' => 'Nadcházející', 'dnes' => 'Dnes', 'historie' => 'Proběhlé termíny', 'vse' => 'Vše'];
        $jeFiltr = $obdobi !== 'nadchazejici' || $stav > 0;
        $akceUrl = 'index.php?' . http_build_query(array_filter(['page' => 'rezervace', 'obdobi' => $obdobi !== 'nadchazejici' ? $obdobi : '', 'stav' => $stav ?: ''], fn($v) => $v !== ''));
        $csrf = $this->csrfToken();

        require ROOT . 'app/view/admin/rezervace.php';
    }

    private function zpracujAkci(RezervaceModel $model, array $user, bool $jeSpravce): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        $akce = $_POST['akce'] ?? '';
        $idUser = (int) $user['id'];

        switch ($akce) {
            case 'schvalit':
                $ok = $model->zmenStav($id, [RezervaceModel::CEKA], RezervaceModel::SCHVALENA, $idUser, null);
                $this->presmeruj($ok ? 'Rezervace byla schválena.' : null, $ok ? null : 'Rezervaci nejde schválit (už má jiný stav).');
                break;
            case 'zahajit':
                $r = $model->podleId($id);
                $ok = $r !== null && $r['datum'] <= date('Y-m-d')
                    && $model->zmenStav($id, [RezervaceModel::SCHVALENA], RezervaceModel::PROBIHA);
                $this->presmeruj($ok ? 'Prohlídka byla zahájena.' : null, $ok ? null : 'Prohlídku nejde zahájit (není schválená, nebo ještě nenastal její den).');
                break;
            case 'dokoncit':
                $ok = $model->zmenStav($id, [RezervaceModel::PROBIHA], RezervaceModel::DOKONCENA);
                $this->presmeruj($ok ? 'Prohlídka byla dokončena.' : null, $ok ? null : 'Prohlídku nejde dokončit (neprobíhá).');
                break;
            case 'zamitnout':
                if (!$jeSpravce) {                     // brigádník schvaluje, ale nezamítá
                    http_response_code(403);
                    require ROOT . 'app/view/error/403.php';
                    exit;
                }
                $duvod = trim((string) ($_POST['duvod'] ?? ''));
                if ($duvod === '' || mb_strlen($duvod) > 255) {
                    $this->presmeruj(null, 'Při zamítnutí uveďte důvod (1 až 255 znaků).');
                }
                $ok = $model->zmenStav($id, [RezervaceModel::CEKA, RezervaceModel::SCHVALENA], RezervaceModel::ZAMITNUTA, $idUser, $duvod);
                $this->presmeruj($ok ? 'Rezervace byla zamítnuta.' : null, $ok ? null : 'Rezervaci nejde zamítnout (už je zahájená nebo zamítnutá).');
                break;
            default:
                http_response_code(400);
                exit('Neznámá akce');
        }
    }

    private function doplnZobrazeni(array &$r): void
    {
        $r['datum_cz'] = date('j. n. Y', strtotime($r['datum']));
        $r['cas_text'] = substr($r['cas'], 0, 5);
        $r['barva'] = self::BARVY[(int) $r['id_stav']] ?? 'secondary';
    }

    private function presmeruj(?string $uspech, ?string $chyba = null): void
    {
        if ($uspech !== null) {
            $_SESSION['flash'] = $uspech;
        }
        if ($chyba !== null) {
            $_SESSION['flash_chyba'] = $chyba;
        }
        $params = ['page' => 'rezervace'];
        foreach (['obdobi', 'stav'] as $klic) {
            if (is_string($_GET[$klic] ?? null) && $_GET[$klic] !== '') {
                $params[$klic] = $_GET[$klic];
            }
        }
        header('Location: index.php?' . http_build_query($params));
        exit;
    }
}