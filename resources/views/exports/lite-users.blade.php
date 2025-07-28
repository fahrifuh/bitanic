<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>No Handphone</th>
        <th>NIK</th>
        <th>Jenis Kelamin</th>
        <th>Tanggal Lahir</th>
        <th>Alamat</th>
        <th>Tanggal Dibuat</th>
        <th>Tanggal Diubah</th>
    </tr>
    </thead>
    <tbody>
    @foreach($liteUsers as $liteUser)
        <tr>
            <td>{{ $liteUser->name }}</td>
            <td>{{ $liteUser->phone_number }}</td>
            <td>{{ $liteUser->nik }}</td>
            <td>{{ $liteUser->gender == 'l' ? 'Laki-laki' : 'Perempuan' }}</td>
            <td>{{ $liteUser->birth_date }}</td>
            <td>{{ $liteUser->address }}</td>
            <td>{{ $liteUser->created_at }}</td>
            <td>{{ $liteUser->updated_at }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
