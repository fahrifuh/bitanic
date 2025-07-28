<x-app-layout>
    @push('styles')
        <style>
            #pest-image {
                height: 100%;
                object-fit: cover;
            }

            .preview-image {
                width: 100%;
                /* Adjust the width as desired */
                object-fit: cover;
                aspect-ratio: 4/3;
                border: 1px solid #9f999975;
            }

            .device-status {
                background-color: #c3c3c3;
                width: 20px;
                height: 20px;
                border-radius: 20px;
            }

            .bg-on-status {
                background-color: #00ff08 !important;
            }

            .bg-disabled-status {
                background-color: #ff0000 !important;
            }

            .event-none {
                pointer-events: none;
            }

            @media (max-width: 600px) {
                .preview-image {
                    width: calc(100% - 10px);
                }
            }

            .description {
                background-color: #8d8d8d36;
                padding: 10px;
            }

            .cursor-pointer {
                cursor: pointer;
            }
        </style>
    @endpush
    {{-- Header --}}
    <x-slot name="header">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / <a
                    href="{{ route('bitanic.transaction-komodity.index') }}">Transaksi Komoditi</a> /</span> Detail
            {{ $farmerTransaction->code }}</h4>
    </x-slot>
    {{-- End Header --}}


    <div class="row d-flex justify-content-center">
        <div class="col-12 col-md-6">
            @if (session()->has('success'))
                <x-alert-message class="alert-success">{{ session()->get('success') }}</x-alert-message>
            @endif
            @if (session()->has('failed'))
                <x-alert-message class="alert-danger">{{ session()->get('failed') }}</x-alert-message>
            @endif
            <!-- Validation Errors -->
            <x-auth-validation-errors class="mb-4" :errors="$errors" />
            <!-- Striped Rows -->
            <div class="card">
                <div class="card-body">
                    <h6>Order ID {{ $farmerTransaction->code }}</h6>
                    <h2>Total Rp&nbsp;{{ number_format_1($farmerTransaction->total) }}</h2>
                    <h5 class="border-bottom mb-1">Status</h5>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span>Status Pembayaran</span>

                        <div>
                            <span @class([
                                'badge',
                                'bg-label-success' => $farmerTransaction->status == 'settlement',
                                'bg-label-secondary' => $farmerTransaction->status == 'pending',
                                'bg-label-danger' => in_array($farmerTransaction->status, [
                                    'expire',
                                    'cancel',
                                    'deny',
                                    'failure',
                                ]),
                            ])>
                                {{ $farmerTransaction->status }}
                            </span>
                            <button class="btn btn-sm btn-icon btn-warning" onclick="statusUpdate(this.dataset)"
                                data-id="{{ $farmerTransaction->id }}" title="Cek status transaksi">
                                <i class='bx bx-refresh fs-5'></i>
                            </button>
                        </div>
                    </div>
                    @foreach ($farmerTransaction->farmer_transaction_shops as $farmer_transaction_shop)
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span>Status Pengiriman Toko {{ $farmer_transaction_shop->shop_name }}</span>

                            <div>
                                <span @class([
                                    'badge',
                                    'bg-label-success' => $farmer_transaction_shop->shipping_status == 2,
                                    'bg-label-secondary' => $farmer_transaction_shop->shipping_status == 0,
                                    'bg-label-info' => $farmer_transaction_shop->shipping_status == 1,
                                ])>
                                    @if ($farmerTransaction->status == 'settlement')
                                        @switch($farmer_transaction_shop->shipping_status)
                                            @case(0)
                                                Sedang Dikemas
                                            @break

                                            @case(1)
                                                Sedang Dikirim
                                            @break

                                            @case(2)
                                                Diterima
                                            @break

                                            @default
                                                -
                                        @endswitch
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endforeach

                    <h5 class="border-bottom mb-1 mt-3">Detail Harga</h5>
                    @foreach ($farmerTransaction->farmer_transaction_shops as $famer_transaction_shop)
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span>
                                <b>{{ $farmer_transaction_shop->shop_name }}</b>
                            </span>
                            <span>
                            </span>
                        </div>
                        @foreach ($famer_transaction_shop->farmer_transaction_items as $farmer_transaction_item)
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span>Harga Produk @if ($farmer_transaction_item->quantity > 1)
                                        (x{{ $farmer_transaction_item->quantity }})
                                    @endif
                                </span>
                                <span>
                                    Rp&nbsp;{{ number_format_1($farmer_transaction_item->product_price * $farmer_transaction_item->quantity) }}
                                </span>
                            </div>
                            @if ($farmer_transaction_item->discount)
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span>Discount Produk {{ $farmer_transaction_item->discount }}% @if ($farmer_transaction_item->quantity > 1)
                                            (x{{ $farmer_transaction_item->quantity }})
                                        @endif
                                    </span>
                                    <span>
                                        -Rp&nbsp;{{ number_format_1($farmer_transaction_item->quantity * floor($farmer_transaction_item->product_price * ($farmer_transaction_item->discount / 100))) }}
                                    </span>
                                </div>
                            @endif
                        @endforeach
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span>Biaya Pengiriman Toko</span>
                            <span>
                                Rp&nbsp;{{ number_format_1($famer_transaction_shop->total_shipping) }}
                            </span>
                        </div>
                    @endforeach
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span>Biaya Bank</span>
                        <span>
                            Rp&nbsp;{{ number_format_1($farmerTransaction->bank_fees) }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span>Biaya Platform</span>
                        <span>
                            Rp&nbsp;{{ number_format_1($farmerTransaction->platform_fees) }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center border-top mb-3">
                        <span>Total</span>
                        <span>
                            Rp&nbsp;{{ number_format_1($farmerTransaction->total) }}
                        </span>
                    </div>

                    <h5 class="border-bottom mb-1">Detail Payment</h5>
                    <div class="d-flex flex-column align-items-start gap-1 mb-3">
                        <span><strong>Timestamp</strong>:
                            <br>{{ $farmerTransaction->created_at->formatLocalized('%d %B %Y') }}
                            {{ $farmerTransaction->created_at->format('H:i:s') }}</span>
                        <span><strong>Bank</strong>:
                            <br>{{ $farmerTransaction->bank_name }}</span>
                        <span><strong>Midtrans Token</strong>:
                            <br>{{ $farmerTransaction->midtrans_token }}</span>
                    </div>

                    <div class="accordion mt-3" id="accordionExample">
                        <div class="card accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
                                    data-bs-target="#accordionTwo" aria-expanded="false" aria-controls="accordionTwo">
                                    Detail Pembeli
                                </button>
                            </h2>
                            <div id="accordionTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body d-flex flex-column align-items-start gap-2">
                                    <span><strong>Nama</strong>:
                                        <br>{{ $farmerTransaction->user?->name ?? '-' }}</span>
                                    <span><strong>Nomor Handphone</strong>:
                                        <br>{{ $farmerTransaction->user?->phone_number ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="card accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
                                    data-bs-target="#accordionThree" aria-expanded="false"
                                    aria-controls="accordionThree">
                                    Detail Pengiriman
                                </button>
                            </h2>
                            <div id="accordionThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    @foreach ($farmerTransaction->farmer_transaction_shops as $farmer_transaction_shop)
                                        <h5 class="border-bottom mb-1">Toko {{ $farmer_transaction_shop->shop_name }}</h5>
                                        <div class="d-flex flex-column align-items-start gap-2 mb-2">
                                            @if ($farmer_transaction_shop->delivery_receipt)
                                                <span><strong>Resi</strong>: <br><span
                                                        id="resi-text">{{ $farmer_transaction_shop->delivery_receipt }}
                                                        <span class="cursor-pointer" id="copy-element" data-bs-toggle="tooltip"
                                                            data-bs-offset="0,4" data-bs-placement="top" data-bs-html="false"
                                                            title="Copy" onclick="copyResi(this)">
                                                            <i class='bx bx-copy-alt'></i>
                                                        </span></span>
                                                </span>
                                            @endif
                                            <span><strong>Kurir</strong>: <br>{{ $farmer_transaction_shop->courier }}</span>
                                            <span><strong>Tipe</strong>: <br>{{ $farmer_transaction_shop->type }}</span>
                                            <span><strong>Harga Pengiriman</strong>:
                                                <br>Rp&nbsp;{{ number_format($farmer_transaction_shop->total_shipping, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                    <h5 class="border-bottom mb-1">Detail Penerima</h5>
                                    <div class="d-flex flex-column align-items-start gap-2">
                                        <span><strong>Nama Penerima</strong>:
                                            <br>{{ $farmerTransaction->user_recipient_name }}</span>
                                        <span><strong>Nomor Handphone Penerima</strong>:
                                            <br>{{ $farmerTransaction->user_recipient_phone_number }}</span>
                                        <span><strong>Alamat Penerima</strong>:
                                            <br>{{ $farmerTransaction->user_recipient_address }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
                                    data-bs-target="#detailProduct" aria-expanded="false" aria-controls="detailProduct">
                                    Detail Produk
                                </button>
                            </h2>
                            <div id="detailProduct" class="accordion-collapse collapse" aria-labelledby="headingThree"
                                data-bs-parent="#accordionExample">
                                <div class="table-responsive text-wrap">
                                    <table class="table table-striped" id="table-crops">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Produk</th>
                                                <th>Quantity</th>
                                                <th>Harga</th>
                                                <th>Diskon</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($farmerTransaction->farmer_transaction_shops as $farmer_transaction_shop)
                                                <tr>
                                                    <td colspan="6">Toko {{ $farmer_transaction_shop->shop_name }}</td>
                                                </tr>
                                                @foreach ($farmer_transaction_shop->farmer_transaction_items as $farmer_transaction_item)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $farmer_transaction_item->product_name }}</td>
                                                        <td>{{ $farmer_transaction_item->quantity }}</td>
                                                        <td>Rp&nbsp;{{ number_format($farmer_transaction_item->product_price, 0, ',', '.') }}
                                                        </td>
                                                        <td>-Rp&nbsp;{{ number_format(floor($farmer_transaction_item->product_price * ($farmer_transaction_item->discount / 100)), 0, ',', '.') }}
                                                        </td>
                                                        <td>
                                                            Rp&nbsp;{{ number_format(
                                                                ($farmer_transaction_item->product_price - floor($farmer_transaction_item->product_price * ($farmer_transaction_item->discount / 100))) * $farmer_transaction_item->quantity,
                                                                0, ',', '.'
                                                            ) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
    </div>

    @include('bitanic.shop.transaction._modal-resi', ['transaction_id' => $farmerTransaction->id])

    @push('scripts')
        <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
        <script>
            function copyResi(copyElement) {
                // Get the text content from the span
                const spanText = document.getElementById("resi-text").textContent;

                // Create a temporary textarea element
                const tempTextarea = document.createElement("textarea");
                tempTextarea.value = spanText;

                // Append the textarea to the DOM
                document.body.appendChild(tempTextarea);

                // Select the text in the textarea
                tempTextarea.select();
                tempTextarea.setSelectionRange(0, 99999); // For mobile devices

                // Execute the copy command
                document.execCommand("copy");

                // Clean up: remove the temporary textarea
                document.body.removeChild(tempTextarea);

                console.dir(copyElement)

                document.querySelector('.tooltip-inner').textContent = "Copied"

                setTimeout(() => {
                    document.querySelector('.tooltip-inner').textContent = "Copy"
                }, 1000);
            }

            const shippingUpdate = async ({
                id,
                resi
            }) => {
                showSpinner()

                const settings = {
                    method: 'PUT',
                    headers: {
                        'x-csrf-token': "{{ csrf_token() }}",
                        'Accept': "application/json",
                    },
                    body: JSON.stringify({
                        'resi': resi
                    })
                }

                const [data, error] = await yourRequest(
                    "{{ route('bitanic.shop.transaction-shipping-update', 'ID') }}".replace('ID',
                        id), settings
                )

                if (error) {

                    deleteSpinner()

                    let errorMessage = ''

                    if ("messages" in error) {
                        let element = ``
                        for (const key in error.messages) {
                            if (Object.hasOwnProperty.call(error.messages, key)) {
                                error.messages[key].forEach(message => {
                                    element += `<li>${message}</li>`;
                                });
                            }
                        }

                        errorMessage = `<ul>${element}</ul>`
                    } else {
                        errorMessage = error.message
                    }

                    Swal.fire({
                        html: errorMessage,
                        icon: "error",
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }

                Swal.fire({
                    text: "Kamu berhasil update status!",
                    icon: "success",
                    showConfirmButton: false,
                    allowOutsideClick: false
                })

                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }

            const statusUpdate = async ({
                id,
            }) => {
                showSpinner()

                const settings = {
                    method: 'PUT',
                    headers: {
                        'x-csrf-token': "{{ csrf_token() }}",
                        'Accept': "application/json",
                    },
                }

                const [data, error] = await yourRequest(
                    "{{ route('bitanic.transaction-komodity.update-status', 'ID') }}".replace('ID',
                        id), settings
                )

                if (error) {

                    deleteSpinner()

                    let errorMessage = ''

                    if ("messages" in error) {
                        let element = ``
                        for (const key in error.messages) {
                            if (Object.hasOwnProperty.call(error.messages, key)) {
                                error.messages[key].forEach(message => {
                                    element += `<li>${message}</li>`;
                                });
                            }
                        }

                        errorMessage = `<ul>${element}</ul>`
                    } else {
                        errorMessage = error.message
                    }

                    Swal.fire({
                        html: errorMessage,
                        icon: "error",
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }

                Swal.fire({
                    text: "Kamu berhasil update status!",
                    icon: "success",
                    showConfirmButton: false,
                    allowOutsideClick: false
                })

                window.location.reload();
            }
            const deleteData = async ({
                id,
                name,
                deleteUrl,
                redirectUrl = null
            }) => {
                const result = await Swal.fire({
                    text: "Menghapus data tidak dapat dibatalkan, dan semua data yang berhubungan akan hilang",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Ya, hapus!",
                    cancelButtonText: "Tidak, batalkan",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                })

                if (!result.value) {
                    return false
                }

                showSpinner()

                const settings = {
                    method: 'DELETE',
                    headers: {
                        'x-csrf-token': "{{ csrf_token() }}",
                        'Accept': "application/json",
                    }
                }

                const [data, error] = await yourRequest(
                    deleteUrl.replace('ID',
                        id), settings
                )

                if (error) {

                    deleteSpinner()

                    let errorMessage = ''

                    if ("messages" in error) {
                        let element = ``
                        for (const key in error.messages) {
                            if (Object.hasOwnProperty.call(error.messages, key)) {
                                error.messages[key].forEach(message => {
                                    element += `<li>${message}</li>`;
                                });
                            }
                        }

                        errorMessage = `<ul>${element}</ul>`
                    } else {
                        errorMessage = error.message
                    }

                    Swal.fire({
                        html: errorMessage,
                        icon: "error",
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }

                Swal.fire({
                    text: "Kamu berhasil menghapus data " + name + "!",
                    icon: "success",
                    showConfirmButton: false,
                    allowOutsideClick: false
                })

                if (redirectUrl) {
                    window.location = redirectUrl
                } else {
                    window.location.reload();
                }
            }

            const changeStatus = (pumpStatus, pumpElement) => {
                if (!pumpElement) {
                    return false
                }
                switch (pumpStatus) {
                    case 0:
                        pumpElement.classList.remove("bg-on-status")
                        break;
                    case 1:
                        pumpElement.classList.add("bg-on-status")
                        break;

                    default:
                        break;
                }
            }

            function timestampToDate(timestamp) {
                const date = new Date(timestamp);
                const year = date.getFullYear();
                const month = date.getMonth() + 1; // Months are zero-based
                const day = date.getDate();
                const hours = date.getHours();
                const minutes = date.getMinutes();
                const seconds = date.getSeconds();

                // Format the date as desired (e.g., YYYY-MM-DD HH:MM:SS)
                const formattedDate = `* ${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                return formattedDate;
            }

            const updateDataLiteSensors = ({
                tds,
                ph,
                temperature,
                water_temperature,
                humidity,
                last_updated_telemetri
            }) => {
                document.querySelector('#sensor-tds').textContent = tds + ' ppm'
                document.querySelector('#sensor-ph').textContent = ph
                document.querySelector('#sensor-temperature').textContent = parseFloat(temperature).toFixed(2) + '°C'
                document.querySelector('#sensor-water-temperature').textContent = parseFloat(water_temperature).toFixed(2) +
                    '°C'
                document.querySelector('#sensor-humidity').textContent = parseFloat(humidity).toFixed(2) + '%'
                document.querySelector('#last-timestamp').textContent = timestampToDate(last_updated_telemetri)
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");
            })
        </script>
    @endpush
</x-app-layout>
