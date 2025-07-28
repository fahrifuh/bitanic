<?php

use App\Http\Controllers\Api\MidtransController;
use App\Http\Controllers\Api\Mobile\AddressController;
use App\Http\Controllers\Api\Mobile\ArticleController;
use App\Http\Controllers\Api\Mobile\AuthController;
use App\Http\Controllers\Api\Mobile\BalanceWithdrawController;
use App\Http\Controllers\Api\Mobile\BankController;
use App\Http\Controllers\Api\Mobile\BitanicProductController;
use App\Http\Controllers\Api\Mobile\CartController;
use App\Http\Controllers\Api\Mobile\CommodityController;
use App\Http\Controllers\Api\Mobile\CropController;
use App\Http\Controllers\Api\Mobile\CropForSaleController;
use App\Http\Controllers\Api\Mobile\DeviceController;
use App\Http\Controllers\Api\Mobile\FeddbackController;
use App\Http\Controllers\Api\Mobile\FertilizationController;
use App\Http\Controllers\Api\Mobile\GardenController;
use App\Http\Controllers\Api\Mobile\HarvestProduceController;
use App\Http\Controllers\Api\Mobile\HomeController;
use App\Http\Controllers\Api\Mobile\Hydroponic\AuthController as HydroponicAuthController;
use App\Http\Controllers\Api\Mobile\Hydroponic\DeviceController as HydroponicDeviceController;
use App\Http\Controllers\Api\Mobile\Hydroponic\UserController as HydroponicUserController;
use App\Http\Controllers\Api\Mobile\InvectedGardenController;
use App\Http\Controllers\Api\Mobile\LandController;
use App\Http\Controllers\Api\Mobile\Lite\AuthController as LiteAuthController;
use App\Http\Controllers\Api\Mobile\Lite\DeviceController as LiteDeviceController;
use App\Http\Controllers\Api\Mobile\Lite\FeedbackController;
use App\Http\Controllers\Api\Mobile\Lite\PumpController;
use App\Http\Controllers\Api\Mobile\Lite\ScheduleController;
use App\Http\Controllers\Api\Mobile\Lite\UserController as LiteUserController;
use App\Http\Controllers\Api\Mobile\MarketplaceController;
use App\Http\Controllers\Api\Mobile\MemberController;
use App\Http\Controllers\Api\Mobile\PestController;
use App\Http\Controllers\Api\Mobile\PoinController;
use App\Http\Controllers\Api\Mobile\ProductController;
use App\Http\Controllers\Api\Mobile\RcsController;
use App\Http\Controllers\Api\Mobile\RscGardenController;
use App\Http\Controllers\Api\Mobile\ShopController;
use App\Http\Controllers\Api\Mobile\StickTelemetriController;
use App\Http\Controllers\Api\Mobile\SubscriptionController;
use App\Http\Controllers\Api\Mobile\TransactionController;
use App\Http\Controllers\Api\Mobile\UserController;
use App\Http\Controllers\Api\Mobile\v2\DeviceController as V2DeviceController;
use App\Http\Controllers\Api\Mobile\WithdrawalBankController;
use App\Http\Controllers\Bitanic\UpdateFirmwareController;
use Faker\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sactrum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/midtrans/c76685f5-68c8-4604-b66b-50789fcbe6e1', [MidtransController::class, 'paymentRedirect']);

