<?php
/*
 * Řídí průběh: přijme formulář, zeptá se modelu na uživatele, ověří heslo, založí session
 */
namespace App\Controller;

use App\Core\BaseController;
use App\Model\UzivatelModel;

class LoginController extends BaseController
{
    /** Obslouží index.php?page=login (zobrazení formuláře i jeho zpracování). */
    public function login(): void
    {
        // Přihlášený uživatel se přihlašovat nemusí.
        if (isset($_SESSION['user'])) {
            header('Location: index.php');
            exit;
        }

        // Tyto proměnné uvidí šablona (viz require na konci metody).
        $chyba = '';
        $login = '';             // co uživatel napsal do prvního políčka: login nebo e-mail (vrací se do formuláře při chybě)

        // Jednorázová zpráva po registraci (uloží ji RegisterController). Po přečtení se smaže.
        $uspech = $_SESSION['flash'] ?? '';
        unset($_SESSION['flash']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user  = $_POST['user'] ?? [];                 // pole user[login] a user[heslo] z formuláře
            $login = trim($user['login'] ?? '');           // POZOR: políčko se jmenuje user[login], ne login
            $heslo = $user['heslo'] ?? '';

            $tokenOk = $this->csrfPlatny();

            $prihlasen = $tokenOk ? $this->overUzivatele($login, (string) $heslo) : null;

            if ($prihlasen) {
                session_regenerate_id(true);          // nové ID session po přihlášení
                unset($_SESSION['csrf']);             // po přihlášení nový token
                $_SESSION['user'] = $prihlasen;       // takhle si web pamatuje, kdo je přihlášen
                header('Location: index.php');
                exit;
            }
            // Stejná chyba pro špatné jméno i špatné heslo.
            $chyba = $tokenOk
                ? 'Přihlašovací údaje nejsou správné'
                : 'Platnost formuláře vypršela. Zkuste prosím znovu';
        }

        $csrf = $this->csrfToken();

        require ROOT . 'app/view/login.php';          // VIEW: šablona vidí $chyba, $login, $uspech
    }

    /** Obslouží index.php?page=logout. */
    public function logout(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }
        $this->overCsrf();
        $_SESSION = [];                                // vymazat data (nikdy unset($_SESSION))
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        header('Location: index.php');
        exit;
    }

    private function overUzivatele(string $identifikator, string $heslo): ?array
    {
        $u = (new UzivatelModel())->podleLoginuNeboEmailu($identifikator);   // dotaz provede MODEL
        // password_verify porovná heslo s hashem z databáze (heslo se neukládá jako text)
        if ($u && $u['heslo'] && password_verify($heslo, $u['heslo'])) {
            return [
                'id'    => (int) $u['id_uzivatele'],
                'jmeno' => trim($u['jmeno'] . ' ' . ($u['prijmeni'] ?? '')),
                'role'  => $u['role_zam'],
            ];
        }
        return null;
    }
}