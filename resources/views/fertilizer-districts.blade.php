<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kebutuhan Pupuk Per Kota/Kecamatan
        </h2>
    </x-slot>

    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="row">
                    @foreach ($districts as $id => $dis_name)
                        <div class="col-lg-4 col-md-12 col-4 mb-4">
                            <a href="{{ route('dashboard.kebutuhan-pupuk.subdistrict', ['district' => $id]) }}">
                                <div class="card h-100">
                                    <div class="card-body d-flex align-items-center justify-content-center">
                                        <div>
                                            <span class="fw-semibold text-center d-block mb-1">{{ $dis_name }} <br> N-P-K</span>
                                            <h4 class="card-title text-center mb-2" id="district-{{ $id }}">
                                                <x-dashboard-spinner></x-dashboard-spinner>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->

    @push('scripts')
        <script>

            const getDashboardData = async () => {
                try {
                    let activityData = []

                    const settings = {
                        method: 'GET',
                        headers: {
                            'x-csrf-token': '{{ csrf_token() }}'
                        },
                    }

                    const [data, error] = await yourRequest("{{ route('web.get-kebutuhan-pupuk.district', ['city' => $city]) }}", settings)

                    if (error) {
                        if ("messages" in error) {
                            let errorMessage = ''

                            let element = ``
                            for (const key in error.messages) {
                                if (Object.hasOwnProperty.call(error.messages, key)) {
                                    error.messages[key].forEach(message => {
                                        element += `<li>${message}</li>`;
                                    });
                                }
                            }

                            errorMessage = `<ul>${element}</ul>`

                            Swal.fire({
                                html: errorMessage,
                                icon: "error",
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        }

                        return false
                    }

                    console.log(data);

                    const districts = data.districts

                    for (const id in districts) {
                        if (Object.hasOwnProperty.call(districts, id)) {
                            const district = districts[id];
                            
                            document.getElementById('district-'+id).innerHTML = district.count_fertilizer_n + "kg - " + district.count_fertilizer_p + "kg - " + district.count_fertilizer_k + "kg"
                        }
                    }

                } catch (error) {
                    console.log(error);
                }
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

                getDashboardData()
            });
        </script>
    @endpush
</x-app-layout>
