<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Scholarship;

class ScholarshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $scholarships = [
            [
                'name' => 'Beasiswa Unggulan',
                'description' => 'Beasiswa untuk mahasiswa berprestasi dengan IPK minimal 3.5',
                'amount' => 5000000, // 5 juta per semester
                'provider' => 'Universitas Indonesia',
                'criteria' => json_encode([
                    'min_gpa' => 3.5,
                    'economic_status' => 'any',
                    'major' => null,
                    'max_age' => 25,
                ]),
                'application_deadline' => now()->addMonths(3),
                'is_active' => true,
            ],
            [
                'name' => 'Beasiswa Kurang Mampu',
                'description' => 'Beasiswa untuk mahasiswa dari keluarga kurang mampu',
                'amount' => 3000000, // 3 juta per semester
                'provider' => 'Kementerian Pendidikan',
                'criteria' => json_encode([
                    'min_gpa' => 3.0,
                    'economic_status' => 'low_income',
                    'major' => null,
                    'max_age' => 30,
                ]),
                'application_deadline' => now()->addMonths(2),
                'is_active' => true,
            ],
            [
                'name' => 'Beasiswa Teknik Informatika',
                'description' => 'Beasiswa khusus untuk mahasiswa Teknik Informatika berprestasi',
                'amount' => 4000000, // 4 juta per semester
                'provider' => 'PT Tech Corp',
                'criteria' => json_encode([
                    'min_gpa' => 3.25,
                    'economic_status' => 'any',
                    'major' => 'Teknik Informatika',
                    'max_age' => 26,
                ]),
                'application_deadline' => now()->addMonths(4),
                'is_active' => true,
            ],
            [
                'name' => 'Beasiswa Olahraga',
                'description' => 'Beasiswa untuk mahasiswa berprestasi di bidang olahraga',
                'amount' => 2500000, // 2.5 juta per semester
                'provider' => 'KONI',
                'criteria' => json_encode([
                    'min_gpa' => 2.75,
                    'economic_status' => 'any',
                    'major' => null,
                    'achievement' => 'sports',
                    'max_age' => 24,
                ]),
                'application_deadline' => now()->addMonths(1),
                'is_active' => true,
            ],
        ];

        foreach ($scholarships as $scholarship) {
            Scholarship::create($scholarship);
        }
    }
}
