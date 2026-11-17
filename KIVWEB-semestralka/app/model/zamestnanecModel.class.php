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

    public function vseSPoctem(): array {
        return $this->db->select(
            'SELECT z.ID_zamestnance, z.jmeno, z.prijmeni, r.role_zam,
                    (SELECT COUNT(*) FROM pece pe
                      WHERE pe.ID_zamestnance = z.ID_zamestnance
                        AND (pe.datum_od IS NULL OR pe.datum_od <= CURDATE())
                        AND (pe.datum_do IS NULL OR pe.datum_do >= CURDATE())) AS pocet
             FROM zamestnanci z JOIN role_zam r ON r.id_role = z.id_role
             ORDER BY z.prijmeni, z.jmeno'
        );
    }

    public function role(): array {
        return $this->db->select(
          'SELECT id_role, role_zam FROM role_zam ORDER BY id_role'
        );
    }

    public function loginExistuje(string $login): bool {
        return $this->db->select(
            'SELECT 1 FROM zamestnanci WHERE login = :login LIMIT 1',
            [':login' => $login]
        ) !== [];
    }

    public function vytvor(string $jmeno, string $prijmeni, int $vek, int $idRole, string $login, string $hash): int {
        $this->db->execute(
            'INSERT INTO zamestnanci (jmeno, prijmeni, vek, id_role, login, heslo)
             VALUES (:jmeno, :prijmeni, :vek, :role, :login, :heslo)',
            [
                ':jmeno'    => $jmeno,
                ':prijmeni' => $prijmeni,
                ':vek'      => $vek,
                ':role'     => $idRole,
                ':login'    => $login,
                ':heslo'    => $hash,
            ]
        );
        return (int) $this->db->lastId();
    }

    public function seznam():array {
        return $this->db->select(
            'SELECT z.ID_zamestnance, z.jmeno, z.prijmeni, z.login, r.role_zam,
            (SELECT COUNT(*) FROM pece pe WHERE pe.ID_zamestnance = z.ID_zamestnance) AS pocet_pece
            FROM zamestnanci z JOIN role_zam r ON r.id_role = z.id_role
            ORDER BY z.prijmeni, z.jmeno'
        );
    }

    public function podleId(int $id): ?array
    {
        $radky = $this->db->select(
            'SELECT z.ID_zamestnance, z.jmeno, z.prijmeni, z.login, r.role_zam,
            (SELECT COUNT(*) FROM pece pe WHERE pe.ID_zamestnance = z.ID_zamestnance) AS pocet_pece
            FROM zamestnanci z JOIN role_zam r ON r.id_role = z.id_role
            WHERE z.ID_zamestnance = :id',
            [':id' => $id]
        );
        return $radky[0] ?? null;
    }

    public function smazat(int $id): bool
    {
        return $this->db->execute('DELETE FROM zamestnanci WHERE ID_zamestnance = :id', [':id' => $id]) > 0;
    }
}