<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description"
        content="Website Landing Page untuk memberi beberapa informasi mengenai Bitanic dan Produk-produk dari Bitanic">
    <title>Bitanic</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('bitanic-landing/tab_icon.ico') }}" />
    <link rel="preload" as="image" href="{{ asset('bitanic-landing/carousel-image-1.webp') }}" />
    <link rel="preload" href="{{ asset('bootstrap-5.2.3-dist/css/bootstrap.min.css') }}" as="style"
        onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="{{ asset('bootstrap-5.2.3-dist/css/bootstrap.min.css') }}">
    </noscript>

    <link rel="preload" href="{{ asset('css/landing.css') }}" as="style"
        onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    </noscript>
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
        as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    </noscript>

    {{-- SweetAlert2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    {{-- <link rel="stylesheet" href="{{ asset('bootstrap-5.2.3-dist/css/bootstrap.min.css') }}"> --}}
    {{-- <link rel="stylesheet" href="{{ asset('css/landing.css') }}"> --}}
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"> --}}
    <style>
        /* text di kiri */
        .carousel-text {
            max-width: 45%;
            z-index: 10;
        }

        /* gambar di kanan */
        .carousel-img {
            max-width: 55%;
            overflow: hidden;
        }

        /* gradient overlay dari kiri ke kanan (putih → transparan) */
        .gradient-overlay {
            pointer-events: none;
            background: linear-gradient(to right, rgba(255, 255, 255, 1) 20%, rgba(255, 255, 255, 0));
            z-index: 5;
        }

        /* posisi dan style custom arrows */
        .custom-prev,
        .custom-next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: white;
            border: none;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3e8f55;
            transition: all .3s;
            z-index: 20;
        }

        /* panah kanan dan kiri */
        .custom-prev {
            right: 80px;
        }

        .custom-next {
            right: 20px;
        }

        /* efek active (hover atau focus) */
        .custom-prev:hover,
        .custom-prev:focus,
        .custom-next:hover,
        .custom-next:focus {
            background: #3e8f55;
            color: white;
        }

        .custom-margin {
            margin-inline: 4.5rem;
        }

        /* responsive: tumpuk teks di atas gambar pada mobile */
        @media (max-width: 768px) {
            #heroCarousel .carousel-item {
                flex-direction: column;
            }

            .carousel-text {
                max-width: 100%;
                text-align: center;
            }

            .carousel-img {
                max-width: 100%;
            }

            .gradient-overlay {
                display: none;
            }

            .custom-prev {
                right: 0;
            }

            .custom-next {
                right: 0;
            }

            .custom-margin {
                margin-inline: 1.5rem;
            }
        }

        .text-rotate {
            writing-mode: horizontal-tb;
            transform: none;
            border-left: none;
            border-bottom: 2px solid black;
        }

        .article-thumbnail {
            min-height: 225px !important;
        }

        .custom-mt-modal {
            margin-top: 0rem;
        }

        @media (min-width: 768px) {
            .custom-mt-modal {
                margin-top: 3.5rem;
            }
        }

        @media (min-width: 992px) {
            .text-rotate {
                writing-mode: vertical-lr;
                transform: rotate(180deg);
                border-left: 2px solid black;
                border-bottom: none;
            }
        }

        @media (min-width: 576px) {
            .article-thumbnail {
                min-height: 250px !important;
            }
        }

        .nav-tabs .nav-link.active {
            color: #3e8f55 !important;
            font-weight: bold;
            position: relative;
        }

        .nav-tabs .nav-link.active::after {
            content: "";
            height: 3px;
            background-color: #3e8f55;
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            border-radius: 2px;
        }

        .rounded-top-5 {
            border-top-left-radius: 2rem;
            border-top-right-radius: 2rem;
        }

        .text-truncate-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>

