<?php
/*
 * CONFIG – nastavení připojení k databázi jako třída s konstantami.
 *
 * Třída se dá načíst autoloaderem, takže ji nemusíme ručně vkládat přes require_once.
 * Použití kdekoli v kódu:  use App\Config\Config;   a pak   Config::DB_HOST
 */
namespace App\Config;

class Config
{
    public const DB_HOST = '127.0.0.1';   // adresa databázového serveru (127.0.0.1 = tento počítač)
    public const DB_PORT = '3306';        // port MySQL (výchozí 3306)
    public const DB_NAME = 'zvirata';     // název databáze – bez něj PHP neví, kterou databázi použít
    public const DB_USER = 'root';        // uživatel (v Laragonu root)
    public const DB_PASS = 'root';            // heslo (v Laragonu prázdné)
}