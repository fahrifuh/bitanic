<x-app-layout>

  @push('styles')
  {{-- Cluster --}}
  <link rel="stylesheet" href="{{ asset('css/MarkerCluster.css') }}">
  <link rel="stylesheet" href="{{ asset('css/MarkerCluster.Default.css') }}">
  <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">
  <style>
    #myMap {
      height: 250px;
    }

    .leaflet-legend {
      background-color: #f5f5f9;
      border-radius: 10%;
      padding: 10px;
      color: #3e8f55;
      box-shadow: 4px 3px 5px 5px #8d8989a8;
    }

    #previewContainer {
      width: 100%;
      position: relative;
      padding-bottom: 56.25%; /* 16:9 aspect ratio (9/16 * 100) */
      overflow: hidden;
    }

    #previewImage {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  </style>
  @endpush
  {{-- Header --}}
  <x-slot name="header">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / Toko / </span> <a href="{{ route('bitanic.shop.index') }}">{{ $shop->name }}</a> / Edit</h4>
  </x-slot>
  {{-- End Header --}}

  <div class="row">
    <div class="col-md-12">
      <!-- Striped Rows -->
      <div class="card">
        <div class="card-body">
          <form action="{{ route('bitanic.shop.update') }}" method="POST" id="form-product" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <!-- Validation Errors -->
            <x-auth-validation-errors class="mb-4" :errors="$errors" />

            @if (session()->has('success'))
                <div class="alert alert-success" role="alert">{{ session()->get('success') }}</div>
            @endif

            <div class="row g-2 mb-3">
              <div class="col mb-0">
                <label for="data-input-name" class="form-label">Nama Toko</label>
                <input type="text" id="data-input-name" class="form-control" name="name" value="{{ $shop->name }}" />
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <label for="data-input-alamat" class="form-label">Alamat</label>
                <textarea class="form-control" id="data-input-alamat" name="address" rows="2" placeholder="Jl. XXX">{{ $shop->address }}</textarea>
              </div>
            </div>
            <div class="row g-2 mb-3">
              <div class="col">
                <label for="data-input-lat" class="form-label">Latitude</label>
                <input type="numeric" class="form-control" id="data-input-lat" name="latitude" value="{{ $shop->latitude }}" />
              </div>
              <div class="col">
                <label for="data-input-lng" class="form-label">Longitude</label>
                <input type="numeric" class="form-control" id="data-input-lng" name="longitude" value="{{ $shop->longitude }}" />
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <div class="bg-info text-white p-3 rounded" role="alert" id="alert-map">Klik 2 kali pada peta untuk set marker toko.</div>
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <div id="myMap"></div>
              </div>
            </div>
            <div class="row g-3">
                <div class="col-12">
                  <div class="bg-info text-white p-3 rounded" role="alert" id="alert-map">Foto tidak wajib diisi</div>
                </div>
                <div class="col-12 col-md-3 col-lg-3">
                  <div id="previewContainer">
                    <img id="previewImage" src="{{ asset($shop->picture) }}" alt="Preview Image" />
                  </div>
                </div>
                <div class="col-12 col-md-9">
                    <label for="fileInput" class="form-label">Foto Toko</label>
                    <input class="form-control" type="file" name="picture" id="fileInput" accept="image/png, image/jpg, image/jpeg"
                    aria-describedby="defaultFormControlHelp"/>
                    <div id="defaultFormControlHelp" class="form-text">
                    Format: PNG,JPG,JPEG; Ratio 16:9;Size: 10MB;
                    </div>
                </div>
            </div>
            <div class="row my-3 g-2">
              <div class="col-12 col-md-6 col-lg-6">
                <label for="data-input-bank-account" class="form-label">No Rekening</label>
                <input type="text" id="data-input-bank-account" class="form-control" name="bank_account" value="{{ $shop->bank_account }}" />
              </div>
              <div class="col-12 col-md-6 col-lg-6">
                <label for="data-input-bank-type" class="form-label">Bank</label>
                <select class="form-select" id="data-input-bank-type" name="bank_id" aria-label="Default select example">
                    @foreach ($withdrawalBanks as $id => $name)
                        <option value="{{ $id }}" @if($name == $shop->bank_type) selected @endif>{{ $name }}</option>
                    @endforeach
                </select>
              </div>
            </div>
            <div class="row">
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
  <script src="{{ asset('leaflet/leaflet.js') }}"></script>
  <script src="{{ asset('theme/js/ui-popover.js') }}"></script>
  <script>
    let latlngs = [];
    let gardensMarker = [];
    let marker;
    const days = ['minggu', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];

    let stateData = {
            lat: -6.869080223722067,
            lng: 107.72491693496704
        };

    const randomNumber = (min, max) => {
      return Math.floor(Math.random() * (max - min) + min)
    }

    function getColor(d) {
      return d > 1000 ? '#800026' :
        d > 500 ? '#BD0026' :
        d > 200 ? '#E31A1C' :
        d > 100 ? '#FC4E2A' :
        d > 50 ? '#FD8D3C' :
        d > 20 ? '#FEB24C' :
        d > 10 ? '#FED976' :
        '#FFEDA0';
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

    const btnSubmit = document.getElementById('submit-btn')
    btnSubmit.addEventListener('submit', e => {
      // Show loading indication
      btnSubmit.setAttribute('data-kt-indicator', 'on');

      // Disable button to avoid multiple click
      btnSubmit.disabled = true;

      // document.getElementById('form-product').submit()
    })

    document.addEventListener("DOMContentLoaded", () => {
      console.log("Hello World!");

      let latitude = document.querySelector('[name="latitude"]').value
      let longitude = document.querySelector('[name="longitude"]').value
      latlngs = [parseFloat(latitude), parseFloat(longitude)]

      // Layer MAP
      const map = L.map('myMap', {
        preferCanvas: true,
        layers: [googleStreets],
        zoomControl: true
      }).setView(latlngs, 12);

      document.getElementById('fileInput').addEventListener('change', function(event) {
          var file = event.target.files[0];

          var reader = new FileReader();
          reader.onload = function(event) {
              document.getElementById('previewImage').src = event.target.result;
          };

          reader.readAsDataURL(file);
      });

      map.doubleClickZoom.disable();

      marker = L.marker(latlngs, {
        draggable: false
      }).addTo(map)

      map.on('dblclick', function(e) {
        stateData.lat = e.latlng.lat;
        stateData.lng = e.latlng.lng;

        marker.setLatLng([stateData.lat, stateData.lng])
        fillForm();
      });

      // Event input latitude
      $('body').on('input', '#data-input-lat', function() {
        stateData.lat = $('#data-input-lat').val();
        var newLatLng = new L.LatLng(stateData.lat, stateData.lng);
        marker.setLatLng(newLatLng);

        defaultCoordinate = new L.LatLng(stateData.lat, stateData.lng);
        map.panTo(defaultCoordinate);
      });

      // Event input longitude
      $('body').on('input', '#data-input-lng', function() {
        stateData.lng = $('#data-input-lng').val();
        var newLatLng = new L.LatLng(stateData.lat, stateData.lng);
        marker.setLatLng(newLatLng);

        defaultCoordinate = new L.LatLng(stateData.lat, stateData.lng);
        map.panTo(defaultCoordinate);
      });

      // Fill form
      function fillForm() {
        $(`#data-input-lat`).val(stateData.lat);
        $(`#data-input-lng`).val(stateData.lng);
      }
    })
  </script>
  @endpush
</x-app-layout>
