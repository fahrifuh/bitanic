<header class="bitanic__navbar">
    <div class="bitanic__container bitanic__nav-container">
        <div class="bitanic__logo">
            <img src="{{ asset('bitanic-landing/navbar-logo.png') }}" alt="navbar-logo">
        </div>
        <nav>
            <ul>
                <li><a href="#">Beranda</a></li>
                <li><a href="#">Tentang</a></li>
                <li><a href="#">Produk</a></li>
                <li><a href="#">Layanan</a></li>
                <li><a href="#">Artikel</a></li>
                <li><a href="#">Karir</a></li>
                <li><a href="#">Mitra</a></li>
                <li><a href="#">Kontak</a></li>
            </ul>
        </nav>
        <a href="{{ route('login') }}" class="bitanic__button-masuk">Masuk</a>
    </div>
</header>
