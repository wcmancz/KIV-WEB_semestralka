<?php
/*
 * DATABASE – třída, která se stará o spojení s databází a o provádění SQL dotazů.
 *
 * PREPARED STATEMENTS – SQL kód a hodnoty se posílají zvlášť, takže se nedá zneužít SQL injection.
 *
 * Použití:
 *   $db = new Database();
 *   $zvirata = $db->select('SELECT * FROM zvirata WHERE pavilon = :p', [':p' => 1]);
 *   $db->execute('INSERT INTO zvirata (jmeno_zvirete) VALUES (:j)', [':j' => 'Panda']);
 */

namespace App\Core;

use App\Config\Config;
use PDO;
use PDOException;

class Database
{
    // Samotné spojení s databází (objekt PDO). private = je dostupné jen uvnitř této třídy.
    private PDO $conn;

    /** Konstruktor se spustí sám při `new Database()` a vytvoří spojení. */
    public function __construct()
    {
        try {
            $this->conn = new PDO(
            // DSN = adresa databáze; hodnoty jsou ve třídě Config. charset=utf8mb4 zajistí správnou češtinu.
                'mysql:host=' . Config::DB_HOST . ';port=' . Config::DB_PORT . ';dbname=' . Config::DB_NAME . ';charset=utf8mb4',
                Config::DB_USER, Config::DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // chyby SQL vyhodí výjimku místo tichého selhání
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // řádky se vrací jako pole ['sloupec' => hodnota]
                ]
            );
        } catch (PDOException $e) {
            error_log($e->getMessage());     // podrobná chyba jen do logu PHP (návštěvník ji nevidí)
            http_response_code(503);         // 503 = služba je dočasně nedostupná
            die('Připojení k databázi se nepodařilo.');   // obecná zpráva bez hesel a adres
        }
    }

    /**
     * SELECT: vrátí pole řádků.
     * $params jsou hodnoty pro zástupné názvy v SQL (:nazev), např. [':login' => 'jan'].
     */
    public function select(string $sql, array $params = []): array
    {
        $stmt = $this->conn->prepare($sql);          // 1) připrav šablonu SQL
        foreach ($params as $name => $value) {
            $stmt->bindValue($name, $value);         // 2) dosaď hodnoty (bindValue = vloží aktuální hodnotu hned)
        }
        $stmt->execute();                            // 3) proveď
        return $stmt->fetchAll();                    // 4) vrať všechny řádky
    }

    /** INSERT / UPDATE / DELETE: vrátí počet změněných řádků. */
    public function execute(string $sql, array $params = []): int
    {
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $name => $value) {
            $stmt->bindValue($name, $value);
        }
        $stmt->execute();
        return $stmt->rowCount();
    }

    /** ID posledního vloženého řádku (např. po INSERT do tabulky s AUTO_INCREMENT). */
    public function lastId(): string { return $this->conn->lastInsertId(); }
}