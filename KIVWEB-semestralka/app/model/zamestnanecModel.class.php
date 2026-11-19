<?php
/*
 * Zaměstnanci = uživatelé, jejichž role není `navstevnik`. Přihlášení, registrace a kontroly loginu/e-mailu jsou v UzivatelModel.
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

    public function vseSPoctem(): array {
        return $this->db->select(
            'SELECT z.id_uzivatele AS ID_zamestnance, z.jmeno, z.prijmeni, r.role_zam,
                    (SELECT COUNT(*) FROM pece pe
                      WHERE pe.ID_zamestnance = z.id_uzivatele
                        AND (pe.datum_od IS NULL OR pe.datum_od <= CURDATE())
                        AND (pe.datum_do IS NULL OR pe.datum_do >= CURDATE())) AS pocet
             FROM uzivatele z JOIN role_zam r ON r.id_role = z.id_role
             WHERE r.role_zam <> \'navstevnik\'
             ORDER BY z.prijmeni, z.jmeno'
        );
    }

    public function role(): array {
        return $this->db->select(
            'SELECT id_role, role_zam FROM role_zam WHERE role_zam <> \'navstevnik\' ORDER BY id_role'
        );
    }

    public function vytvor(string $jmeno, string $prijmeni, int $vek, int $idRole, string $email, string $login, string $hash): int {
        $this->db->execute(
            'INSERT INTO uzivatele (jmeno, prijmeni, vek, id_role, email, login, heslo)
             VALUES (:jmeno, :prijmeni, :vek, :role, :email, :login, :heslo)',
            [
                ':jmeno'    => $jmeno,
                ':prijmeni' => $prijmeni,
                ':vek'      => $vek,
                ':role'     => $idRole,
                ':email'    => $email,
                ':login'    => $login,
                ':heslo'    => $hash,
            ]
        );
        return (int) $this->db->lastId();
    }

    public function seznam():array {
        return $this->db->select(
            'SELECT z.id_uzivatele AS ID_zamestnance, z.jmeno, z.prijmeni, z.login, z.email, r.role_zam,
            (SELECT COUNT(*) FROM pece pe WHERE pe.ID_zamestnance = z.id_uzivatele) AS pocet_pece
            FROM uzivatele z JOIN role_zam r ON r.id_role = z.id_role
            WHERE r.role_zam <> \'navstevnik\'
            ORDER BY z.prijmeni, z.jmeno'
        );
    }

    public function podleId(int $id): ?array
    {
        $radky = $this->db->select(
            'SELECT z.id_uzivatele AS ID_zamestnance, z.jmeno, z.prijmeni, z.login, z.email, r.role_zam,
            (SELECT COUNT(*) FROM pece pe WHERE pe.ID_zamestnance = z.id_uzivatele) AS pocet_pece
            FROM uzivatele z JOIN role_zam r ON r.id_role = z.id_role
            WHERE z.id_uzivatele = :id AND r.role_zam <> \'navstevnik\'',
            [':id' => $id]
        );
        return $radky[0] ?? null;
    }

    public function smazat(int $id): bool
    {
        // smaže jen zaměstnance, nikdy návštěvníka (kdyby někdo podvrhl cizí ID)
        return $this->db->execute(
                'DELETE u FROM uzivatele u JOIN role_zam r ON r.id_role = u.id_role
             WHERE u.id_uzivatele = :id AND r.role_zam <> \'navstevnik\'',
                [':id' => $id]
            ) > 0;
    }
}