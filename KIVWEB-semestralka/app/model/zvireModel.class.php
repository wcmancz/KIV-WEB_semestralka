<?php
/*
 * dotazy do databáze a úprava dat do nich
 */

namespace App\Model;

use App\Core\Database;

class ZvireModel{
    private Database $db;

    public function __construct(){
        $this->db = new Database();
    }

    /**
     * @return array
     * vrátí pavilony a v každém dané zvíře
     *  $pavilony[ID]['info'] = údaje o pavilonu
     *  $pavilony[ID]['zvirata'] = seznam zvířat
     */
    public function pavilonyAZvirata(): array
    {
        $radky = $this->db->select(
            'SELECT pav.ID_pavilon, pav.jmeno_pavilonu, pav.lokace, pav.rozloha,
            z.id_zvirata, z.jmeno_zvirete, p.typ_potravy
            FROM pavilon pav
            LEFT JOIN zvirata z ON z.pavilon = pav.ID_pavilon
            LEFT JOIN potrava p ON p.ID_potrava = z.potrava
            ORDER BY pav.jmeno_pavilonu, z.jmeno_zvirete'
        );

        $pavilony = [];
        foreach($radky as $r){
            $pavilony[$r['ID_pavilon']]['info'] = $r;
            if ($r['jmeno_zvirete'] !== null){
                $pavilony[$r['ID_pavilon']]['zvirata'][] = $r;
            }
        }
        return $pavilony;
    }

    public function potravy(): array{
        return $this->db->select(
            'SELECT ID_potrava, typ_potravy FROM potrava ORDER BY ID_potrava'
        );
    }

    public function pavilonySPoctem(): array {
        return $this->db->select(
            'SELECT pav.ID_pavilon, pav.jmeno_pavilonu, pav.lokace, pav.rozloha, COUNT(z.id_zvirata) AS pocet
             FROM pavilon pav
             LEFT JOIN zvirata z ON z.pavilon = pav.ID_pavilon
             GROUP BY pav.ID_pavilon, pav.jmeno_pavilonu, pav.lokace, pav.rozloha
             ORDER BY pav.jmeno_pavilonu'
        );
    }

    public function pridat (string $jmeno, int $pavilon, ?int $potrava): int {
        $this->db->execute(
            'INSERT INTO zvirata (jmeno_zvirete, pavilon, potrava) VALUES (:jmeno, :pavilon, :potrava)',
            [':jmeno' => $jmeno, ':pavilon' => $pavilon, ':potrava' => $potrava]
        );
        return (int) $this->db->lastId();
    }

    public function seznam(): array
    {
        return $this->db->select(
            'SELECT z.id_zvirata, z.jmeno_zvirete, pav.jmeno_pavilonu,
            (SELECT COUNT(*) FROM pece pe WHERE pe.ID_zvirata = z.id_zvirata) AS pocet_pece
            FROM zvirata z LEFT JOIN pavilon pav ON pav.ID_pavilon = z.pavilon
            ORDER BY z.jmeno_zvirete'
        );
    }

    /** Seznam zvířat pro správu: $q hledá ve jménu, pavilonu a potravě, $pavilon (0 = všechny) omezí na jeden pavilon. */
    public function hledat(string $q = '', int $pavilon = 0): array
    {
        $kde = [];
        $params = [];
        if ($q !== '') {
            $kde[] = '(z.jmeno_zvirete LIKE :q1 OR pav.jmeno_pavilonu LIKE :q2 OR p.typ_potravy LIKE :q3)';
            $params[':q1'] = $params[':q2'] = $params[':q3'] = '%' . addcslashes($q, '%_\\') . '%';
        }
        if ($pavilon > 0) {
            $kde[] = 'z.pavilon = :pavilon';
            $params[':pavilon'] = $pavilon;
        }
        return $this->db->select(
            'SELECT z.id_zvirata, z.jmeno_zvirete, pav.jmeno_pavilonu, p.typ_potravy,
                    (SELECT COUNT(*) FROM pece pe WHERE pe.ID_zvirata = z.id_zvirata) AS pocet_pece
             FROM zvirata z
             LEFT JOIN pavilon pav ON pav.ID_pavilon = z.pavilon
             LEFT JOIN potrava p ON p.ID_potrava = z.potrava
             ' . ($kde ? 'WHERE ' . implode(' AND ', $kde) : '') . '
             ORDER BY z.jmeno_zvirete, z.id_zvirata',
            $params
        );
    }

    /** Zvíře včetně ID pavilonu a potravy (pro předvyplnění formuláře úpravy), nebo null. */
    public function proUpravu(int $id): ?array
    {
        $radky = $this->db->select(
            'SELECT id_zvirata, jmeno_zvirete, pavilon, potrava FROM zvirata WHERE id_zvirata = :id',
            [':id' => $id]
        );
        return $radky[0] ?? null;
    }

    public function upravit(int $id, string $jmeno, int $pavilon, ?int $potrava): void
    {
        $this->db->execute(
            'UPDATE zvirata SET jmeno_zvirete = :jmeno, pavilon = :pavilon, potrava = :potrava WHERE id_zvirata = :id',
            [':jmeno' => $jmeno, ':pavilon' => $pavilon, ':potrava' => $potrava, ':id' => $id]
        );
    }

    public function podleId(int $id): ?array
    {
        $radky = $this->db->select(
            'SELECT z.id_zvirata, z.jmeno_zvirete, pav.jmeno_pavilonu,
            (SELECT COUNT(*) FROM pece pe WHERE pe.ID_zvirata = z.id_zvirata) AS pocet_pece
            FROM zvirata z LEFT JOIN pavilon pav ON pav.ID_pavilon = z.pavilon
            WHERE z.id_zvirata = :id',
            [':id' => $id]
        );
        return $radky[0] ?? null;
    }

    public function smazat(int $id): bool
    {
        return $this->db->execute('DELETE FROM zvirata WHERE id_zvirata = :id', [':id' => $id]) > 0;
    }

    public function detail(int $id): ?array
    {
        $radky = $this->db->select(
            'SELECT z.id_zvirata, z.jmeno_zvirete, pav.jmeno_pavilonu, pav.lokace, p.typ_potravy
             FROM zvirata z
             LEFT JOIN pavilon pav ON pav.ID_pavilon = z.pavilon
             LEFT JOIN potrava p ON p.ID_potrava = z.potrava
             WHERE z.id_zvirata = :id',
            [':id' => $id]
        );
        return $radky[0] ?? null;
    }
}