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
            z.jmeno_zvirete, p.typ_potravy
            FROM pavilon pav
            LEFT JOIN zvirata z ON z.pavilon = pav.ID_pavilon
            LEFT JOIN potrava p ON p.ID_potrava = z.potrava
            ORDER BY pav.jmeno_pavilonu, z.jmeno_zvirete'
        );

        $pavilony = [];
        foreach($radky as $r){
            $pavilony[$r['ID_pavilon']]['info'] = $r;
            if ($r['jmeno_zvirete'] != null){
                $pavilony[$r['ID_pavilon']]['zvirata'][] = $r;
            }
        }
        return $pavilony;
    }
}