<?php

namespace App\Model;

use App\Core\Database;

class PeceModel {
    private Database $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function typy(): array {
        return $this->db->select('SELECT ID_typ_pece, nazev_typu FROM typ_pece ORDER BY ID_typ_pece');
    }

    public function vse(?int $idZamestnance = null): array
    {
        return $this->db->select(
            'SELECT pe.ID_pece, pe.datum_od, pe.datum_do, pe.typ_pece, t.nazev_typu,
                    z.jmeno, z.prijmeni, r.role_zam, zv.jmeno_zvirete, pav.jmeno_pavilonu
             FROM pece pe
             JOIN uzivatele z ON z.id_uzivatele = pe.ID_zamestnance
             JOIN role_zam r ON r.id_role = z.id_role
             JOIN zvirata zv ON zv.id_zvirata = pe.ID_zvirata
             LEFT JOIN pavilon pav ON pav.ID_pavilon = zv.pavilon
             LEFT JOIN typ_pece t ON t.ID_typ_pece = pe.typ_pece
             ' . ($idZamestnance !== null ? 'WHERE pe.ID_zamestnance = :id' : '') . '
             ORDER BY pe.ID_pece DESC',
            $idZamestnance !== null ? [':id' => $idZamestnance] : []
        );
    }

    public function sluzbyZamestnance(int $idZamestnance): array {
        $radky = $this->db->select(
            'SELECT pe.ID_zvirata, pe.datum_od, t.nazev_typu
             FROM pece pe JOIN typ_pece t ON t.ID_typ_pece = pe.typ_pece
             WHERE pe.ID_zamestnance = :id
               AND (pe.datum_od IS NULL OR pe.datum_od <= CURDATE())
               AND (pe.datum_do IS NULL OR pe.datum_do >= CURDATE())',
            [':id' => $idZamestnance]
        );
        $sluzby = [];
        foreach ($radky as $r) {
            $sluzby[$r['ID_zvirata']][] = $r;     // seskupení podle zvířete
        }
        return $sluzby;
    }

    public function pridat(int $zamestnanec, array $zvirata, int $typ, string $od, ?string $do): void
    {
        $radky  = [];
        $params = [];
        foreach (array_values($zvirata) as $i => $idZvirete) {
            $radky[] = "(:zam$i, :zv$i, :od$i, :do$i, :typ$i)";
            $params[":zam$i"] = $zamestnanec;
            $params[":zv$i"]  = $idZvirete;
            $params[":od$i"]  = $od;
            $params[":do$i"]  = $do;
            $params[":typ$i"] = $typ;
        }
        $this->db->execute(
            'INSERT INTO pece (ID_zamestnance, ID_zvirata, datum_od, datum_do, typ_pece) VALUES ' . implode(', ', $radky),
            $params
        );
    }

    public function ukoncit(int $id): bool {
        $zmeneno = $this->db->execute(
            'UPDATE pece SET datum_do = CURDATE()
             WHERE ID_pece = :id
               AND datum_od <= CURDATE()
               AND (datum_do IS NULL OR datum_do > CURDATE())',
            [':id' => $id]
        );
        return $zmeneno > 0;
    }

    public function smazat(int $id): bool {
        return $this->db->execute('DELETE FROM pece WHERE ID_pece = :id', [':id' => $id]) > 0;
    }

}