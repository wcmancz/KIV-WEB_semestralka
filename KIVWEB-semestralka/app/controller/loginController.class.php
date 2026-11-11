<?php
/*
 * Řídí průběh: přijme formulář, zeptá se modelu na uživatele, ověří heslo, založí session
 */
namespace App\Controller;

use App\Model\UzivatelModel;
use App\Model\ZamestnanecModel;

class LoginController
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
        $typ   = 'navstevnik';   // zvolený typ účtu; při prvním zobrazení je vybraný „Návštěvník“
        $login = '';             // co uživatel napsal do prvního políčka (vrací se do formuláře při chybě)

        // Jednorázová zpráva po registraci (uloží ji RegisterController). Po přečtení se smaže.
        $uspech = $_SESSION['flash'] ?? '';
        unset($_SESSION['flash']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user  = $_POST['user'] ?? [];                 // pole user[login] a user[heslo] z formuláře
            $login = trim($user['login'] ?? '');           // POZOR: políčko se jmenuje user[login], ne login
            $heslo = $user['heslo'] ?? '';
            $typ   = ($_POST['typ'] ?? '') === 'zamestnanec' ? 'zamestnanec' : 'navstevnik';

            // Podle zvoleného typu se uživatel hledá v jiné tabulce.
            $prihlasen = $typ === 'zamestnanec'
                ? $this->overZamestnance($login, $heslo)
                : $this->overNavstevnika($login, $heslo);

            if ($prihlasen) {
                session_regenerate_id(true);          // nové ID session po přihlášení
                $_SESSION['user'] = $prihlasen;       // takhle si web pamatuje, kdo je přihlášen
                header('Location: index.php');
                exit;
            }
            // Stejná chyba pro špatné jméno i špatné heslo.
            $chyba = 'Přihlašovací údaje nejsou správné.';
        }

        require ROOT . 'app/view/login.php';          // VIEW: šablona vidí $chyba, $typ, $login, $uspech
    }

    /** Obslouží index.php?page=logout. */
    public function logout(): void
    {
        $_SESSION = [];                                // vymazat data (nikdy unset($_SESSION))
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        header('Location: index.php');
        exit;
    }

    /** Vrátí údaje zaměstnance pro session, nebo null, když jméno nebo heslo nesedí. */
    private function overZamestnance(string $login, string $heslo): ?array
    {
        $z = (new ZamestnanecModel())->podleLoginu($login);        // dotaz provede MODEL
        // password_verify porovná heslo s hashem z databáze (heslo se neukládá jako text)
        if ($z && $z['heslo'] && password_verify($heslo, $z['heslo'])) {
            return ['typ' => 'zamestnanec', 'id' => (int) $z['ID_zamestnance'],
                'jmeno' => $z['jmeno'] . ' ' . $z['prijmeni'], 'role' => $z['role_zam']];
        }
        return null;
    }

    /** Vrátí údaje návštěvníka pro session, nebo null, když e-mail nebo heslo nesedí. */
    private function overNavstevnika(string $email, string $heslo): ?array
    {
        $n = (new UzivatelModel())->podleEmailu($email);
        if ($n && password_verify($heslo, $n['heslo'])) {
            return ['typ' => 'navstevnik', 'id' => (int) $n['id_uzivatele'],
                'jmeno' => $n['jmeno'], 'role' => 'navstevnik'];
        }
        return null;
    }
}