<body>
    <div id="app">
        @include('bitanic.landing.navbar')
        @include('bitanic.landing.home')
        <!-- @include('bitanic.landing.footer') -->
    </div>
    
    {{-- SweetAlert2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Bootstrapt --}}
    <script src="{{ asset('bootstrap-5.2.3-dist/js/bootstrap.min.js') }}" defer></script>

    {{-- Axios --}}
    <script src="https://unpkg.com/axios/dist/axios.min.js" defer></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tabs = document.querySelectorAll("#articleTabs .nav-link");
            const contents = document.querySelectorAll(".article-content");

            tabs.forEach(tab => {
                tab.addEventListener("click", function(e) {
                    e.preventDefault();

                    // Remove active dari semua tab
                    tabs.forEach(t => t.classList.remove("active"));
                    this.classList.add("active");

                    // Ambil kategori dari data-tab
                    const kategori = this.dataset.tab;

                    // Tampilkan hanya section yang cocok
                    contents.forEach(section => {
                        section.style.display = (section.dataset.section === kategori) ?
                            'block' : 'none';
                    });
                });
            });

            const modalArticleToggler = document.querySelectorAll('.detail-article-toggle');
            const modalArticleTitle = document.getElementById('detailArticleLabel');
            const modalArticleDate = document.getElementById('detailArticleDate');
            const modalArticleContent = document.getElementById('detailArticleDescription');
            const modalArticleThumbnail = document.getElementById('detailArticleThumbnail');
            const modalArticleWriter = document.getElementById('detailArticleWriter');
            const modalArticleType = document.getElementById('detailArticleType');
            const modalArticle = new bootstrap.Modal(document.getElementById('detailArticleModal'));

            modalArticleToggler.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const title = this.dataset.title;
                    const date = this.dataset.date;
                    const description = this.dataset.description;
                    const thumbnail = this.dataset.thumbnail;
                    const writer = this.dataset.writer;
                    const type = this.dataset.type;

                    modalArticleTitle.textContent = title;
                    modalArticleDate.textContent = date;
                    modalArticleWriter.textContent = writer;
                    modalArticleContent.innerHTML = description;
                    modalArticleThumbnail.src = thumbnail;
                    modalArticleType.textContent = type.charAt(0).toUpperCase() + type.slice(1);

                    modalArticle.show();
                });
            });

            const modalEl = document.getElementById('detailProductModal');
            const modalProduct = new bootstrap.Modal(document.getElementById('detailProductModal'));
            const modalProductToggler = document.querySelectorAll('.detail-product-toggle');
            const modalProductTitle = document.getElementById('detailProductTitle');
            const modalProductImage = document.getElementById('detailProductThumbnail');
            const modalProductDescription = document.getElementById('detailProductDescription');

            document.getElementById('contact-now').addEventListener('click', function(e) {
                e.preventDefault();
                if (modalProduct) {
                    modalEl.addEventListener('hidden.bs.modal', function onHidden() {
                        modalEl.removeEventListener('hidden.bs.modal',
                            onHidden); // hapus event listener agar tidak dipanggil lagi
                        const target = document.getElementById('form-contact');
                        if (target) {
                            target.scrollIntoView({
                                behavior: 'smooth'
                            });
                        }
                    });
                    modalProduct.hide();
                }
            });

            modalProductToggler.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const productTitle = this.dataset.title;
                    const productImage = this.dataset.image;
                    const productDescription = this.dataset.description;

                    modalProductTitle.textContent = productTitle;
                    modalProductImage.src = productImage;
                    modalProductDescription.innerHTML = productDescription;

                    modalProduct.show();
                })
            })

            const modalHistoryToggler = document.querySelectorAll('.detail-history-toggle');
            const modalHistoryContent = document.getElementById('detailHistoryDescription');
            const modalHistoryImage = document.getElementById('detailHistoryImage');
            const modalHistory = new bootstrap.Modal(document.getElementById('detailHistoryModal'));

            modalHistoryToggler.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const description = this.dataset.description;
                    const image = this.dataset.image;

                    modalHistoryContent.innerHTML = description;
                    modalHistoryImage.src = image;

                    modalHistory.show();
                });
            });
        });

        const sections = document.querySelectorAll("section[id]");

        // Add an event listener listening for scroll
        window.addEventListener("scroll", navHighlighter);

        function navHighlighter() {

            // Get current scroll position
            let scrollY = window.pageYOffset;
            const div = document.querySelector('#contact');
            const divBottom = div.offsetTop + div.offsetHeight;
            const windowBottom = window.pageYOffset + window.innerHeight;

            // Now we loop through sections to get height, top and ID values for each
            sections.forEach(current => {
                const sectionHeight = current.offsetHeight;
                const sectionTop = current.offsetTop - 100;
                let sectionId = current.getAttribute("id");

                // console.log(scrollY, sectionTop, sectionHeight);

                /*
                - If our current scroll position enters the space where current section on screen is, add .active class to corresponding navigation link, else remove it
                - To know which link needs an active class, we use sectionId variable we are getting while looping through sections as an selector
                */

                if (windowBottom >= divBottom) {
                    document.querySelector('.menu a[href="#contact"]')?.classList.add("active");
                } else if (
                    scrollY > sectionTop &&
                    scrollY <= sectionTop + sectionHeight
                ) {
                    document.querySelector('.menu a[href="#' + sectionId + '"]')?.classList.add("active");
                } else {
                    document.querySelector('.menu a[href="#' + sectionId + '"]')?.classList?.remove("active");
                }
            });
        }

        // Submit button handler
        const submitButton = document.getElementById('btn-send');
        submitButton.addEventListener('click', async function(e) {
            // Prevent default button action
            e.preventDefault();

            // Show loading indication
            submitButton.setAttribute('data-kt-indicator', 'on');

            // Disable button to avoid multiple click
            submitButton.disabled = true;

            // Simulate form submission. For more info check the plugin's official documentation: https://sweetalert2.github.io/
            let url, formSubmited;
            const formData = new FormData();
            const captchaResponse = grecaptcha.getResponse();

            formData.append("name", document.getElementById('contact-input-name').value)
            formData.append("email", document.getElementById('contact-input-email').value)
            formData.append("message", document.getElementById('contact-input-message').value)
            formData.append("g-recaptcha-response", captchaResponse)

            url = "{{ route('landing.contact-us-message.store') }}"

            try {
                const response = await axios.post(url, formData)
                // window.location.reload();
                Swal.fire({
                    text: 'Pesan berhasil dikirim',
                    icon: "success",
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });

                // Remove loading indication
                submitButton.removeAttribute('data-kt-indicator');

                // Enable button
                submitButton.disabled = false;
            } catch (error) {
                let errorMessage = error

                if (error.hasOwnProperty('response')) {
                    if (error.response.status == 422) {
                        errorMessage = 'Data yang dikirim tidak sesuai'
                    } else if (error.response.status == 400) {
                        let element = ``
                        for (const key in error.response.data.messages) {
                            if (Object.hasOwnProperty.call(error.response.data.messages, key)) {
                                error.response.data.messages[key].forEach(message => {
                                    element += `<li>${message}</li>`;
                                });
                            }
                        }

                        errorMessage = `<ul>${element}</ul>`
                    } else {
                        errorMessage = error
                    }
                }

                Swal.fire({
                    html: errorMessage,
                    icon: "error",
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });

                // Remove loading indication
                submitButton.removeAttribute('data-kt-indicator');

                // Enable button
                submitButton.disabled = false;
            }
        });
    </script>
</body>

</html>
