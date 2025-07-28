<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="{{ asset('theme/vendor/fonts/boxicons.css') }}" />

    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>
    <div id="app">
        <main id="mainLogin">
            <div class="login">
                <div class="d-flex" style="height: 100%">
                    <div class="logo mt-5 text-center">
                        <a href="/">
                            <img src="{{ asset('bitanic-landing/logo.png') }}" alt="">
                        </a>
                    </div>
                    <div class="form-section">
                        <div class="p-4 form">

                            <!-- Session Status -->
                            <x-auth-session-status class="mb-4" :status="session('status')" />

                            <!-- Validation Errors -->
                            <x-auth-validation-errors class="mb-4" :errors="$errors" />
                            <div class="head"
                                style="font-style: normal;
                                font-weight: bold;
                                font-size: 25px;
                                line-height: 36px;
                                color: #42a16b;">
                                Sign In
                            </div>
                            <form method="POST" action="{{ route('login') }}" autocomplete="off">
                                @csrf
                                <div class="mb-4 mt-4">
                                    <label class="mb-2" for="phone_number">Nomor Handphone</label>
                                    <input type="text" class="input-form" name="phone_number" id="phone_number" />
                                </div>

                                <div class="mb-3">
                                    <label class="mb-2" for="password">Password</label>
                                    <div class="input-group mb-3">
                                        <input type="password" id="input-password"
                                            class="form-control border-radius-left" aria-describedby="button-addon2"
                                            name="password">
                                        <button class="btn btn-icon btn-bitanic border-radius-right" type="button"
                                            id="button-addon2">
                                            <i class='bx bx-show'></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    {!! NoCaptcha::display() !!}
                                </div>

                                <div class="text-center d-block">
                                    <button type="submit" class="btn btn-login mt-4">Login</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {!! NoCaptcha::renderJs() !!}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous">
    </script>
    <script src="{{ asset('js/login.js') }}"></script>
    <script>
        window.onload = () => {
            console.log('Hello world');

            document.querySelector('#button-addon2').addEventListener('click', e => {
                hidePassword('button-addon2', 'input-password')
            })
        }
    </script>
</body>

</html>