Route::prefix('mobile/lite')->group(function(){
    Route::post('/login', [LiteAuthController::class, 'login']);
    Route::post('/register', [LiteAuthController::class, 'register']);
    Route::post('/check-phone-number', [LiteAuthController::class, 'checkPhoneNumber']);

    Route::middleware('auth:sanctum')->group(function(){
        Route::post('/logout', [LiteAuthController::class, 'logout']);

        Route::get('profile', [LiteUserController::class, 'show']);
        Route::put('profile/update', [LiteUserController::class, 'update']);
        Route::put('profile/update-picture', [LiteUserController::class, 'updatePicture']);
        Route::put('profile/update-password', [LiteUserController::class, 'updatePassword']);

        Route::post('device-activation', [LiteDeviceController::class, 'deviceActivation']);
        Route::get('lite-device/activated', [LiteDeviceController::class, 'activatedDevice']);
        Route::get('lite-device/{lite_device}', [LiteDeviceController::class, 'show']);
        Route::post('lite-device/{lite_device}/setting-device', [LiteDeviceController::class, 'settingDevice']);
        Route::post('lite-device/{lite_device}/manual-device', [LiteDeviceController::class, 'manualDevice']);
        Route::put('lite-device/{lite_device}/update-mode', [LiteDeviceController::class, 'updateDeviceMode']);

        Route::put('lite-device/{lite_device}/pump/{lite_device_pump}/update-name', [PumpController::class, 'updateName']);

        Route::post('lite-device/{lite_device}/schedule', [ScheduleController::class, 'store']);
        Route::delete('lite-device/{lite_device}/schedule/{lite_device_schedule}', [ScheduleController::class, 'destroy']);

        Route::get('/master-tanaman', [CropController::class, 'masterTanaman']);

        Route::post('feedback', [FeedbackController::class, 'store']);
    });
});

