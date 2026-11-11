<?php
/*
 * Controller řídí průběh: přijme data z formuláře, zkontroluje je, zavolá model (uložení do databáze)
 * a rozhodne, co dál
 *
 */
namespace App\Controller;

use App\Model\UzivatelModel;
use PDOException;

class RegisterController
{
    public function register(): void
    {
        // Přihlášený uživatel se registrovat nemusí.
        if (isset($_SESSION['user'])) {
            header('Location: index.php');
            exit;
        }

        $model = new UzivatelModel();

        // Tyto proměnné uvidí šablona (viz require na konci metody).
        $chyby = [];     // chyby podle políčka: $chyby['email'] = '...'
        $jmeno = '';     // hodnoty se vracejí do formuláře, aby je uživatel při chybě nemusel psát znovu
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $jmeno  = trim($_POST['jmeno'] ?? '');
            $email  = trim($_POST['email'] ?? '');
            $heslo  = $_POST['heslo'] ?? '';
            $heslo2 = $_POST['heslo2'] ?? '';

            // Každá kontrola je samostatná, takže se ukážou všechny chyby najednou.
            // mb_strlen počítá znaky (ne bajty), takže české znaky se počítají správně.
            if ($jmeno === '' || mb_strlen($jmeno) > 50) {
                $chyby['jmeno'] = 'Zadejte jméno (nejvýše 50 znaků).';
            }

            if ($email === '' || mb_strlen($email) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $chyby['email'] = 'Zadejte platný e-mail.';
            } elseif ($model->existujeEmail($email)) {            // dotaz provede MODEL
                $chyby['email'] = 'Účet s tímto e-mailem už existuje.';
            }

            if (mb_strlen($heslo) < 8) {
                $chyby['heslo'] = 'Heslo musí mít alespoň 8 znaků.';
            }

            if ($heslo !== $heslo2) {
                $chyby['heslo2'] = 'Hesla se neshodují.';
            }

            // Bez chyb se účet uloží a uživatel se přesměruje na přihlášení.
            if (!$chyby) {
                try {
                    $model->vytvor($jmeno, $email, $heslo);       // uložení provede MODEL
                    $_SESSION['flash'] = 'Účet byl vytvořen. Nyní se můžete přihlásit.';
                    header('Location: index.php?page=login');
                    exit;
                } catch (PDOException $e) {
                    // SQLSTATE 23000 (text!) = porušení unikátního klíče: e-mail se mezitím zaregistroval jinde.
                    if ($e->getCode() === '23000') {
                        $chyby['email'] = 'Účet s tímto e-mailem už existuje.';
                    } else {
                        throw $e;
                    }
                }
            }
        }

        // VIEW: načte šablonu. Proměnné $chyby, $jmeno a $email jsou v ní dostupné,
        // protože se šablona vkládá uvnitř této metody. (To je 2. varianta MVC z přednášky – výstup tiskne šablona.)
        require ROOT . 'app/view/register.php';
    }
}