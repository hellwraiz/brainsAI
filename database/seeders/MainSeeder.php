<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Main;
use Illuminate\Database\Seeder;

class MainSeeder extends Seeder
{
    public function run(): void
    {
        Main::create([
            'title' => 'Product One',
            'description' => 'This is the first product.',
            'background_video' => 'background1.mp4',
        ]);

        Main::create([
            'title' => 'Product Two',
            'description' => 'This is the second product.',
            'background_video' => 'background2.mp4',
        ]);
    }
}

