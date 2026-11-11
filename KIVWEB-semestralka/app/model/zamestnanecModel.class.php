<?php
/*
 * Každá tabulka (entita) má svůj model: návštěvníci jsou v UzivatelModel, zaměstnanci tady.
 */
namespace App\Model;

use App\Core\Database;

class ZamestnanecModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /** Zaměstnanec podle přihlašovacího jména (včetně názvu role), nebo null, když neexistuje. */
    public function podleLoginu(string $login): ?array
    {
        $radky = $this->db->select(
            'SELECT z.ID_zamestnance, z.jmeno, z.prijmeni, z.heslo, r.role_zam
             FROM zamestnanci z JOIN role_zam r ON r.id_role = z.id_role
             WHERE z.login = :login',
            [':login' => $login]
        );
        return $radky[0] ?? null;
    }
}