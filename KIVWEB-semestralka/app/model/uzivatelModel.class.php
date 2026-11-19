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

    /** Aktuální role přihlášeného uživatele (kontrola při každém požadavku), nebo null, když účet už neexistuje. */
    public function stav(int $id): ?array
    {
        $radky = $this->db->select(
            'SELECT r.role_zam FROM uzivatele u JOIN role_zam r ON r.id_role = u.id_role WHERE u.id_uzivatele = :id',
            [':id' => $id]
        );
        return $radky[0] ?? null;
    }

    /** Všechny role (pro výběr role u uživatele). */
    public function role(): array
    {
        return $this->db->select('SELECT id_role, role_zam FROM role_zam ORDER BY id_role');
    }

    private const VYBER = 'SELECT u.id_uzivatele, u.jmeno, u.prijmeni, u.login, u.email, u.id_role, u.vytvoreno, r.role_zam,
                    (SELECT COUNT(*) FROM pece pe WHERE pe.ID_zamestnance = u.id_uzivatele) AS pocet_pece
             FROM uzivatele u JOIN role_zam r ON r.id_role = u.id_role';

    /** Všichni uživatelé včetně návštěvníků; $q hledá ve jménu, loginu, e-mailu a roli. */
    public function hledat(string $q = ''): array
    {
        $params = [];
        $kde = '';
        if ($q !== '') {
            $kde = " WHERE CONCAT_WS(' ', u.jmeno, u.prijmeni) LIKE :q1 OR u.login LIKE :q2 OR u.email LIKE :q3 OR r.role_zam LIKE :q4";
            $params[':q1'] = $params[':q2'] = $params[':q3'] = $params[':q4'] = '%' . addcslashes($q, '%_\\') . '%';
        }
        return $this->db->select(self::VYBER . $kde . ' ORDER BY r.id_role, u.prijmeni, u.jmeno, u.id_uzivatele', $params);
    }

    public function podleId(int $id): ?array
    {
        $radky = $this->db->select(self::VYBER . ' WHERE u.id_uzivatele = :id', [':id' => $id]);
        return $radky[0] ?? null;
    }

    public function zmenRoli(int $id, int $idRole): void
    {
        $this->db->execute('UPDATE uzivatele SET id_role = :role WHERE id_uzivatele = :id', [':role' => $idRole, ':id' => $id]);
    }

    public function smazat(int $id): bool
    {
        return $this->db->execute('DELETE FROM uzivatele WHERE id_uzivatele = :id', [':id' => $id]) > 0;
    }
}