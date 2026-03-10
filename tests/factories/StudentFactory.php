<?php

namespace tests\factories;

use common\models\Student;
use common\components\BranchScope;

class StudentFactory
{
    private static $firstNames = [
        'Anvar',
        'Bekzod',
        'Dilshod',
        'Elyor',
        'Farhod',
        'Golib',
        'Hikmat',
        'Isfandiyor',
        'Javohir',
        'Kamron',
        'Laziz',
        'Mansur',
        'Nodir',
        'Olim',
        'Pulat',
        'Qodir',
        'Rustam',
        'Sardor',
        'Temur',
        'Ulugbek',
        'Vohid',
        'Xurshid',
        'Yodgor',
        'Zafar',
        'Aziza',
        'Barno',
        'Charos',
        'Dildora',
        'Ezoza',
        'Feruza',
        'Gulnora',
        'Hafiza',
        'Iroda',
        'Jamila',
        'Komila',
        'Lola',
        'Malika',
        'Nigora',
        'Oydin',
        'Parizoda',
        'Ra’no',
        'Sevara',
        'Tursunoy',
        'Umida',
        'Vazira',
        'Xolida',
        'Yulduz',
        'Zulayho',
        'Shaxnoza',
        'Nilufar'
    ];

    private static $lastNames = [
        'Abidov',
        'Baxromov',
        'Choriev',
        'Davronov',
        'Eshonov',
        'Fayzullaev',
        'Ganiev',
        'Hamidov',
        'Ismoilov',
        'Juraev',
        'Karimov',
        'Latipov',
        'Mahmudov',
        'Nazarov',
        'Orifov',
        'Polatov',
        'Qosimov',
        'Rahimov',
        'Sultonov',
        'Toshmatov'
    ];

    public static function make($overrides = [])
    {
        $gender = rand(1, 2);
        $firstName = self::$firstNames[array_rand(self::$firstNames)];
        $lastName = self::$lastNames[array_rand(self::$lastNames)];

        // Append gender suffix for RU-style last names if needed, but keeping it simple for now
        if ($gender == 2 && substr($lastName, -2) !== 'ov' && substr($lastName, -2) !== 'ev') {
            // logic for -ova / -eva
        }

        $data = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'middle_name' => 'O\'g\'li',
            'phone' => '+9989' . rand(0, 9) . rand(1000000, 9999999),
            'gender' => $gender,
            'status' => Student::STATUS_NEW,
            'branch_id' => BranchScope::getBranchId() ?: 1,
        ];

        return array_merge($data, $overrides);
    }

    public static function create($overrides = [])
    {
        $model = new Student();
        $model->setAttributes(self::make($overrides));
        if ($model->save()) {
            return $model;
        }
        throw new \Exception("Failed to save student: " . json_encode($model->errors));
    }

    public static function createMany($count, $overrides = [])
    {
        $students = [];
        for ($i = 0; $i < $count; $i++) {
            $students[] = self::create($overrides);
        }
        return $students;
    }
}
