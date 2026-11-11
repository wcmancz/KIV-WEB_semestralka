<?php
/*
 * AUTOLOADER – automatické načítání tříd.
 *
 * Bez něj by se každá třída musela načítat ručně (require_once). S autoloaderem stačí třídu použít
 * (`new Database()`) a PHP si v ten okamžik samo zavolá tuto funkci, která najde správný soubor.
 *
 * Pravidlo (podle namespace):
 *   App\Core\Database      ->  app/core/database.class.php
 *   App\Config\Config      ->  app/config/config.class.php
 *
 *   - jméno třídy bez prvního dílu „App“ se rozdělí na složky a jméno souboru
 *   - složky i jméno souboru jsou malými písmeny, ke jménu souboru se přidá .class.php
 *
 * Soubor se načítá jednou, z index.php (přípona .inc.php = soubor, který se vkládá do jiného).
 */

// Ochrana: soubor má smysl jen tehdy, když ho načetl index.php (ten definuje ROOT).
if (!defined('ROOT')) {
    exit;
}

spl_autoload_register(function (string $trida): void {
    $prefix = 'App\\';                                               // jen naše třídy (začínají na App\)
    if (strncmp($trida, $prefix, strlen($prefix)) !== 0) {
        return;                                                      // třídy PHP (PDO...) autoloader nezajímají
    }

    $casti  = explode('\\', substr($trida, strlen($prefix)));         // 'Core\Database' -> ['Core', 'Database']
    $soubor = strtolower(array_pop($casti)) . '.class.php';          // 'database.class.php'
    $slozka = $casti ? strtolower(implode('/', $casti)) . '/' : '';  // 'core/'

    $cesta = ROOT . 'app/' . $slozka . $soubor;                      // ROOT = kořen projektu (z index.php)
    if (is_file($cesta)) {
        require $cesta;                                              // soubor existuje -> načti ho
    }
});