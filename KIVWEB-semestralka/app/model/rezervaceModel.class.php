<?php

namespace App\Model;

use App\Core\Database;

class RezervaceModel
{
    public const CEKA = 1;
    public const SCHVALENA = 2;
    public const PROBIHA = 3;
    public const DOKONCENA = 4;
    public const ZAMITNUTA = 5;

    /** Začátky prohlídek v jednom dni. */
    public const SLOTY = ['10:00', '12:00', '14:00', '16:00'];
    public const KAPACITA = 20;
    public const MAX_OSOB = 10;
    public const DNI_DOPREDU = 60;

    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function stavy(): array
    {
        return $this->db->select('SELECT id_stav, nazev FROM stav_rezervace ORDER BY id_stav');
    }

    private const VYBER = 'SELECT r.id_rezervace, r.id_uzivatele, r.datum, r.cas, r.pocet_osob, r.id_stav, r.duvod_zamitnuti,
                    s.nazev AS stav, CONCAT_WS(\' \', u.jmeno, u.prijmeni) AS navstevnik, u.login, u.email
             FROM rezervace r
             JOIN stav_rezervace s ON s.id_stav = r.id_stav
             JOIN uzivatele u ON u.id_uzivatele = r.id_uzivatele';

    public function mojeRezervace(int $idUzivatele): array
    {
        return $this->db->select(
            self::VYBER . ' WHERE r.id_uzivatele = :id ORDER BY r.datum DESC, r.cas DESC, r.id_rezervace DESC',
            [':id' => $idUzivatele]
        );
    }

    public function nejblizsi(int $idUzivatele): ?array
    {
        $radky = $this->db->select(
            self::VYBER . ' WHERE r.id_uzivatele = :id AND r.datum >= CURDATE() AND r.id_stav NOT IN (:zam, :dok)
             ORDER BY r.datum, r.cas LIMIT 1',
            [':id' => $idUzivatele, ':zam' => self::ZAMITNUTA, ':dok' => self::DOKONCENA]
        );
        return $radky[0] ?? null;
    }

    public function hledat(string $obdobi, int $stav): array
    {
        $kde = [];
        $params = [];
        switch ($obdobi) {
            case 'dnes':      $kde[] = 'r.datum = CURDATE()'; break;
            case 'historie':  $kde[] = 'r.datum < CURDATE()'; break;
            case 'vse':       break;
            default:          $kde[] = 'r.datum >= CURDATE()';
        }
        if ($stav > 0) {
            $kde[] = 'r.id_stav = :stav';
            $params[':stav'] = $stav;
        }
        $razeni = $obdobi === 'historie' ? 'r.datum DESC, r.cas DESC' : 'r.datum, r.cas';
        return $this->db->select(
            self::VYBER . ($kde ? ' WHERE ' . implode(' AND ', $kde) : '') . ' ORDER BY ' . $razeni . ', r.id_rezervace LIMIT 300',
            $params
        );
    }

    public function volno(string $datum, string $cas): int
    {
        $radky = $this->db->select(
            'SELECT COALESCE(SUM(pocet_osob), 0) AS obsazeno FROM rezervace WHERE datum = :d AND cas = :c AND id_stav <> :zam',
            [':d' => $datum, ':c' => $cas, ':zam' => self::ZAMITNUTA]
        );
        return max(0, self::KAPACITA - (int) $radky[0]['obsazeno']);
    }

    public function vytvor(int $idUzivatele, string $datum, string $cas, int $osob): bool
    {
        return $this->db->execute(
                'INSERT INTO rezervace (id_uzivatele, datum, cas, pocet_osob, id_stav)
             SELECT :u, :d, :c, :n, :stav FROM DUAL
             WHERE (SELECT COALESCE(SUM(r.pocet_osob), 0) FROM rezervace r WHERE r.datum = :d2 AND r.cas = :c2 AND r.id_stav <> :zam1) + :n2 <= :kap
               AND NOT EXISTS (SELECT 1 FROM rezervace x WHERE x.id_uzivatele = :u2 AND x.datum = :d3 AND x.cas = :c3 AND x.id_stav <> :zam2)',
                [
                    ':u' => $idUzivatele, ':d' => $datum, ':c' => $cas, ':n' => $osob, ':stav' => self::CEKA,
                    ':d2' => $datum, ':c2' => $cas, ':zam1' => self::ZAMITNUTA, ':n2' => $osob, ':kap' => self::KAPACITA,
                    ':u2' => $idUzivatele, ':d3' => $datum, ':c3' => $cas, ':zam2' => self::ZAMITNUTA,
                ]
            ) > 0;
    }

    public function podleId(int $id): ?array
    {
        $radky = $this->db->select(self::VYBER . ' WHERE r.id_rezervace = :id', [':id' => $id]);
        return $radky[0] ?? null;
    }

    public function zmenStav(int $id, array $zStavu, int $naStav, ?int $schvalovatel = null, ?string $duvod = null): bool
    {
        $zStavu = implode(',', array_map('intval', $zStavu));    // ID stavů jsou čísla z konstant, ne vstup uživatele
        return $this->db->execute(
                "UPDATE rezervace
             SET id_stav = :na, id_schvalovatele = COALESCE(:sch, id_schvalovatele), duvod_zamitnuti = :duvod
             WHERE id_rezervace = :id AND id_stav IN ($zStavu)",
                [':na' => $naStav, ':sch' => $schvalovatel, ':duvod' => $duvod, ':id' => $id]
            ) > 0;
    }

    public function zrusitVlastni(int $id, int $idUzivatele): bool
    {
        return $this->db->execute(
                'DELETE FROM rezervace WHERE id_rezervace = :id AND id_uzivatele = :u AND id_stav IN (:s1, :s2) AND datum > CURDATE()',
                [':id' => $id, ':u' => $idUzivatele, ':s1' => self::CEKA, ':s2' => self::SCHVALENA]
            ) > 0;
    }
}