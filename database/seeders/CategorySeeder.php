<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Campeões' => 'CHAMPIONS',
            'Leais' => 'LOYAL',
            'Potenciais clientes leais' => 'POTENTIAL_LOYAL_CUSTOMERS',
            'Recentes' => 'RECENT',
            'Promissores' => 'PROMISING',
            'Precisam de Atenção' => 'NEED_ATTENTION',
            'Prestes a dormir' => 'ABOUT_TO_SLEEP',
            'Não pode perder' => 'DO_NOT_LOSE',
            'Em risco' => 'AT_RISK',
            'Hibernando' => 'HIBERNATING',
            'Perdido' => 'LOST'
        ];

        foreach ($categories as $name => $constant) {
            DB::table('categories')->updateOrInsert(
                ['name' => $name],
                ['name' => $name, 'code' => $constant]
            );
        }
    }
}
