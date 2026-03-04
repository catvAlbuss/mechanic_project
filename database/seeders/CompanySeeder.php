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
        Company::updateOrCreate([
            'avatar'=>null,
            'ruc'=>'20123456789',
            'company_name'=>'Taller Mecánico Herrera S.A.C.',
            'address'=>'Av. Los artesanos 123',
            'district'=>'Huánuco',
            'province'=>'Huánuco',
            'department'=>'Huánuco',
            'state'=>'active',
            'registration_date'=>now(),
            'config'=>[''],
        ]);
    }
}
