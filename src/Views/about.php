<?php
// Load helpers
require_once __DIR__ . '/components/helpers.php';

/**
 * @var string $title
 */
use Fluxor\View;
?>
<?php View::extend('layouts/main'); ?>

<?php View::section('title'); ?>
<?= View::e($title ?? 'About Us') ?>
<?php View::endSection(); ?>

<?php View::section('styles'); ?>
<style>
    .timeline-item {
        position: relative;
        padding-left: 2rem;
        border-left: 2px solid var(--primary-500);
        margin-bottom: 2rem;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -0.5rem;
        top: 0;
        width: 1rem;
        height: 1rem;
        background: var(--gradient-primary);
        border-radius: 50%;
    }
    
    .team-card {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 1.5rem;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .team-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }
    
    .team-avatar {
        width: 100px;
        height: 100px;
        margin: 0 auto 1rem;
        border-radius: 50%;
        background: var(--gradient-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        font-weight: bold;
        color: white;
    }
    
    .value-card {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 1rem;
        padding: 1.5rem;
        text-align: center;
    }
</style>
<?php View::endSection(); ?>

<?php View::section('content'); ?>
<!-- Hero Section -->
<section class="relative overflow-hidden py-20">
    <div class="absolute inset-0 bg-gradient-to-br from-primary-100/20 via-transparent to-primary-100/20 dark:from-primary-900/10"></div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="text-center max-w-3xl mx-auto">
            <h1 class="text-5xl md:text-6xl font-bold mb-6">About CDN App</h1>
            <p class="text-xl text-gray-600 dark:text-gray-300 mb-8">
                We're on a mission to make content delivery fast, secure, and accessible to everyone.
            </p>
        </div>
    </div>
</section>

<!-- Mission Section -->
<section class="py-16">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <div class="w-16 h-1 bg-primary-500 mb-6 rounded-full"></div>
                <h2 class="text-3xl font-bold mb-4">Our Mission</h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 mb-6">
                    To democratize content delivery by providing enterprise-grade CDN technology at accessible prices, 
                    empowering developers and businesses to deliver exceptional digital experiences worldwide.
                </p>
                <p class="text-gray-600 dark:text-gray-400">
                    Founded in 2024, CDN App has grown from a simple idea to a robust platform serving thousands of 
                    users across the globe. We believe that fast, reliable content delivery should be available to everyone, 
                    not just large enterprises.
                </p>
            </div>
            <div class="glass-card p-8 text-center">
                <div class="text-5xl font-bold text-primary-500 mb-4">10k+</div>
                <p class="text-lg font-semibold mb-2">Active Users</p>
                <p class="text-gray-500 dark:text-gray-400">and growing</p>
                <div class="grid grid-cols-2 gap-4 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div>
                        <div class="text-2xl font-bold">200+</div>
                        <p class="text-sm text-gray-500">Edge Locations</p>
                    </div>
                    <div>
                        <div class="text-2xl font-bold">99.9%</div>
                        <p class="text-sm text-gray-500">Uptime SLA</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Values -->
<section class="py-16 bg-gray-50 dark:bg-gray-900/50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-4">Our Values</h2>
            <p class="text-gray-600 dark:text-gray-400">The principles that guide everything we do</p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <div class="value-card">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                    <i class="fas fa-bolt text-2xl text-primary-500"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Speed First</h3>
                <p class="text-gray-600 dark:text-gray-400">We optimize every millisecond to ensure the fastest delivery possible.</p>
            </div>
            
            <div class="value-card">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                    <i class="fas fa-shield-alt text-2xl text-primary-500"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Security by Default</h3>
                <p class="text-gray-600 dark:text-gray-400">Security isn't an afterthought - it's built into everything we do.</p>
            </div>
            
            <div class="value-card">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                    <i class="fas fa-users text-2xl text-primary-500"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Developer First</h3>
                <p class="text-gray-600 dark:text-gray-400">We build tools that developers love to use, with clean APIs and great docs.</p>
            </div>
        </div>
    </div>
</section>

<!-- Timeline -->
<section class="py-16">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-4">Our Journey</h2>
            <p class="text-gray-600 dark:text-gray-400">From idea to industry leader</p>
        </div>
        
        <div class="max-w-3xl mx-auto">
            <div class="timeline-item">
                <h3 class="text-xl font-bold mb-2">2024 - The Beginning</h3>
                <p class="text-gray-600 dark:text-gray-400">CDN App was founded with a simple vision: make CDN technology accessible to all.</p>
            </div>
            <div class="timeline-item">
                <h3 class="text-xl font-bold mb-2">2024 - First Release</h3>
                <p class="text-gray-600 dark:text-gray-400">Launched our first version with core CDN features and image processing capabilities.</p>
            </div>
            <div class="timeline-item">
                <h3 class="text-xl font-bold mb-2">2025 - Global Expansion</h3>
                <p class="text-gray-600 dark:text-gray-400">Expanded to 200+ edge locations worldwide, reducing latency by 80%.</p>
            </div>
            <div class="timeline-item">
                <h3 class="text-xl font-bold mb-2">2026 - Enterprise Ready</h3>
                <p class="text-gray-600 dark:text-gray-400">Launched enterprise features including advanced analytics, custom domains, and SLA guarantees.</p>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="py-16 bg-gray-50 dark:bg-gray-900/50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-4">Meet the Team</h2>
            <p class="text-gray-600 dark:text-gray-400">Passionate people building the future of content delivery</p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <div class="team-card">
                <div class="team-avatar">AA</div>
                <h3 class="text-xl font-semibold mb-1">Arlindo Abdul</h3>
                <p class="text-primary-500 mb-3">Founder & CEO</p>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Passionate about making technology accessible to everyone.</p>
            </div>
            
            <div class="team-card">
                <div class="team-avatar">JD</div>
                <h3 class="text-xl font-semibold mb-1">Jane Doe</h3>
                <p class="text-primary-500 mb-3">Head of Engineering</p>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Leading our engineering team to build scalable solutions.</p>
            </div>
            
            <div class="team-card">
                <div class="team-avatar">MS</div>
                <h3 class="text-xl font-semibold mb-1">Mike Smith</h3>
                <p class="text-primary-500 mb-3">Product Manager</p>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Turning user feedback into amazing features.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-16">
    <div class="container mx-auto px-4">
        <div class="glass-card p-12 text-center">
            <h2 class="text-3xl font-bold mb-4">Join Our Community</h2>
            <p class="text-lg text-gray-600 dark:text-gray-400 mb-6 max-w-2xl mx-auto">
                Be part of our journey. Follow us for updates, tips, and behind-the-scenes content.
            </p>
            <div class="flex justify-center gap-4">
                <a href="#" class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center hover:bg-primary-100 dark:hover:bg-primary-900 transition">
                    <i class="fab fa-github text-xl"></i>
                </a>
                <a href="#" class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center hover:bg-primary-100 dark:hover:bg-primary-900 transition">
                    <i class="fab fa-twitter text-xl"></i>
                </a>
                <a href="#" class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center hover:bg-primary-100 dark:hover:bg-primary-900 transition">
                    <i class="fab fa-linkedin text-xl"></i>
                </a>
            </div>
        </div>
    </div>
</section>
<?php View::endSection(); ?>