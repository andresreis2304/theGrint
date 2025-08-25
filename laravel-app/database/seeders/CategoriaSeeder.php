<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $names = ['Drivers','Woods','Hybrids','Driving Irons','Irons','Wedges','Putters'];

        foreach ($names as $name) {
            DB::table('categoria')->insert(['nombre' => $name]);
        }
    }
}
