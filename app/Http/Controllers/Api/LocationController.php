<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UgandaLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LocationController extends Controller
{
    public function getRegions()
    {
        return Cache::remember('regions', 3600, function () {
            return UgandaLocation::where('type', 'region')
                ->select('name')
                ->orderBy('name')
                ->pluck('name');
        });
    }

    public function getDistricts($region)
    {
        return Cache::remember("districts_{$region}", 3600, function () use ($region) {
            $regionRecord = UgandaLocation::where('type', 'region')
                ->where('name', $region)
                ->select('id')
                ->first();
                
            if (!$regionRecord) {
                return response()->json([]);
            }
            
            return UgandaLocation::where('type', 'district')
                ->where('parent_id', $regionRecord->id)
                ->select('name')
                ->orderBy('name')
                ->pluck('name');
        });
    }

    public function getCounties($district)
    {
        return Cache::remember("counties_{$district}", 3600, function () use ($district) {
            $districtRecord = UgandaLocation::where('type', 'district')
                ->where('name', $district)
                ->select('id')
                ->first();
                
            if (!$districtRecord) {
                return response()->json([]);
            }
            
            return UgandaLocation::where('type', 'county')
                ->where('parent_id', $districtRecord->id)
                ->select('name')
                ->orderBy('name')
                ->pluck('name');
        });
    }

    public function getSubcounties(Request $request)
    {
        $county = $request->county;
        return Cache::remember("subcounties_{$county}", 3600, function () use ($county) {
            $countyRecord = UgandaLocation::where('type', 'county')
                ->where('name', $county)
                ->select('id')
                ->first();
                
            if (!$countyRecord) {
                return response()->json([]);
            }
            
            return UgandaLocation::where('type', 'subcounty')
                ->where('parent_id', $countyRecord->id)
                ->select('name')
                ->orderBy('name')
                ->pluck('name');
        });
    }

    public function getParishes(Request $request)
    {
        $subcounty = $request->subcounty;
        return Cache::remember("parishes_{$subcounty}", 3600, function () use ($subcounty) {
            $subcountyRecord = UgandaLocation::where('type', 'subcounty')
                ->where('name', $subcounty)
                ->select('id')
                ->first();
                
            if (!$subcountyRecord) {
                return response()->json([]);
            }
            
            return UgandaLocation::where('type', 'parish')
                ->where('parent_id', $subcountyRecord->id)
                ->select('name')
                ->orderBy('name')
                ->pluck('name');
        });
    }

    public function getVillages(Request $request)
    {
        $parish = $request->parish;
        return Cache::remember("villages_{$parish}", 3600, function () use ($parish) {
            $parishRecord = UgandaLocation::where('type', 'parish')
                ->where('name', $parish)
                ->select('id')
                ->first();
                
            if (!$parishRecord) {
                return response()->json([]);
            }
            
            return UgandaLocation::where('type', 'village')
                ->where('parent_id', $parishRecord->id)
                ->select('name')
                ->orderBy('name')
                ->pluck('name');
        });
    }
}
