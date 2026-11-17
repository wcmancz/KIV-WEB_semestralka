<?php
/*
 * vstupní bod webu (rozcestník) a úvodní stránka s přehledem zvířat.
 * každá stránka se otvírá přes tento soubor podle toho co říká parametr ?page=
 */
const ROOT = __DIR__ . '/';

// Jediný ruční require pro třídy. Autoloader pak při prvním použití třídy (new Database)
require ROOT . 'app/core/autoloader.inc.php';

// Samotné `use` nic nenačítá, třída se načte až ve chvíli, kdy ji poprvé použijeme.
use App\Config\Config;
use App\Controller\RegisterController;
use App\Controller\LoginController;
use App\Controller\UvodController;
use App\Controller\PeceController;
use App\Controller\ZvireController;
use App\Controller\PridatController;

error_reporting(E_ALL);
ini_set('display_errors', Config::DEBUG ? 1 : 0);

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);

session_start();   // musí být před jakýmkoli výstupem; pak je dostupné pole $_SESSION

$page = $_GET['page'] ?? 'uvod';

switch ($page) {
    case 'uvod':
        (new UvodController())->uvod();
        break;
    case 'login':
        (new LoginController())->login();
        break;
    case 'logout':
        (new LoginController())->logout();
        break;
    case 'register':
        (new RegisterController())->register();
        break;
    case 'admin':
        (new PeceController())->sprava();
        break;
    case 'pridat':
        (new PridatController())->pridat();
        break;
    case 'vymazat':
        (new PridatController())->vymazat();
        break;
    case 'zvire':
        (new ZvireController())->detail();
        break;
    default:
        http_response_code(404);
        echo 'Stránka nenalezena.';
}