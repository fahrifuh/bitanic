<style>
    * {
        font-family: Arial, Helvetica, sans-serif;
    }

    .page-break {
        page-break-after: always;
    }

    table {
        width: 100%;
    }

    table.rsc-telemetry,
    table.rsc-telemetry th,
    table.rsc-telemetry td {
        border-collapse: collapse;
        border: 1 solid #000;
    }

    th {
        font-size: .75rem;
        white-space: normal !important;
        padding: 5px;
    }

    td {
        font-size: .75rem;
        word-wrap: break-word;
        word-break: break-word;
        padding: 5px;
    }

    .text-center {
        text-align: center !important;
    }
</style>
<div>
    <div>
        <h3 class="text-center">RSC Telemetri</h3>
        <div>Perangkat: {{ $rscGarden->device->device_series }}</div>
        <div>Kebun: {{ $rscGarden->garden->name }}</div>
        <div>Timestamp: {{ $rscGarden->created_at }}</div>
        <div>Rata-rata N: {{ number_format($rscGarden->avg_n, 2) }} mg/kg |
            Rata-rata P: {{ number_format($rscGarden->avg_p, 2) }} mg/kg |
            Rata-rata K: {{ number_format($rscGarden->avg_k, 2) }} mg/kg</div>
    </div>
    <table class="rsc-telemetry">
        <tbody>
            <tr>
                <th>#</th>
                <th>N(mg/kg)</th>
                <th>P(mg/kg)</th>
                <th>K(mg/kg)</th>
                <th>EC(uS/cm)</th>
                <th>pH</th>
                <th>Suhu(°C)</th>
                <th>Kelembapan(%)</th>
                <th>Suhu Tanah(°C)</th>
                <th>Kelembapan Tanah(%)</th>
                <th>Latitude</th>
                <th>Longitude</th>
            </tr>
            @foreach($rscTelemetries as $rscTelemetri)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ $rscTelemetri->samples->n }}</td>
                    <td class="text-center">{{ $rscTelemetri->samples->p }}</td>
                    <td class="text-center">{{ $rscTelemetri->samples->k }}</td>
                    <td class="text-center">{{ $rscTelemetri->samples->ec }}</td>
                    <td class="text-center">{{ optional($rscTelemetri->samples)->ph ?? '-' }}</td>
                    <td class="text-center">{{ $rscTelemetri->samples->ambient_temperature }}</td>
                    <td class="text-center">{{ $rscTelemetri->samples->ambient_humidity }}</td>
                    <td class="text-center">{{ $rscTelemetri->samples->soil_temperature }}</td>
                    <td class="text-center">{{ $rscTelemetri->samples->soil_moisture }}</td>
                    <td>{{ $rscTelemetri->latitude }}</td>
                    <td>{{ $rscTelemetri->longitude }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
