<?php
/*
 * vstupní bod webu (rozcestník) a úvodní stránka s přehledem zvířat.
 */
const ROOT = __DIR__ . '/';

// Jediný ruční require pro třídy. Autoloader pak při prvním použití třídy (new Database)
require ROOT . 'app/core/autoloader.inc.php';

// Samotné `use` nic nenačítá, třída se načte až ve chvíli, kdy ji poprvé použijeme.
use App\Controller\RegisterController;
use App\Controller\LoginController;
use App\Controller\UvodController;

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
    default:
        http_response_code(404);
        echo 'Stránka nenalezena.';
}