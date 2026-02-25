<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UgandaLocation;

class UgandaLocationsSeeder extends Seeder
{
    public function run()
    {
        // Regions
        $central = UgandaLocation::create(['name' => 'Central', 'type' => 'region']);
        $eastern = UgandaLocation::create(['name' => 'Eastern', 'type' => 'region']);
        $northern = UgandaLocation::create(['name' => 'Northern', 'type' => 'region']);
        $western = UgandaLocation::create(['name' => 'Western', 'type' => 'region']);

        // Central Region Districts
        $kampala = UgandaLocation::create(['name' => 'Kampala', 'type' => 'district', 'parent_id' => $central->id]);
        $wakiso = UgandaLocation::create(['name' => 'Wakiso', 'type' => 'district', 'parent_id' => $central->id]);
        $mukono = UgandaLocation::create(['name' => 'Mukono', 'type' => 'district', 'parent_id' => $central->id]);
        $mpigi = UgandaLocation::create(['name' => 'Mpigi', 'type' => 'district', 'parent_id' => $central->id]);
        $masaka = UgandaLocation::create(['name' => 'Masaka', 'type' => 'district', 'parent_id' => $central->id]);

        // Eastern Region Districts
        $jinja = UgandaLocation::create(['name' => 'Jinja', 'type' => 'district', 'parent_id' => $eastern->id]);
        $mbale = UgandaLocation::create(['name' => 'Mbale', 'type' => 'district', 'parent_id' => $eastern->id]);
        $iganga = UgandaLocation::create(['name' => 'Iganga', 'type' => 'district', 'parent_id' => $eastern->id]);
        $tororo = UgandaLocation::create(['name' => 'Tororo', 'type' => 'district', 'parent_id' => $eastern->id]);
        $soroti = UgandaLocation::create(['name' => 'Soroti', 'type' => 'district', 'parent_id' => $eastern->id]);

        // Northern Region Districts
        $gulu = UgandaLocation::create(['name' => 'Gulu', 'type' => 'district', 'parent_id' => $northern->id]);
        $lira = UgandaLocation::create(['name' => 'Lira', 'type' => 'district', 'parent_id' => $northern->id]);
        $kitgum = UgandaLocation::create(['name' => 'Kitgum', 'type' => 'district', 'parent_id' => $northern->id]);
        $arua = UgandaLocation::create(['name' => 'Arua', 'type' => 'district', 'parent_id' => $northern->id]);

        // Western Region Districts
        $mbarara = UgandaLocation::create(['name' => 'Mbarara', 'type' => 'district', 'parent_id' => $western->id]);
        $kasese = UgandaLocation::create(['name' => 'Kasese', 'type' => 'district', 'parent_id' => $western->id]);
        $hoima = UgandaLocation::create(['name' => 'Hoima', 'type' => 'district', 'parent_id' => $western->id]);
        $kabale = UgandaLocation::create(['name' => 'Kabale', 'type' => 'district', 'parent_id' => $western->id]);

        // Kampala Counties
        $kampalaC = UgandaLocation::create(['name' => 'Kampala Central', 'type' => 'county', 'parent_id' => $kampala->id]);
        $kawempe = UgandaLocation::create(['name' => 'Kawempe', 'type' => 'county', 'parent_id' => $kampala->id]);
        $makindye = UgandaLocation::create(['name' => 'Makindye', 'type' => 'county', 'parent_id' => $kampala->id]);
        $nakawa = UgandaLocation::create(['name' => 'Nakawa', 'type' => 'county', 'parent_id' => $kampala->id]);
        $rubaga = UgandaLocation::create(['name' => 'Rubaga', 'type' => 'county', 'parent_id' => $kampala->id]);

        // Kampala Central Subcounties
        $central_div = UgandaLocation::create(['name' => 'Central Division', 'type' => 'subcounty', 'parent_id' => $kampalaC->id]);
        $kololo = UgandaLocation::create(['name' => 'Kololo', 'type' => 'subcounty', 'parent_id' => $kampalaC->id]);
        $nakasero = UgandaLocation::create(['name' => 'Nakasero', 'type' => 'subcounty', 'parent_id' => $kampalaC->id]);

        // Central Division Parishes
        $nakasero_p = UgandaLocation::create(['name' => 'Nakasero Parish', 'type' => 'parish', 'parent_id' => $central_div->id]);
        $kololo_p = UgandaLocation::create(['name' => 'Kololo Parish', 'type' => 'parish', 'parent_id' => $central_div->id]);
        $mengo_p = UgandaLocation::create(['name' => 'Mengo Parish', 'type' => 'parish', 'parent_id' => $central_div->id]);

        // Nakasero Parish Villages
        UgandaLocation::create(['name' => 'Nakasero Village A', 'type' => 'village', 'parent_id' => $nakasero_p->id]);
        UgandaLocation::create(['name' => 'Nakasero Village B', 'type' => 'village', 'parent_id' => $nakasero_p->id]);
        UgandaLocation::create(['name' => 'Nakasero Village C', 'type' => 'village', 'parent_id' => $nakasero_p->id]);

        // Kawempe Subcounties
        $kawempe_div = UgandaLocation::create(['name' => 'Kawempe Division', 'type' => 'subcounty', 'parent_id' => $kawempe->id]);
        $makerere = UgandaLocation::create(['name' => 'Makerere', 'type' => 'subcounty', 'parent_id' => $kawempe->id]);

        // Kawempe Parishes
        $kawempe_p = UgandaLocation::create(['name' => 'Kawempe Parish', 'type' => 'parish', 'parent_id' => $kawempe_div->id]);
        $bwaise_p = UgandaLocation::create(['name' => 'Bwaise Parish', 'type' => 'parish', 'parent_id' => $kawempe_div->id]);

        // Kawempe Villages
        UgandaLocation::create(['name' => 'Kawempe Village A', 'type' => 'village', 'parent_id' => $kawempe_p->id]);
        UgandaLocation::create(['name' => 'Kawempe Village B', 'type' => 'village', 'parent_id' => $kawempe_p->id]);
        UgandaLocation::create(['name' => 'Bwaise Village A', 'type' => 'village', 'parent_id' => $bwaise_p->id]);

        // Wakiso Counties
        $busiro = UgandaLocation::create(['name' => 'Busiro', 'type' => 'county', 'parent_id' => $wakiso->id]);
        $kyadondo = UgandaLocation::create(['name' => 'Kyadondo', 'type' => 'county', 'parent_id' => $wakiso->id]);

        // Busiro Subcounties
        $entebbe = UgandaLocation::create(['name' => 'Entebbe', 'type' => 'subcounty', 'parent_id' => $busiro->id]);
        $katabi = UgandaLocation::create(['name' => 'Katabi', 'type' => 'subcounty', 'parent_id' => $busiro->id]);

        // Entebbe Parishes
        $entebbe_p = UgandaLocation::create(['name' => 'Entebbe Central Parish', 'type' => 'parish', 'parent_id' => $entebbe->id]);
        UgandaLocation::create(['name' => 'Entebbe Village A', 'type' => 'village', 'parent_id' => $entebbe_p->id]);
        UgandaLocation::create(['name' => 'Entebbe Village B', 'type' => 'village', 'parent_id' => $entebbe_p->id]);

        // Jinja Counties
        $jinja_mun = UgandaLocation::create(['name' => 'Jinja Municipality', 'type' => 'county', 'parent_id' => $jinja->id]);
        $butembe = UgandaLocation::create(['name' => 'Butembe', 'type' => 'county', 'parent_id' => $jinja->id]);

        // Jinja Subcounties
        $jinja_central = UgandaLocation::create(['name' => 'Jinja Central', 'type' => 'subcounty', 'parent_id' => $jinja_mun->id]);
        $walukuba = UgandaLocation::create(['name' => 'Walukuba', 'type' => 'subcounty', 'parent_id' => $jinja_mun->id]);

        // Jinja Parishes
        $jinja_p = UgandaLocation::create(['name' => 'Jinja Central Parish', 'type' => 'parish', 'parent_id' => $jinja_central->id]);
        UgandaLocation::create(['name' => 'Jinja Village A', 'type' => 'village', 'parent_id' => $jinja_p->id]);
        UgandaLocation::create(['name' => 'Jinja Village B', 'type' => 'village', 'parent_id' => $jinja_p->id]);

        // Mbale Counties
        $mbale_mun = UgandaLocation::create(['name' => 'Mbale Municipality', 'type' => 'county', 'parent_id' => $mbale->id]);
        $bungokho = UgandaLocation::create(['name' => 'Bungokho', 'type' => 'county', 'parent_id' => $mbale->id]);

        // Mbale Subcounties
        $industrial = UgandaLocation::create(['name' => 'Industrial Division', 'type' => 'subcounty', 'parent_id' => $mbale_mun->id]);
        $industrial_p = UgandaLocation::create(['name' => 'Industrial Parish', 'type' => 'parish', 'parent_id' => $industrial->id]);
        UgandaLocation::create(['name' => 'Industrial Village A', 'type' => 'village', 'parent_id' => $industrial_p->id]);

        // Gulu Counties
        $gulu_mun = UgandaLocation::create(['name' => 'Gulu Municipality', 'type' => 'county', 'parent_id' => $gulu->id]);
        $aswa = UgandaLocation::create(['name' => 'Aswa', 'type' => 'county', 'parent_id' => $gulu->id]);

        // Gulu Subcounties
        $bardege = UgandaLocation::create(['name' => 'Bardege Division', 'type' => 'subcounty', 'parent_id' => $gulu_mun->id]);
        $bardege_p = UgandaLocation::create(['name' => 'Bardege Parish', 'type' => 'parish', 'parent_id' => $bardege->id]);
        UgandaLocation::create(['name' => 'Bardege Village A', 'type' => 'village', 'parent_id' => $bardege_p->id]);

        // Mbarara Counties
        $mbarara_mun = UgandaLocation::create(['name' => 'Mbarara Municipality', 'type' => 'county', 'parent_id' => $mbarara->id]);
        $kashari = UgandaLocation::create(['name' => 'Kashari', 'type' => 'county', 'parent_id' => $mbarara->id]);

        // Mbarara Subcounties
        $kamukuzi = UgandaLocation::create(['name' => 'Kamukuzi Division', 'type' => 'subcounty', 'parent_id' => $mbarara_mun->id]);
        $kamukuzi_p = UgandaLocation::create(['name' => 'Kamukuzi Parish', 'type' => 'parish', 'parent_id' => $kamukuzi->id]);
        UgandaLocation::create(['name' => 'Kamukuzi Village A', 'type' => 'village', 'parent_id' => $kamukuzi_p->id]);

        echo "Uganda locations seeded successfully!\n";
    }
}
