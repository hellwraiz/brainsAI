<?php

namespace Database\Seeders;

use App\Models\Text;
use Illuminate\Database\Seeder;

class TextSeeder extends Seeder
{
    public function run(): void
    {

        Text::truncate();
        Text::create([
            'type' => 'work title',
            'content' => 'lorem ipsum'
        ]);
        Text::create([
            'type' => 'work desc',
            'content' => 'lorem ipsum'
        ]);
        Text::create([
            'type' => 'shorts title',
            'content' => 'lorem ipsum'
        ]);
        Text::create([
            'type' => 'shorts desc',
            'content' => 'lorem ipsum'
        ]);
        Text::create([
            'type' => 'about main title',
            'content' => 'lorem ipsum'
        ]);
        Text::create([
            'type' => 'about left title',
            'content' => 'lorem ipsum'
        ]);
        Text::create([
            'type' => 'about middle title',
            'content' => 'lorem ipsum'
        ]);
        Text::create([
            'type' => 'about right title',
            'content' => 'lorem ipsum'
        ]);
        Text::create([
            'type' => 'about left desc',
            'content' => 'lorem ipsum'
        ]);
        Text::create([
            'type' => 'about middle desc',
            'content' => 'lorem ipsum'
        ]);
        Text::create([
            'type' => 'about right desc',
            'content' => 'lorem ipsum'
        ]);
        Text::create([
            'type' => 'contact title',
            'content' => 'lorem ipsum'
        ]);
    }
}

