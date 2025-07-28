<x-app-layout>

  @push('styles')
  {{-- Cluster --}}
  <link rel="stylesheet" href="{{ asset('css/MarkerCluster.css') }}">
  <link rel="stylesheet" href="{{ asset('css/MarkerCluster.Default.css') }}">
  <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">
  <link rel="stylesheet" href="{{ asset('css/extend.css') }}">
  <style>
    #myMap {
      height: 450px;
    }

    .leaflet-legend {
      background-color: #f5f5f9;
      border-radius: 10%;
      padding: 10px;
      color: #3e8f55;
      box-shadow: 4px 3px 5px 5px #8d8989a8;
    }

    .preview-image {
      width: calc(100% - 100px);
      /* Adjust the width as desired */
      object-fit: cover;
      aspect-ratio: 4/3;
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
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master @if(Auth::user()->role == 'admin') / <a href="{{ route('bitanic.farmer.index') }}">Data Petani</a> @endif / {{ $farmer->full_name }} / <a href="{{ route('bitanic.land.index', $farmer->id) }}">Data Lahan</a> / </span> create</h4>
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
          <form action="{{ route('bitanic.land.update', ['farmer' => $farmer->id, 'land' => $land->id]) }}" method="POST" id="form-product" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <!-- Validation Errors -->
            <x-auth-validation-errors class="mb-4" :errors="$errors" />

            <div class="row g-2 mb-3">
              <div class="col mb-0">
                <label for="data-input-name" class="form-label">Nama Lahan</label>
                <input type="text" id="data-input-name" class="form-control" name="name" value="{{ $land->name }}" />
              </div>
              <div class="col mb-0">
                <label for="data-input-area" class="form-label">Luas</label>
                <div class="input-group">
                    <input type="number" step="0.01" min="0" class="form-control" id="data-input-area" name="area" value="{{ $land->area }}" placeholder="0" aria-label="0" aria-describedby="basic-addon13" />
                    <span class="input-group-text" id="basic-addon13">m²</span>
                </div>
              </div>
            </div>
            <div class="row g-2">
              <div class="col mb-3">
                <label for="data-input-latitude" class="form-label">Latitude</label>
                <input type="numeric" class="form-control" id="data-input-latitude" name="latitude" value="{{ $land->latitude }}" />
              </div>
              <div class="col mb-3">
                <label for="data-input-longitude" class="form-label">Longitude</label>
                <input type="numeric" class="form-control" id="data-input-longitude" name="longitude" value="{{ $land->longitude }}" />
              </div>
              <div class="col mb-3">
                <label for="data-input-altitude" class="form-label">Altitude</label>
                <div class="input-group">
                    <input type="number" step=".01" class="form-control" id="data-input-altitude" name="altitude" value="{{ $land->altitude }}" aria-describedby="basic-altitude" />
                    <span class="input-group-text" id="basic-altitude">mdpl</span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <div class="bg-info text-white p-3 rounded" role="alert" id="alert-map">Klik 2 kali pada peta untuk set marker kebun.</div>
              </div>
            </div>
            <input type="hidden" name="polygon" id="data-input-polygon" value="{{ json_encode($land->polygon) }}" />
            <div class="row g-3 mb-3">
              <div class="col-12">
                <div id="myMap"></div>
              </div>
              <div class="col-12">
                  <label for="data-input-alamat" class="form-label">Warna Polygon</label>
                  <input class="form-control" id="data-input-color" type="color" name="color" value="#{{ $land->color }}" />
              </div>
            </div>
            <div class="row">
              <div class="col-12 col-md-3 mb-3">
                <label for="" class="form-label">Foto</label>
                <img src="{{ asset($land->image) }}" alt="preview-img" class="preview-image img-thumbnail">
              </div>
              <div class="col-12 col-md-9 mb-3">
                <div class="row g-2">
                    <div class="col-12">
                        <div class="bg-info text-white p-3 rounded" role="alert" id="alert-map">Foto dapat dikosongkan</div>
                    </div>
                    <div class="col-12">
                        <label for="data-input-image" class="form-label">Foto</label>
                        <input class="form-control" type="file" id="data-input-image"
                            name="image" accept="image/png, image/jpg, image/jpeg"
                            aria-describedby="pictureHelp" />
                        <div id="pictureHelp" class="form-text">Format gambar JPG, JPEG, PNG. Maks.
                            2MB</div>
                    </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <label for="data-input-alamat" class="form-label">Alamat</label>
                <textarea class="form-control" id="data-input-alamat" name="address" rows="2" placeholder="Jl. XXX">{{ $land->address }}</textarea>
              </div>
            </div>
            <div class="row">
                <div class="col">
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
  <script src="{{ asset('js/extend.js') }}"></script>
  <!-- <script src="{{ asset('js/extra.js') }}"></script> -->
  <script>
    let latlngs = [];
    let gardensMarker = [];
    let gardensPolygon = [];
    let marker, polygon;
    const days = ['minggu', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];

    function eventFile(input) {
        // Validate
        if (input.files && input.files[0]) {
            let fileSize = input.files[0].size / 1024 / 1024; //MB Format
            let fileType = input.files[0].type;

            // validate size
            if(fileSize > 10){
                showAlert('Ukuran File tidak boleh lebih dari 2mb !');
                input.value = '';
                return false;
            }

            // validate type
            if(["image/jpeg", "image/jpg", "image/png"].indexOf(fileType) < 0){
                showAlert('Format File tidak valid !');
                input.value = '';
                return false;
            }

            let reader = new FileReader();

            reader.onload = function(e) {
                document.querySelector('.preview-image').setAttribute('src', e.target.result)
            }

            reader.readAsDataURL(input.files[0]); // convert to base64 string
        }
    }

    function showAlert (title) {
        Swal.fire({
            icon: 'warning',
            title,
            confirmButtonText: 'Kembali',
        })
    }

    let stateData = {
            lat: "{{ $land->latitude }}",
            lng: "{{ $land->longitude }}",
            polygon: JSON.parse("{{ json_encode($land->polygon) }}"),
            layerPolygon: null
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
    // Layer MAP
    const map = L.map('myMap', {
      preferCanvas: true,
      layers: [googleStreets],
      zoomControl: true
    }).setView([stateData.lat, stateData.lng], 16);

    var editableLayers = new L.FeatureGroup();
    map.addLayer(editableLayers);

    var optionDraw = {
        position: 'topright',
        draw: {
            polyline: false,
            polygon: {
                allowIntersection: false, // Restricts shapes to simple polygons
                drawError: {
                    message: '<strong>Oh snap!<strong> you can\'t draw that!' // Message that will show when intersect
                },
            },
            circle: false, // Turns off this drawing tool
            circlemarker: false, // Turns off this drawing tool
            rectangle: false,
            marker: false
        },
        edit: {
            featureGroup: editableLayers, //REQUIRED!!
            remove: true
        },
    };

    var drawControl = new L.Control.Draw(optionDraw);
    map.addControl(drawControl);

    var getPopupContent = function(layer) {
        // Marker - add lat/long
        if (layer instanceof L.Marker || layer instanceof L.CircleMarker) {
            return strLatLng(layer.getLatLng());
        // Circle - lat/long, radius
        } else if (layer instanceof L.Circle) {
            var center = layer.getLatLng(),
                radius = layer.getRadius();
            return "Center: "+strLatLng(center)+"<br />"
                    +"Radius: "+_round(radius, 2)+" m";
        // Rectangle/Polygon - area
        } else if (layer instanceof L.Polygon) {
            var ll = layer._defaultShape ? layer._defaultShape() : layer.getLatLngs(),
                area = L.GeometryUtil.geodesicArea(ll);
            luasPolygon = area.toLocaleString();
            document.querySelector('#data-input-area').value = area.toFixed(2)
            return "Area: "+L.GeometryUtil.readableArea(area, true);
        // Polyline - distance
        } else if (layer instanceof L.Polyline) {
            var ll = layer._defaultShape ? layer._defaultShape() : layer.getLatLngs(),
                distance = 0;
            if (ll.length < 2) {
                return "Distance: N/A";
            } else {
                for (var i = 0; i < ll.length-1; i++) {
                    distance += ll[i].distanceTo(ll[i+1]);
                }
                return "Distance: "+_round(distance, 2)+" m";
            }
        }
        return null;
    };

    map.on(L.Draw.Event.CREATED, function (e) {
        var type = e.layerType,
            layer = e.layer;

        console.log(e);

        if (type === 'polygon') {
            stateData.polygon = layer.getLatLngs()[0];

            layer.setStyle({
                color: document.querySelector('#data-input-color').value
            })

            fillPolygon(stateData.polygon.map((val, _) => [val.lat, val.lng]))
            stateData.layerPolygon = layer
        }

        if(editableLayers && editableLayers.getLayers().length!==0){
            editableLayers.clearLayers();
        }

        var content = getPopupContent(layer);
        if (content !== null) {
            layer.bindPopup(content);
        }

        editableLayers.addLayer(layer);
        // drawnItems.addLayer(layer)
    });

    map.on('draw:edited', function (e) {
        var layers = e.layers;
        layers.eachLayer(function (layer) {
            stateData.polygon = layer.getLatLngs()[0];
            fillPolygon(stateData.polygon.map((val, _) => [val.lat, val.lng]))
            let content = getPopupContent(layer);
            if (content !== null) {
                layer.setPopupContent(content);
                stateData.layerPolygon = layer
            }
        });
    });

    map.on('draw:deleted', function (e) {
        stateData.polygon = []
        document.getElementById('data-input-polygon').value = null
        stateData.layerPolygon = null
    });


    map.on('dblclick', function(e){
        if (marker) {
            marker.remove()
        }
        stateData.lat = e.latlng.lat;
        stateData.lng = e.latlng.lng;

        marker = L.marker([stateData.lat,stateData.lng]).bindPopup(`Lokasi`, {closeButton: false}).addTo(map).openPopup();
    //   radiusMove();
        fillForm()
    });

    const btnSubmit = document.getElementById('submit-btn')
    btnSubmit.addEventListener('submit', e => {
      // Show loading indication
      btnSubmit.setAttribute('data-kt-indicator', 'on');

      // Disable button to avoid multiple click
      btnSubmit.disabled = true;

      // document.getElementById('form-product').submit()
    })

    // Fill form
    function fillForm() {
        $(`#data-input-latitude`).val(stateData.lat);
        $(`#data-input-longitude`).val(stateData.lng);
    }

    function fillPolygon(polygon) {
        document.getElementById('data-input-polygon').value = JSON.stringify(polygon)
    }


    function addNonGroupLayers(sourceLayer, targetGroup) {
        if (sourceLayer instanceof L.LayerGroup) {
            sourceLayer.eachLayer(function (layer) {
                addNonGroupLayers(layer, targetGroup);
            });
            console.log('added to new layer');
        } else {
            console.log('added to layer');
            targetGroup.addLayer(sourceLayer);
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        console.log("Hello World!");

        map.doubleClickZoom.disable();

        // For Editing
        let geojson = {
            "type": "FeatureCollection",
            "features": [{
                "type": "Feature",
                "properties": {},
                "geometry": {
                    "type": "Polygon",
                    "coordinates": stateData.polygon.map((val, _) => [val[1], val[0]])
                }
            }]
        };

        let polyGroup = L.polygon(stateData.polygon, {
            color: document.querySelector('#data-input-color').value
        }).addTo(map)

        stateData.layerPolygon = polyGroup

        document.querySelector('#data-input-color').addEventListener('change', e => {
            console.dir(e.target.value)
            stateData.layerPolygon?.setStyle({
                color: e.target.value
            })
        })

        // let geoJsonGroup = L.geoJson(geojson).addTo(map);
        addNonGroupLayers(polyGroup, editableLayers);

        marker = L.marker([stateData.lat, stateData.lng], {
            draggable: false
        }).addTo(map)

        // Handle File upload
        document.querySelector('#data-input-image').addEventListener('change', e => {
            if (e.target.files.length == 0) {
                // $('.profile').attr('src', defaultImage);
            } else {
                eventFile(e.target);
            }
        })

        //   map.on('dblclick', function(e) {
        //     if (document.getElementById('polygon-switch').checked) {
        //       polygon.remove()
        //       latlngs.push([e.latlng.lat, e.latlng.lng])
        //       polygon = L.polygon(latlngs, {
        //         color: 'red'
        //       }).addTo(map);

        //       document.getElementById('data-input-polygon').value = JSON.stringify(latlngs)
        //     } else {
        //       marker.remove()
        //       stateData.lat = e.latlng.lat;
        //       stateData.lng = e.latlng.lng;

        //       marker = L.marker([stateData.lat, stateData.lng]).addTo(map).openPopup();
        //     }
        //   });

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

    })
  </script>
  @endpush
</x-app-layout>
