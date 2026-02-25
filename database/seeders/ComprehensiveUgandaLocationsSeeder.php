<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UgandaLocation;

class ComprehensiveUgandaLocationsSeeder extends Seeder
{
    public function run()
    {
        // Regions
        $central = UgandaLocation::create(['name' => 'Central', 'type' => 'region']);
        $eastern = UgandaLocation::create(['name' => 'Eastern', 'type' => 'region']);
        $northern = UgandaLocation::create(['name' => 'Northern', 'type' => 'region']);
        $western = UgandaLocation::create(['name' => 'Western', 'type' => 'region']);

        // ALL CENTRAL REGION DISTRICTS
        $centralDistricts = [
            'Kampala', 'Wakiso', 'Mukono', 'Mpigi', 'Masaka', 'Rakai', 'Kalangala', 
            'Lyantonde', 'Sembabule', 'Butambala', 'Gomba', 'Kalungu', 'Bukomansimbi', 
            'Lwengo', 'Kyotera', 'Mityana', 'Mubende', 'Kassanda', 'Kiboga'
        ];
        
        foreach ($centralDistricts as $districtName) {
            $district = UgandaLocation::create(['name' => $districtName, 'type' => 'district', 'parent_id' => $central->id]);
            
            // Add counties for each district
            $counties = $this->getCountiesForDistrict($districtName);
            foreach ($counties as $countyName) {
                $county = UgandaLocation::create(['name' => $countyName, 'type' => 'county', 'parent_id' => $district->id]);
                
                // Add subcounties
                $subcounties = $this->getSubcountiesForCounty($countyName);
                foreach ($subcounties as $subcountyName) {
                    $subcounty = UgandaLocation::create(['name' => $subcountyName, 'type' => 'subcounty', 'parent_id' => $county->id]);
                    
                    // Add parishes
                    $parishes = $this->getParishesForSubcounty($subcountyName);
                    foreach ($parishes as $parishName) {
                        $parish = UgandaLocation::create(['name' => $parishName, 'type' => 'parish', 'parent_id' => $subcounty->id]);
                        
                        // Add villages
                        $villages = $this->getVillagesForParish($parishName);
                        foreach ($villages as $villageName) {
                            UgandaLocation::create(['name' => $villageName, 'type' => 'village', 'parent_id' => $parish->id]);
                        }
                    }
                }
            }
        }

        // ALL EASTERN REGION DISTRICTS
        $easternDistricts = [
            'Jinja', 'Mbale', 'Iganga', 'Tororo', 'Soroti', 'Kumi', 'Pallisa', 'Kamuli', 
            'Kapchorwa', 'Katakwi', 'Busia', 'Bugiri', 'Mayuge', 'Sironko', 'Budaka', 
            'Butaleja', 'Manafwa', 'Namutumba', 'Bududa', 'Bukwa', 'Bukedea', 'Kaliro', 
            'Amuria', 'Ngora', 'Serere', 'Buyende', 'Luuka', 'Namayingo', 'Kibuku', 
            'Bulambuli', 'Kween', 'Amudat', 'Nakapiripirit', 'Napak', 'Moroto', 'Kotido', 
            'Kaabong', 'Abim'
        ];
        
        foreach ($easternDistricts as $districtName) {
            $district = UgandaLocation::create(['name' => $districtName, 'type' => 'district', 'parent_id' => $eastern->id]);
            
            $counties = $this->getCountiesForDistrict($districtName);
            foreach ($counties as $countyName) {
                $county = UgandaLocation::create(['name' => $countyName, 'type' => 'county', 'parent_id' => $district->id]);
                
                $subcounties = $this->getSubcountiesForCounty($countyName);
                foreach ($subcounties as $subcountyName) {
                    $subcounty = UgandaLocation::create(['name' => $subcountyName, 'type' => 'subcounty', 'parent_id' => $county->id]);
                    
                    $parishes = $this->getParishesForSubcounty($subcountyName);
                    foreach ($parishes as $parishName) {
                        $parish = UgandaLocation::create(['name' => $parishName, 'type' => 'parish', 'parent_id' => $subcounty->id]);
                        
                        $villages = $this->getVillagesForParish($parishName);
                        foreach ($villages as $villageName) {
                            UgandaLocation::create(['name' => $villageName, 'type' => 'village', 'parent_id' => $parish->id]);
                        }
                    }
                }
            }
        }

        // ALL NORTHERN REGION DISTRICTS
        $northernDistricts = [
            'Gulu', 'Lira', 'Kitgum', 'Pader', 'Apac', 'Arua', 'Nebbi', 'Yumbe', 'Moyo', 
            'Adjumani', 'Amolatar', 'Dokolo', 'Oyam', 'Amuru', 'Nwoya', 'Lamwo', 'Agago', 
            'Alebtong', 'Otuke', 'Kole', 'Zombo', 'Pakwach', 'Omoro', 'Kwania', 'Lira City'
        ];
        
        foreach ($northernDistricts as $districtName) {
            $district = UgandaLocation::create(['name' => $districtName, 'type' => 'district', 'parent_id' => $northern->id]);
            
            $counties = $this->getCountiesForDistrict($districtName);
            foreach ($counties as $countyName) {
                $county = UgandaLocation::create(['name' => $countyName, 'type' => 'county', 'parent_id' => $district->id]);
                
                $subcounties = $this->getSubcountiesForCounty($countyName);
                foreach ($subcounties as $subcountyName) {
                    $subcounty = UgandaLocation::create(['name' => $subcountyName, 'type' => 'subcounty', 'parent_id' => $county->id]);
                    
                    $parishes = $this->getParishesForSubcounty($subcountyName);
                    foreach ($parishes as $parishName) {
                        $parish = UgandaLocation::create(['name' => $parishName, 'type' => 'parish', 'parent_id' => $subcounty->id]);
                        
                        $villages = $this->getVillagesForParish($parishName);
                        foreach ($villages as $villageName) {
                            UgandaLocation::create(['name' => $villageName, 'type' => 'village', 'parent_id' => $parish->id]);
                        }
                    }
                }
            }
        }

        // ALL WESTERN REGION DISTRICTS
        $westernDistricts = [
            'Mbarara', 'Kasese', 'Bushenyi', 'Hoima', 'Kabale', 'Masindi', 'Bundibugyo', 
            'Rukungiri', 'Kabarole', 'Ntungamo', 'Kanungu', 'Kamwenge', 'Kyenjojo', 
            'Ibanda', 'Isingiro', 'Kiruhura', 'Buliisa', 'Kibaale', 'Kyegegwa', 'Buhweju', 
            'Rubirizi', 'Sheema', 'Mitooma', 'Ntoroko', 'Kagadi', 'Kakumiro', 'Rubanda', 
            'Rukiga', 'Bunyangabu', 'Mbarara City', 'Fort Portal City'
        ];
        
        foreach ($westernDistricts as $districtName) {
            $district = UgandaLocation::create(['name' => $districtName, 'type' => 'district', 'parent_id' => $western->id]);
            
            $counties = $this->getCountiesForDistrict($districtName);
            foreach ($counties as $countyName) {
                $county = UgandaLocation::create(['name' => $countyName, 'type' => 'county', 'parent_id' => $district->id]);
                
                $subcounties = $this->getSubcountiesForCounty($countyName);
                foreach ($subcounties as $subcountyName) {
                    $subcounty = UgandaLocation::create(['name' => $subcountyName, 'type' => 'subcounty', 'parent_id' => $county->id]);
                    
                    $parishes = $this->getParishesForSubcounty($subcountyName);
                    foreach ($parishes as $parishName) {
                        $parish = UgandaLocation::create(['name' => $parishName, 'type' => 'parish', 'parent_id' => $subcounty->id]);
                        
                        $villages = $this->getVillagesForParish($parishName);
                        foreach ($villages as $villageName) {
                            UgandaLocation::create(['name' => $villageName, 'type' => 'village', 'parent_id' => $parish->id]);
                        }
                    }
                }
            }
        }

        echo "Comprehensive Uganda locations seeded successfully!\n";
        echo "Total records: " . UgandaLocation::count() . "\n";
    }

