<section class="bitanic__product">
    <div class="bitanic__container" style="padding: 75px 0;">
        <div class="bitanic__product-header">
            <h1>Produk <span>Bitanic</span></h1>
            <p>Bitanic memberikan berbagai Produk untuk kebutuhan petani dan meningkatkan ke prisisian dalam pertanian
            </p>
        </div>
        <div class="bitanic__product-list">
            @for ($i = 0; $i < 9; $i++)
                <div class="bitanic__product-card">
                    <div class="bitanic__product-card-image">
                        <img src="{{ asset('bitanic-landing/product-1.png') }}" alt="product-1">
                    </div>
                    <div class="bitanic__product-card-body">
                        <h5 class="bitanic__product-card-title">Bitanic Lite</h5>
                        <p class="bitanic__product-card-description">Kontrol Pompa Hybrid (Automatic, By Sensor, By
                            Time, Manual) untuk manajemen irigasi yang
                            fleksibel. Sistem fertilisasi & irigasi yang canggih untuk penggunaan sumber daya yang
                            efisien. Layanan Cloud & Edge Monitoring untuk pemantauan data tanaman yang akurat.</p>
                        <div class="bitanic__product-card-extra">
                            <div class="bitanic__product-card-price">Rp&nbsp;8.000.000,00</div>
                            <div class="bitanic__product-card-link">
                                <a href="#">Read more...</a>
                            </div>
                        </div>
                        <div class="bitanic__product-card-tags">
                            <div class="bitanic__product-card-tag">
                                <img src="{{ asset('bitanic-landing/circle-check.svg') }}" alt="tags">
                                Green House
                            </div>
                            <div class="bitanic__product-card-tag">
                                <img src="{{ asset('bitanic-landing/circle-check.svg') }}" alt="tags">
                                Open Field
                            </div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
        <div class="bitanic__marketplace">
            <div class="bitanic__marketplace-image">
                <img src="{{ asset('bitanic-landing/marketplace-mobile.png') }}" alt="marketplace-mobile">
            </div>
            <div class="bitanic__marketplace-description">
                <div class="bitanic__marketplace-description-1">
                    <h5 class="bitanic__marketplace-title-1">Bitanic Marketplace</h5>
                    <ul>
                        <li>Pemasaran Hasil Pertanian</li>
                        <li>Penjualan Alat Pertanian</li>
                        <li>Jasa dibidang Pertanian</li>
                    </ul>
                </div>
                <div class="bitanic__marketplace-description-2">
                    <h5 class="bitanic__marketplace-title-2">3% per-transaksi</h5>
                    <div class="bitanic__marketplace-tags">
                        <div class="bitanic__marketplace-tag">
                            <img src="{{ asset('bitanic-landing/circle-check.svg') }}" alt="tags">
                            Green House
                        </div>
                        <div class="bitanic__marketplace-tag">
                            <img src="{{ asset('bitanic-landing/circle-check.svg') }}" alt="tags">
                            Open Field
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
