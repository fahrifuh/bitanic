<?php

use App\Http\Controllers\Api\GeneralController;
use App\Http\Controllers\Bitanic\AboutOurStartup;
use App\Http\Controllers\Bitanic\AccountDeleteController;
use App\Http\Controllers\Bitanic\AccountSettingController;
use App\Http\Controllers\Bitanic\AdvertisementController;
use App\Http\Controllers\Bitanic\ArticleController;
use App\Http\Controllers\Bitanic\BalanceWithdrawAdminController;
use App\Http\Controllers\Bitanic\BalanceWithdrawUserController;
use App\Http\Controllers\Bitanic\BankController;
use App\Http\Controllers\Bitanic\BitanicProductController;
use App\Http\Controllers\Bitanic\CityController;
use App\Http\Controllers\Bitanic\CommodityController;
use App\Http\Controllers\Bitanic\ContactUseMessageController;
use App\Http\Controllers\Bitanic\ContactUsSettingController;
use App\Http\Controllers\Bitanic\CropController;
use App\Http\Controllers\Bitanic\CropForSaleController;
use App\Http\Controllers\Bitanic\DashboardController;
use App\Http\Controllers\Bitanic\DeviceController;
use App\Http\Controllers\Bitanic\DistrictController;
use App\Http\Controllers\Bitanic\FarmerController;
use App\Http\Controllers\Bitanic\FarmerGroupController;
use App\Http\Controllers\Bitanic\FarmerKtpController;
use App\Http\Controllers\Bitanic\FarmerTransactionController;
use App\Http\Controllers\Bitanic\FeedbackController;
use App\Http\Controllers\Bitanic\FertilizationController;
use App\Http\Controllers\Bitanic\FormulaController;
use App\Http\Controllers\Bitanic\GardenController;
use App\Http\Controllers\Bitanic\HarvestProduceController;
use App\Http\Controllers\Bitanic\InterpretationController;
use App\Http\Controllers\Bitanic\InvectedGardenController;
use App\Http\Controllers\Bitanic\InvestorController;
use App\Http\Controllers\Bitanic\LadingController;
use App\Http\Controllers\Bitanic\LandController;
use App\Http\Controllers\Bitanic\LandingManagement\CareerController;
use App\Http\Controllers\Bitanic\LandingManagement\FaqController;
use App\Http\Controllers\Bitanic\LandingManagement\GalleryController;
use App\Http\Controllers\Bitanic\LandingManagement\ProductController as LandingManagementProductController;
use App\Http\Controllers\Bitanic\LandingManagement\ServiceController;
use App\Http\Controllers\Bitanic\LandingManagement\TestimonyController;
use App\Http\Controllers\Bitanic\Lite\DeviceController as LiteDeviceController;
use App\Http\Controllers\Bitanic\Lite\FeedbackController as LiteFeedbackController;
use App\Http\Controllers\Bitanic\Lite\PumpController;
use App\Http\Controllers\Bitanic\Lite\UserController;
use App\Http\Controllers\Bitanic\LogActivityController;
use App\Http\Controllers\Bitanic\Marketplace\ProductController;
use App\Http\Controllers\Bitanic\MemberController;
use App\Http\Controllers\Bitanic\PartnerController;
use App\Http\Controllers\Bitanic\PestController;
use App\Http\Controllers\Bitanic\PractitionerController;
use App\Http\Controllers\Bitanic\ProductSettingController;
use App\Http\Controllers\Bitanic\ProvinceController;
use App\Http\Controllers\Bitanic\RscGardenController;
use App\Http\Controllers\Bitanic\SellerController;
use App\Http\Controllers\Bitanic\ShopController;
use App\Http\Controllers\Bitanic\ShopKtpController;
use App\Http\Controllers\Bitanic\ShopTransactionController;
use App\Http\Controllers\Bitanic\StickTelemetriController;
use App\Http\Controllers\Bitanic\SubdistrictController;
use App\Http\Controllers\Bitanic\SubscriptionController;
use App\Http\Controllers\Bitanic\TelemetriController;
use App\Http\Controllers\Bitanic\TransactionController;
use App\Http\Controllers\Bitanic\TransactionSettingController;
use App\Http\Controllers\Bitanic\UpdateFirmwareController;
use App\Http\Controllers\Bitanic\UserProductController;
use App\Http\Controllers\Bitanic\v2\DashboardAdminController;
use App\Http\Controllers\Bitanic\v2\DeviceController as V2DeviceController;
use App\Http\Controllers\Bitanic\v2\InterpretationController as V2InterpretationController;
use App\Http\Controllers\Bitanic\v2\NecessityDifferenceController;
use App\Http\Controllers\Bitanic\v2\SelenoidController;
use App\Http\Controllers\Bitanic\WithdrawalBankController;
use App\Http\Controllers\Halter\LogCageController;
use App\Http\Controllers\Halter\LogController;
use App\Http\Controllers\Hydroponic\DeviceController as HydroponicDeviceController;
use App\Http\Controllers\Hydroponic\DeviceTelemetryController as HydroponicDeviceTelemetryController;
use App\Http\Controllers\Hydroponic\UserController as HydroponicUserController;
use App\Models\Interpretation;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [LadingController::class, 'index'])->name('landing.index');

