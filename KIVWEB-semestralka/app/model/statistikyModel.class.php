<?php
/*
 * Statistiky péče. Využívá pohledy (views) z databáze:
 *   v_aktualni_pece   – péče platné dnes
 *   v_statistika_pece – souhrn péče za každého zaměstnance (počet záznamů, zvířat a dnů)
 */
namespace App\Model;

use App\Core\Database;

class StatistikyModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function souhrn(): array
    {
        $radky = $this->db->select(
            'SELECT
                (SELECT COUNT(*) FROM zvirata) AS zvirat,
                (SELECT COUNT(*) FROM uzivatele u JOIN role_zam r ON r.id_role = u.id_role WHERE r.role_zam <> \'navstevnik\') AS zamestnancu,
                (SELECT COUNT(*) FROM v_aktualni_pece) AS aktivni_pece,
                (SELECT COUNT(*) FROM zvirata z WHERE z.id_zvirata NOT IN (SELECT id_zvirata FROM v_aktualni_pece)) AS bez_pece,
                (SELECT COUNT(*) FROM problem_zvirete WHERE vyreseno IS NULL) AS otevrene_problemy'
        );
        return $radky[0];
    }

    public function zamestnanci(): array
    {
        return $this->db->select(
            'SELECT s.ID_zamestnance, s.zamestnanec, s.role_zam, s.pocet_zaznamu, s.pocet_zvirat, s.dnu_pece,
                    (SELECT COUNT(*) FROM v_aktualni_pece a WHERE a.ID_zamestnance = s.ID_zamestnance) AS dnes
             FROM v_statistika_pece s
             ORDER BY s.pocet_zaznamu DESC, s.zamestnanec'
        );
    }

    public function nejpecovanejsi(int $limit = 10): array
    {
        return $this->db->select(
            'SELECT z.id_zvirata, z.jmeno_zvirete, pav.jmeno_pavilonu, COUNT(pe.ID_pece) AS zaznamu,
                    (SELECT COUNT(*) FROM v_aktualni_pece a WHERE a.id_zvirata = z.id_zvirata) AS dnes
             FROM zvirata z
             LEFT JOIN pavilon pav ON pav.ID_pavilon = z.pavilon
             LEFT JOIN pece pe ON pe.ID_zvirata = z.id_zvirata
             GROUP BY z.id_zvirata, z.jmeno_zvirete, pav.jmeno_pavilonu
             ORDER BY zaznamu DESC, z.jmeno_zvirete
             LIMIT ' . (int) $limit
        );
    }

    public function bezDnesniPece(): array
    {
        return $this->db->select(
            'SELECT z.id_zvirata, z.jmeno_zvirete, pav.jmeno_pavilonu
             FROM zvirata z LEFT JOIN pavilon pav ON pav.ID_pavilon = z.pavilon
             WHERE z.id_zvirata NOT IN (SELECT id_zvirata FROM v_aktualni_pece)
             ORDER BY pav.jmeno_pavilonu, z.jmeno_zvirete'
        );
    }

    public function podleDruhu(): array
    {
        return $this->db->select(
            'SELECT t.nazev_typu AS nazev, COUNT(pe.ID_pece) AS celkem,
                    (SELECT COUNT(*) FROM v_aktualni_pece a WHERE a.typ_pece = t.nazev_typu) AS dnes
             FROM typ_pece t LEFT JOIN pece pe ON pe.typ_pece = t.ID_typ_pece
             GROUP BY t.ID_typ_pece, t.nazev_typu
             ORDER BY celkem DESC, t.nazev_typu'
        );
    }

    public function podlePavilonu(): array
    {
        return $this->db->select(
            'SELECT pav.jmeno_pavilonu AS nazev, COUNT(DISTINCT z.id_zvirata) AS zvirat,
                    (SELECT COUNT(*) FROM v_aktualni_pece a WHERE a.jmeno_pavilonu = pav.jmeno_pavilonu) AS dnes
             FROM pavilon pav LEFT JOIN zvirata z ON z.pavilon = pav.ID_pavilon
             GROUP BY pav.ID_pavilon, pav.jmeno_pavilonu
             ORDER BY zvirat DESC, pav.jmeno_pavilonu'
        );
    }
}