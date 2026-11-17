<?php

namespace App\Controller;

use App\Core\BaseController;
use App\Model\ProblemModel;
use App\Model\ZvireModel;

class ZvireController extends BaseController {
    public function detail(): void {
        $this->vyzadatRoli(['superadmin', 'admin', 'zamestnanec', 'brigadnik']);
        $user = $this->user();

        $id = (int)($_GET['id'] ?? 0);
        $zvire = (new ZvireModel())->detail($id);
        if($zvire === null){
            http_response_code(404);
            exit('Zvíře nenalezeno');
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
                    if(!$problemy->typExistuje($typ)) {
                        $this->presmeruj($id, null, 'Vyberte typ problému ze seznamu.');
                    }
                    $problemy->pridat($id, $typ, (int) $user['id']);
                    $this->presmeruj($id, 'Problém byl nahlášen.');
                    break;
                case 'vyresit_problem':
                    $ok = $problemy->vyresit((int) ($_POST['id_problemu'] ?? 0), $id);
                    $this->presmeruj($id, $ok ? 'Problém byl označen jako vyřešený.' : null,
                        $ok ? null : 'Problém se nepodařilo označit jako vyřešený.');
                    break;
                default:
                    http_response_code(400);
                    exit('Neznámá akce');
            }
        }

        $typyProblemu = $problemy->typy();
        $otevrene = $problemy->otevrene($id);
        $vyresene = $problemy->vyresene($id);

        $csrf = $this->csrfToken();
        require ROOT . 'app/view/zvire.php';
    }

    private function presmeruj(int $id, ?string $uspech = null, ?string $chyba = null): void
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
}