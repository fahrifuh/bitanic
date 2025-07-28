<!-- Menu -->

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="#" class="app-brand-link">
            <span class="app-brand-logo demo">
                <x-application-logo class="block h-10 fill-current text-gray-600" />
            </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li @class(['menu-item', 'active' => Request::is('dashboard*')])>
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Utama</span>
        </li>
        @if (auth()->user()->role == 'admin')
            <li @class([
                'menu-item',
                'active open' =>
                    !Request::is('bitanic/farmer-group*') &&
                    (Request::is('bitanic/lite-user*') || Request::is('bitanic/farmer*')),
            ])>

                {{-- <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-box"></i>
                    <div data-i18n="Basic">Pengguna</div>
                </a>
                <ul class="menu-sub">
                    <li @class([
                        'menu-item',
                        'active' =>
                            !Request::is('bitanic/farmer-group*') && Request::is('bitanic/farmer*'),
                    ])>
                        <a href="{{ route('bitanic.farmer.index') }}" class="menu-link">
                            <div data-i18n="Without menu">Pengguna Bitanic</div>
                        </a>
                    </li>
                    <li @class(['menu-item', 'active' => Request::is('bitanic/lite-user*')])>
                        <a href="{{ route('bitanic.lite-user.index') }}" class="menu-link">
                            <div data-i18n="Without navbar">Pengguna Bitanic Lite</div>
                        </a>
                    </li>
                </ul> --}}
                <a href="{{ route('bitanic.farmer.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-user-circle"></i>
                    <div data-i18n="Basic">Pengguna Bitanic</div>
                </a>
            </li>
            @if (request()->getHost() != 'control.bitanic.id')
                <li class="menu-item {{ Request::is('bitanic/farmer-group*') ? 'active' : false }}">
                    <a href="{{ route('bitanic.farmer-group.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-group"></i>
                        <div data-i18n="Basic">Kelompok Petani</div>
                    </a>
                </li>
            @endif
        @elseif (auth()->user()->role == 'farmer')
            <li class="menu-item {{ Request::is('*/land*') ? 'active' : false }}">
                <a href="{{ route('bitanic.land.index', ['farmer' => auth()->user()->farmer->id]) }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-shape-square"></i>
                    <div data-i18n="Basic">Lahan</div>
                </a>
            </li>
            <li @class([
                'menu-item',
                'active open' =>
                    Request::is('bitanic/pest*') ||
                    Request::is('bitanic/invected-gardens*'),
            ])>
                <a href="{{ route('bitanic.pest.index') }}" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-bug"></i>
                    <div data-i18n="Basic">Manajemen Hama & Penyakit</div>
                </a>
                <ul class="menu-sub">
                    <li @class(['menu-item', 'active' => Request::is('bitanic/pest*')])>
                        <a href="{{ route('bitanic.pest.index') }}" class="menu-link">
                            <div data-i18n="Without menu">Data Master Hama</div>
                        </a>
                    </li>
                    <li @class([
                        'menu-item',
                        'active' => Request::is('bitanic/invected-gardens*'),
                    ])>
                        <a href="{{ route('bitanic.invected-gardens.index') }}" class="menu-link">
                            <div data-i18n="Without navbar">Kebun Terinfeksi</div>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item {{ Request::is('bitanic/shop*') ? 'active' : false }}">
                <a href="{{ route('bitanic.shop.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-store-alt"></i>
                    <div data-i18n="Basic">Toko</div>
                </a>
            </li>
            <li class="menu-item {{ Request::is('bitanic/formula*') ? 'active' : false }}">
                <a href="{{ route('bitanic.formula.create') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-water"></i>
                    <div data-i18n="Basic">Formula</div>
                </a>
            </li>
        @endif
        @if (auth()->user()->role == 'admin')
            @if (request()->getHost() != 'control.bitanic.id')
                {{-- <li @class([
                        'menu-item',
                        'active open' =>
                            Request::is('bitanic/crop*') || Request::is('bitanic/crop-for-sale*'),
                    ])>
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-grid"></i>
                            <div data-i18n="Basic">Manajemen Tanaman</div>
                        </a>
                        <ul class="menu-sub">
                            <li @class([
                                'menu-item',
                                'active' =>
                                    Request::is('bitanic/crop*') && !Request::is('bitanic/crop-for-sale*'),
                            ])>
                                <a href="{{ route('bitanic.crop.index') }}" class="menu-link">
                                    <div data-i18n="Without menu">Tanaman yang Ditanam</div>
                                </a>
                            </li>
                            <li @class([
                                'menu-item',
                                'active' => Request::is('bitanic/crop-for-sale*'),
                            ])>
                                <a href="{{ route('bitanic.crop-for-sale.index') }}" class="menu-link">
                                    <div data-i18n="Without navbar">Komoditi yang Dijual</div>
                                </a>
                            </li>
                        </ul>
                    </li> --}}

                {{-- <li @class([
                        'menu-item',
                        'active open' =>
                            Request::is('bitanic/pest*') ||
                            Request::is('bitanic/invected-gardens*'),
                    ])>
                        <a href="{{ route('bitanic.pest.index') }}" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-bug"></i>
                            <div data-i18n="Basic">Manajemen Hama & Penyakit</div>
                        </a>
                        <ul class="menu-sub">
                            <li @class(['menu-item', 'active' => Request::is('bitanic/pest*')])>
                                <a href="{{ route('bitanic.pest.index') }}" class="menu-link">
                                    <div data-i18n="Without menu">Data Master Hama</div>
                                </a>
                            </li>
                            <li @class([
                                'menu-item',
                                'active' => Request::is('bitanic/invected-gardens*'),
                            ])>
                                <a href="{{ route('bitanic.invected-gardens.index') }}" class="menu-link">
                                    <div data-i18n="Without navbar">Kebun Terinfeksi</div>
                                </a>
                            </li>
                        </ul>
                    </li> --}}
            @endif
        @endif
        @if (request()->getHost() == 'control.bitanic.id')
            <li @class([
                'menu-item',
                'active open' =>
                    Request::is('bitanic/device*') ||
                    Request::is('bitanic/v3/device*') ||
                    Request::is('bitanic/lite-device*'),
            ])>
                <a href="{{ route('bitanic.pest.index') }}" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-chip"></i>
                    <div data-i18n="Basic">Manajemen Perangkat</div>
                </a>
                <ul class="menu-sub">
                    <li @class([
                        'menu-item',
                        'active' =>
                            Request::is('bitanic/device*') || Request::is('bitanic/v3/device*'),
                    ])>
                        <a href="{{ route('bitanic.device.index') }}" class="menu-link">
                            <div data-i18n="Without menu">Bitanic Pro & RSC</div>
                        </a>
                    </li>
                    @if (auth()->user()->role == 'admin')
                        <li @class(['menu-item', 'active' => Request::is('bitanic/lite-device*')])>
                            <a href="{{ route('bitanic.lite-device.index') }}" class="menu-link">
                                <div data-i18n="Without navbar">Bitanic Lite</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif
        @if (auth()->user()->role == 'admin')
            {{-- <li @class([
                    'menu-item',
                    'active open' =>
                        Request::is('bitanic/formula*') ||
                        Request::is('bitanic/necessity-difference*') ||
                        Request::is('bitanic/interpretation*'),
                ])>
                    <a href="{{ route('bitanic.pest.index') }}" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-water"></i>
                        <div data-i18n="Basic">Manajemen Pemupukan</div>
                    </a>
                    <ul class="menu-sub">
                        <li @class(['menu-item', 'active' => Request::is('bitanic/formula*')])>
                            <a href="{{ route('bitanic.formula.index') }}" class="menu-link">
                                <div data-i18n="Without menu">Formula</div>
                            </a>
                        </li>
                        <li @class([
                            'menu-item',
                            'active' => Request::is('bitanic/interpretation*'),
                        ])>
                            <a href="{{ route('bitanic.interpretation.index') }}" class="menu-link">
                                <div data-i18n="Without navbar">Interpretasi</div>
                            </a>
                        </li>
                        <li @class([
                            'menu-item',
                            'active' => Request::is('bitanic/necessity-difference*'),
                        ])>
                            <a href="{{ route('bitanic.necessity-difference.index') }}" class="menu-link">
                                <div data-i18n="Without navbar">Kebutuhan Dolomit</div>
                            </a>
                        </li>
                    </ul>
                </li> --}}
            @if (request()->getHost() != 'control.bitanic.id')
                <li @class([
                    'menu-item',
                    'active open' =>
                        Request::is('bitanic/investor*') ||
                        Request::is('bitanic/seller*') ||
                        Request::is('bitanic/researcher*') ||
                        Request::is('bitanic/partner*'),
                ])>
                    <a href="{{ route('bitanic.pest.index') }}" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-dollar"></i>
                        <div data-i18n="Basic">Kemitraan & Investasi</div>
                    </a>
                    <ul class="menu-sub">
                        <li @class(['menu-item', 'active' => Request::is('bitanic/investor*')])>
                            <a href="{{ route('bitanic.investor.index') }}" class="menu-link">
                                <div data-i18n="Without menu">Investor</div>
                            </a>
                        </li>
                        <li @class(['menu-item', 'active' => Request::is('bitanic/seller*')])>
                            <a href="{{ route('bitanic.seller.index') }}" class="menu-link">
                                <div data-i18n="Without navbar">Mitra Bisnis</div>
                            </a>
                        </li>
                        <li @class(['menu-item', 'active' => Request::is('bitanic/partner*')])>
                            <a href="{{ route('bitanic.partner.index') }}" class="menu-link">
                                <div data-i18n="Without navbar">Mitra Strategi</div>
                            </a>
                        </li>
                        <li @class(['menu-item', 'active' => Request::is('bitanic/researcher*')])>
                            <a href="{{ route('bitanic.researcher.index') }}" class="menu-link">
                                <div data-i18n="Without navbar">Peneliti</div>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="menu-item {{ Request::is('bitanic/bitanic-product*') ? 'active' : false }}">
                    <a href="{{ route('bitanic.bitanic-product.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-package"></i>
                        <div data-i18n="Basic">Produk Bitanic</div>
                    </a>
                </li>
                <li @class([
                    'menu-item',
                    'active open' =>
                        (Request::is('bitanic/transaction*') &&
                            !Request::is('bitanic/transaction-setting*')) ||
                        Request::is('bitanic/bank*') ||
                        Request::is('bitanic/withdrawal-bank*') ||
                        Request::is('bitanic/transaction-komoditi*') ||
                        Request::is('bitanic/admin/balance-withdraw*'),
                ])>
                    <a href="{{ route('bitanic.pest.index') }}" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bxs-bank"></i>
                        <div data-i18n="Basic">Manajemen Keuangan & Transaksi</div>
                    </a>
                    <ul class="menu-sub">
                        <li @class(['menu-item', 'active' => Request::is('bitanic/bank*')])>
                            <a href="{{ route('bitanic.bank.index') }}" class="menu-link">
                                <div data-i18n="Without menu">Bank</div>
                            </a>
                        </li>
                        <li @class([
                            'menu-item',
                            'active' => Request::is('bitanic/withdrawal-bank*'),
                        ])>
                            <a href="{{ route('bitanic.withdrawal-bank.index') }}" class="menu-link">
                                <div data-i18n="Without navbar">Bank Penarikan</div>
                            </a>
                        </li>
                        <li @class([
                            'menu-item',
                            'active' =>
                                Request::is('bitanic/transaction*') &&
                                !Request::is('bitanic/transaction-komoditi*') &&
                                !Request::is('bitanic/transaction-setting*'),
                        ])>
                            <a href="{{ route('bitanic.transaction.index') }}" class="menu-link">
                                <div data-i18n="Without navbar">Transaksi Produk Bitanic</div>
                            </a>
                        </li>
                        <li @class([
                            'menu-item',
                            'active' => Request::is('bitanic/transaction-komoditi*'),
                        ])>
                            <a href="{{ route('bitanic.transaction-komodity.index') }}" class="menu-link">
                                <div data-i18n="Without navbar">Transaksi Komoditi</div>
                            </a>
                        </li>
                        <li @class([
                            'menu-item',
                            'active' => Request::is('bitanic/admin/balance-withdraw*'),
                        ])>
                            <a href="{{ route('bitanic.admin.balance-withdraw.index') }}" class="menu-link">
                                <div data-i18n="Without navbar">Penarikan Saldo</div>
                            </a>
                        </li>
                    </ul>
                </li>
                <li @class([
                    'menu-item',
                    'active open' =>
                        Request::is('bitanic/member*') || Request::is('bitanic/feedback*'),
                ])>
                    <a href="{{ route('bitanic.pest.index') }}" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-id-card"></i>
                        <div data-i18n="Basic">Manajemen User</div>
                    </a>
                    <ul class="menu-sub">
                        <li @class(['menu-item', 'active' => Request::is('bitanic/member*')])>
                            <a href="{{ route('bitanic.member.index') }}" class="menu-link">
                                <div data-i18n="Without navbar">Kategori Member</div>
                            </a>
                        </li>
                        <li @class(['menu-item', 'active' => Request::is('bitanic/feedback*')])>
                            <a href="{{ route('bitanic.feedback-regular.index') }}" class="menu-link">
                                <div data-i18n="Without navbar">Ulasan User</div>
                            </a>
                        </li>
                    </ul>
                </li>
                <li @class(['menu-item', 'active open' => Request::is('bitanic/ktp*')])>
                    <a href="#" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-id-card"></i>
                        <div data-i18n="Basic">Manajemen KTP</div>
                    </a>
                    <ul class="menu-sub">
                        <li @class(['menu-item', 'active' => Request::is('bitanic/ktp/farmer*')])>
                            <a href="{{ route('bitanic.ktp-farmer.index') }}" class="menu-link">
                                <div data-i18n="Without navbar">KTP Pengguna</div>
                            </a>
                        </li>
                        <li @class(['menu-item', 'active' => Request::is('bitanic/ktp/shop*')])>
                            <a href="{{ route('bitanic.ktp-shop.index') }}" class="menu-link">
                                <div data-i18n="Without navbar">KTP Toko</div>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
        @endif
        @if (auth()->user()->role == 'admin')
            @if (request()->getHost() == 'control.bitanic.id')
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">Hidroponik</span>
                </li>
                <li class="menu-item {{ Request::is('bitanic/hydroponic/user*') ? 'active' : false }}">
                    <a href="{{ route('bitanic.hydroponic.user.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-dollar"></i>
                        <div data-i18n="Basic">User</div>
                    </a>
                </li>
                <li class="menu-item {{ Request::is('bitanic/hydroponic/device*') ? 'active' : false }}">
                    <a href="{{ route('bitanic.hydroponic.device.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-dollar"></i>
                        <div data-i18n="Basic">Perangkat</div>
                    </a>
                </li>
            @endif

            @if (request()->getHost() != 'control.bitanic.id')
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">Wilayah</span>
                </li>
                <li @class([
                    'menu-item',
                    'active open' =>
                        Request::is('bitanic/province*') ||
                        Request::is('bitanic/city*') ||
                        Request::is('bitanic/district*') ||
                        Request::is('bitanic/subdistrict*'),
                ])>
                    <a href="{{ route('bitanic.pest.index') }}" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-map-alt"></i>
                        <div data-i18n="Basic">Data Wilayah</div>
                    </a>
                    <ul class="menu-sub">
                        @if (!auth()->user()->city_id)
                            <li @class(['menu-item', 'active' => Request::is('bitanic/province*')])>
                                <a href="{{ route('bitanic.province.index') }}" class="menu-link">
                                    <div data-i18n="Without menu">Provinsi</div>
                                </a>
                            </li>
                            <li @class(['menu-item', 'active' => Request::is('bitanic/city*')])>
                                <a href="{{ route('bitanic.city.index') }}" class="menu-link">
                                    <div data-i18n="Without navbar">Kabupaten/Kota</div>
                                </a>
                            </li>
                        @endif
                        <li @class(['menu-item', 'active' => Request::is('bitanic/district*')])>
                            <a href="{{ route('bitanic.district.index') }}" class="menu-link">
                                <div data-i18n="Without navbar">Kecamatan</div>
                            </a>
                        </li>
                        <li @class(['menu-item', 'active' => Request::is('bitanic/subdistrict*')])>
                            <a href="{{ route('bitanic.subdistrict.index') }}" class="menu-link">
                                <div data-i18n="Without navbar">Desa</div>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
        @endif
        @if (auth()->user()->role == 'farmer')
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Bitanic+</span>
            </li>
            <li class="menu-item {{ Request::is('bitanic/member*') ? 'active' : false }}">
                <a href="{{ route('bitanic.member.current') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-id-card"></i>
                    <div data-i18n="Basic">Kelola Membermu</div>
                </a>
            </li>
        @endif
        @if (auth()->user()->role == 'admin' && request()->getHost() != 'control.bitanic.id')
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Marketplace</span>
            </li>
            <li class="menu-item {{ Request::is('bitanic/marketplace/user-product*') ? 'active' : false }}">
                <a href="{{ route('bitanic.user-product.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-id-card"></i>
                    <div data-i18n="Basic">Produk Pengguna</div>
                </a>
            </li>
        @endif
        @if (auth()->user()->role == 'admin' && !auth()->user()->city_id)
            @if (request()->getHost() != 'control.bitanic.id')
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">Mobile</span>
                </li>
                <li @class([
                    'menu-item',
                    'active open' =>
                        Request::is('bitanic/advertisement*') ||
                        Request::is('bitanic/article*'),
                ])>
                    <a href="{{ route('bitanic.pest.index') }}" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bxs-devices"></i>
                        <div data-i18n="Basic">Konten di Aplikasi Mobile</div>
                    </a>
                    <ul class="menu-sub">
                        <li @class([
                            'menu-item',
                            'active' => Request::is('bitanic/advertisement*'),
                        ])>
                            <a href="{{ route('bitanic.advertisement.index') }}" class="menu-link">
                                <div data-i18n="Without navbar">Iklan</div>
                            </a>
                        </li>
                        <li @class(['menu-item', 'active' => Request::is('bitanic/article*')])>
                            <a href="{{ route('bitanic.article.index') }}" class="menu-link">
                                <div data-i18n="Without navbar">Artikel</div>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Pengaturan</span>
            </li>
            <li @class([
                'menu-item',
                'active open' =>
                    Request::is('bitanic/firmware*') ||
                    Request::is('bitanic/account-delete-request*') ||
                    Request::is('bitanic/transaction-setting*'),
            ])>
                <a href="{{ route('bitanic.pest.index') }}" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-cog"></i>
                    <div data-i18n="Basic">Pengaturan</div>
                </a>
                <ul class="menu-sub">
                    @if (request()->getHost() == 'control.bitanic.id')
                        <li @class(['menu-item', 'active' => Request::is('bitanic/firmware*')])>
                            <a href="{{ route('bitanic.firmware.index') }}" class="menu-link">
                                <div data-i18n="Without navbar">Update Firmware</div>
                            </a>
                        </li>
                    @endif
                    @if (request()->getHost() != 'control.bitanic.id')
                        <li @class([
                            'menu-item',
                            'active' => Request::is('bitanic/transaction-setting*'),
                        ])>
                            <a href="{{ route('bitanic.transaction-setting.index') }}" class="menu-link">
                                <div data-i18n="Without navbar">Setting Transaksi</div>
                            </a>
                        </li>
                        <li @class([
                            'menu-item',
                            'active' => Request::is('bitanic/account-delete-request*'),
                        ])>
                            <a href="{{ route('bitanic.account-delete-request.index') }}" class="menu-link">
                                <div data-i18n="Without navbar">Permintaan Hapus Akun</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
            <li class="menu-item {{ Request::is('bitanic/log-activity*') ? 'active' : false }}">
                <a href="{{ route('bitanic.log-activity.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-box"></i>
                    <div data-i18n="Basic">Log Aktivitas</div>
                </a>
            </li>
            @if (request()->getHost() != 'control.bitanic.id')
                @php
                    $countData = App\Models\ContactUsMessage::where('status', 0)->count();
                @endphp
                <li class="menu-item {{ Request::is('bitanic/contact-us-message*') ? 'active' : false }}">
                    <a href="{{ route('bitanic.contact-us-message.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-message-dots"></i>
                        <div data-i18n="Basic">
                            Pesan dari Kontak Kami
                            @if ($countData > 0)
                                <span class="badge bg-secondary rounded-pill">{{ $countData }}</span>
                            @endif
                        </div>
                    </a>
                </li>
            @endif
            {{-- @if (request()->getHost() != 'control.bitanic.id')
                <li class="menu-item {{ Request::is('bitanic/landing-setting*') ? 'open' : false }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-dock-top"></i>
                        <div data-i18n="Account Settings">Pengaturan Halaman Utama</div>
                    </a>
                    <ul class="menu-sub">
                        <li
                            class="menu-item {{ Request::is('bitanic/landing-setting/product-setting*') ? 'active' : false }}">
                            <a href="{{ route('bitanic.product-setting.index') }}" class="menu-link">
                                <div data-i18n="Account">Produk</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ Request::is('bitanic/landing-setting/about-our-startup-setting*') ? 'active' : false }}">
                            <a href="{{ route('bitanic.about-our-startup-setting.index') }}" class="menu-link">
                                <div data-i18n="Account">Tentang Startup</div>
                            </a>
                        </li>
                        <li
                            class="menu-item {{ Request::is('bitanic/landing-setting/contact-us-setting*') ? 'active' : false }}">
                            <a href="{{ route('bitanic.contact-us-setting.index') }}" class="menu-link">
                                <div data-i18n="Account">Kontak Kami</div>
                            </a>
                        </li>
                        <li class="menu-item {{ Request::is('bitanic/landing-setting/career*') ? 'active' : false }}">
                            <a href="{{ route('bitanic.career.index') }}" class="menu-link">
                                <div data-i18n="Account">Karir</div>
                            </a>
                        </li>
                        <li class="menu-item {{ Request::is('bitanic/landing-setting/service*') ? 'active' : false }}">
                            <a href="{{ route('bitanic.service.index') }}" class="menu-link">
                                <div data-i18n="Account">Layanan</div>
                            </a>
                        </li>
                        <li class="menu-item {{ Request::is('bitanic/landing-setting/product*') ? 'active' : false }}">
                            <a href="{{ route('bitanic.product.index') }}" class="menu-link">
                                <div data-i18n="Account">Produk</div>
                            </a>
                        </li>
                        <li class="menu-item {{ Request::is('bitanic/landing-setting/gallery*') ? 'active' : false }}">
                            <a href="{{ route('bitanic.gallery.index') }}" class="menu-link">
                                <div data-i18n="Account">Galeri</div>
                            </a>
                        </li>
                        <li class="menu-item {{ Request::is('bitanic/landing-setting/testimony*') ? 'active' : false }}">
                            <a href="{{ route('bitanic.testimony.index') }}" class="menu-link">
                                <div data-i18n="Account">Testimoni</div>
                            </a>
                        </li>
                        <li class="menu-item {{ Request::is('bitanic/landing-setting/faq*') ? 'active' : false }}">
                            <a href="{{ route('bitanic.faq.index') }}" class="menu-link">
                                <div data-i18n="Account">FAQ</div>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif --}}
        @endif
    </ul>
</aside>
<!-- / Menu -->