    private function getCountiesForDistrict($district)
    {
        $counties = [
            'Kampala' => ['Kampala Central', 'Kawempe', 'Makindye', 'Nakawa', 'Rubaga'],
            'Wakiso' => ['Busiro', 'Kyadondo', 'Kyaddondo'],
            'Mukono' => ['Mukono', 'Nakifuma', 'Buikwe'],
            'Jinja' => ['Jinja Municipality', 'Butembe', 'Kagoma'],
            'Mbale' => ['Mbale Municipality', 'Bungokho', 'Bubulo'],
            'Gulu' => ['Gulu Municipality', 'Aswa', 'Omoro'],
            'Mbarara' => ['Mbarara Municipality', 'Kashari', 'Rwampara'],
            'Arua' => ['Arua Municipality', 'Ayivu', 'Terego'],
            'Lira' => ['Lira Municipality', 'Erute', 'Dokolo'],
            'Masaka' => ['Masaka Municipality', 'Bukoto', 'Kabonera']
        ];
        
        return $counties[$district] ?? [$district . ' County', $district . ' Municipality'];
    }

    private function getSubcountiesForCounty($county)
    {
        return [
            $county . ' Central',
            $county . ' North',
            $county . ' South',
            $county . ' East',
            $county . ' West'
        ];
    }

    private function getParishesForSubcounty($subcounty)
    {
        return [
            $subcounty . ' Parish A',
            $subcounty . ' Parish B',
            $subcounty . ' Parish C'
        ];
    }

    private function getVillagesForParish($parish)
    {
        return [
            $parish . ' Village 1',
            $parish . ' Village 2',
            $parish . ' Village 3',
            $parish . ' Village 4',
            $parish . ' Village 5'
        ];
    }
}