<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="{{ asset('theme/vendor/fonts/boxicons.css') }}" />

    <link rel="stylesheet" href="{{ asset('bootstrap-5.2.3-dist/css/bootstrap.min.css') }}">

    <link rel="stylesheet" href="{{ asset('css/landing2.css') }}">
    <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">
</head>

<body>
    <div style="width: 100%;">
        @include('bitanic.new-landing.navbar')
        @include('bitanic.new-landing.hero')
        @include('bitanic.new-landing.about-us')
        @include('bitanic.new-landing.mitra')
        @include('bitanic.new-landing.choose-us')
        @include('bitanic.new-landing.mobile-app')
        @include('bitanic.new-landing.layanan')
        @include('bitanic.new-landing.product')
        {{-- @include('bitanic.new-landing.pricing') --}}
        @include('bitanic.new-landing.gallery')
        @include('bitanic.new-landing.carrer')
        @include('bitanic.new-landing.testimoni')
        @include('bitanic.new-landing.article')
        @include('bitanic.new-landing.faq')
        @include('bitanic.new-landing.contact-us')
        @include('bitanic.new-landing.footer')
    </div>
    <script src="{{ asset('theme/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('leaflet/leaflet.js') }}"></script>
    <script>
        let lastScrollTop = 0;
        const navbar = document.querySelector('.bitanic__navbar');
        const slider = document.querySelector('.bitanic__image-slider');
        const slides = document.querySelectorAll('.bitanic__slide');
        let slideIndex = 0;

        function showSlide(index) {
            slides.forEach((slide) => {
                slide.style.left = `${index * -100}%`;
            });
        }

        // Layer MAP
        let googleStreets = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        });
        let googleStreetsSecond = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        });
        let googleStreetsThird = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        });
        // Layer MAP
        const map = L.map('myMap', {
            preferCanvas: true,
            layers: [googleStreets],
            zoomControl: true
        }).setView([-6.869080223722067, 107.72491693496704], 12);

        window.addEventListener('scroll', () => {
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            if (scrollTop > lastScrollTop) {
                // Scroll down: hide the navbar
                navbar.style.top = '-100px';
            } else {
                // Scroll up: show the navbar
                navbar.style.top = '0';
            }

            lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; // For mobile or negative scrolling
        });

        window.addEventListener("load", (event) => {
            setInterval(() => {
                slideIndex = (slideIndex + 1) % slides.length;
                showSlide(slideIndex);
            }, 3000); // Adjust interval as needed
        });
    </script>
</body>

</html>
