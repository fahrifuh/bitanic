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
        <th>Desa</th>
        <th>Kecamatan</th>
        <th>Kabupaten/Kota</th>
        <th>Provinsi</th>
    </tr>
    </thead>
    <tbody>
    @foreach($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->phone_number }}</td>
            <td>{{ $user->farmer->nik }}</td>
            <td>{{ $user->farmer->gender == 'l' ? 'Laki-laki' : 'Perempuan' }}</td>
            <td>{{ $user->farmer->birth_date }}</td>
            <td>{{ $user->farmer->address }}</td>
            <td>{{ $user->created_at }}</td>
            <td>{{ $user->updated_at }}</td>
            <td>{{ $user->subdistrict?->subdis_name }}</td>
            <td>{{ $user->subdistrict?->district?->dis_name }}</td>
            <td>{{ $user->subdistrict?->district?->city?->city_name }}</td>
            <td>{{ $user->subdistrict?->district?->city?->province?->prov_name }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
