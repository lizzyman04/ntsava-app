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
                'description' => 'Perfeito para começar. 1GB de armazenamento e 20GB de banda mensal.',
                'storage_limit_bytes' => 1073741824,
                'bandwidth_limit_bytes' => 21474836480,
                'max_file_size_bytes' => 104857600,
                'allowed_mime_types' => '["jpg","jpeg","png","gif","webp"]',
                'price' => '0.00',
                'currency' => 'MZN',
                'is_active' => 1,
                'sort_order' => 1,
            ],
            [
                'name' => 'Plus',
                'slug' => 'plus',
                'description' => 'Ideal para pequenos projetos. 5GB de armazenamento e 50GB de banda mensal.',
                'storage_limit_bytes' => 5368709120,
                'bandwidth_limit_bytes' => 53687091200,
                'max_file_size_bytes' => 524288000,
                'allowed_mime_types' => '["jpg","jpeg","png","gif","webp","pdf","doc","docx"]',
                'price' => '100.00',
                'currency' => 'MZN',
                'is_active' => 1,
                'sort_order' => 2,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'Para profissionais. 50GB de armazenamento e 200GB de banda mensal.',
                'storage_limit_bytes' => 53687091200,
                'bandwidth_limit_bytes' => 214748364800,
                'max_file_size_bytes' => 1073741824,
                'allowed_mime_types' => '["jpg","jpeg","png","gif","webp","pdf","doc","docx","zip"]',
                'price' => '500.00',
                'currency' => 'MZN',
                'is_active' => 1,
                'sort_order' => 3,
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'Sob consulta. Entre em contato para um plano personalizado.',
                'storage_limit_bytes' => 0,
                'bandwidth_limit_bytes' => 0,
                'max_file_size_bytes' => 53687091200,
                'allowed_mime_types' => '["jpg","jpeg","png","gif","webp","pdf","doc","docx","zip","mp4","mov"]',
                'price' => '0.00',
                'currency' => 'MZN',
                'is_active' => 1,
                'sort_order' => 4,
            ],
        ];

        $table->insert($data)->saveData();
    }
}