Route::prefix('mobile/hydroponic')->group(function(){
    Route::post('login', [HydroponicAuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function(){
        Route::post('logout', [HydroponicAuthController::class, 'logout']);

        Route::get('user/detail', [HydroponicUserController::class, 'show']);
        Route::put('user/update/profile', [HydroponicUserController::class, 'updateProfile']);
        Route::put('user/update/picture', [HydroponicUserController::class, 'updatePicture']);
        Route::put('user/update/password', [HydroponicUserController::class, 'updatePassword']);

        Route::put('device/activation', [HydroponicDeviceController::class, 'activateDevice']);
        Route::apiResource('device', HydroponicDeviceController::class)
            ->parameters(['device' => 'hydroponicDevice'])
            ->only(['index', 'show']);
        Route::prefix('device/{hydroponicDevice}')->group(function(){
            Route::get('latest-telemetry', [HydroponicDeviceController::class, 'latestTelemetry']);
            Route::get('latest-telemetries', [HydroponicDeviceController::class, 'latestTelemetries']);
            Route::put('update-threshold', [HydroponicDeviceController::class, 'updateThreshold']);
            Route::put('update-auto', [HydroponicDeviceController::class, 'updateAuto']);
            Route::put('update-pump', [HydroponicDeviceController::class, 'updatePump']);
        });
    });
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/check-no-hp', [AuthController::class, 'checkPhoneNumber']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::group([
    'middleware' => ['auth:sanctum', 'farmer'],
    'prefix' => 'mobile',
], function (){
    Route::get('/profile', [UserController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('member', [MemberController::class, 'index']);
    Route::get('subscription/current', [SubscriptionController::class, 'currentSubscription']);
    Route::post('subscription/store', [SubscriptionController::class, 'storeSubsciptions']);
    Route::put('subscription/cancel', [SubscriptionController::class, 'updateCancelMember']);

    Route::get('bank', [BankController::class, 'index']);
    Route::get('withdrawal-bank', [WithdrawalBankController::class, 'index']);

    Route::get('/artikel', [ArticleController::class, 'listArtikel']);
    Route::get('/artikel/{id}/view', [ArticleController::class, 'artikelView']);
    Route::get('/artikel/{id}/like', [ArticleController::class, 'artikelLike']);
    Route::delete('/artikel/{id}/like', [ArticleController::class, 'deleteLike']);

    Route::post('/pengajuan-hapus-akun', [UserController::class, 'pengajuanHapusAkun']);

    Route::get('/detail-akun', [UserController::class, 'detailAkun']);
    Route::post('/update-nama', [UserController::class, 'updateName']);
    Route::post('/update-no-hp', [UserController::class, 'updatePhoneNumber']);
    Route::post('/update-password', [UserController::class, 'changePassword']);
    Route::get('/ktp', [UserController::class, 'getKtp']);
    Route::post('/update-ktp', [UserController::class, 'updateKtp']);

    Route::apiResource('address', AddressController::class);

    Route::post('/aktivasi-bitanic-plus', [UserController::class, 'aktivasiBitanicPlus']);

    Route::get('/available-lands', [GardenController::class, 'getAvailableLands']);
    Route::get('/available-devices', [GardenController::class, 'getAvailableDevices']);

    Route::get('/kebun', [GardenController::class, 'listKebun']);
    Route::get('/kebun/{id}', [GardenController::class, 'detailKebun']);
    Route::post('/kebun/{id}/change-status', [GardenController::class, 'changeGardenStatus']);
    Route::post('/kebun', [GardenController::class, 'store']);
    Route::post('/kebun/{id}/update', [GardenController::class, 'update']);
    Route::post('/kebun/{id}/motor-update', [GardenController::class, 'setStatusMotor']);
    Route::post('/kebun/{id}/send-command-pe', [GardenController::class, 'setPeStatus']);
    Route::delete('/kebun/{id}', [GardenController::class, 'destroy']);

    Route::prefix('/kebun/{garden}')->group(function(){
        Route::get('/schedule', [FertilizationController::class, 'index']);
        Route::get('/schedule/{id}', [FertilizationController::class, 'show']);
        Route::post('/schedule/store', [FertilizationController::class, 'store']);
        Route::delete('/schedule/{id}', [FertilizationController::class, 'destroy']);

        Route::get('/hasil-panen', [HarvestProduceController::class, 'index']);
        Route::post('/hasil-panen/store', [HarvestProduceController::class, 'store']);
        Route::delete('/hasil-panen/{id}', [HarvestProduceController::class, 'destroy']);

        Route::post('/device/{device}/check-active', [DeviceController::class, 'checkStatus']);

        Route::get('/invected', [InvectedGardenController::class, 'index']);
        Route::post('/invected', [InvectedGardenController::class, 'store']);
        Route::get('/invected/{id}', [InvectedGardenController::class, 'show']);
        Route::post('/invected/{id}/update', [InvectedGardenController::class, 'update']);
        Route::post('/invected/{id}/update-status', [InvectedGardenController::class, 'updateStatus']);
        Route::delete('/invected/{id}', [InvectedGardenController::class, 'destroy']);

        Route::get('/commodity/current', [CommodityController::class, 'index']);
        Route::post('/commodity/current', [CommodityController::class, 'store']);
        Route::put('/commodity/current/update-yield', [CommodityController::class, 'updateYield']);
        Route::delete('/commodity/current/{commodity}', [CommodityController::class, 'destroy']);
        Route::get('/commodity/finished', [CommodityController::class, 'indexFinished']);

        Route::get('rsc', [GardenController::class, 'rscGardens']);
        Route::get('rsc/recent-telemetries', [GardenController::class, 'recentRscGardenTelemetries']);
    });

    Route::get('/home', [HomeController::class, 'home']);

    // Pest
    Route::get('/pest', [PestController::class, 'index']);

    Route::get('/tanaman', [CropController::class, 'listTanaman']);
    Route::get('/master-tanaman', [CropController::class, 'masterTanaman']);
    Route::get('/hasil-panen-saya', [CropController::class, 'hasilPanen']);

    // Route::post('/panen', [CropController::class, 'tanamanPanen']);

    Route::get('/perangkat', [DeviceController::class, 'listPerangkat']);
    Route::get('/perangkat-aktivasi', [DeviceController::class, 'perangkatAktifasi']);
    Route::get('/perangkat-tidak-dipakai', [DeviceController::class, 'perangkatTidakDipakai']);
    Route::post('/perangkat', [DeviceController::class, 'aktifasiPerangkat']);
    Route::post('/perangkat/{serial_number}/send-manual-message', [DeviceController::class, 'updateManualSelenoid']);
    Route::post('/perangkat/{device:device_series}/store-formula', [V2DeviceController::class, 'getScheduleTimes']);
    Route::get('/perangkat/{sn}', [DeviceController::class, 'detailPerangkat']);

    Route::get('intepretasi-status', [V2DeviceController::class, 'getAllIntepretasiStatus']);

    Route::post('/v3-schedule/store', [V2DeviceController::class, 'storeSecondSchedule']);
    Route::delete('/v3-schedule/reset/{fertilization}', [V2DeviceController::class, 'resetPerangkat']);

    Route::get('/bitanic-plus-saya', [PoinController::class, 'bitanicPlusSaya']);
    Route::get('/reward-poin-bitanic', [PoinController::class, 'rewardPoinPlus']);

    Route::put('/land/{land}/update-polygon', [LandController::class, 'updatePolygon']);
    Route::apiResource('/land', LandController::class);

    Route::group(['prefix' => '/shop'], function(){
        Route::get('/product', [ProductController::class, 'index']);
        Route::post('/product/store', [ProductController::class, 'store']);
        Route::get('/product/{product}', [ProductController::class, 'show']);
        Route::put('/product/{product}/update', [ProductController::class, 'update']);
        Route::delete('/product/{product}', [ProductController::class, 'destroy']);

        Route::get('balance-withdraw', [BalanceWithdrawController::class, 'index']);
        Route::post('balance-withdraw', [BalanceWithdrawController::class, 'store']);
    });

    Route::get('/shop', [ShopController::class, 'index']);
    Route::post('/shop/store', [ShopController::class, 'store']);
    Route::put('/shop/bank-account-update', [ShopController::class, 'updateBankAccount']);
    Route::put('/shop/update', [ShopController::class, 'update']);
    Route::get('/shop/transactions/being-packed', [ShopController::class, 'getBeingPackedTransactions']);
    Route::get('/shop/transactions/shipped', [ShopController::class, 'getShippedReceivedTransactions']);
    Route::put('/shop/transactions/{farmerTransactionShop}/update-receipt', [ShopController::class, 'updateDeliveryReceipt']);
    Route::put('/shop/transactions/{farmerTransactionShop}/cancel', [ShopController::class, 'cancelTransaction']);
    Route::delete('/shop', [ShopController::class, 'destroy']);

    Route::apiResource('cart', CartController::class);

    Route::get('/marketplace/produk', [MarketplaceController::class, 'getProducts']);
    Route::get('/marketplace/produk/{product}', [MarketplaceController::class, 'detailProduct']);
    Route::get('marketplace/transactions', [MarketplaceController::class, 'getTransactions']);
    Route::get('marketplace/transactions/pending', [MarketplaceController::class, 'pendingTransactions']);
    Route::get('marketplace/transactions/shipping', [MarketplaceController::class, 'shippingTransactionShops']);
    Route::get('marketplace/transactions/detail/{farmer_transaction}', [MarketplaceController::class, 'detailTransaction']);
    Route::put('marketplace/transactions/detail/{farmer_transaction}/cancel', [MarketplaceController::class, 'cancelTransaction']);
    Route::get('marketplace/transactions/shop-detail/{farmer_transaction_shop}', [MarketplaceController::class, 'detailShippingShop']);
    Route::put('marketplace/transactions/shop-detail/{farmer_transaction_shop}/accept-shipping', [MarketplaceController::class, 'acceptedShipping']);

    Route::get('rsc/{rscGarden}/telemetry', [RscGardenController::class, 'telemetries']);

    Route::post('/rsc-garden', [RscGardenController::class, 'store']);

    Route::apiResource('/stick-telemetri', StickTelemetriController::class)->only(['index','store','show']);

    Route::post('/rsc-telemetry/check-device', [RcsController::class, 'checkDevice']);
    Route::apiResource('/rsc-telemetri', RcsController::class)->only(['store']);

    Route::post('feedback', [FeddbackController::class, 'store']);

    Route::get('bitanic-product', [BitanicProductController::class, 'index']);
    Route::get('bitanic-product/{bitanic_product}', [BitanicProductController::class, 'show']);

    Route::get('transaction-setting', [TransactionController::class, 'platformFees']);

    Route::get('transaction', [TransactionController::class, 'getAllTransactions']);
    Route::get('transaction/{transaction}', [TransactionController::class, 'getDetailTransaction']);
    Route::post('transaction', [TransactionController::class, 'store']);
    Route::put('transaction/{transaction}/cancel', [TransactionController::class, 'cancelTransaction']);
    Route::put('transaction/{transaction}/accept-shipping', [TransactionController::class, 'acceptedShipping']);

    Route::get('crop-for-sale', [CropForSaleController::class, 'index']);

    Route::post('payout', [MarketplaceController::class, 'payout']);
});
