<x-app-layout>

    @push('styles')
        <style>
            .preview-image {
                width: 100%;
                /* Adjust the width as desired */
                object-fit: cover;
                aspect-ratio: 1/1;
                border: 1px solid #9f999975;
            }

            .event-none {
                pointer-events: none;
            }

            @media (max-width: 600px) {
                .preview-image {
                    width: calc(100% - 10px);
                }
            }
        </style>
    @endpush
    {{-- Header --}}
    <x-slot name="header">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb fs-4">
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);">Hidroponik</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('bitanic.hydroponic.user.index') }}">User</a>
                </li>
                <li class="breadcrumb-item">
                    <a
                        href="{{ route('bitanic.hydroponic.user.show', $hydroponicUser->id) }}">{{ $hydroponicUser->name }}</a>
                </li>
                <li class="breadcrumb-item active">Edit Password</li>
            </ol>
        </nav>
    </x-slot>
    {{-- End Header --}}

    @if (session()->has('success'))
        <x-alert-message class="alert-success">{{ session()->get('success') }}</x-alert-message>
    @endif

    <div class="row d-flex justify-content-center">
        <div class="col-md-6">
            <!-- Striped Rows -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('bitanic.hydroponic.user.update-password', $hydroponicUser->id) }}" method="POST"
                        id="form-product">
                        @csrf
                        @method('PUT')
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        <div class="row g-2">
                            <div class="col-12">
                                <label for="data-input-name" class="form-label">Password Baru</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="input-password"
                                        placeholder="············" aria-describedby="input-password2"
                                        name="password" required>
                                    <span id="input-password2" class="input-group-text cursor-pointer"
                                        onclick="passwordChangeType(this)"><i
                                            class="bx bx-hide"></i></span>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="data-input-email" class="form-label">Konfirmasi Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="input-password-confirmation"
                                        placeholder="············" aria-describedby="input-password-confirmation2"
                                        name="password_confirmation" required>
                                    <span id="input-password-confirmation2" class="input-group-text cursor-pointer"
                                        onclick="passwordChangeType(this)"><i
                                            class="bx bx-hide"></i></span>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary float-end" id="submit-btn">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
        <script>
            const passwordChangeType = (e) => {
                let inputType = e.previousElementSibling.type

                switch (inputType) {
                    case "text":
                        e.previousElementSibling.type = "password"
                        break;
                    case "password":
                        e.previousElementSibling.type = "text"
                        break;

                    default:
                        e.previousElementSibling.type = "password"
                        break;
                }

                e.firstElementChild.classList.toggle("bx-hide")
                e.firstElementChild.classList.toggle("bx-show")
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

            })
        </script>
    @endpush
</x-app-layout>
