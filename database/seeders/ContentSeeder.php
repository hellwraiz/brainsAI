<?php

namespace Database\Seeders;

use App\Models\Content;
use App\Models\ScrollImage;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        // Adding sample video content
        Content::create([
            'title' => 'Product One',
            'description' => 'This is the first product.',
            'content_url' => '/storage/content/sample_videos/background1.mp4',
            'isLocal' => true,
            'isVideo' => true,
            'order' => 0
        ]);

        Content::create([
            'title' => 'Product Two. This one has a longer description to test the text wrapping functionality in the UI.',
            'description' => 'This is the second product. This one has a longer description to test the text wrapping functionality in the UI.',
            'content_url' => '/storage/content/sample_videos/background2.mp4',
            'isLocal' => true,
            'isVideo'=> true,
            'order' => 1
        ]);

        // Adding sample short content
        Content::create([
            'title' => 'Product One',
            'description' => 'This is the first product.',
            'content_url' => '/storage/content/sample_videos/background1.mp4',
            'isLocal' => true,
            'isVideo' => false,
            'order' => 0
        ]);

        Content::create([
            'title' => 'Product Two. This one has a longer description to test the text wrapping functionality in the UI.',
            'description' => 'This is the second product. This one has a longer description to test the text wrapping functionality in the UI.',
            'content_url' => '/storage/content/sample_videos/background2.mp4',
            'isLocal' => true,
            'isVideo'=> false,
            'order' => 1
        ]);

        // Adding sample scroll images
        ScrollImage::create([
            'img_url' => '/storage/aboutScroll/sample_images/image1.webp',
            'order' => 0
        ]);

        ScrollImage::create([
            'img_url' => '/storage/aboutScroll/sample_images/image2.webp',
            'order' => 1
        ]);

        ScrollImage::create([
            'img_url' => '/storage/aboutScroll/sample_images/image3.webp',
            'order' => 2
        ]);

        ScrollImage::create([
            'img_url' => '/storage/aboutScroll/sample_images/image4.webp',
            'order' => 3
        ]);

        ScrollImage::create([
            'img_url' => '/storage/aboutScroll/sample_images/image5.webp',
            'order' => 4
        ]);

        ScrollImage::create([
            'img_url' => '/storage/aboutScroll/sample_images/image6.webp',
            'order' => 5
        ]);

        ScrollImage::create([
            'img_url' => '/storage/aboutScroll/sample_images/image7.webp',
            'order' => 6
        ]);

        ScrollImage::create([
            'img_url' => '/storage/aboutScroll/sample_images/image8.webp',
            'order' => 7
        ]);

        ScrollImage::create([
            'img_url' => '/storage/aboutScroll/sample_images/image9.webp',
            'order' => 8
        ]);
    }
}

