<?php
// Load helpers
require_once __DIR__ . '/components/helpers.php';

/**
 * @var string $title
 * @var array $stats
 */
use Fluxor\View;
?>
<?php View::extend('layouts/main'); ?>

<?php View::section('title'); ?>
<?= View::e($title ?? 'About Ntsava') ?>
<?php View::endSection(); ?>

<?php View::section('content'); ?>
<!-- Hero -->
<section class="relative overflow-hidden py-20 bg-gradient-to-br from-primary-50 to-white">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-4">About Ntsava</h1>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto">We're on a mission to make file storage and delivery simple, secure, and accessible to everyone.</p>
    </div>
</section>

<!-- Mission -->
<section class="py-20">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Our Mission</h2>
                <p class="text-gray-600 mb-4 leading-relaxed">
                    Ntsava was born from a simple idea: file storage and delivery shouldn't be complicated. 
                    We believe that every developer, creator, and business deserves access to enterprise-grade 
                    infrastructure without the enterprise complexity.
                </p>
                <p class="text-gray-600 leading-relaxed">
                    Named after the traditional basket used to carry goods to market, Ntsava is your digital basket — 
                    a simple, reliable way to store, manage, and deliver your files anywhere in the world.
                </p>
            </div>
            <div class="glass-card p-8 text-center">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <div class="text-4xl font-bold text-primary-600"><?= $stats['total_users'] ?? '1k+' ?></div>
                        <p class="text-sm text-gray-500 mt-1">Active Users</p>
                    </div>
                    <div>
                        <div class="text-4xl font-bold text-primary-600"><?= $stats['total_files'] ?? '10k+' ?></div>
                        <p class="text-sm text-gray-500 mt-1">Files Stored</p>
                    </div>
                    <div>
                        <div class="text-4xl font-bold text-primary-600"><?= $stats['total_storage'] ?? '100+ GB' ?></div>
                        <p class="text-sm text-gray-500 mt-1">Total Storage</p>
                    </div>
                    <div>
                        <div class="text-4xl font-bold text-primary-600"><?= $stats['founded'] ?? '2026' ?></div>
                        <p class="text-sm text-gray-500 mt-1">Founded</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values -->
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Our Values</h2>
            <p class="text-gray-600">The principles that guide everything we do</p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="glass-card p-6 text-center">
                <div class="w-14 h-14 rounded-xl bg-primary-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bolt text-2xl text-primary-600"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Simplicity First</h3>
                <p class="text-gray-500">Clean APIs, intuitive interface, and minimal complexity</p>
            </div>
            <div class="glass-card p-6 text-center">
                <div class="w-14 h-14 rounded-xl bg-primary-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-2xl text-primary-600"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Security by Default</h3>
                <p class="text-gray-500">Your data is protected with enterprise-grade security</p>
            </div>
            <div class="glass-card p-6 text-center">
                <div class="w-14 h-14 rounded-xl bg-primary-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-heart text-2xl text-primary-600"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Developer Love</h3>
                <p class="text-gray-500">Tools and APIs that developers actually enjoy using</p>
            </div>
        </div>
    </div>
</section>

<!-- Journey -->
<section class="py-20">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Our Journey</h2>
            <p class="text-gray-600">From idea to reality</p>
        </div>
        <div class="max-w-3xl mx-auto space-y-8">
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center font-bold">1</div>
                <div>
                    <h3 class="text-xl font-semibold mb-1">Feb 12, 2026 - The Beginning</h3>
                    <p class="text-gray-500">Ntsava was founded with a vision to simplify file storage and delivery</p>
                </div>
            </div>
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center font-bold">2</div>
                <div>
                    <h3 class="text-xl font-semibold mb-1">March 30, 2026 - First Release</h3>
                    <p class="text-gray-500">Launched core features: upload, storage, and basic CDN delivery</p>
                </div>
            </div>
            <!-- <div class="flex gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center font-bold">3</div>
                <div>
                    <h3 class="text-xl font-semibold mb-1">March 30, 2026 - Smart Processing</h3>
                    <p class="text-gray-500">Added image optimization, resizing, and format conversion</p>
                </div>
            </div> -->
        </div>
    </div>
</section>

<!-- Mission Statement -->
<section class="py-20 bg-primary-600">
    <div class="container mx-auto px-4 text-center">
        <i class="fas fa-quote-left text-4xl text-primary-200 mb-4"></i>
        <p class="text-xl md:text-2xl text-gray-800 max-w-3xl mx-auto mb-6 leading-relaxed">
            We believe that technology should empower, not complicate. Ntsava is built to be the simplest, most reliable way to store and deliver your digital assets.
        </p>
        <p class="text-primary-100">— The Ntsava Team</p>
    </div>
</section>
<?php View::endSection(); ?>