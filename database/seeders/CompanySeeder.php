<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use function Symfony\Component\Clock\now;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            [
                'avatar' => 'companies/default1.png',
                'ruc' => '12345678901',
                'company_name' => 'Empresa Demo 1',
                'address' => 'Av. Principal 123',
                'district' => 'Miraflores',
                'province' => 'Lima',
                'department' => 'Lima',
                'state' => 'active',
                'registration_date' => now(),
                'config' => json_encode([
                    'theme' => 'light',
                    'timezone' => 'America/Lima',
                    'language' => 'es'
                ]),
            ],
            [
                'avatar' => 'companies/default2.png',
                'ruc' => '10987654321',
                'company_name' => 'Empresa Demo 2',
                'address' => 'Jr. Secundario 456',
                'district' => 'San Isidro',
                'province' => 'Lima',
                'department' => 'Lima',
                'state' => 'active',
                'registration_date' => now(),
                'config' => json_encode([
                    'theme' => 'dark',
                    'timezone' => 'America/Lima',
                    'language' => 'en'
                ]),
            ]
        ];

        foreach($companies as $company){
            Company::firstOrCreate(
                ['ruc' => $company['ruc']],
                $company
            );
        }
    }
}
