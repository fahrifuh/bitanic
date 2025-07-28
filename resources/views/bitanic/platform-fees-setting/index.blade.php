<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Setting /</span> Transaction Setting</h4>
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
                    <form action="{{ route('bitanic.transaction-setting.update') }}" method="POST" id="'form-product">
                        @csrf
                        @method('PUT')
                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />
                        <div class="row">
                            <div class="col mb-3">
                                <label for="data-input-platform-fees" class="form-label">Biaya Platform</label>
                                <input type="number" min="0" id="data-input-platform-fees" class="form-control" name="platform_fees" value="{{ $transactionSetting->platform_fees }}" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <button type="submit" class="btn btn-primary" id="submit-btn">Simpan</button>
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
            const btnSubmit = document.getElementById('submit-btn')
            btnSubmit.addEventListener('submit', e => {
                // Show loading indication
                btnSubmit.setAttribute('data-kt-indicator', 'on');

                // Disable button to avoid multiple click
                btnSubmit.disabled = true;

                // document.getElementById('form-product').submit()
            })
        </script>
    @endpush
</x-app-layout>
