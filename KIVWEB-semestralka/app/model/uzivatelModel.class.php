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

    public function podleLoginuNeboEmailu(string $identifikator): ?array
    {
        $radky = $this->db->select(
            'SELECT u.id_uzivatele, u.jmeno, u.prijmeni, u.heslo, r.role_zam
             FROM uzivatele u JOIN role_zam r ON r.id_role = u.id_role
             WHERE u.login = :login OR u.email = :email
             LIMIT 1',
            [':login' => $identifikator, ':email' => $identifikator]
        );
        return $radky[0] ?? null;
    }

    /** Existuje už účet s tímto e-mailem? */
    public function existujeEmail(string $email): bool
    {
        $radky = $this->db->select('SELECT 1 FROM uzivatele WHERE email = :email', [':email' => $email]);
        return count($radky) > 0;
    }

    public function existujeLogin(string $login): bool
    {
        return $this->db->select('SELECT 1 FROM uzivatele WHERE login = :login', [':login' => $login]) !== [];
    }

    public function vytvorNavstevnika(string $jmeno, string $email, string $login, string $heslo): void
    {
        $this->db->execute(
            'INSERT INTO uzivatele (jmeno, email, login, heslo, id_role)
             VALUES (:jmeno, :email, :login, :heslo, (SELECT id_role FROM role_zam WHERE role_zam = \'navstevnik\' LIMIT 1))',
            [
                ':jmeno' => $jmeno,
                ':email' => $email,
                ':login' => $login,
                ':heslo' => password_hash($heslo, PASSWORD_DEFAULT),
            ]
        );
    }
}