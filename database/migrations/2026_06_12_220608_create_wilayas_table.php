<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wilayas', function (Blueprint $table) {
            $table->id('id_wilaya');
            $table->string('code', 2)->unique();   // '01' à '58'
            $table->string('nom_wilaya', 100);
        });

        // Seed des 58 wilayas
        $wilayas = [
            ['01','Adrar'],['02','Chlef'],['03','Laghouat'],['04','Oum El Bouaghi'],
            ['05','Batna'],['06','Béjaïa'],['07','Biskra'],['08','Béchar'],
            ['09','Blida'],['10','Bouira'],['11','Tamanrasset'],['12','Tébessa'],
            ['13','Tlemcen'],['14','Tiaret'],['15','Tizi Ouzou'],['16','Alger'],
            ['17','Djelfa'],['18','Jijel'],['19','Sétif'],['20','Saïda'],
            ['21','Skikda'],['22','Sidi Bel Abbès'],['23','Annaba'],['24','Guelma'],
            ['25','Constantine'],['26','Médéa'],['27','Mostaganem'],['28',"M'Sila"],
            ['29','Mascara'],['30','Ouargla'],['31','Oran'],['32','El Bayadh'],
            ['33','Illizi'],['34','Bordj Bou Arréridj'],['35','Boumerdès'],['36','El Tarf'],
            ['37','Tindouf'],['38','Tissemsilt'],['39','El Oued'],['40','Khenchela'],
            ['41','Souk Ahras'],['42','Tipaza'],['43','Mila'],['44','Aïn Defla'],
            ['45','Naâma'],['46','Aïn Témouchent'],['47','Ghardaïa'],['48','Relizane'],
            ['49',"El M'Ghair"],['50','El Menia'],['51','Ouled Djellal'],['52','Bordj Baji Mokhtar'],
            ['53','Béni Abbès'],['54','Timimoun'],['55','Touggourt'],['56','Djanet'],
            ['57','In Salah'],['58','In Guezzam'],
        ];

        $rows = array_map(fn($w) => ['code' => $w[0], 'nom_wilaya' => $w[1]], $wilayas);

        DB::table('wilayas')->insert($rows);
    }

    public function down(): void
    {
        Schema::dropIfExists('wilayas');
    }
};