Route::get('/new-landing', function () {
    return view('bitanic.new-landing.main');
});

Route::get('/mitra-page', [LadingController::class, 'mitra']);

Route::post('contact-us-message', [ContactUseMessageController::class, 'store'])->name('landing.contact-us-message.store');

Route::get('/privacy', function () {
    return view('privacy');
});

Route::get('update/firmware', [UpdateFirmwareController::class, 'downloadFile'])->name('firmware.douwnload');

// Route::get('/blank', function () {
//     return view('blank');
// })->middleware(['auth'])->name('dashboard');

Route::domain('control.bitanicv2.test')->group(function () {
    Route::get('test', function () {
        return 'test';
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('/dashboard/kebutuhan-pupuk/province', [DashboardController::class, 'getCountFertilizerByProvince'])->name('dashboard.kebutuhan-pupuk.province');
    Route::get('/dashboard/kebutuhan-pupuk/cities/{province}', [DashboardController::class, 'getCountFertilizerByCity'])->name('dashboard.kebutuhan-pupuk.city');
    Route::get('/dashboard/kebutuhan-pupuk/districts/{city}', [DashboardController::class, 'getCountFertilizerByDistrict'])->name('dashboard.kebutuhan-pupuk.district');
    Route::get('/dashboard/kebutuhan-pupuk/subdistricts/{district}', [DashboardController::class, 'getCountFertilizerBySubdistrict'])->name('dashboard.kebutuhan-pupuk.subdistrict');
    Route::get('/dashboard/kebutuhan-pupuk/groups/{subdistrict}', [DashboardController::class, 'getCountFertilizerByGroup'])->name('dashboard.kebutuhan-pupuk.group');
    Route::get('/dashboard/kebutuhan-pupuk/farmers/{group}', [DashboardController::class, 'getCountFertilizerByFarmer'])->name('dashboard.kebutuhan-pupuk.farmer');
    Route::get('/dashboard/rekapitulasi-transaksi', [DashboardController::class, 'rekapitulasiTransaksiBitanic'])->name('dashboard.rekapitulasi-transaksi');

    Route::prefix('/bitanic')->name('bitanic.')->group(function () {

        Route::middleware('admin')->group(function () {
            Route::get('dashboard-admin/get-lands', [DashboardAdminController::class, 'getLands'])->name('dashboard-admin.get-lands');

            Route::resource('interpretation', V2InterpretationController::class)->only(['index', 'update']);
            Route::apiResource('necessity-difference', NecessityDifferenceController::class);

            Route::post('/article/{id}', [ArticleController::class, 'update']);
            Route::resource('/article', ArticleController::class)->except(['show']);

            Route::post('/advertisement/{id}', [AdvertisementController::class, 'update']);
            Route::apiResource('/advertisement', AdvertisementController::class)->except(['show']);

            Route::post('/researcher/{id}', [PractitionerController::class, 'update']);
            Route::apiResource('/researcher', PractitionerController::class)->except(['show']);

            Route::post('/partner/{id}', [PartnerController::class, 'update']);
            Route::apiResource('/partner', PartnerController::class)->except(['show']);

            Route::post('/seller/{id}', [SellerController::class, 'update']);
            Route::apiResource('/seller', SellerController::class)->except(['show']);

            Route::post('/investor/{id}', [InvestorController::class, 'update']);
            Route::apiResource('/investor', InvestorController::class)->except(['show']);

            Route::post('/crop/{id}', [CropController::class, 'update']);
            Route::resource('/crop', CropController::class);

            Route::resource('/pest', PestController::class)->except(['index', 'show']);

            Route::apiResource('/province', ProvinceController::class)->except(['show']);
            Route::apiResource('/city', CityController::class)->except(['show']);
            Route::apiResource('/district', DistrictController::class)->except(['show']);
            Route::apiResource('/subdistrict', SubdistrictController::class)->except(['show']);

            Route::get('/log-activity', [LogActivityController::class, 'index'])->name('log-activity.index');

            Route::get('/log-halter', [LogController::class, 'index'])->name('log-halter.index');
            Route::get('/log-kandang', [LogCageController::class, 'index'])->name('log-cage.index');

            Route::get('/account-delete-request', [AccountDeleteController::class, 'index'])->name('account-delete-request.index');
            Route::post('/account-delete-request/accept', [AccountDeleteController::class, 'accept'])->name('account-delete-request.accept');
            Route::post('/account-delete-request/decline', [AccountDeleteController::class, 'decline'])->name('account-delete-request.decline');

            Route::get('/contact-us-message', [ContactUseMessageController::class, 'index'])->name('contact-us-message.index');
            Route::post('/contact-us-message/{id}/change-status', [ContactUseMessageController::class, 'changeStatus'])->name('contact-us-message.change-status');
            Route::get('/contact-us-message/{contactUsMessage}/create-message', [ContactUseMessageController::class, 'createMessage'])->name('contact-us-message.create-message');
            Route::post('/contact-us-message/{contactUsMessage}/store-message', [ContactUseMessageController::class, 'storeMessage'])->name('contact-us-message.store-message');
            Route::delete('/contact-us-message/all', [ContactUseMessageController::class, 'destroyAll'])->name('contact-us-message.destroy-all');
            Route::delete('/contact-us-message/{id}', [ContactUseMessageController::class, 'destroy'])->name('contact-us-message.destroy');

            Route::prefix('/landing-setting')->group(function () {
                Route::resource('/contact-us-setting', ContactUsSettingController::class)->only(['index', 'store']);
                Route::resource('/about-our-startup-setting', AboutOurStartup::class)->only(['index', 'store']);
                Route::get('/about-our-startup-setting/create-event-images', [AboutOurStartup::class, 'createEventImages'])->name('about-our-startup-setting.create-event-images');
                Route::post('/about-our-startup-setting/store-event-images', [AboutOurStartup::class, 'storeEventImages'])->name('about-our-startup-setting.store-event-images');
                Route::get('/about-our-startup-setting/delete-event-images', [AboutOurStartup::class, 'deleteImages'])->name('about-our-startup-setting.delete-event-images');
                Route::delete('/about-our-startup-setting/destroy-event-images', [AboutOurStartup::class, 'destroyImages'])->name('about-our-startup-setting.destroy-event-images');

                Route::get('/product-setting', [ProductSettingController::class, 'index'])->name('product-setting.index');
                Route::put('/product-setting/update', [ProductSettingController::class, 'update'])->name('product-setting.update');

                Route::resource('career', CareerController::class);
                Route::put('career/{career}/change-status', [CareerController::class, 'updateStatus'])->name('career.change-status');

                Route::resource('service', ServiceController::class);
                
                Route::resource('product', LandingManagementProductController::class);

                Route::resource('gallery', GalleryController::class)->except('show');

                Route::resource('testimony', TestimonyController::class);

                Route::resource('faq', FaqController::class)->except('show');
            });

            Route::put('/farmer-group/{id}/add-farmers', [FarmerGroupController::class, 'addFarmers'])->name('farmer-group.add-farmers');
            Route::put('/farmer-group/{id}/remove-farmers', [FarmerGroupController::class, 'removeFarmers'])->name('farmer-group.remove-farmers');
            Route::apiResource('/farmer-group', FarmerGroupController::class)->except(['show']);
            Route::resource('/farmer', FarmerController::class);
            Route::get('/farmer/export/excel', [FarmerController::class, 'exportExcel'])->name('farmer.export-excel');

            Route::get('lite-user/{liteUser}/edit-password', [UserController::class, 'editPassword'])->name('lite-user.edit-password');
            Route::put('lite-user/{liteUser}/update-password', [UserController::class, 'updatePassword'])->name('lite-user.update-password');
            Route::resource('lite-user', UserController::class);
            Route::get('/lite-user/export/excel', [UserController::class, 'exportExcel'])->name('lite-user.export-excel');

            Route::get('firmware', [UpdateFirmwareController::class, 'index'])->name('firmware.index');
            Route::get('firmware/create', [UpdateFirmwareController::class, 'create'])->name('firmware.create');
            Route::post('firmware', [UpdateFirmwareController::class, 'store'])->name('firmware.store');
            Route::put('firmware/{update_firmware}/update-selected', [UpdateFirmwareController::class, 'updateSelected'])->name('firmware.update-selected');
            Route::delete('firmware/{update_firmware}', [UpdateFirmwareController::class, 'destroy'])->name('firmware.destroy');

            Route::get('/feedback/regular', [FeedbackController::class, 'index'])->name('feedback-regular.index');
            Route::get('/feedback/regular/{feedback}', [FeedbackController::class, 'show'])->name('feedback-regular.show');
            Route::delete('/feedback/regular/{feedback}', [FeedbackController::class, 'destroy'])->name('feedback-regular.destroy');
            Route::get('/feedback/lite', [LiteFeedbackController::class, 'index'])->name('feedback-lite.index');
            Route::get('/feedback/lite/{lite_feedback}', [LiteFeedbackController::class, 'show'])->name('feedback-lite.show');
            Route::delete('/feedback/lite/{lite_feedback}', [LiteFeedbackController::class, 'destroy'])->name('feedback-lite.destroy');

            Route::resource('bitanic-product', BitanicProductController::class);

            Route::resource('bank', BankController::class);
            Route::resource('withdrawal-bank', WithdrawalBankController::class);

            Route::get('transaction', [TransactionController::class, 'index'])->name('transaction.index');
            Route::get('transaction/{transaction}', [TransactionController::class, 'show'])->name('transaction.show');
            Route::put('transaction/{transaction}/update-status', [TransactionController::class, 'updateStatus'])->name('transaction.update-status');
            Route::put('transaction/{transaction}/update-shipping', [TransactionController::class, 'updateShipping'])->name('transaction.update-shipping');

            Route::get('transaction-komoditi', [FarmerTransactionController::class, 'index'])->name('transaction-komodity.index');
            Route::get('transaction-komoditi/{farmerTransaction}', [FarmerTransactionController::class, 'show'])->name('transaction-komodity.show');
            Route::put('transaction-komoditi/{farmerTransaction}/update-status', [FarmerTransactionController::class, 'updateStatus'])->name('transaction-komodity.update-status');

            Route::resource('crop-for-sale', CropForSaleController::class);

            Route::get('transaction-setting', [TransactionSettingController::class, 'index'])->name('transaction-setting.index');
            Route::put('transaction-setting/update', [TransactionSettingController::class, 'update'])->name('transaction-setting.update');

            Route::get('admin/balance-withdraw', [BalanceWithdrawAdminController::class, 'index'])->name('admin.balance-withdraw.index');
            Route::put('admin/balance-withdraw/{balanceWithdraw}/update-status', [BalanceWithdrawAdminController::class, 'updateStatus'])->name('admin.balance-withdraw.update-status');

            Route::resource('member', MemberController::class);
            Route::get('member/{member}/subscription', [SubscriptionController::class, 'index'])->name('member.subscription');

            Route::get('ktp/farmer', [FarmerKtpController::class, 'index'])->name('ktp-farmer.index');
            Route::get('ktp/farmer/{farmer}/ktp', [FarmerKtpController::class, 'showKtp'])->name('ktp-farmer.ktp');
            Route::post('ktp/farmer/{farmer}', [FarmerKtpController::class, 'update'])->name('ktp-farmer.update');

            Route::get('ktp/shop', [ShopKtpController::class, 'index'])->name('ktp-shop.index');
            Route::get('ktp/shop/{shop}/ktp', [ShopKtpController::class, 'showKtp'])->name('ktp-shop.ktp');
            Route::post('ktp/shop/{shop}', [ShopKtpController::class, 'update'])->name('ktp-shop.update');

            Route::resource('lite-device', LiteDeviceController::class);
            Route::resource('lite-device.lite-device-pump', PumpController::class);

            Route::get('/marketplace/user-product', [UserProductController::class, 'index'])->name('user-product.index');
            Route::get('/marketplace/user-product/{product}', [UserProductController::class, 'show'])->name('user-product.show');
            Route::put('/marketplace/user-product/{product}/update-status', [UserProductController::class, 'updateDisable'])->name('user-product.update-disable');

            Route::prefix('hydroponic')->name('hydroponic.')->group(function () {
                Route::resource('user', HydroponicUserController::class)->parameters([
                    'user' => 'hydroponicUser'
                ]);
                Route::prefix('user')->name('user.')->group(function () {
                    Route::get('/{hydroponicUser}/edit-password', [HydroponicUserController::class, 'editPassword'])->name('edit-password');
                    Route::put('/{hydroponicUser}/update-password', [HydroponicUserController::class, 'updatePassword'])->name('update-password');
                });

                Route::resource('device', HydroponicDeviceController::class)->parameters([
                    'device' => 'hydroponicDevice'
                ]);
                Route::get('device/{hydroponicDevice}/telemetry', [HydroponicDeviceTelemetryController::class, 'index'])
                    ->name('device.telemetry.index');
                Route::get('device/{hydroponicDevice}/telemetry/export/excel', [HydroponicDeviceTelemetryController::class, 'exportExcel'])
                    ->name('device.telemetry.export-excel');
            });
        });

        Route::resource('/pest', PestController::class)->only(['index', 'show']);

        Route::apiResource('/invected-gardens', InvectedGardenController::class);
        Route::put('/invected-gardens/{id}/update-status', [InvectedGardenController::class, 'updateStatus'])->name('invected-gardens.update-status');

        Route::post('interpretation/get-status', [InterpretationController::class, 'getStatus'])->name('interpretation.get-status');
        Route::get('/get-garden', [GardenController::class, 'getGardens'])->name('get-garden');

        Route::prefix('/farmer/{farmer}')->group(function () {
            Route::get('/land/get-lands', [LandController::class, 'getLands'])->name('land.get-lands');
            Route::get('/land/{land}/get-land', [LandController::class, 'getLand'])->name('land.get-land');
            Route::get('/land/{land}/get-land-rsc', [LandController::class, 'getLandRsc'])->name('land.get-land-rsc');
            Route::get('/land/{land}/export/excel', [LandController::class, 'exportExcelRsc'])->name('land.export-excel');
            Route::resource('/land', LandController::class);

            Route::prefix('/land/{land}')->group(function () {
                Route::put('/garden/{garden}/change-status', [GardenController::class, 'changeStatus'])->name('garden.change-status');
                Route::resource('garden', GardenController::class);

                Route::prefix('/garden/{garden}')->group(function () {
                    Route::get('commodity', [CommodityController::class, 'create'])->name('commodity.create');
                    Route::post('commodity', [CommodityController::class, 'store'])->name('commodity.store');
                    Route::get('commodity/history', [CommodityController::class, 'historyCommodities'])->name('commodity.history');
                    Route::get('commodity/{commodity}/edit-yield', [CommodityController::class, 'editYield'])->name('commodity.edit-yield');
                    Route::put('commodity/{commodity}/update-yield', [CommodityController::class, 'updateYield'])->name('commodity.update-yield');
                    Route::delete('commodity/{commodity}', [CommodityController::class, 'destroy'])->name('commodity.destroy');

                    Route::get('get-rsc-garden/{rscGarden}', [RscGardenController::class, 'getRscGarden'])->name('rsc-garden.get-rsc-garden');
                    Route::get('rsc/history', [RscGardenController::class, 'history'])->name('rsc-garden.history');
                    Route::get('rsc/{rscGarden}/export/excel', [RscGardenController::class, 'exportExcel'])->name('rsc-garden.export-excel');
                    Route::get('rsc/{rscGarden}/export/pdf', [RscGardenController::class, 'exportPdf'])->name('rsc-garden.export-pdf');
                    Route::delete('rsc/{rscGarden}', [RscGardenController::class, 'destroy'])->name('rsc-garden.destroy');
                });
            });

            Route::resource('/stick-telemetri', StickTelemetriController::class)->only(['index', 'destroy']);
            Route::get('/stick-telemetri/get-area', [StickTelemetriController::class, 'getArea'])->name('stick-telemetri.get-area');
            Route::get('/stick-telemetri/{stik_telemetri}/get-telemetri', [StickTelemetriController::class, 'getTelemetri'])->name('stick-telemetri.get-telemetri');

            Route::prefix('/garden/{garden}')->group(function () {
                Route::post('/fertilization/store', [FertilizationController::class, 'store'])->name('fertilization.store');
                Route::post('/fertilization/{id}/resend-setting', [FertilizationController::class, 'resendSetting'])->name('fertilization.resend-setting');
                Route::put('/fertilization/{id}/save-and-reset-fertilization', [FertilizationController::class, 'stopAndSaveFertilization'])->name('fertilization.save-fertilization');
                Route::delete('/fertilization/{id}/destroy-fertilization', [FertilizationController::class, 'destroyAndResetDevice'])->name('fertilization.destroy-fertilization');

                Route::get('/telemetri', [TelemetriController::class, 'index'])->name('telemetri.index');

                Route::resource('/harvest-produce', HarvestProduceController::class)->only(['index', 'store', 'destroy']);
            });
        });

        Route::get('farmer-crops/{crop}', [CropController::class, 'show'])->name('farmer.crops');

        Route::middleware('farmer')->group(function () {
            Route::get('/shop/product/create', [ProductController::class, 'create'])->name('shop.product-create');
            Route::post('/shop/product/store', [ProductController::class, 'store'])->name('shop.product-store');
            Route::get('/shop/product/{product}', [ProductController::class, 'show'])->name('shop.product-show');
            Route::get('/shop/product/{product}/edit', [ProductController::class, 'edit'])->name('shop.product-edit');
            Route::put('/shop/product/{product}/update', [ProductController::class, 'update'])->name('shop.product-update');
            Route::delete('/shop/product/{product}', [ProductController::class, 'destroy'])->name('shop.product-destroy');

            Route::prefix('shop')->name('shop.')->group(function () {
                Route::get('transaction', [ShopTransactionController::class, 'index'])->name('transaction-index');
                Route::get('transaction/{id}', [ShopTransactionController::class, 'show'])->name('transaction-show');
                Route::put('transaction/{id}/shipping-update', [ShopTransactionController::class, 'updateShipping'])->name('transaction-shipping-update');
                Route::put('transaction/{farmerTransaction}/status-update', [ShopTransactionController::class, 'updateStatus'])->name('transaction-status-update');

                Route::get('balance-withdraw', [BalanceWithdrawUserController::class, 'index'])->name('balance-withdraw.index');
                Route::post('balance-withdraw', [BalanceWithdrawUserController::class, 'store'])->name('balance-withdraw.store');
            });

            Route::get('/shop/edit', [ShopController::class, 'edit'])->name('shop.edit');
            Route::put('/shop/update', [ShopController::class, 'update'])->name('shop.update');
            Route::delete('/shop', [ShopController::class, 'destroy'])->name('shop.destroy');
            Route::get('/shop/ktp', [ShopController::class, 'showKtp'])->name('shop.show-ktp');
            Route::get('/shop/ktp/edit', [ShopController::class, 'editKtp'])->name('shop.edit-ktp');
            Route::put('/shop/ktp', [ShopController::class, 'updateKtp'])->name('shop.update-ktp');
            Route::resource('/shop', ShopController::class)->except(['show', 'edit', 'update', 'destroy']);

            Route::prefix('/setting')->name('setting-')->group(function () {
                Route::prefix('/account')->name('account.')->group(function () {
                    Route::get('/', [AccountSettingController::class, 'index'])->name('index');
                    Route::get('/ktp', [AccountSettingController::class, 'showKtp'])->name('show-ktp');
                    Route::put('/update', [AccountSettingController::class, 'update'])->name('update');
                    Route::put('/update-ktp', [AccountSettingController::class, 'updateKtp'])->name('update-ktp');
                    Route::post('/update-picture', [AccountSettingController::class, 'updateProfilePicture'])->name('update-picture');
                    Route::delete('/destroy-account', [AccountSettingController::class, 'destroyAccount'])->name('destroy-account');
                });
            });

            Route::delete('/fertilization/{id}/finished-delete', [FertilizationController::class, 'destroyFinished'])->name('fertilization.destroy-finishid');

            Route::get('farmer/member/current', [MemberController::class, 'currentMember'])->name('member.current');
            Route::get('farmer/member/chose', [MemberController::class, 'choseMember'])->name('member.chose');
            Route::get('farmer/member/purchase/{member}', [MemberController::class, 'paymentMember'])->name('member.purchase');
            Route::post('farmer/member/subscription', [MemberController::class, 'storeSubsciptions'])->name('subscription.store');
            Route::get('farmer/member/cancel', [MemberController::class, 'cancelMember'])->name('member.cancel-show');
            Route::put('farmer/member/cancel', [MemberController::class, 'updateCancelMember'])->name('member.cancel-update');
        });

        Route::resource('formula', FormulaController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

        Route::get('/v3/device/{device}/selenoid/create', [SelenoidController::class, 'create'])->name('v3-device.selenoid.create');
        Route::post('/v3/device/{device}/selenoid/store', [SelenoidController::class, 'store'])->name('v3-device.selenoid.store');
        Route::get('/v3/device/{device}/selenoid/{selenoid}/edit', [SelenoidController::class, 'edit'])->name('v3-device.selenoid.edit');
        Route::put('/v3/device/{device}/selenoid/{selenoid}/update', [SelenoidController::class, 'update'])->name('v3-device.selenoid.update');
        Route::delete('/v3/device/{device}/selenoid/{selenoid}', [SelenoidController::class, 'destroy'])->name('v3-device.selenoid.destroy');
        Route::post('/v3/device/store', [V2DeviceController::class, 'store'])->name('v3-device.store');
        Route::get('/v3/device/{device}/get-lands', [V2DeviceController::class, 'getLands'])->name('v3-device.get-lands');
        Route::get('/v3/device/{device}/formula', [V2DeviceController::class, 'formulas'])->name('v3-device.formula');
        Route::get('/v3/device/{device}/formula/{formula}', [V2DeviceController::class, 'output'])->name('v3-device.output');
        Route::post('/v3/device/{device}/selenoid-status-change', [V2DeviceController::class, 'sendManualControlTwo'])->name('v3-device.status-change');
        Route::get('/v3/device/{device}/fertilization/{fertilization}', [V2DeviceController::class, 'scheduleShow'])->name('v3-device.fertilization-show');
        Route::get('/v3/device/{device}/telemetri-sv', [V2DeviceController::class, 'telemetriSelenoids'])->name('v3-device.telemetri-selenoids');
        Route::get('/v3/device/{device}/edit', [V2DeviceController::class, 'edit'])->name('v3-device.edit');
        Route::put('/v3/device/{device}/update', [V2DeviceController::class, 'update'])->name('v3-device.update');
        Route::get('/v3/device/{device}', [V2DeviceController::class, 'show'])->name('v3-device.show');

        Route::post('/v3/store-new-schedule', [V2DeviceController::class, 'storePenjadwalan'])->name('v3-store-new-schedule');
        Route::post('/v3/schedule/reset/{id}', [V2DeviceController::class, 'resetPerangkat'])->name('v3-schedule.reset');
        Route::post('/v3/schedule/resend-message/{id}', [V2DeviceController::class, 'kirimUlangSetting'])->name('v3-schedule.resend-message');
        Route::post('/v3/schedule/stop/{id}', [V2DeviceController::class, 'hentikanPemupukan'])->name('v3-schedule.stop');

        Route::get('/device/{device}/edit-specification', [DeviceController::class, 'editSpecification'])->name('device.edit-specification');
        Route::put('/device/{device}/update-specification', [DeviceController::class, 'updateSpecification'])->name('device.update-specification');
        Route::get('/device/{device}/edit-pe/{pe}', [DeviceController::class, 'editPe'])->name('device.edit-pe');
        Route::put('/device/{device}/update-pe/{pe}', [DeviceController::class, 'updatePe'])->name('device.update-pe');
        Route::resource('/device', DeviceController::class);
        Route::post('/device/{device}/reset', [DeviceController::class, 'resetDevice'])->name('device.reset');
        Route::put('/device/{id}/change-status', [DeviceController::class, 'changeStatus'])->name('device.change-status');
        Route::put('/device/{device}/change-pe-status', [DeviceController::class, 'setPeStatus'])->name('device.change-pe-status');
    });


    Route::prefix('/web')->name('web.')->group(function () {
        Route::get('/get-device/{farmer}/{garden?}', [DeviceController::class, 'getDevice'])->name('get-device');
        Route::get('/farmer/{farmer}/gardens', [GardenController::class, 'getGardensPolygon'])->name('gardens.get');
        Route::get('/get-garden-polygon/{id}', [GardenController::class, 'getGardenPolygon'])->name('get-garden-polygon');
        Route::get('/get-pests', [GeneralController::class, 'getPests'])->name('pests.get');

        // Fertilization web
        Route::get('/fertilization-list/{garden}', [GardenController::class, 'getListFertilization'])->name('fertilization-list');
        Route::get('/fertilization/{garden}', [GardenController::class, 'getFertilization'])->name('fertilization');
        Route::get('/schedules/{garden}', [GardenController::class, 'getSchedules'])->name('schedules');
        Route::put('/motor-status/{garden}/update', [GardenController::class, 'setStatusMotor'])->name('motor-status.update');

        Route::get('/get-farmers-from-subdis/{subdis}', [GeneralController::class, 'getFarmersFromSubdis'])->name('get-farmers-from-subdis');

        // wilayah
        Route::prefix('wilayah')->name('wilayah.')->group(function () {
            Route::get('/provinces', [GeneralController::class, 'getProvinces'])->name('provinces');
            Route::get('/cities/{province?}', [GeneralController::class, 'getCities'])->name('cities');
            Route::get('/districts/{city?}', [GeneralController::class, 'getDistricts'])->name('districts');
            Route::get('/subdistricts/{district?}', [GeneralController::class, 'getSubdistricts'])->name('subdistricts');
        });

        // Farmer Groups
        Route::get('/farmer-groups/{group}/farmers', [GeneralController::class, 'getFarmersFromGroup'])->name('farmer-group-farmers');
        Route::get('/farmer-groups/{subdistrict?}', [GeneralController::class, 'getFarmerGroups'])->name('farmer-groups');

        Route::prefix('dashboard')->group(function () {
            Route::get('/get-planting-activity', [GeneralController::class, 'getAktivitasMenanamDashboard'])->name('get-planting-activity');
            Route::get('/get-dashboard-data', [GeneralController::class, 'getDashboardData'])->name('get-dashboard-data');
            Route::get('/get-dashboard-data/kebutuhan-pupuk', [GeneralController::class, 'getFertilizer'])->name('get-fertilizer');
            Route::get('/get-kebutuhan-pupuk/province', [GeneralController::class, 'getCountFertilizerByProvince'])->name('get-kebutuhan-pupuk.province');
            Route::get('/get-kebutuhan-pupuk/cities/{province}', [GeneralController::class, 'getCountFertilizerByCity'])->name('get-kebutuhan-pupuk.city');
            Route::get('/get-kebutuhan-pupuk/district/{city}', [GeneralController::class, 'getCountFertilizerByDistrict'])->name('get-kebutuhan-pupuk.district');
            Route::get('/get-kebutuhan-pupuk/subdistrict/{district}', [GeneralController::class, 'getCountFertilizerBySubdistrict'])->name('get-kebutuhan-pupuk.subdistrict');
            Route::get('/get-kebutuhan-pupuk/groups/{subdistrict}', [GeneralController::class, 'getCountFertilizerByGroup'])->name('get-kebutuhan-pupuk.group');
            Route::get('/get-kebutuhan-pupuk/farmers/{group}', [GeneralController::class, 'getCountFertilizerByFarmer'])->name('get-kebutuhan-pupuk.farmer');

            Route::get('/top-10-farmers', [GeneralController::class, 'topTenForFarmers'])->name('top-ten-farmers.get');
            Route::get('/count-planted', [GeneralController::class, 'countPlanted'])->name('count-planted.get');
            Route::get('/planted-crops', [GeneralController::class, 'indexPlantedCrops'])->name('planted-crops.index');
            Route::get('/active-gardens', [GeneralController::class, 'activeGardensMonths'])->name('active-gardens.index');

            // Map
            Route::get('map-gardens', [GeneralController::class, 'getGardens'])->name('map.get');
            Route::get('map-gardens/{id}', [GeneralController::class, 'showGarden'])->name('map.show');
        });
    });
});

require __DIR__ . '/auth.php';
