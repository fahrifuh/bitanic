<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActiveGarden;
use App\Models\City;
use App\Models\Crop;
use App\Models\Device;
use App\Models\District;
use App\Models\Farmer;
use App\Models\FarmerGroup;
use App\Models\Garden;
use App\Models\Land;
use App\Models\LiteDevice;
use App\Models\Pest;
use App\Models\Province;
use App\Models\RscGarden;
use App\Models\RscGardenTelemetry;
use App\Models\Subdistrict;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GeneralController extends Controller
{
    public function getDevice(Request $request, $id = null)
    {
        if ($id == null) {
            $data = Device::query()
                ->whereNull('garden_id')
                ->where('farmer_id', $request->user()->farmer->id)
                ->get();
        } else {
            $data = Device::query()
                ->where('farmer_id', $request->user()->farmer->id)
                ->where(function($q)use($id){
                    $q->where('garden_id', $id)
                        ->orWhereNull('garden_id');
                })
                ->get();
        }

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function countPlantingActivity($activity)
    {
        $farmer_id = optional(auth()->user()->farmer)->id ?? null;


        $gardens = Garden::query();

        switch ($activity) {
            case 'planting':
                $gardens = $gardens->planting();
                break;
            case 'maintenance_period':
                $gardens = $gardens->maintenancePeriod();
                break;
            case 'harvest_period':
                $gardens = $gardens->harvestPeriod();
                break;
        }

        if (auth()->user()->role == 'farmer') {
            return $gardens->whereHas('land', function($l)use($farmer_id){$l->where('farmer_id', $farmer_id);})->count();
        }

        if (auth()->user()->role == 'admin' && auth()->user()->city_id != null) {
            $gardens = $gardens->whereHas('land.farmer.user.subdistrict.district', function($query){
                $query->where('city_id', auth()->user()->city_id);
            });
        }

        $gardens = $gardens->count();

        return $gardens;
    }

    public function getAktivitasMenanamDashboard()
    {
        $data['count_planting'] = $this->countPlantingActivity('planting');
        $data['count_maintenance_period'] = $this->countPlantingActivity('maintenance_period');
        $data['count_harvest_period'] = $this->countPlantingActivity('harvest_period');

        return response()->json($data, 200);
    }

    public function countGardens()
    {
        $farmer_id = optional(auth()->user()->farmer)->id ?? null;

        if (auth()->user()->role == 'farmer') return Garden::whereHas('land', function($l)use($farmer_id){$l->where('farmer_id', $farmer_id);})->count();

        $gardens = Garden::query();

        if (auth()->user()->role == 'admin' && auth()->user()->city_id != null) {
            $gardens = $gardens->whereHas('farmer.user.subdistrict.district', function($query){
                $query->where('city_id', auth()->user()->city_id);
            });
        }

        return $gardens->count();
    }

    public function countDevices()
    {
        $farmer_id = optional(auth()->user()->farmer)->id ?? null;

        $count_pro_devices = Device::query()
            ->when(auth()->user()->role == 'farmer', function($query, $role)use($farmer_id){
                return $query->where('farmer_id', $farmer_id);
            })
            ->when((auth()->user()->role == 'admin' && auth()->user()->city_id != null), function($query, $status){
                return $query->whereHas('farmer.user.subdistrict.district', function($query){
                    $query->where('city_id', auth()->user()->city_id);
                });
            })
            ->count();

        $count_lite_devices = LiteDevice::query()
            ->count();

        return [$count_pro_devices, $count_lite_devices];
    }

    public function countVegies()
    {
        $farmer_id = optional(auth()->user()->farmer)->id ?? null;

        if (auth()->user()->role == 'farmer') return Crop::whereHas('garden.land', function($g)use($farmer_id){$g->where('farmer_id', $farmer_id);})->sayur()->count();

        $crops = Crop::query();

        if (auth()->user()->role == 'admin' && auth()->user()->city_id != null) {
            $crops = $crops->whereHas('garden.land.farmer.user.subdistrict.district', function($query){
                $query->where('city_id', auth()->user()->city_id);
            });
        }

        return $crops->sayur()->count();
    }

    public function countFruits()
    {
        $farmer_id = optional(auth()->user()->farmer)->id ?? null;

        if (auth()->user()->role == 'farmer') return Crop::whereHas('garden.land', function($g)use($farmer_id){$g->where('farmer_id', $farmer_id);})->buah()->count();

        $crops = Crop::query();

        if (auth()->user()->role == 'admin' && auth()->user()->city_id != null) {
            $crops = $crops->whereHas('garden.land.farmer.user.subdistrict.district', function($query){
                $query->where('city_id', auth()->user()->city_id);
            });
        }

        return $crops->buah()->count();
    }

    public function countFertilizer(?int $year = null)
    {
        $farmer_id = optional(auth()->user()->farmer)->id ?? null;

        $rsc_gardens = RscGarden::query()
            ->selectRaw('MAX(id) as id') // Get the last created_at within each hour
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month_group') // Extract hour from timestamp considering the interval
            ->when($year, function($query, $year){
                return $query->whereYear('created_at', $year);
            })
            ->when(auth()->user()->role != 'admin', function($query, $bool){
                return $query->whereHas('garden.land.farmer', function($query){
                    $query->where('user_id', auth()->id());
                });
            })
            ->groupBy('month_group')
            ->orderBy('month_group')
            ->pluck('id');

        $rsc_garden_telemetries = RscGardenTelemetry::query()
            ->selectRaw("rsc_garden_id, AVG(JSON_EXTRACT(samples, '$.n')) as avg_n, AVG(JSON_EXTRACT(samples, '$.p')) as avg_p, AVG(JSON_EXTRACT(samples, '$.k')) as avg_k")
            ->whereIn('rsc_garden_id', $rsc_gardens)
            ->groupBy('rsc_garden_id')
            ->get()
            ->reduce(function ($carry, $item, $key) {
                return (object) [
                    'sum_n' => $carry->sum_n + $item->avg_n,
                    'sum_p' => $carry->sum_p + $item->avg_p,
                    'sum_k' => $carry->sum_k + $item->avg_k,
                ];
            }, (object) [
                'sum_n' => 0,
                'sum_p' => 0,
                'sum_k' => 0,
            ]);

        return $rsc_garden_telemetries;
    }

    public function getFertilizer(Request $request) : JsonResponse
    {
        $year = $request->query('year', now()->year);
        return response()->json([
            'fertilization' => $this->countFertilizer($year)
        ]);
    }

    public function getDashboardData()
    {
        $farmer_id = optional(auth()->user()->farmer)->id ?? null;
        $farmers = Farmer::query();

        if (auth()->user()->role == 'admin' && auth()->user()->city_id != null) {
            $farmers = $farmers->whereHas('user.subdistrict.district', function($query){
                $query->where('city_id', auth()->user()->city_id);
            });
        }

        $data['count_farmers'] = (auth()->user()->role == 'farmer') ? 0 : $farmers->count();
        $data['count_gardens'] = $this->countGardens();
        $data['count_devices'] = $this->countDevices()[0];
        $data['count_lite_devices'] = $this->countDevices()[1];
        $data['count_vegies'] = $this->countVegies();
        $data['count_fruits'] = $this->countFruits();
        $data['count_fertilizer_n'] = 0;
        $data['count_fertilizer_p'] = 0;
        $data['count_fertilizer_k'] = 0;
        $data['count_fertilizer'] = $this->countFertilizer();

        return response()->json($data, 200);
    }

    public function getProvinces()
    {
        $provinces = Province::query();

        if (auth()->user()->role == 'admin' && auth()->user()->city_id != null) {
            $provinces = $provinces->whereHas('city', function($query){
                $query->where('id', auth()->user()->city_id);
            });
        }

        return response()->json([
            'provinces' => $provinces->get(['id', 'prov_name'])
        ], 200);
    }

    public function getCities($province = null)
    {
        $cities = City::query();

        if (is_numeric($province)) {
            $cities = $cities->where('province_id', $province);
        }

        if (auth()->user()->role == 'admin' && auth()->user()->city_id != null) {
            $cities = $cities->where('id', auth()->user()->city_id);
        }

        return response()->json([
            'cities' => $cities->get(['city_name', 'id'])
        ], 200);
    }

    public function getDistricts($city = null)
    {
        $districts = District::query();

        if (is_numeric($city)) {
            $districts = $districts->where('city_id', $city);
        }

        if (auth()->user()->role == 'admin' && auth()->user()->city_id != null) {
            $districts = $districts->whereHas('city', function($query){
                $query->where('id', auth()->user()->city_id);
            });
        }

        return response()->json([
            'districts' => $districts->get(['dis_name', 'id'])
        ], 200);
    }

    public function getSubdistricts($district = null)
    {
        $subdistricts = Subdistrict::query();

        if (is_numeric($district)) {
            $subdistricts = $subdistricts->where('dis_id', $district);
        }

        if (auth()->user()->role == 'admin' && auth()->user()->city_id != null) {
            $subdistricts = $subdistricts->whereHas('district.city', function($query){
                $query->where('id', auth()->user()->city_id);
            });
        }

        return response()->json([
            'subdistricts' => $subdistricts->get(['subdis_name', 'id'])
        ], 200);
    }

    public function getFarmerGroups($subdistrict = null)
    {
        $groups = FarmerGroup::query();

        if ($subdistrict) {
            $groups = $groups->where('subdis_id', $subdistrict);
        }

        return response()->json([
            'groups' => $groups->get(['id', 'name'])
        ], 200);
    }

    public function getFarmersFromGroup($group)
    {
        $farmers = User::query()
            ->has('farmer')
            ->with(['farmer' => function($query){
                $query->select(['id', 'user_id', 'nik']);
            }])
            ->whereHas('farmer.group', function($query)use($group){
                $query->where('group_id', $group);
            })
            ->get(['id', 'name', 'phone_number']);

        return response()->json([
            'farmers' => $farmers
        ], 200);
    }

    public function getFarmersFromSubdis($subdis)
    {
        $farmers = User::query();

        if (auth()->user()->role == 'admin' && auth()->user()->city_id != null) {
            $farmers = $farmers->whereHas('subdistrict.district', function($query){
                $query->where('city_id', auth()->user()->city_id);
            });
        }

        $farmers = $farmers->has('farmer')
            ->with(['farmer' => function($query){
                $query->select(['id', 'user_id', 'nik']);
            }])
            ->whereHas('subdistrict', function($query)use($subdis){
                $query->where('subdis_id', $subdis);
            })
            ->doesntHave('farmer.group')
            ->get(['id', 'name', 'phone_number']);

        return response()->json([
            'farmers' => $farmers
        ], 200);
    }

    public function getCountFertilizerByProvince()
    {
        $provinces = Province::query()->get(['id', 'prov_name']);

        $provinces_id = $provinces->pluck('id');
        $gardens = Garden::query()
            ->with([
                'land:id,farmer_id',
                'land.farmer:id,group_id',
                'land.farmer.group:id,subdis_id',
                'land.farmer.group.subdistrict:id,dis_id',
                'land.farmer.group.subdistrict.district:id,city_id',
                'land.farmer.group.subdistrict.district.city:id,province_id'
            ])
            ->whereHas('land.farmer', function($query){
                $query->whereNotNull('group_id');
            })
            ->whereHas('land.farmer.group.subdistrict.district.city', function($query)use($provinces_id){
                $query->whereIn('province_id', $provinces_id);
            })
            ->whereNotNull('nitrogen')
            ->whereNotNull('phosphor')
            ->whereNotNull('kalium')
            ->get(['id', 'land_id', 'nitrogen', 'phosphor', 'kalium']);

        $data = [];

        foreach ($provinces as $value) {
            $count_fertilizer_n = 0;
            $count_fertilizer_p = 0;
            $count_fertilizer_k = 0;
            foreach ($gardens as $garden) {
                if ($garden->land->farmer->group->subdistrict->district->city->province_id == $value->id) {
                    $count_fertilizer_n += $garden->nitrogen;
                    $count_fertilizer_p += $garden->phosphor;
                    $count_fertilizer_k += $garden->kalium;
                }
            }
            $data[$value->id] = [
                'count_fertilizer_n' => $count_fertilizer_n,
                'count_fertilizer_p' => $count_fertilizer_p,
                'count_fertilizer_k' => $count_fertilizer_k
            ];
        }

        return response()->json([
            'provinces' => $data
        ], 200);
    }

    public function getCountFertilizerByCity($province)
    {
        $cities = City::query()->where('province_id', $province)->get(['id', 'city_name']);

        $cities_id = $cities->pluck('id');
        $gardens = Garden::query()
            ->with([
                'land:id,farmer_id',
                'land.farmer:id,group_id',
                'land.farmer.group:id,subdis_id',
                'land.farmer.group.subdistrict:id,dis_id',
                'land.farmer.group.subdistrict.district:id,city_id',
            ])
            ->whereHas('land.farmer', function($query){
                $query->whereNotNull('group_id');
            })
            ->whereHas('land.farmer.group.subdistrict.district', function($query)use($cities_id){
                $query->whereIn('city_id', $cities_id);
            })
            ->whereNotNull('nitrogen')
            ->whereNotNull('phosphor')
            ->whereNotNull('kalium')
            ->get(['id', 'land_id', 'nitrogen', 'phosphor', 'kalium']);

        $data = [];

        foreach ($cities as $value) {
            $count_fertilizer_n = 0;
            $count_fertilizer_p = 0;
            $count_fertilizer_k = 0;
            foreach ($gardens as $garden) {
                if ($garden->land->farmer->group->subdistrict->district->city_id == $value->id) {
                    $count_fertilizer_n += $garden->nitrogen;
                    $count_fertilizer_p += $garden->phosphor;
                    $count_fertilizer_k += $garden->kalium;
                }
            }
            $data[$value->id] = [
                'count_fertilizer_n' => $count_fertilizer_n,
                'count_fertilizer_p' => $count_fertilizer_p,
                'count_fertilizer_k' => $count_fertilizer_k
            ];
        }

        return response()->json([
            'cities' => $data
        ], 200);
    }

    public function getCountFertilizerByDistrict($city)
    {
        $districts = District::query()->where('city_id', $city)->get(['id']);

        $districts_id = $districts->pluck('id');
        $gardens = Garden::query()
            ->with([
                'land:id,farmer_id',
                'land.farmer:id,group_id',
                'land.farmer.group:id,subdis_id',
                'land.farmer.group.subdistrict:id,dis_id'
            ])
            ->whereHas('land.farmer', function($query){
                $query->whereNotNull('group_id');
            })
            ->whereHas('land.farmer.group.subdistrict', function($query)use($districts_id){
                $query->whereIn('dis_id', $districts_id);
            })
            ->whereNotNull('nitrogen')
            ->whereNotNull('phosphor')
            ->whereNotNull('kalium')
            ->get(['id', 'land_id', 'nitrogen', 'phosphor', 'kalium']);

        $data = [];

        foreach ($districts as $value) {
            $count_fertilizer_n = 0;
            $count_fertilizer_p = 0;
            $count_fertilizer_k = 0;
            foreach ($gardens as $garden) {
                if ($garden->land->farmer->group->subdistrict->dis_id == $value->id) {
                    $count_fertilizer_n += $garden->nitrogen;
                    $count_fertilizer_p += $garden->phosphor;
                    $count_fertilizer_k += $garden->kalium;
                }
            }
            $data[$value->id] = [
                'count_fertilizer_n' => $count_fertilizer_n,
                'count_fertilizer_p' => $count_fertilizer_p,
                'count_fertilizer_k' => $count_fertilizer_k
            ];
        }

        return response()->json([
            'districts' => $data
        ], 200);
    }

    public function getCountFertilizerBySubdistrict($district)
    {
        $subdistricts = Subdistrict::query()->where('dis_id', $district)->get(['id']);

        $subdistricts_id = $subdistricts->pluck('id');
        $gardens = Garden::query()
            ->with([
                'land:id,farmer_id',
                'land.farmer:id,group_id',
                'land.farmer.group:id,subdis_id'
            ])
            ->whereHas('land.farmer', function($query){
                $query->whereNotNull('group_id');
            })
            ->whereHas('land.farmer.group', function($query)use($subdistricts_id){
                $query->whereIn('subdis_id', $subdistricts_id);
            })
            ->whereNotNull('nitrogen')
            ->whereNotNull('phosphor')
            ->whereNotNull('kalium')
            ->get(['id', 'land_id', 'nitrogen', 'phosphor', 'kalium']);

        $data = [];

        foreach ($subdistricts as $value) {
            $count_fertilizer_n = 0;
            $count_fertilizer_p = 0;
            $count_fertilizer_k = 0;
            foreach ($gardens as $garden) {
                if ($garden->land->farmer->group->subdis_id == $value->id) {
                    $count_fertilizer_n += $garden->nitrogen;
                    $count_fertilizer_p += $garden->phosphor;
                    $count_fertilizer_k += $garden->kalium;
                }
            }
            $data[$value->id] = [
                'count_fertilizer_n' => $count_fertilizer_n,
                'count_fertilizer_p' => $count_fertilizer_p,
                'count_fertilizer_k' => $count_fertilizer_k
            ];
        }

        return response()->json([
            'subdistricts' => $data
        ], 200);
    }

    public function getCountFertilizerByGroup($subdistrict)
    {
        $groups = FarmerGroup::query()->where('subdis_id', $subdistrict)->get(['id']);

        $groups_id = $groups->pluck('id');
        $gardens = Garden::query()
            ->with([
                'land:id,farmer_id',
                'land.farmer:id,group_id'
            ])
            ->whereHas('land.farmer', function($query){
                $query->whereNotNull('group_id');
            })
            ->whereHas('land.farmer', function($query)use($groups_id){
                $query->whereIn('group_id', $groups_id);
            })
            ->whereNotNull('nitrogen')
            ->whereNotNull('phosphor')
            ->whereNotNull('kalium')
            ->get(['id', 'land_id', 'nitrogen', 'phosphor', 'kalium']);

        $data = [];

        foreach ($groups as $value) {
            $count_fertilizer_n = 0;
            $count_fertilizer_p = 0;
            $count_fertilizer_k = 0;
            foreach ($gardens as $garden) {
                if ($garden->land->farmer->group_id == $value->id) {
                    $count_fertilizer_n += $garden->nitrogen;
                    $count_fertilizer_p += $garden->phosphor;
                    $count_fertilizer_k += $garden->kalium;
                }
            }
            $data[$value->id] = [
                'count_fertilizer_n' => $count_fertilizer_n,
                'count_fertilizer_p' => $count_fertilizer_p,
                'count_fertilizer_k' => $count_fertilizer_k
            ];
        }

        return response()->json([
            'groups' => $data
        ], 200);
    }

    public function getCountFertilizerByFarmer($group)
    {
        $farmers = Farmer::query()->whereNotNull('group_id')->where('group_id', $group)->get(['id']);

        $farmers_id = $farmers->pluck('id');
        $gardens = Garden::query()
            ->with([
                'land:id,farmer_id'
            ])
            ->whereHas('land.farmer', function($query)use($farmers_id){
                $query->whereNotNull('group_id')->whereIn('group_id', $farmers_id);
            })
            ->whereNotNull('nitrogen')
            ->whereNotNull('phosphor')
            ->whereNotNull('kalium')
            ->get(['id', 'land_id', 'nitrogen', 'phosphor', 'kalium']);

        $data = [];

        foreach ($farmers as $value) {
            $count_fertilizer_n = 0;
            $count_fertilizer_p = 0;
            $count_fertilizer_k = 0;
            foreach ($gardens as $garden) {
                if ($garden->land->farmer_id == $value->id) {
                    $count_fertilizer_n += $garden->nitrogen;
                    $count_fertilizer_p += $garden->phosphor;
                    $count_fertilizer_k += $garden->kalium;
                }
            }
            $data[$value->id] = [
                'count_fertilizer_n' => $count_fertilizer_n,
                'count_fertilizer_p' => $count_fertilizer_p,
                'count_fertilizer_k' => $count_fertilizer_k
            ];
        }

        return response()->json([
            'farmers' => $data
        ], 200);
    }

    public function getGardens(Request $request)
    {
        $gardens = Land::query()
            ->with(['use_garden:id,land_id']);

        if (auth()->user()->role == 'farmer') {
            $gardens = $gardens->where('farmer_id', auth()->user()->farmer->id);
        }

        if (auth()->user()->role == 'admin' && auth()->user()->city_id != null) {
            $gardens = $gardens->whereHas('farmer.user.subdistrict.district', function($query){
                $query->where('city_id', auth()->user()->city_id);
            });
        }

        if ($request->query('search')) {
            $search = '%'.$request->query('search').'%';

            $gardens = $gardens->where(function($query)use($search){
                $query->where('name', 'LIKE', $search)
                    ->orWhereHas('farmer.group', function($group)use($search){
                        $group->where('name', 'LIKE', $search);
                    });
            });
        }

        $gardens = $gardens->get(['id', 'latitude', 'longitude', 'name']);

        return response()->json([
            'gardens' => $gardens,
            'query' => $request->query('search'),
            'message' => 'Data kebun'
        ], 200);
    }

    public function showGarden($id)
    {
        $land = Land::query()
            ->with([
                'farmer:id,user_id,full_name,picture',
                'gardens:id,land_id,name,polygon,color,area,category,gardes_type,harvest_status',
                'gardens.currentCommodity:id,crop_id,garden_id',
                'gardens.currentCommodity.crop:id,crop_name',
            ])
            ->find($id);

        if (auth()->user()->role == 'farmer' && $land->farmer_id != auth()->user()->farmer->id) {
            return response()->json([
                'messages' => [
                    'errors' => ["Kebun bukan milik anda!"]
                ]
            ], 403);
        }

        return response()->json([
            'land' => $land,
            'message' => "Data lahan ".$land->name
        ], 200);
    }

    public function countPlanted()
    {
        $crops = Crop::query();

        if (auth()->user()->role == 'admin' && auth()->user()->city_id != null) {
            $crops = $crops->whereHas('garden.farmer.user.subdistrict.district', function($query){
                $query->where('city_id', auth()->user()->city_id);
            });
        }

        $crops = $crops->withCount(['garden as count_planted' => function($query){
                $query->where('harvest_status', '<>', 0);
            }])
            ->whereHas('garden', function($query){
                $query->where('harvest_status', '<>', 0);
            })
            ->get(['id', 'crop_name']);

        return response()->json([
            'crops' => $crops,
            'message' => "Data tanaman dengan jumlah ditanam"
        ], 200);
    }

    public function indexPlantedCrops()
    {
        $crops = Crop::query()
            ->select(['id', 'crop_name'])
            ->withCount(['garden as count_planted' => function($query){
                $query->where('harvest_status', '<>', 0);
            }])
            ->orderBy('count_planted', 'desc')
            ->get();
            // ->paginate(5);

        return response()->json([
            'crops' => $crops,
            'message' => "Data tanaman dengan jumlah ditanam"
        ], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function topTenForFarmers()
    {
        $active_gardens = ActiveGarden::query()
            ->with([
                'garden:id,land_id',
                'garden.land:id,farmer_id',
            ])
            ->whereHas('garden', function($query){
                $query->where('harvest_status', '<>', 0);
            })
            ->get(['id', 'garden_id']);

        $farmers = Farmer::query();

        if (auth()->user()->role == 'admin' && auth()->user()->city_id != null) {
            $farmers = $farmers->whereHas('user.subdistrict.district', function($query){
                $query->where('city_id', auth()->user()->city_id);
            });
        }

        $farmers = $farmers->select(['id', 'full_name', 'picture'])
            ->whereIn('id', $active_gardens->pluck('garden.land.farmer_id')->all())
            ->get();


        foreach ($farmers as $farmer) {
            $check = $active_gardens->filter(function($value, $key)use($farmer){
                return $value->garden->farmer_id === $farmer->id;
            });

            $farmer['count_activity'] = 0;
            if ($check->all()) {
                $farmer['count_activity'] = $check->count();
            }
        }

        $sorted = $farmers->sortBy('count_activity');

        return response()->json([
            'farmers' => $farmers->values()->all()
        ], 200);
    }

    public function activeGardensMonths()
    {
        $activeGardens = ActiveGarden::query();

        if (auth()->user()->role == 'admin' && auth()->user()->city_id != null) {
            $activeGardens = $activeGardens->whereHas('garden.land.farmer.user.subdistrict.district', function($query){
                $query->where('city_id', auth()->user()->city_id);
            });
        }
        $activeGardens = $activeGardens->where(function($query){
                $query->whereYear('active_date', today('Asia/Jakarta')->format('Y'));
            })
            ->orWhere(function($query){
                $query->whereYear('finished_date', today('Asia/Jakarta')->format('Y'));
            })
            ->get();

        $months = [];

        foreach (get_month() as $key =>  $month) {
            $index = $key + 1;
            $count = 0;
            foreach ($activeGardens as $active) {
                $active_date = now('Asia/Jakarta')->parse($active->active_date)->format('m');
                $finished_date = $active->finished_date ? now('Asia/Jakarta')->parse($active->finished_date)->format('m') : now('Asia/Jakarta')->format('m');
                if ($active_date == $index || $finished_date == $index) {
                    $count++;
                }
            }
            array_push($months, (object) [
                'x' => $month,
                'y' => $count
            ]);
        }

        return response()->json([
            'months' => $months
        ], 200);
    }

    public function getPests(Request $request)
    {
        $setPicture = $request->query('picture');
        $selects = ['id', 'name', 'pest_type'];

        if (is_bool($setPicture) && $setPicture == true) {
            $selects = array_merge($selects, ['picture']);
        }

        $pests = Pest::get($selects);

        return response()->json([
            'pests' => $pests,
            'message' => 'data hama'
        ], 200);
    }
}
