<x-app-layout>

    @push('styles')
        <style>
            .bank-avatar {
                width: 100px;
            }

            .bank-avatar img {
                width: 100%;
                height: 100%;
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
                    href="{{ route('bitanic.member.index') }}">Member</a> / <a
                    href="{{ route('bitanic.member.show', $member->id) }}">{{ $member->name }}</a> /</span> {{ ucwords($type) }} Subscription
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
                <div class="row">
                    <div class="col-md-12">
                        <div class="float-start m-3">
                        </div>
                        <div class="float-end m-3">
                        </div>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th>Timestamp</th>
                                <th>Nama</th>
                                <th>No HP</th>
                                <th>NIK</th>
                                <th>Jenis Kelamin</th>
                                <th>Expired</th>
                                @if ($type == 'history')
                                    <th>Status</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($subscriptions as $subscription)
                                <tr>
                                    <td>{{ ($subscriptions->currentPage() - 1) * $subscriptions->perPage() + $loop->iteration }}
                                    </td>
                                    <td>{{ carbon_format_id_flex($subscription->created_at->format('d-m-Y'), '-', ' ') }}&nbsp;{{ $subscription->created_at->format('H:i:s') }}
                                    </td>
                                    <td>
                                        <a href="javascript:;" type="button" class="avatar pull-up"
                                            data-bs-toggle="modal" data-bs-target="#modalFoto"
                                            data-foto="{{ asset($subscription->user->farmer->picture) }}"
                                            style="display: inline-block;">
                                            <img src="{{ asset($subscription->user->farmer->picture) }}" alt="Avatar"
                                                class="rounded-circle" />
                                        </a>
                                        {{ $subscription->user->name }}
                                    </td>
                                    <td>{{ $subscription->user->phone_number }}</td>
                                    <td>{{ $subscription->user->farmer->nik }}</td>
                                    <td>
                                        {{ $subscription->user->farmer->gender == 'l' ? 'Laki - laki' : 'Perempuan' }}
                                    </td>
                                    <td>
                                        {{ carbon_format_id_flex(now()->parse($subscription->expired)->format('d-m-Y'),'-',' ') }}
                                    </td>
                                    @if ($type == 'history')
                                        <td>
                                            @if ($subscription->is_canceled == 1)
                                                <span class="badge bg-danger">Dibatalkan</span>
                                            @elseif ($subscription->is_canceled == 0 && now()->lte($subscription->expired))
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Expired</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $type == 'history' ? 8 : 7 }}" class="text-center">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                {{ $subscriptions->links() }}
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <!--/ Striped Rows -->
        </div>
    </div>

    @include('bitanic.lite.farmer._modal-picture')

    @push('scripts')
        <script>
            // btn picture
            const myModalPrev = new bootstrap.Modal(document.getElementById("modalFoto"), {});
            const modalFoto = document.getElementById('modalFoto')
            modalFoto.addEventListener('show.bs.modal', function(event) {
                // Button that triggered the modal
                const button = event.relatedTarget
                // Extract info from data-bs-* attributes
                // const recipient = button.getAttribute('data-bs-whatever')
                const modalTitle = modalFoto.querySelector('.modal-title')
                modalTitle.textContent = 'Foto Petani'

                for (let index = 0; index < button.attributes.length; index++) {
                    if (button.attributes[index].nodeName.includes('data-foto')) {
                        document.getElementById('iframe').src = button.attributes[index].nodeValue
                    }
                }

            })

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");
            });
        </script>
    @endpush
</x-app-layout>
