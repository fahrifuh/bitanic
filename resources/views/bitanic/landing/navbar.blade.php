<div class="d-flex align-items-center justify-content-between justify-content-lg-start" id="topNav">
    <div class="title text-center" style="">
        <a href="/">
            <img src="{{ asset('bitanic-landing/Bitanicgreen.png') }}" alt="Logo Brand" class="" width="100%"
                height="23">
        </a>
    </div>
    <div class="menu">
        <ul>
            <li><a href="#banner" class="btn">Beranda</a></li>
            <li><a href="#product" class="btn">Produk</a></li>
            <li><a href="#mitra-icons" class="btn">Mitra</a></li>
            <li><a href="#about" class="btn">Tentang Kami</a></li>
            <li><a href="#article" class="btn">Artikel</a></li>
	    <li><a href="#contact" class="btn">Kontak</a></li>
        </ul>
    </div>
    <div>
        @guest
            <a href="/login" class="btn btn-bitanic fw-bold d-flex align-items-center">Login <i
                    class="bi bi-arrow-right-circle-fill fs-4 ms-2 text-warning"></i></a>
        @else
            <a href="/dashboard" class="btn btn-bitanic fw-bold d-flex align-items-center">Buka Dashboard <i
                    class="bi bi-arrow-right-circle-fill fs-4 ms-2 text-warning"></i></a>
        @endguest
    </div>
</div>
