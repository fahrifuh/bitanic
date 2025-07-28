<main id="mainHome">
    <!-- Modal History Detail-->
    <div class="modal fade custom-mt-modal" id="detailHistoryModal" tabindex="-1" aria-label="Tentang Kami"
        aria-hidden="true" style="z-index: 99999; height: 92vh;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <img src="" alt="Thumb" height="250" width="100%" id="detailHistoryImage"
                    class="object-fit-contain">
                {{-- <div class="modal-header">
                    <h1 class="modal-title fs-5" id="detailProductTitle"></h1>
                </div> --}}
                <div class="modal-body">
                    {{-- <p class="text-bitanic fw-semibold fs-5" id="detailProductPrice"></p> --}}
                    <div id="detailHistoryDescription"></div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Product Detail-->
    <div class="modal fade custom-mt-modal" id="detailProductModal" tabindex="-1" aria-labelledby="detailProductTitle"
        aria-hidden="true" style="z-index: 99999; height: 92vh;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <img src="" alt="Thumb" height="250" width="100%" id="detailProductThumbnail"
                    class="object-fit-contain">
                <div class="modal-header justify-content-center">
                    <h1 class="modal-title fs-5 fw-semibold" id="detailProductTitle"></h1>
                </div>
                <div class="modal-body">
                    <div id="detailProductDescription"></div>
                </div>
                <div class="modal-footer">
                    <a href="#form-contact" id="contact-now"
                        class="text-decoration-none text-dark fw-semibold me-auto">Anda
                        tertarik? <span class="text-bitanic fw-semibold text-decoration-underline">Hubungi kami
                            sekarang!</span> </a>
                    <button type="button" class="btn btn-secondary ms-auto" data-bs-dismiss="modal">Kembali</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Article Detail -->
    <div class="modal fade custom-mt-modal" id="detailArticleModal" tabindex="-1" aria-labelledby="detailArticleLabel"
        aria-hidden="true" style="z-index: 99999; height: 92vh;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header flex-column align-items-start">
                    <h1 class="modal-title fs-5" id="detailArticleLabel"></h1>
                    <div class="d-flex align-items-center gap-2">
                        <p class="text-muted m-0" id="detailArticleDate"></p>
                        <span class="fw-bold fs-3">&CenterDot;</span>
                        <p id="detailArticleWriter" class="m-0"></p>
                        <span class="fw-bold fs-3">&CenterDot;</span>
                        <span class="badge rounded-pill bg-bitanic" id="detailArticleType"></span>
                    </div>
                </div>
                <div class="modal-body">
                    <img src="" alt="Thumbnail" height="250" width="100%" class="img-fluid"
                        id="detailArticleThumbnail">
                    <div id="detailArticleDescription" class="mt-4"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                </div>
            </div>
        </div>
    </div>

    <section class="d-flex justify-content-center align-items-center" id="banner">
        <div id="heroCarousel" class="carousel slide position-relative" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active px-5">
                    <div class="d-flex align-items-center">
                        <!-- TEXT BLOCK -->
                        <div class="carousel-text p-5">
                            <h1 class="fw-bold lh-sm mb-2">
                                <span class="fw-bold">Precision <span class="text-bitanic fw-bold">Agriculture</span>
                                </span>
                                <span class="text-bitanic fw-bold">Ecosystem</span>
                            </h1>
                            <p class="text-muted fs-6 lh-sm">Bersama <span class="text-bitanic fw-bold">Bitanic</span>
                                petani
                                dapat
                                meningkatkan efisiensi, produktivitas dan profitabilitas dengan atau tanpa internet dan
                                sumber daya listrik yang terbatas.</p>
                            <a href="#about" class="btn btn-bitanic mt-3">
                                <div class="d-flex align-items-center gap-3 fw-bold">
                                    Selengkapnya <i class="bi bi-arrow-right-circle-fill ms-1 text-warning fs-4"></i>
                                </div>
                            </a>
                        </div>
                        <!-- IMAGE BLOCK -->
                        <div class="carousel-img position-relative flex-fill d-none d-sm-flex">
                            <img src="{{ asset('bitanic-landing/carousel-image-1.webp') }}" class="img-fluid"
                                alt="Slide 1" loading="eager" width="700" height="429" fetchpriority="high">
                        </div>
                    </div>
                </div>
                <div class="carousel-item px-5">
                    <div class="d-flex align-items-center">
                        <!-- TEXT BLOCK -->
                        <div class="carousel-text p-5">
                            <h1 class="fw-bold lh-sm mb-2">
                                <span class="fw-bold"><span class="fw-bold text-bitanic">Real-time</span>
                                    Monitoring</span>
                                <span class="fw-bold">& Control</span>
                            </h1>
                            <p class="text-muted fs-6 lh-sm">Pantau dan kendalikan Soil Checker serta pompa air secara
                                langsung melalui aplikasi Bitanic.</p>
                            <a href="#about" class="btn btn-bitanic mt-3">
                                <div class="d-flex align-items-center gap-3 fw-bold">
                                    Selengkapnya <i class="bi bi-arrow-right-circle-fill ms-1 text-warning fs-4"></i>
                                </div>
                            </a>
                        </div>
                        <!-- IMAGE BLOCK -->
                        <div class="carousel-img position-relative flex-fill d-none d-sm-flex">
                            <img src="{{ asset('bitanic-landing/carousel-image-2.webp') }}" class="img-fluid"
                                alt="Slide 2" loading="eager" width="800" height="340" fetchpriority="high">
                        </div>
                    </div>
                </div>
                <div class="carousel-item px-5">
                    <div class="d-flex align-items-center">
                        <!-- TEXT BLOCK -->
                        <div class="carousel-text p-5">
                            <h1 class="fw-bold lh-sm mb-2">
                                <span class="fw-bold"><span class="fw-bold text-bitanic">Smart Farming</span>
                                    for</span>
                                <span class="fw-bold">Everyone</span>
                            </h1>
                            <p class="text-muted fs-6 lh-sm">Teknologi pertanian cerdas yang mudah digunakan oleh siapa
                                pun—mulai dari petani hingga institusi.</p>
                            <a href="#about" class="btn btn-bitanic mt-3">
                                <div class="d-flex align-items-center gap-3 fw-bold">
                                    Selengkapnya <i class="bi bi-arrow-right-circle-fill ms-1 text-warning fs-4"></i>
                                </div>
                            </a>
                        </div>
                        <!-- IMAGE BLOCK -->
                        <div class="carousel-img position-relative flex-fill d-none d-sm-flex">
                            <img src="{{ asset('bitanic-landing/carousel-image-3.webp') }}" class="img-fluid"
                                alt="Slide 3" loading="eager" width="800" fetchpriority="high" height="341">
                        </div>
                    </div>
                </div>

            </div>

            <!-- custom arrows -->
            <button class="carousel-control-prev custom-prev" type="button" data-bs-target="#heroCarousel"
                data-bs-slide="prev">
                <i class="bi bi-arrow-left-circle-fill fs-2"></i>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next custom-next" type="button" data-bs-target="#heroCarousel"
                data-bs-slide="next">
                <i class="bi bi-arrow-right-circle-fill fs-2"></i>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section>
    <section id="product" class="py-3 px-4 mb-2 h-auto">
        {{-- <div class="d-flex item-product justify-content-center align-items-center">
            <div class="product-img-container">
                <img src="{{ asset('bitanic-landing/Frame1967.png') }}" alt="" width="100" loading="lazy">
            </div>
        </div>
        <div class="d-flex" id="bitanicApp">
            <div class="download">
                <h4 class="mb-4">Unduh Aplikasi <span style="color:var(--vt-c-base2); font-weight:bold">Bitanic</span>
                    Kita Sekarang!</h4>
                <div class="d-flex">
                    <div class="gplay me-3">
                        <a href="https://play.google.com/store/apps/details?id=com.makerindo.bitanic">
                            <img src="{{ asset('bitanic-landing/gplay.png') }}" alt="" width="100"
                                loading="lazy">
                        </a>
                    </div>
                </div>
            </div>
            <img src="{{ asset('bitanic-landing/mobile_2.png') }}" alt="" class="mockup" loading="lazy">
        </div>
        <div class="d-flex" id="bitanicWeb">
            <div class="download">
                <h3 class="mb-1">Website Bitanic</h3>
                <div class="d-flex">
                    <p>Monitoring & Kontrol Dashboard</p>
                </div>
            </div>

            <img src="{{ asset('bitanic-landing/web-2-new.png') }}" alt="" class="mockup" loading="lazy">
            <img src="{{ asset('bitanic-landing/decor.png') }}" alt="" width="600" class="decor"
                loading="lazy">
        </div> --}}
        <div class="card rounded-5 bg-bitanic py-4 mx-auto">
            <h2 class="text-white text-center fw-bold">Bitanic Produk</h2>
            <p class="text-center text-white fs-6 fw-light px-2 px-md-3 m-0">Temukan berbagai fitur unggulan Bitanic
                yang
                dirancang
                untuk
                mendukung pertanian cerdas dan efisien.</p>
            <div class="row mx-auto g-5 px-md-3 mt-2">
                @forelse ($bitanicProducts as $product)
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100">
                            <img src="{{ asset($product->picture) }}" class="card-img-top object-fit-contain"
                                width="100%" height="250" alt="Product Image" loading="lazy">
                            <div class="card-body h-100 d-flex flex-column justify-content-between gap-4">
                                <div>
                                    <span class="card-title fw-bolder fs-4 lh-base">{{ $product->name }}</span>
                                    <p class="card-text text-truncate-3 mt-2">{!! strip_tags($product->description) !!}</p>
                                </div>
                                <button type="button" data-bs-toggle="modal" data-bs-target="#detailProductModal"
                                    data-title="{{ $product->name }}"
                                    data-image="{{ asset($product->picture) }}"
                                    data-description="{!! $product->description !!}"
                                    class="btn btn-bitanic fw-bold d-flex align-items-center detail-product-toggle w-auto ms-auto">
                                    Lihat detail
                                    <i class="bi bi-arrow-right-circle-fill fs-4 ms-2 text-warning"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-white text-center fs-4">Belum Ada Produk Yang Tersedia.</p>
                @endforelse
            </div>
        </div>
    </section>
    <section class="py-3 px-4 mb-5 mx-md-5 mx-auto h-auto" id="mitra-icons">
        <h2 class="fw-bold text-bitanic">Mitra Bitanic</h2>
        <p class="fs-6">Bitanic bekerja sama dengan berbagai mitra strategis, mulai dari komunitas petani hingga
            institusi pendidikan dan swasta, untuk menghadirkan solusi pertanian cerdas yang berkelanjutan dan berdampak
            nyata.</p>
        <div class="border border-3 rounded-pill" style="width: 10%; border-color: #3e8f55 !important;"></div>
        <div class="row g-4 mt-3">
            @foreach ($mitra as $item)
                <div class="col-lg-4 col-md-6">
                    <div
                        class="card border border-3 rounded-5 align-items-center justify-content-center gap-4 px-2 py-3 h-100">
                        <img src="{{ $item->picture ? asset($item->picture) : asset('bitanic-photo/dummy-image.png') }}"
                            alt="Logo IPB University" class="img-fluid" width="100" height="100"
                            loading="lazy">
                        <span class="fw-semibold fs-5 text-center">{{ $item->name }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    <section class="mb-5 mx-md-5 mx-auto h-auto" id="about">
        {{-- <div class="d-flex flex-wrap justify-content-center">
            <div class="m-3 d-flex justify-content-center">
                <div id="carouselExampleControls" class="carousel slide img-about" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach ($about_our_startup->event_images as $event_image)
                            <div class="carousel-item @if ($loop->first) active @endif">
                                <img src="{{ asset($event_image) }}" class="d-block w-100" loading="lazy"
                                    alt="">
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
            <div class="m-3 d-flex justify-content-center">
                <div class="about-card d-flex align-items-center">
                    <div>
                        <h2>
                            Tentang <b>Startup</b>
                        </h2>
                        <p>
                            {{ $about_our_startup->description }}
                        </p>
                    </div>
                </div>
            </div>
        </div> --}}
        <h2 class="text-bitanic fw-bold text-center">Tentang Kami</h2>
        <p class="text-center fs-6">Bersama Bitanic petani dapat meningkatkan efisiensi, produktivitas dan
            profitabilitas
            dengan atau tanpa internet dan sumber daya listrik yang terbatas.</p>
        <div class="d-flex flex-lg-row flex-column gap-5 my-5">
            <img src="{{ asset($latestHistory[1]->picture) }}" alt="Image 1" class="rounded rounded-3 img-fluid"
                width="500" height="333" loading="lazy">
            <div class="d-flex flex-column">
                <p class="text-truncate-3">{!! strip_tags($latestHistory[1]->description) !!}</p>
                <button type="button" data-bs-toggle="modal" data-bs-target="#detailHistoryModal"
                    data-image="{{ asset($latestHistory[1]->picture) }}" data-description="{!! $latestHistory[1]->description !!}"
                    class="btn btn-bitanic fw-bold d-flex align-items-center w-auto me-auto detail-history-toggle">
                    Selengkapnya
                    <i class="bi bi-arrow-right-circle-fill fs-4 ms-2 text-warning"></i>
                </button>
            </div>
        </div>
        <div class="d-flex flex-lg-row flex-column my-5 gap-4">
            <span class="text-rotate text-center fw-bold ps-2 fs-3 text-bitanic">Our Mission</span>
            <div>
                {!! $latestVisiMisi->description !!}
            </div>
            <img src="{{ asset($latestVisiMisi->picture) }}" alt="Image 2" width="500" height="333"
                class="object-fit-contain my-auto img-fluid" loading="lazy">
        </div>
        <div class="d-flex flex-lg-row flex-column gap-5 my-5">
            <img src="{{ asset($latestHistory[0]->picture) }}" alt="Image 1" width="500" height="333"
                class="rounded rounded-3 img-fluid" loading="lazy">
            <div class="d-flex flex-column">
                <p class="text-truncate-3">{!! strip_tags($latestHistory[0]->description) !!}</p>
                <button type="button" data-bs-toggle="modal" data-bs-target="#detailHistoryModal"
                    data-image="{{ asset($latestHistory[0]->picture) }}" data-description="{!! $latestHistory[0]->description !!}"
                    class="btn btn-bitanic fw-bold d-flex align-items-center w-auto me-auto detail-history-toggle">
                    Selengkapnya
                    <i class="bi bi-arrow-right-circle-fill fs-4 ms-2 text-warning"></i>
                </button>
            </div>
        </div>
    </section>
    <section class="py-3 px-lg-4 px-2 mx-md-5 mx-sm-3 mx-2 mb-5 h-auto" id="article">
        <h2 class="fw-bold text-bitanic">Artikel</h2>
        <p class="lh-sm">Temukan berbagai artikel informatif seputar teknologi pertanian, inovasi digital, kisah
            sukses petani, serta
            update kegiatan dan riset Bitanic. Semua disajikan untuk memperluas wawasan dan menginspirasi transformasi
            pertanian masa depan.</p>
        <ul class="nav nav-tabs border-0 mb-4" id="articleTabs">
            <li class="nav-item">
                <a href="#" class="nav-link text-dark active" data-tab="semua">Semua Artikel</a>
            </li>
            @foreach ($articleTypes as $type)
                <li class="nav-item">
                    <a href="#" class="nav-link text-dark" data-tab="{{ $type }}">
                        {{ ucfirst(str_replace('_', ' ', $type)) }}
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="article-content" data-section="semua">
            <div class="row g-4">
                @forelse($articles->whereIn('type', ['sayuran', 'buah', 'umum'])->take(6) as $article)
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 rounded-5 border border-dark">
                            <img src="{{ asset($article->picture) }}" alt="Thumbnail"
                                class="rounded-top-5 article-thumbnail" loading="lazy" width="100%"
                                height="250">
                            <div class="h-100 d-flex flex-column justify-content-between mx-3 my-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span>{{ $article->created_at->translatedFormat('d F Y ') }}</span>
                                    <span class="fw-bold fs-3">&CenterDot;</span>
                                    <span>{{ $article->writer }}</span>
                                    <span class="fw-bold fs-3">&CenterDot;</span>
                                    <span class="badge rounded-pill bg-bitanic">{{ ucfirst($article->type) }}</span>
                                </div>
                                <div class="my-2">
                                    <span class="fw-semibold fs-4 lh-sm">{{ $article->title }}</span>
                                    <p class="fs-6 text-truncate-3 mt-2">{!! strip_tags($article->description) !!}</p>
                                </div>
                                <button type="button" data-bs-toggle="modal" data-bs-target="#detailArticleModal"
                                    data-title="{{ $article->title }}"
                                    data-description="{{ $article->description }}"
                                    data-date="{{ $article->created_at->translatedFormat('d F Y') }}"
                                    data-thumbnail="{{ asset($article->picture) }}"
                                    data-writer="{{ $article->writer }}" data-type="{{ $article->type }}"
                                    class="btn btn-bitanic rounded-pill fw-bold d-flex align-items-center detail-article-toggle w-auto ms-auto ">
                                    Baca Artikel Ini
                                    <i class="bi bi-arrow-right-circle-fill fs-4 ms-2 text-warning"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted">Belum ada artikel.</div>
                @endforelse
            </div>
        </div>

        {{-- Artikel per type --}}
        @foreach ($articleTypes as $type)
            <div class="article-content" data-section="{{ $type }}" style="display: none;">
                <div class="row g-4">
                    @forelse($articles->where('type', $type)->values() as $article)
                        <div class="col-lg-4 col-md-6">
                            <div class="card h-100 rounded-5 border border-dark">
                                <img src="{{ asset($article->picture) }}" alt="Thumbnail"
                                    class="rounded-top-5 article-thumbnail" loading="lazy"
                                    style="min-height: 250px !important;" width="100%" height="250">
                                <div class="h-100 d-flex flex-column justify-content-between mx-3 my-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span>{{ $article->created_at->translatedFormat('d F Y ') }}</span>
                                        <span class="fw-bold fs-3">&CenterDot;</span>
                                        <span>{{ $article->writer }}</span>
                                        <span class="fw-bold fs-3">&CenterDot;</span>
                                        <span
                                            class="badge rounded-pill bg-bitanic">{{ ucfirst($article->type) }}</span>
                                    </div>
                                    <div class="my-2">
                                        <span class="fw-semibold fs-4 lh-sm">{{ $article->title }}</span>
                                        <p class="fs-6 text-truncate-3 mt-2">{!! strip_tags($article->description) !!}</p>
                                    </div>
                                    <button type="button" data-bs-toggle="modal"
                                        data-bs-target="#detailArticleModal" data-title="{{ $article->title }}"
                                        data-description="{{ $article->description }}"
                                        data-date="{{ $article->created_at->translatedFormat('d F Y') }}"
                                        data-thumbnail="{{ asset($article->picture) }}"
                                        data-writer="{{ $article->writer }}" data-type="{{ $article->type }}"
                                        class="btn btn-bitanic rounded-pill fw-bold d-flex align-items-center detail-article-toggle w-auto ms-auto ">
                                        Baca Artikel Ini
                                        <i class="bi bi-arrow-right-circle-fill fs-4 ms-2 text-warning"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center text-muted">Tidak ada artikel untuk kategori ini.</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </section>
    <div class="card bg-bitanic rounded-4 py-3 px-5 mb-5 custom-margin">
        <h2 class="text-white fw-bold mb-2">Let's Learn Together!</h2>
        <div class="d-flex flex-md-row flex-column justify-content-md-between align-items-md-center h-100 gap-4">
            <p class="text-white fw-light m-0">"Belajar bersama memahami teknologi pertanian cerdas, mulai dari dasar
                hingga penerapan nyata di
                lapangan"</p>
            <a href="#" class="btn btn-warning text-white h-auto w-auto ms-md-0 ms-auto">Mulai Sekarang</a>
        </div>
    </div>
    <section class="py-3 px-lg-4 px-2 mb-5 mx-md-5 mx-2 h-auto" id="mobileApps">
        <div class="row g-4">
            <div class="col-md-6 d-flex justify-content-center align-items-center">
                <img src="{{ asset('bitanic-landing/mobile_2.png') }}" alt="Mobile Apps Bitanic" class="img-fluid"
                    width="450" loading="lazy">
            </div>
            <div class="col-md-6">
                <div class="d-flex flex-column align-items-start">
                    <h2 class="fw-bold text-bitanic">Bitanic Juga Tersedia Untuk Mobile!</h2>
                    <ul>
                        <li>Monitoring</li>
                        <li>Kontrol Pompa</li>
                        <li>Visualisasi Data GIS</li>
                        <li>Interaksi Sosial</li>
                        <li>Laporan Tanaman & Hama</li>
                        <li>Pemasaran</li>
                    </ul>
                    <a href="https://play.google.com/store/apps/details?id=com.makerindo.bitanic" target="_blank"
                        class="btn btn-bitanic w-auto me-auto ms-md-0 ms-auto d-flex align-items-center gap-4">
                        <img src="{{ asset('bitanic-landing/playstore.png') }}" alt="Logo Play Store"
                            class="img-fluid" width="50" height="50">
                        <div>
                            <p class="m-0 text-start">Tersedia di</p>
                            <p class="fw-bold m-0 fs-4">Play Store</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>
    <section class="py-3 px-4 mx-md-5 mx-auto mb-5 h-auto" id="contact">
        <h2 class="text-bitanic fw-bold">Hubungi Kami</h2>
        <p>Punya ide, pertanyaan, atau sekadar ingin ngobrol soal pertanian cerdas? <br> Kami senang mendengarnya!
            Silakan
            hubungi kami kapan saja.</p>
        <div class="border border-3 rounded-pill" style="width: 10%; border-color: #3e8f55 !important;"></div>
        <div class="row g-4 my-5">
            <div class="col-lg-4">
                <div class="card rounded-4 border border-2 px-3 py-4 h-100">
                    <div class="d-flex gap-3">
                        <i class="bi bi-geo-alt-fill text-bitanic fs-4"></i>
                        <div>
                            <span class="fs-4 fw-semibold">Lokasi</span>
                            <p>Pesona Ciganitri, No. A 39, Bojongsoang, Cipagalo, Bojongsoang, Bandung Regency, West
                                Java 40287.</p>
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <i class="bi bi-envelope-fill text-bitanic fs-4"></i>
                        <div>
                            <span class="fs-4 fw-semibold">Kontak Kami</span>
                            <p class="m-0">cs@bitanic.id</p>
                            <p>+62 81546865286</p>
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <i class="bi bi-telephone-fill text-bitanic fs-4"></i>
                        <div>
                            <span class="fs-4 fw-semibold d-block mb-2">Media Sosial</span>
                            <a href="https://www.linkedin.com/showcase/bitanic-indonesia/"
                                class="w-auto h-auto text-decoration-none">
                                <img src="{{ asset('theme/img/icons/brands/linkedin.png') }}" alt="LinkedIn Icon"
                                    width="25" height="25" class="me-2" loading="lazy">
                            </a>
                            <a href="https://www.instagram.com/bitanicindonesia/"
                                class="w-auto h-auto text-decoration-none">
                                <img src="{{ asset('theme/img/icons/brands/instagram.png') }}" alt="Instagram Icon"
                                    width="25" height="25" loading="lazy">
                            </a>
                        </div>
                    </div>
                    <div class="mt-4">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15611.639092960559!2d107.65626420323346!3d-6.9728654285912866!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e9465bf21013%3A0x52be50500715e36c!2sPT.%20Makerindo%20Prima%20Solusi!5e0!3m2!1sid!2sid!4v1750326176688!5m2!1sid!2sid"
                            width="100%" height="200" style="border:0;" allowfullscreen="" loading="lazy"
                            title="Lokasi Workshop Bitanic" class="border border-2 rounded-3 border-dark"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card rounded-4 border border-2 px-3 py-4 h-100 gap-3" id="form-contact">
                    <span class="fs-3 fw-semibold text-center">Sampaikan pesan Anda di sini.</span>
                    <form action="">
                        <div class="d-flex mb-3">
                            <label for="name">Nama</label>
                            <input type="text" class="ps-2 rounded-3 form-control border-2 border-dark mt-2"
                                id="contact-input-name" placeholder="Nama">
                        </div>
                        <div class="d-flex mb-3">
                            <label for="email">Email</label>
                            <input type="text" class="ps-2 rounded-3 form-control border-2 border-dark mt-2"
                                id="contact-input-email" placeholder="Email">
                        </div>
                        <div class="d-flex mb-3">
                            <label for="contact-input-message">Pesan</label>
                            <textarea name="message" id="contact-input-message" cols="45" rows="10"
                                class="ps-2 rounded-3 form-control border-2 border-dark mt-2"></textarea>
                        </div>
                        <div class="mb-3">
                            {!! NoCaptcha::display() !!}
                            @error('g-recaptcha-response')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        <div class="text-center mt-2">
                            <button class="btn btn-bitanic w-100" id="btn-send">Kirim</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

{!! NoCaptcha::renderJs() !!}
