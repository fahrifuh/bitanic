<table>
    <tbody>
        <tr>
            <td>#</td>
            <td>N(mg/kg)</td>
            <td>P(mg/kg)</td>
            <td>K(mg/kg)</td>
            <td>EC(uS/cm)</td>
            <td>pH</td>
            <td>Suhu(°C)</td>
            <td>Kelembapan(%)</td>
            <td>Suhu Tanah(°C)</td>
            <td>Kelembapan Tanah(%)</td>
            <td>Latitude</td>
            <td>Longitude</td>
        </tr>
    @foreach($rscTelemetries as $rscTelemetri)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $rscTelemetri->samples->n }}</td>
            <td>{{ $rscTelemetri->samples->p }}</td>
            <td>{{ $rscTelemetri->samples->k }}</td>
            <td>{{ $rscTelemetri->samples->ec }}</td>
            <td>{{ optional($rscTelemetri->samples)->ph ?? '-' }}</td>
            <td>{{ $rscTelemetri->samples->ambient_temperature }}</td>
            <td>{{ $rscTelemetri->samples->ambient_humidity }}</td>
            <td>{{ $rscTelemetri->samples->soil_temperature }}</td>
            <td>{{ $rscTelemetri->samples->soil_moisture }}</td>
            <td>{{ $rscTelemetri->latitude }}</td>
            <td>{{ $rscTelemetri->longitude }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
