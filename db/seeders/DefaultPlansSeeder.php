<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class DefaultPlansSeeder extends AbstractSeed
{
    public function run(): void
    {
        $table = $this->table('plans');

        $data = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Perfect to get started. 1GB storage and 20GB monthly bandwidth.',
                'storage_limit_bytes' => 1073741824,
                'bandwidth_limit_bytes' => 21474836480,
                'max_file_size_bytes' => 104857600,
                'allowed_mime_types' => '["jpg","jpeg","png","gif","webp","bmp","mp3","mp4","pdf","txt"]',
                'price' => '0.00',
                'currency' => 'MZN',
                'is_active' => 1,
                'sort_order' => 1,
            ],
            [
                'name' => 'Plus',
                'slug' => 'plus',
                'description' => 'Ideal for small projects. 5GB storage and 50GB monthly bandwidth.',
                'storage_limit_bytes' => 5368709120,
                'bandwidth_limit_bytes' => 53687091200,
                'max_file_size_bytes' => 524288000,
                'allowed_mime_types' => '["jpg","jpeg","png","gif","webp","bmp","svg","ico","mp3","mp4","pdf","txt","doc","docx","xls","xlsx","ppt","pptx","zip","rar"]',
                'price' => '100.00',
                'currency' => 'MZN',
                'is_active' => 1,
                'sort_order' => 2,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'For professionals. 50GB storage and 200GB monthly bandwidth.',
                'storage_limit_bytes' => 53687091200,
                'bandwidth_limit_bytes' => 214748364800,
                'max_file_size_bytes' => 1073741824,
                'allowed_mime_types' => '["jpg","jpeg","png","gif","webp","bmp","svg","ico","tiff","heic","mp3","wav","flac","mp4","mov","avi","mkv","pdf","txt","doc","docx","xls","xlsx","ppt","pptx","odt","ods","odp","zip","rar"]',
                'price' => '500.00',
                'currency' => 'MZN',
                'is_active' => 1,
                'sort_order' => 3,
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'Contact us for a custom plan.',
                'storage_limit_bytes' => 0,
                'bandwidth_limit_bytes' => 0,
                'max_file_size_bytes' => 53687091200,
                'allowed_mime_types' => '["jpg","jpeg","png","gif","webp","bmp","svg","ico","tiff","heic","mp3","wav","flac","aac","ogg","mp4","mov","avi","mkv","webm","m4v","pdf","txt","doc","docx","xls","xlsx","ppt","pptx","odt","ods","odp","zip","rar","7z","gz","tar","psd","ai","ttf","woff"]',
                'price' => '0.00',
                'currency' => 'MZN',
                'is_active' => 1,
                'sort_order' => 4,
            ],
        ];

        $table->insert($data)->saveData();
    }
}