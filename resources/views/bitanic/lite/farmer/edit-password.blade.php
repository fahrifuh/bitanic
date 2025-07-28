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

            @media (max-width: 600px) {
                .preview-image {
                    width: calc(100% - 10px);
                }
            }
        </style>
    @endpush
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / <a
                    href="{{ route('bitanic.lite-user.index') }}">Petani Lite</a> / <a
                    href="{{ route('bitanic.lite-user.show', $liteUser->id) }}">{{ $liteUser->name }}</a> /</span> Edit Password
        </h4>
    </x-slot>
    {{-- End Header --}}

    @if (session()->has('success'))
        <x-alert-message class="alert-success">{{ session()->get('success') }}</x-alert-message>
    @endif

    <div class="row">
        <div class="col-md-12">
            <!-- Striped Rows -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('bitanic.lite-user.update-password', $liteUser->id) }}" method="POST"
                        id="form-password" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        <div class="row g-2">
                            <div class="col mb-3">
                                <label for="data-input-password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="data-input-password" name="password"
                                    aria-label="0" aria-describedby="basic-addon13" />
                            </div>
                            <div class="col mb-3">
                                <label for="data-input-password-confirmation" class="form-label">Konfirmasi
                                    Password</label>
                                <input type="password" class="form-control" id="data-input-password-confirmation"
                                    name="password_confirmation" aria-label="0" aria-describedby="basic-addon13" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <button type="button" onclick="confirm()" class="btn btn-primary w-100" id="submit-btn">Simpan</button>
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
            const confirm = async () => {
                const {
                    value: konfirmasi
                } = await Swal.fire({
                    title: "Ketik 'PASSWORD' untuk mengubah password user!",
                    input: "text",
                    showCancelButton: true,
                    inputValidator: (value) => {
                        if (!value) {
                            return "You need to write something!";
                        }
                    }
                });

                if (konfirmasi != 'PASSWORD') {
                    alert('Harap ketik PASSWORD jika ingin mengubah password!')
                    return false
                }

                document.querySelector('#form-password').submit()
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");


            })
        </script>
    @endpush
</x-app-layout>
