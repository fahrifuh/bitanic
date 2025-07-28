<?php

namespace App\Enums;

enum UserGender: string
{
    case MALE = 'male';
    case FEMALE = 'female';

    public function getLabelText()
    {
        return match ($this) {
            self::MALE => 'Laki - laki',
            self::FEMALE => 'Perempuan',
        };
    }
}
