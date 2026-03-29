<?php
// Load helpers
require_once __DIR__ . '/components/helpers.php';

/**
 * @var string $title
 * @var array $stats
 * @var array $plans
 */
use Fluxor\View;
?>
<?php View::extend('layouts/main'); ?>

<?php View::section('title'); ?>
<?= View::e($title ?? 'Ntsava') ?>
<?php View::endSection(); ?>

<?php View::section('content'); ?>
<!-- Hero Section -->
<section class="relative overflow-hidden py-4 bg-gradient-to-br from-primary-50 to-white">
    <div class="container mx-auto px-4 relative z-10">
        <div class="text-center max-w-4xl mx-auto">
            <div class="inline-flex items-center gap-2 bg-primary-100 text-primary-700 px-4 py-2 rounded-full text-sm font-medium mb-6">
                <i class="fas fa-basket-shopping"></i>
                <span>Your digital basket</span>
            </div>
            <h1 class="text-5xl md:text-7xl font-bold text-gray-900 mb-6">
                Store. Deliver.
                <span class="text-primary-600">Simplify.</span>
            </h1>
            <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                Ntsava is your complete solution for file storage, image optimization, and global delivery.
                Everything you need, in one basket.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/auth/signup" class="btn-primary">
                    <i class="fas fa-rocket mr-2"></i> Start Free
                </a>
                <a href="/contact" class="btn-outline">
                    <i class="fas fa-headset mr-2"></i> Contact Sales
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-16">
            <div class="text-center">
                <div class="text-3xl font-bold text-primary-600"><?= $stats['uptime'] ?? '99.9%' ?></div>
                <p class="text-sm text-gray-500">Uptime</p>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-primary-600"><?= $stats['active_users'] ?? '1k+' ?></div>
                <p class="text-sm text-gray-500">Active Users</p>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-primary-600"><?= $stats['total_files'] ?? '10k+' ?></div>
                <p class="text-sm text-gray-500">Files Stored</p>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-primary-600"><?= $stats['storage_used'] ?? '100+ GB' ?></div>
                <p class="text-sm text-gray-500">Storage Used</p>
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Everything you need</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Powerful features to manage and deliver your digital assets</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <div class="glass-card p-6 text-center">
                <div class="w-14 h-14 rounded-xl bg-primary-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-cloud-upload-alt text-2xl text-primary-600"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Easy Upload</h3>
                <p class="text-gray-500">Drag & drop or API upload with instant processing</p>
            </div>
            <div class="glass-card p-6 text-center">
                <div class="w-14 h-14 rounded-xl bg-primary-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-magic text-2xl text-primary-600"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Smart Processing</h3>
                <p class="text-gray-500">Resize, optimize, convert images on the fly</p>
            </div>
            <div class="glass-card p-6 text-center">
                <div class="w-14 h-14 rounded-xl bg-primary-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-globe text-2xl text-primary-600"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Global CDN</h3>
                <p class="text-gray-500">Fast delivery worldwide with edge caching</p>
            </div>
            <div class="glass-card p-6 text-center">
                <div class="w-14 h-14 rounded-xl bg-primary-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-2xl text-primary-600"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Secure Storage</h3>
                <p class="text-gray-500">Enterprise-grade security for your files</p>
            </div>
            <div class="glass-card p-6 text-center">
                <div class="w-14 h-14 rounded-xl bg-primary-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-chart-line text-2xl text-primary-600"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Real-time Analytics</h3>
                <p class="text-gray-500">Track usage, bandwidth, and storage</p>
            </div>
            <div class="glass-card p-6 text-center">
                <div class="w-14 h-14 rounded-xl bg-primary-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-key text-2xl text-primary-600"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">API Tokens</h3>
                <p class="text-gray-500">Granular permissions for secure access</p>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section class="py-20">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Simple, Transparent Pricing</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Choose the perfect plan for your needs</p>
        </div>

        <div class="grid md:grid-cols-4 gap-6 max-w-6xl mx-auto">
            <?php foreach ($plans as $plan): ?>
                <div class="glass-card p-6 text-center <?= $plan['is_popular'] ? 'border-2 border-primary-500 relative' : '' ?>">
                    <?php if ($plan['is_popular']): ?>
                        <span class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-primary-600 text-white text-xs px-3 py-1 rounded-full">Popular</span>
                    <?php endif; ?>
                    <h3 class="text-xl font-bold mb-2"><?= htmlspecialchars($plan['name']) ?></h3>
                    <div class="mb-2">
                        <span class="text-3xl font-bold text-primary-600"><?= $plan['price_mzn'] > 0 ? number_format($plan['price_mzn'], 0) : '0' ?> MZN</span>
                        <span class="text-sm text-gray-500">/month</span>
                    </div>
                    <?php if ($plan['price_usd'] > 0): ?>
                        <div class="text-sm text-gray-500 mb-4">
                            ≈ <?= number_format($plan['price_usd'], 2) ?> USD
                        </div>
                    <?php else: ?>
                        <div class="text-sm text-gray-500 mb-4">&nbsp;</div>
                    <?php endif; ?>
                    <ul class="space-y-2 mb-6 text-left">
                        <li class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span><?= $plan['storage_gb'] === 'Unlimited' ? 'Unlimited Storage' : $plan['storage_gb'] . ' GB Storage' ?></span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span><?= $plan['bandwidth_gb'] === 'Unlimited' ? 'Unlimited Bandwidth' : $plan['bandwidth_gb'] . ' GB Bandwidth' ?></span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span>API Access</span>
                        </li>
                        <?php if ($plan['slug'] !== 'free'): ?>
                            <li class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-green-500"></i>
                                <span>Priority Support</span>
                            </li>
                        <?php endif; ?>
                    </ul>
                    <?php if ($plan['slug'] === 'free'): ?>
                        <a href="/auth/signup" class="btn-outline w-full block">Get Started</a>
                    <?php elseif ($plan['slug'] === 'business'): ?>
                        <a href="/contact?plan=business" class="btn-outline w-full block">Contact Sales</a>
                    <?php else: ?>
                        <a href="/contact?plan=<?= $plan['slug'] ?>" class="btn-primary w-full block">Upgrade Now</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-20 bg-primary-600">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Ready to get started?</h2>
        <p class="text-primary-100 mb-8 max-w-2xl mx-auto"> Join to other developers using Ntsava for their file storage and delivery</p>
        <a href="/auth/signup" class="bg-white text-primary-600 hover:bg-gray-100 px-8 py-3 rounded-lg font-semibold transition-colors inline-flex items-center gap-2">
            <i class="fas fa-rocket"></i> Create Free Account
        </a>
    </div>
</section>
<?php View::endSection(); ?>