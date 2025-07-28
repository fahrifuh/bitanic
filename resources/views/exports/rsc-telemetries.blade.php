<table>
    <thead>
    <tr>
        <th>#</th>
        <th>N(mg/kg)</th>
        <th>P(mg/kg)</th>
        <th>K(mg/kg)</th>
        <th>EC</th>
        <th>Suhu(°C)</th>
        <th>Kelembapan(%)</th>
        <th>Suhu Tanah(°C)</th>
        <th>Kelembapan Tanah(%)</th>
        <th>Latitude</th>
        <th>Longitude</th>
    </tr>
    </thead>
    <tbody>
    @foreach($rscTelemetries as $rscTelemetri)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $rscTelemetri->samples->n }}</td>
            <td>{{ $rscTelemetri->samples->p }}</td>
            <td>{{ $rscTelemetri->samples->k }}</td>
            <td>{{ $rscTelemetri->samples->ec }}</td>
            <td>{{ $rscTelemetri->samples->temperature }}</td>
            <td>{{ $rscTelemetri->samples->moisture }}</td>
            <td>{{ $rscTelemetri->samples->soil_temperature }}</td>
            <td>{{ $rscTelemetri->samples->soil_moisture }}</td>
            <td>{{ $rscTelemetri->samples->latitude }}</td>
            <td>{{ $rscTelemetri->samples->longitude }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
