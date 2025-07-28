<?php

namespace App\Providers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Device;
use App\Models\Farmer;
use App\Models\FarmerTransaction;
use App\Models\Fertilization;
use App\Models\Garden;
use App\Models\HydroponicDevice;
use App\Models\HydroponicUser;
use App\Models\Land;
use App\Models\LiteDevice;
use App\Models\LiteUser;
use App\Models\Product;
use App\Models\Telemetri;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('update-garden', function (User $user, Garden $garden) {
            return $user->role != 'farmer' || $user->role == 'farmer' && $user->farmer->id === $garden->land->farmer_id;
        });

        Gate::define('store-fertilization', function (User $user, Garden $garden) {
            return $user->role != 'farmer' || $user->role == 'farmer' && $user->farmer->id === $garden->land->farmer_id;
        });

        Gate::define('delete-fertilization', function (User $user, Fertilization $fertilization) {
            return $user->role != 'farmer' || $user->role == 'farmer' && $user->farmer->id === $fertilization->farmer_id;
        });

        Gate::define('update-device', function (User $user, Device $device) {
            return $user->role != 'farmer' || $user->role == 'farmer' && $user->farmer->id === $device->farmer_id;
        });

        Gate::define('show-telemetri', function (User $user, Telemetri $telemetri) {
            return $user->role != 'farmer' || $user->role == 'farmer' && $user->farmer->id === $telemetri->farmer_id;
        });

        Gate::define('farmers', function (User $user) {
            return $user->role == 'admin'
                        ? Response::allow()
                        : Response::deny('You must be an administrator.');
        });

        Gate::define('user-products', function(User $user, Product $product){
            return $user->role == 'farmer' && $user->farmer->shop && $user->farmer->shop->id == $product->shop_id;
        });

        Gate::define('user-address', function(User $user, Address $address){
            return $user->id == $address->user_id;
        });

        Gate::define('user-cart', function(User $user, Cart $cart){
            return $user->id == $cart->user_id;
        });

        Gate::define('land', function(User $user, Land $land){
            return $user->role == 'admin' || $user->role == 'farmer' && $user->farmer->id == $land->farmer_id;
        });

        Gate::define('transaction-show', function(User $user, FarmerTransaction $farmerTransaction){
            return $user->role == 'admin' || $user->role == 'farmer' && $farmerTransaction->user_id != null && $user->id == $farmerTransaction->user_id;
        });

        Gate::define('lite-device-mobile', function(LiteUser $liteUser, LiteDevice $liteDevice){
            return $liteUser->id == $liteDevice->lite_user_id;
        });

        Gate::define('user-hydroponic-device', function(HydroponicUser $hydroponicUser, HydroponicDevice $hydroponicDevice){
            return $hydroponicUser->id == $hydroponicDevice->user_id;
        });
    }
}
