<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Bitanic</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('bitanic-landing/tab_icon.ico') }}" />

    <link rel="stylesheet" href="{{ asset('bootstrap-5.2.3-dist/css/bootstrap.min.css') }}">

    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>

<body>
    <div id="app">
        @include('bitanic.landing.navbar')
        <main id="mainHome">
            <section class="d-flex justify-content-center align-items-center" id="mitra">
                <div class="card-mitra">
                    <div>
                        <div class="text-mitra text-wrap">
                            Bergabung Menjadi <br/> Mitra Bitanic
                        </div>
                    </div>
                    <div>
                        <a href="{{ $contact_us->mitra_link }}" target="_blank" class="btn btn-light">Register Sekarang</a>
                    </div>
                </div>
            </section>
            <section class="" id="contact">
                <div class="title text-center p-5" style="font-size: 18px">Kontak <span
                        style="color:var(--vt-c-base2); font-weight:bold">Kami</span></div>
                <div class="d-flex flex-wrap" style="margin: 0 50px">
                    <div class="GetIn">
                        <h5>Hubungi Kami</h5>
                        <p>{{ $contact_us->email }}</p>
                        <p>{{ $contact_us->phone_number }}</p>
                        <div class="office mt-5">
                            <h5>Kantor Bandung</h5>
                            <p>{{ $contact_us->address }}</p>
                        </div>
                        <div class="office mt-5">
                            <h5>Perusahaan</h5>
                            <a href="/mitra-page" class="text-decoration-none">Mitra</a>
                        </div>
                        <div class="medsos mt-5">
                            <h5>Temukan media sosial kami</h5>
                            <div class="d-flex mt-4">
                                <div class="in me-4">
                                    <a href="{{ $contact_us->linkedin_link }}">
                                        <img src="{{ asset('bitanic-landing/linkedin-wt.png') }}" alt=""
                                            id="medsos-in" width="60">
                                    </a>
                                </div>
                                <div class="ig me-4">
                                    <a href="{{ $contact_us->ig_link }}">
                                        <img src="{{ asset('bitanic-landing/instagram-wt.png') }}" alt=""
                                            id="medsos-ig" width="60">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card form-contact">
                        <form action="">
                            <div class="d-flex mb-3">
                                <label for="name">Nama</label>
                                <input type="text" class="ps-2" id="contact-input-name" placeholder="Nama">
                            </div>
                            <div class="d-flex mb-3">
                                <label for="email">Email</label>
                                <input type="text" class="ps-2" id="contact-input-email" placeholder="Email">
                            </div>
                            <div class="d-flex mb-3">
                                <label for="message">Pesan</label>
                                <textarea name="message" id="contact-input-message" cols="45" rows="6"></textarea>
                            </div>
                            <div class="text-center">
                                <button class="btn btn-ask w-100" id="btn-send">Kirim</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </main>
    </div>

    {{-- Bootstrapt --}}
    <script src="{{ asset('bootstrap-5.2.3-dist/js/bootstrap.min.js') }}"></script>

    {{-- Sweet Alert 2 --}}
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Axios --}}
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script>
        const sections = document.querySelectorAll("section[id]");

        // Add an event listener listening for scroll
        window.addEventListener("scroll", navHighlighter);

        function navHighlighter() {

            // Get current scroll position
            let scrollY = window.pageYOffset;

            // Now we loop through sections to get height, top and ID values for each
            sections.forEach(current => {
                const sectionHeight = current.offsetHeight;
                const sectionTop = current.offsetTop - 50;
                let sectionId = current.getAttribute("id");

                /*
                - If our current scroll position enters the space where current section on screen is, add .active class to corresponding navigation link, else remove it
                - To know which link needs an active class, we use sectionId variable we are getting while looping through sections as an selector
                */
                if (
                    scrollY > sectionTop &&
                    scrollY <= sectionTop + sectionHeight
                ) {
                    document.querySelector(".menu a[href*=" + sectionId + "]").classList.add("active");
                } else {
                    document.querySelector(".menu a[href*=" + sectionId + "]").classList.remove("active");
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

            formData.append("name", document.getElementById('contact-input-name').value)
            formData.append("email", document.getElementById('contact-input-email').value)
            formData.append("message", document.getElementById('contact-input-message').value)

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
