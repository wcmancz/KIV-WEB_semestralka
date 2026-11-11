<?php
/*
 * Model zná databázi a nic jiného: obsahuje SQL dotazy a vrací data
 */
namespace App\Model;

use App\Core\Database;

class UzivatelModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function podleEmailu(string $email): ?array
    {
        $radky = $this->db->select(
            'SELECT id_uzivatele, jmeno, heslo FROM uzivatele WHERE email = :email',
            [':email' => $email]
        );
        return $radky[0] ?? null;
    }

    /** Existuje už účet s tímto e-mailem? */
    public function existujeEmail(string $email): bool
    {
        $radky = $this->db->select('SELECT 1 FROM uzivatele WHERE email = :email', [':email' => $email]);
        return count($radky) > 0;
    }

    /** Uloží nového návštěvníka. Heslo se ukládá jen jako hash, nikdy jako text. */
    public function vytvor(string $jmeno, string $email, string $heslo): void
    {
        $this->db->execute(
            'INSERT INTO uzivatele (jmeno, email, heslo) VALUES (:jmeno, :email, :heslo)',
            [':jmeno' => $jmeno, ':email' => $email, ':heslo' => password_hash($heslo, PASSWORD_DEFAULT)]
        );
    }
}