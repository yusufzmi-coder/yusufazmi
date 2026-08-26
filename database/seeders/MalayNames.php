<?php

declare(strict_types=1);

namespace Database\Seeders;

/** Faker has no ms_MY locale, so demo data needs its own name pools to look real. */
final class MalayNames
{
    public const MALE = [
        'Ahmad Danish', 'Muhammad Haziq', 'Aiman Firdaus', 'Nurul Iman', 'Amir Hakim',
        'Zulkifli', 'Syafiq Aiman', 'Irfan Hakimi', 'Adam Mikhail', 'Luqman Hakim',
        'Harith Iskandar', 'Rayyan Aqil', 'Daniel Aiman', 'Hafiz Rahman', 'Zikri Aiman',
        'Arif Danial', 'Naufal Haikal', 'Ammar Zafran', 'Idris Zaki', 'Faiz Hakimi',
        'Wan Azlan', 'Tengku Ariff', 'Khairul Anwar', 'Shahrul Nizam', 'Rizqin Haziq',
        'Lim Wei Jie', 'Tan Jia Hao', 'Rajesh Kumar', 'Arun Prakash', 'Chong Ming Han',
    ];

    public const FEMALE = [
        'Nur Aisyah', 'Siti Nurhaliza', 'Nurul Ain', 'Aina Sofea', 'Farah Adriana',
        'Batrisyia Humaira', 'Alya Sofia', 'Nur Damia', 'Insyirah Qaisara', 'Aleesya Zahra',
        'Wardina Husna', 'Nur Iman Sofea', 'Adriana Maisarah', 'Qaseh Balqis', 'Hana Zulaikha',
        'Nurin Alesha', 'Syafiqah Aina', 'Amirah Zahirah', 'Nadhirah Izzati', 'Puteri Sarah',
        'Wan Nur Alisya', 'Tengku Aisyah', 'Siti Khadijah', 'Nur Hidayah', 'Aqilah Sofea',
        'Lee Xin Yi', 'Chan Mei Ling', 'Priya Devi', 'Kavitha Rani', 'Ong Hui Wen',
    ];

    public const SURNAME = [
        'Abdullah', 'Ibrahim', 'Hassan', 'Osman', 'Ismail', 'Yusof', 'Rahman', 'Karim',
        'Salleh', 'Mahmud', 'Aziz', 'Bakar', 'Hamid', 'Zainal', 'Razak', 'Sulaiman',
        'Mansor', 'Jalil', 'Latif', 'Nordin', 'Tan', 'Lim', 'Wong', 'Rajan', 'Menon',
    ];

    public static function male(int $i): string
    {
        return self::MALE[$i % count(self::MALE)];
    }

    public static function female(int $i): string
    {
        return self::FEMALE[$i % count(self::FEMALE)];
    }

    public static function surname(int $i): string
    {
        return self::SURNAME[$i % count(self::SURNAME)];
    }
}
