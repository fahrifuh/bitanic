<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Timestamp</th>
        <th>Suhu(°C)</th>
        <th>Kelembapan(%)</th>
        <th>TDS/PPM</th>
        <th>pH</th>
        <th>Volume Air</th>
    </tr>
    </thead>
    <tbody>
    @foreach($hydroponicDeviceTelemetries as $hydroponicDeviceTelemetry)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $hydroponicDeviceTelemetry->created_at }}</td>
            <td>{{ $hydroponicDeviceTelemetry->sensors->temperature }}</td>
            <td>{{ $hydroponicDeviceTelemetry->sensors->humidity }}</td>
            <td>{{ $hydroponicDeviceTelemetry->sensors->tdm }}</td>
            <td>{{ $hydroponicDeviceTelemetry->sensors->ph }}</td>
            <td>{{ $hydroponicDeviceTelemetry->sensors->water_volume }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
