<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

// Image intervention
if (!function_exists('image_intervention')) {
    /**
     * Application Config.
     *
     * return @var array
     */
    function image_intervention($image, $path, $ratio = false)
    {
        $clientExtension = ($image->getClientOriginalExtension() == null && $image->getClientOriginalExtension() == '') ? 'png' : $image->getClientOriginalExtension();
        $name = strtoupper(Str::random(5)) . '-' . time() . '.' . $clientExtension;

        $imageResize = Image::make($image->getPathName());

        // $imageResize->height() > $imageResize->width() ? $w = null : $h = null;

        // $imageResize->resize($w, $h, function($constraint){
        //     $constraint->aspectRatio();
        // })->crop($w,$h);

        if ($ratio != false) {
            $originalWidth = $imageResize->width();
            $originalHeight = $imageResize->height();

            if ($originalWidth / $originalHeight > $ratio) {
                $height = $originalHeight;
                $width = floor($height * $ratio);
                $x = floor(($originalWidth - $width) / 2);
                $y = 0;
            } else {
                $width = $originalWidth;
                $height = floor($width / $ratio);
                $x = 0;
                $y = floor(($originalHeight - $height) / 2);
            }

            // Crop the image to the desired ratio
            $imageResize = $imageResize->crop($width, $height, $x, $y);
        }

        if (!file_exists($path)) {
            mkdir($path, 755, true);
        }
        // $final_path = public_path($path . $name);
        $imageResize->save(public_path($path . $name));

        return $path . $name;
    }
}

if (!function_exists('generateRandomString')) {
    /**
     * Application Config.
     *
     * return @var array
     */
    function generateRandomString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

if (!function_exists('get_month')) {

    function get_month($index = false)
    {
        $list =  [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        ];

        if (!$index) {
            return $list;
        }

        return $list[((int) $index) - 1];
    }
}

if (!function_exists('getHari')) {
    /**
     * Application Config.
     *
     * return @var string
     */
    function getHari(int $index)
    {
        $hari = [
            'Minggu',
            'Senin',
            'Selasa',
            'Rabu',
            'Kamis',
            'Jumat',
            'Sabtu'
        ];

        return $hari[$index];
    }
}

if (!function_exists('getDayIndex')) {
    /**
     * Application Config.
     *
     * @param  string  $day | minggu, senin, selasa, rabu, kamis, jum'at, sabtu
     * return @var int
     */
    function getDayIndex(String $day): int
    {
        $days = [
            'senin',
            'selasa',
            'rabu',
            'kamis',
            'jum\'at',
            'sabtu',
            'minggu',
        ];

        return array_search($day, $days);
    }
}

if (!function_exists('carbon_format_id_flex')) {

    function carbon_format_id_flex($date, $seperator = "-", $joins = "-")
    {
        list($day, $month, $year) = explode($seperator, $date);
        return $day . $joins . get_month($month) . $joins . $year;
    }
}

if (!function_exists('validateDate')) {

    function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}

if (!function_exists('number_format_1')) {
    /**
     * Application Config.
     *
     * return @var string
     */
    function number_format_1($value, $decimal = 2)
    {
        return number_format($value, $decimal, ',', '.');
    }
}

if (!function_exists('gender_format')) {
    /**
     * Application Config.
     *
     * return @var string
     */
    function gender_format($value)
    {
        if (!$value) {
            return null;
        }

        switch ($value) {
            case 'male':
                return "Laki-laki";
                break;
            case 'female':
                return "Perempuan";
                break;

            default:
                return null;
                break;
        }
    }
}

if (!function_exists('farmerCategory')) {
    /**
     * Application Config.
     *
     * return @var string
     */
    function farmerCategory($value = null)
    {
        if (!$value) {
            return [
                1 => 'Perorangan',
                2 => 'Kelompok',
                3 => 'Perusahaan',
                4 => 'Instansi',
                5 => 'Sekolah/Kampus'
            ];
        }

        switch ($value) {
            case 1:
                return 'Perorangan';
                break;
            case 2:
                return 'Kelompok';
                break;
            case 3:
                return 'Perusahaan';
                break;
            case 4:
                return 'Instansi';
                break;
            case 5:
                return 'Sekolah/Kampus';
                break;

            default:
                return null;
                break;
        }
    }
}

if (!function_exists('firebaseNotification')) {
    /**
     * Application Config.
     *
     * return @var string
     */
    function firebaseNotification(array $fcmTokens, array $messages)
    {
        if (!$fcmTokens) {
            return false;
        }
        if (!$messages) {
            return false;
        }

        $url = config('app.firebase_send_url');

        $serverKey = config('app.firebase_server_key');

        $data = array_merge([
            "registration_ids" => $fcmTokens,
        ], $messages);
        $encodedData = json_encode($data);

        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch);

        // FCM response
        return $result;
    }
}

if (!function_exists('hydroponicPumpLabel')) {
    /**
     * Get label for pump
     *
     */
    function hydroponicPumpLabel(String $pump): ?string
    {
        switch ($pump) {
            case "water":
                return 'Pompa Air';
                break;
            case "nutrient":
                return 'Pompa Nutrisi';
                break;
            case "ph_basa":
                return 'pH Basa';
                break;
            case "ph_asam":
                return 'pH Asam';
                break;
            case "mixer":
                return 'Mixer';
                break;

            default:
                return null;
                break;
        }
    }
}

if (!function_exists('phoneNumberFormat')) {
    /**
     * Get label for pump
     *
     */
    function phoneNumberFormat(String $phoneNumber): string
    {
        preg_match('/(\d{3})(\d{4})(\d{4})/', $phoneNumber, $matches);
        return "{$matches[1]}-{$matches[2]}-{$matches[3]}";
    }
}

if (!function_exists('deleteImage')) {
    /**
     * Get label for pump
     *
     */
    function deleteImage(?String $imagePath): bool
    {
        if (!$imagePath) {
            return false;
        }
        
        if (!\Illuminate\Support\Facades\File::exists(public_path($imagePath))) {
            return false;
        }

        \Illuminate\Support\Facades\File::delete(public_path($imagePath));

        return true;
    }
}
