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

    public const STAVY = [
        'aktivni'   => 'Aktivní',
        'konci'     => 'Končí dnes',
        'planovana' => 'Naplánovaná',
        'ukoncena'  => 'Ukončená',
    ];


    private function podminky(array $filtr, ?int $idZamestnance): array
    {
        $kde = [];
        $params = [];

        if ($idZamestnance !== null) {
            $kde[] = 'pe.ID_zamestnance = :idzam';
            $params[':idzam'] = $idZamestnance;
        }

        $q = trim((string) ($filtr['q'] ?? ''));
        if ($q !== '') {
            // % a _ jsou v LIKE zástupné znaky, hledaný text je proto zneškodníme
            $vzor = '%' . addcslashes($q, '%_\\') . '%';
            $kde[] = "(CONCAT_WS(' ', z.jmeno, z.prijmeni) LIKE :q1
                       OR zv.jmeno_zvirete LIKE :q2
                       OR pav.jmeno_pavilonu LIKE :q3
                       OR t.nazev_typu LIKE :q4)";
            $params[':q1'] = $params[':q2'] = $params[':q3'] = $params[':q4'] = $vzor;
        }

        if (!empty($filtr['typ'])) {
            $kde[] = 'pe.typ_pece = :typ';
            $params[':typ'] = (int) $filtr['typ'];
        }

        switch ($filtr['stav'] ?? '') {
            case 'aktivni':
                $kde[] = '(pe.datum_od IS NULL OR pe.datum_od <= CURDATE()) AND (pe.datum_do IS NULL OR pe.datum_do > CURDATE())';
                break;
            case 'konci':
                $kde[] = '(pe.datum_od IS NULL OR pe.datum_od <= CURDATE()) AND pe.datum_do = CURDATE()';
                break;
            case 'planovana':
                $kde[] = 'pe.datum_od > CURDATE()';
                break;
            case 'ukoncena':
                $kde[] = 'pe.datum_do < CURDATE()';
                break;
        }

        return [$kde ? 'WHERE ' . implode(' AND ', $kde) : '', $params];
    }

    /** Sloupce, podle kterých jde řadit: klíč v adrese => SQL výrazy (pro stav pořadí aktivní, končí dnes, naplánovaná, ukončená). */
    public const RAZENI = [
        'zamestnanec' => ['z.prijmeni', 'z.jmeno'],
        'zvire'       => ['zv.jmeno_zvirete'],
        'pece'        => ['t.nazev_typu'],
        'obdobi'      => ['pe.datum_od', 'pe.datum_do'],
        'stav'        => ['CASE WHEN pe.datum_od > CURDATE() THEN 3 WHEN pe.datum_do < CURDATE() THEN 4 WHEN pe.datum_do = CURDATE() THEN 2 ELSE 1 END'],
    ];

    private const SPOJENI = 'FROM pece pe
             JOIN uzivatele z ON z.id_uzivatele = pe.ID_zamestnance
             JOIN role_zam r ON r.id_role = z.id_role
             JOIN zvirata zv ON zv.id_zvirata = pe.ID_zvirata
             LEFT JOIN pavilon pav ON pav.ID_pavilon = zv.pavilon
             LEFT JOIN typ_pece t ON t.ID_typ_pece = pe.typ_pece';

    /** Jedna stránka záznamů péče podle filtru. Bez řazení jsou nejnovější nahoře. */
    public function hledat(array $filtr, int $limit, int $offset, ?int $idZamestnance = null, string $razeni = '', string $smer = 'asc'): array
    {
        [$kde, $params] = $this->podminky($filtr, $idZamestnance);

        // ORDER BY se skládá jen ze sloupců z whitelistu RAZENI, nikdy z textu od uživatele
        $order = [];
        if (isset(self::RAZENI[$razeni])) {
            $sm = $smer === 'desc' ? 'DESC' : 'ASC';
            foreach (self::RAZENI[$razeni] as $vyraz) {
                $order[] = $vyraz . ' ' . $sm;
            }
        }
        $order[] = 'pe.ID_pece DESC';

        return $this->db->select(
            'SELECT pe.ID_pece, pe.ID_zvirata, pe.datum_od, pe.datum_do, pe.typ_pece, t.nazev_typu,
                    z.jmeno, z.prijmeni, r.role_zam, zv.jmeno_zvirete, pav.jmeno_pavilonu
             ' . self::SPOJENI . ' ' . $kde . '
             ORDER BY ' . implode(', ', $order) . '
             LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset,
            $params
        );
    }

    /** Kolik záznamů vyhovuje filtru (pro stránkování). */
    public function pocet(array $filtr, ?int $idZamestnance = null): int
    {
        [$kde, $params] = $this->podminky($filtr, $idZamestnance);
        $radky = $this->db->select('SELECT COUNT(*) AS pocet ' . self::SPOJENI . ' ' . $kde, $params);
        return (int) $radky[0]['pocet'];
    }

    /** Péče platná dnes (už začala a ještě neskončila, včetně dne konce), seřazená podle pavilonu a zvířete. */
    public function dnes(): array
    {
        return $this->db->select(
            'SELECT pe.ID_pece, pe.ID_zvirata, pe.ID_zamestnance, pe.datum_od, pe.datum_do, t.nazev_typu,
                    z.jmeno, z.prijmeni, zv.jmeno_zvirete, pav.jmeno_pavilonu
             ' . self::SPOJENI . '
             WHERE (pe.datum_od IS NULL OR pe.datum_od <= CURDATE())
               AND (pe.datum_do IS NULL OR pe.datum_do >= CURDATE())
             ORDER BY pav.jmeno_pavilonu, zv.jmeno_zvirete, t.nazev_typu, z.prijmeni, pe.ID_pece'
        );
    }

    /** Zvířata, o která se dnes nikdo nestará (nemají žádnou péči platnou dnes). */
    public function zvirataBezPece(): array
    {
        return $this->db->select(
            'SELECT zv.id_zvirata, zv.jmeno_zvirete, pav.jmeno_pavilonu
             FROM zvirata zv
             LEFT JOIN pavilon pav ON pav.ID_pavilon = zv.pavilon
             WHERE NOT EXISTS (
                 SELECT 1 FROM pece pe
                 WHERE pe.ID_zvirata = zv.id_zvirata
                   AND (pe.datum_od IS NULL OR pe.datum_od <= CURDATE())
                   AND (pe.datum_do IS NULL OR pe.datum_do >= CURDATE())
             )
             ORDER BY pav.jmeno_pavilonu, zv.jmeno_zvirete'
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