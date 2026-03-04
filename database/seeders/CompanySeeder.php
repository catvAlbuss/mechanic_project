<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

<<<<<<<<< Temporary merge branch 1
use function Symfony\Component\Clock\now;

=========
>>>>>>>>> Temporary merge branch 2
class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            [
                'ruc' => '20123456789',
                'avatar' => null,
                'company_name' => 'Taller Mecanico Herrera S.A.C.',
                'address' => 'Av. Los artesanos 123',
                'district' => 'Huanuco',
                'province' => 'Huanuco',
                'department' => 'Huanuco',
                'state' => 'active',
                'registration_date' => now()->toDateString(),
                'config' => [
                    'theme' => 'light',
                    'timezone' => 'America/Lima',
                    'language' => 'es',
                ],
            ],
            [
                'ruc' => '20987654321',
                'avatar' => null,
                'company_name' => 'Servicios Automotrices Norte S.A.C.',
                'address' => 'Jr. Progreso 456',
                'district' => 'Huanuco',
                'province' => 'Huanuco',
                'department' => 'Huanuco',
                'state' => 'active',
                'registration_date' => now()->toDateString(),
                'config' => [
                    'theme' => 'dark',
                    'timezone' => 'America/Lima',
                    'language' => 'es',
                ],
            ],
        ];

        foreach ($companies as $company) {
            Company::updateOrCreate(
                ['ruc' => $company['ruc']],
                $company
            );
        }
    }
}
