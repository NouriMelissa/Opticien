<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * CommuneSeeder
 *
 * Génère automatiquement plusieurs communes pour chaque wilaya.
 * À lancer APRÈS le seed des wilayas (php artisan migrate doit
 * avoir déjà rempli la table wilayas).
 *
 * Usage : php artisan db:seed --class=CommuneSeeder
 */
class CommuneSeeder extends Seeder
{
    public function run(): void
    {
        // ── Données réelles : quelques communes principales par wilaya ──
        // Clé = code wilaya (01 à 58), valeur = liste de communes
        $communesParWilaya = [
            '01' => ['Adrar', 'Reggane', 'Aoulef', 'Timimoun', 'Zaouiet Kounta'],
            '02' => ['Chlef', 'Ténès', 'Boukadir', 'Oued Fodda', 'Ouled Farès'],
            '03' => ['Laghouat', 'Aflou', 'Ksar El Hirane', "Hassi R'Mel"],
            '04' => ['Oum El Bouaghi', 'Aïn Beïda', 'Aïn M\'lila', 'Sigus'],
            '05' => ['Batna', 'Barika', 'Merouana', 'Arris', 'Ain Touta'],
            '06' => ['Béjaïa', 'Akbou', 'Kherrata', 'Amizour', 'Sidi Aich'],
            '07' => ['Biskra', "Sidi Okba", 'Tolga', 'Ouled Djellal'],
            '08' => ['Béchar', 'Kenadsa', 'Abadla', 'Taghit'],
            '09' => ['Blida', 'Boufarik', 'Mouzaïa', 'Larbaa', 'Bougara'],
            '10' => ['Bouira', 'Sour El Ghozlane', 'Lakhdaria', 'Aïn Bessem'],
            '11' => ['Tamanrasset', 'In Salah', 'In Guezzam', 'Abalessa'],
            '12' => ['Tébessa', 'Bir El Ater', 'Cheria', 'El Aouinet'],
            '13' => ['Tlemcen', 'Maghnia', 'Remchi', 'Nedroma', 'Ghazaouet'],
            '14' => ['Tiaret', 'Sougueur', 'Mahdia', 'Frenda'],
            '15' => ['Tizi Ouzou', 'Azazga', 'Draâ Ben Khedda', 'Boghni'],
            '16' => ['Alger Centre', 'Bab El Oued', 'Hussein Dey', 'El Harrach', 'Kouba', 'Birtouta', 'Zeralda'],
            '17' => ['Djelfa', 'Aïn Oussera', 'Messaad', 'Hassi Bahbah'],
            '18' => ['Jijel', 'Taher', 'El Milia', 'Ziama Mansouriah'],
            '19' => ['Sétif', "El Eulma", 'Aïn Oulmene', 'Bougaa'],
            '20' => ['Saïda', 'Aïn El Hadjar', 'Youb', 'El Hassasna'],
            '21' => ['Skikda', 'Collo', 'Azzaba', 'El Hadaiek'],
            '22' => ['Sidi Bel Abbès', 'Telagh', 'Ras El Ma', "Sfisef"],
            '23' => ['Annaba', 'El Bouni', 'Berrahal', 'Seraidi'],
            '24' => ['Guelma', 'Oued Zenati', 'Bouchegouf', 'Héliopolis'],
            '25' => ['Constantine', 'El Khroub', 'Hamma Bouziane', 'Didouche Mourad'],
            '26' => ['Médéa', 'Berrouaghia', "Ksar El Boukhari", 'Tablat'],
            '27' => ['Mostaganem', 'Sayada', "Aïn Tédelès", 'Bouguirat'],
            '28' => ["M'Sila", 'Boussaâda', "Sidi Aïssa", 'Aïn El Hadjel'],
            '29' => ['Mascara', 'Sig', "Tighennif", "Mohammadia"],
            '30' => ['Ouargla', 'Hassi Messaoud', 'Touggourt', "N'Goussa"],
            '31' => ['Oran', "Bir El Djir", "Es Senia", 'Arzew', 'Aïn El Turk'],
            '32' => ['El Bayadh', "Boualem", "Brezina", "Rogassa"],
            '33' => ['Illizi', "Djanet", "Bordj Omar Driss"],
            '34' => ['Bordj Bou Arréridj', "Ras El Oued", "Mansourah", "El Achir"],
            '35' => ['Boumerdès', "Bordj Menaiel", "Khemis El Khechna", "Dellys"],
            '36' => ['El Tarf', "Ben M'Hidi", "Bouteldja", "Drean"],
            '37' => ['Tindouf', "Oum El Assel"],
            '38' => ['Tissemsilt', "Theniet El Had", "Bordj Bou Naama"],
            '39' => ['El Oued', "Robbah", "Guemar", "Debila"],
            '40' => ['Khenchela', "Kais", "Babar", "Bouhmama"],
            '41' => ['Souk Ahras', "Sedrata", "M'Daourouch", "Hanancha"],
            '42' => ['Tipaza', "Cherchell", "Hadjout", "Bou Ismail"],
            '43' => ['Mila', "Chelghoum Laid", "Ferdjioua", "Grarem Gouga"],
            '44' => ['Aïn Defla', "Khemis Miliana", "Miliana", "El Attaf"],
            '45' => ['Naâma', "Mecheria", "Aïn Sefra", "Tiout"],
            '46' => ["Aïn Témouchent", "Hammam Bouhadjar", "El Amria", "Beni Saf"],
            '47' => ['Ghardaïa', "Metlili", "El Atteuf", "Berriane"],
            '48' => ['Relizane', "Oued Rhiou", "Mazouna", "Yellel"],
            '49' => ["El M'Ghair", "Djamaa", "Still", "M'Rara"],
            '50' => ['El Menia', "Hassi Gara", "El Hadjira"],
            '51' => ['Ouled Djellal', "Ras El Miaad", "Sidi Khaled"],
            '52' => ['Bordj Baji Mokhtar', "Timiaouine"],
            '53' => ['Béni Abbès', "Igli", "Kerzaz"],
            '54' => ['Timimoun', "Ouled Said", "Aougrout"],
            '55' => ['Touggourt', "Nezla", "Tebesbest", "El Hadjira"],
            '56' => ['Djanet', "Bordj El Haouas"],
            '57' => ['In Salah', "Foggaret Ezzaouia"],
            '58' => ['In Guezzam', "Tin Zaouatine"],
        ];

        // ── Récupérer le mapping code → id_wilaya ───────────────────
        $wilayas = DB::table('wilayas')->pluck('id_wilaya', 'code');

        $rows = [];

        foreach ($communesParWilaya as $code => $listeCommunes) {
            $idWilaya = $wilayas[$code] ?? null;

            if (!$idWilaya) {
                continue; // Wilaya pas trouvée, on saute (sécurité)
            }

            foreach ($listeCommunes as $nomCommune) {
                $rows[] = [
                    'nom_commune' => $nomCommune,
                    'id_wilaya'   => $idWilaya,
                ];
            }
        }

        // ── Insertion en masse ───────────────────────────────────────
        // chunk() pour éviter de dépasser la limite de paramètres SQL
        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table('communes')->insert($chunk);
        }

        $this->command->info(count($rows) . ' communes insérées pour ' . count($communesParWilaya) . ' wilayas.');
    }
}