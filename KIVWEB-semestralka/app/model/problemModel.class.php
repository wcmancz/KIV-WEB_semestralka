<?php
namespace App\Model;

use App\Core\Database;

class ProblemModel{
    private Database $db;

    public function __construct(){
        $this->db = new Database();
    }

    public function typy(): array{
        return $this->db->select(
            'SELECT id_typ_problemu, nazev FROM typ_problemu ORDER BY id_typ_problemu'
        );
    }

    public function nazevTypu(int $id): ?string{
        $radky = $this->db->select(
            'SELECT nazev FROM typ_problemu WHERE id_typ_problemu = :id', [':id' => $id]
        );
        return $radky[0]['nazev'] ?? null;
    }

    public function otevrene(int $idZvirata): array
    {
        return $this->db->select(
            'SELECT p.id_problemu, p.nahlaseno, p.popis, t.nazev, z.jmeno, z.prijmeni
             FROM problem_zvirete p
             JOIN typ_problemu t ON t.id_typ_problemu = p.id_typ_problemu
             LEFT JOIN uzivatele z ON z.id_uzivatele = p.id_zamestnance
             WHERE p.id_zvirata = :id AND p.vyreseno IS NULL
             ORDER BY p.nahlaseno DESC, p.id_problemu DESC',
            [':id' => $idZvirata]
        );
    }

    /** Posledních pár vyřešených problémů zvířete (historie). */
    public function vyresene(int $idZvirata, int $limit = 5): array
    {
        return $this->db->select(
            'SELECT p.id_problemu, p.nahlaseno, p.vyreseno, p.popis, t.nazev             FROM problem_zvirete p
             JOIN typ_problemu t ON t.id_typ_problemu = p.id_typ_problemu
             WHERE p.id_zvirata = :id AND p.vyreseno IS NOT NULL
             ORDER BY p.vyreseno DESC, p.id_problemu DESC
             LIMIT ' . (int) $limit,
            [':id' => $idZvirata]
        );
    }

    /** Počty neřešených problémů podle zvířat: [id_zvirata => počet]. */
    public function poctyOtevrenych(): array
    {
        $radky = $this->db->select(
            'SELECT id_zvirata, COUNT(*) AS pocet FROM problem_zvirete WHERE vyreseno IS NULL GROUP BY id_zvirata'
        );
        $vysledek = [];
        foreach ($radky as $r) {
            $vysledek[(int) $r['id_zvirata']] = (int) $r['pocet'];
        }
        return $vysledek;
    }

    public function existujeOtevreny(int $idZvirata, int $idTypu, ?string $popis): bool {
        return $this->db->select(
            'SELECT 1 FROM problem_zvirete
             WHERE id_zvirata = :zv AND id_typ_problemu = :typ AND vyreseno IS NULL AND popis <=> :popis
             LIMIT 1',
                [':zv' => $idZvirata, ':typ' => $idTypu, ':popis' => $popis]
            ) !== [];
    }

    public function pridat(int $idZvirata, int $idTypu, int $idZamestnance, ?string $popis = null): void
    {
        $this->db->execute(
            'INSERT INTO problem_zvirete (id_zvirata, id_typ_problemu, popis, id_zamestnance) VALUES (:zv, :typ, :popis, :zam)',
            [':zv' => $idZvirata, ':typ' => $idTypu, ':popis' => $popis, ':zam' => $idZamestnance]
        );
    }

    /** Označí problém u daného zvířete jako vyřešený. Vrací false, když už vyřešený byl nebo neexistuje. */
    public function vyresit(int $idProblemu, int $idZvirata): bool
    {
        return $this->db->execute(
                'UPDATE problem_zvirete SET vyreseno = CURRENT_TIMESTAMP
             WHERE id_problemu = :id AND id_zvirata = :zv AND vyreseno IS NULL',
                [':id' => $idProblemu, ':zv' => $idZvirata]
            ) > 0;
    }
}