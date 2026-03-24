<?php
// Load helpers
require_once __DIR__ . '/components/helpers.php';

/**
 * @var string $title
 * @var string $message
 */
use Fluxor\View;
?>
<?php View::extend('layouts/main'); ?>

<?php View::section('title'); ?>
<?= View::e($title ?? 'CDN App') ?>
<?php View::endSection(); ?>

<?php View::section('styles'); ?>
<style>
    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(245, 158, 11, 0.1);
        backdrop-filter: blur(4px);
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--primary-500);
        margin-bottom: 1.5rem;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
    
    .feature-card {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 1.5rem;
        padding: 2rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .feature-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--gradient-primary);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }
    
    .feature-card:hover::before {
        transform: scaleX(1);
    }
    
    .feature-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-xl);
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 2rem;
        margin: 3rem 0;
    }
    
    .stat-number {
        font-size: 2.5rem;
        font-weight: 800;
        background: var(--gradient-primary);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }
    
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
    }
    
    .cta-section {
        background: var(--gradient-hero);
        border-radius: 2rem;
        padding: 4rem 2rem;
        position: relative;
        overflow: hidden;
    }
    
    .cta-section::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
        background-size: 40px 40px;
        animation: float 20s linear infinite;
    }
    
    @keyframes float {
        from { transform: translate(0, 0); }
        to { transform: translate(40px, 40px); }
    }
    
    .pricing-card {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 1.5rem;
        padding: 2rem;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .pricing-card.popular {
        border: 2px solid var(--primary-500);
        transform: scale(1.05);
        box-shadow: var(--shadow-glow);
    }
    
    .popular-badge {
        position: absolute;
        top: -12px;
        left: 50%;
        transform: translateX(-50%);
        background: var(--gradient-primary);
        color: white;
        padding: 0.25rem 1rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .testimonial-card {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 1.5rem;
        padding: 1.5rem;
        transition: all 0.3s ease;
    }
    
    .testimonial-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }
</style>
<?php View::endSection(); ?>

<?php View::section('content'); ?>
<!-- Hero Section -->
<section class="relative overflow-hidden py-20">
    <div class="absolute inset-0 bg-gradient-to-br from-primary-100/20 via-transparent to-primary-100/20 dark:from-primary-900/10"></div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="text-center max-w-4xl mx-auto">
            <div class="hero-badge">
                <i class="fas fa-rocket"></i>
                <span>Launch your digital assets at light speed</span>
            </div>
            <h1 class="text-5xl md:text-7xl font-bold mb-6">
                Ship Your Files
                <span class="bg-gradient-to-r from-primary-500 to-primary-600 bg-clip-text text-transparent">
                    at Light Speed
                </span>
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-300 mb-8 max-w-2xl mx-auto">
                Enterprise-grade CDN with instant delivery, smart caching, and powerful image processing. 
                Upload once, serve globally.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/auth/signup" class="btn-primary text-lg px-8 py-3 group">
                    <i class="fas fa-rocket mr-2 group-hover:translate-x-1 transition-transform"></i>
                    Get Started Free
                </a>
                <a href="#features" class="btn-outline text-lg px-8 py-3">
                    <i class="fas fa-play mr-2"></i>
                    Watch Demo
                </a>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="stats-grid mt-16">
            <div class="text-center">
                <div class="stat-number">99.9%</div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Uptime SLA</p>
            </div>
            <div class="text-center">
                <div class="stat-number">50ms</div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Avg. Response</p>
            </div>
            <div class="text-center">
                <div class="stat-number">200+</div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Edge Locations</p>
            </div>
            <div class="text-center">
                <div class="stat-number">10k+</div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Active Users</p>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-20">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Everything you need</h2>
            <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                Powerful features to manage and deliver your digital assets
            </p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <div class="feature-card">
                <div class="w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center mb-4">
                    <i class="fas fa-cloud-upload-alt text-2xl text-primary-500"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Instant Upload</h3>
                <p class="text-gray-600 dark:text-gray-400">Drag and drop files or use our powerful API for seamless uploads.</p>
            </div>
            
            <div class="feature-card">
                <div class="w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center mb-4">
                    <i class="fas fa-magic text-2xl text-primary-500"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Smart Processing</h3>
                <p class="text-gray-600 dark:text-gray-400">Resize, optimize, and convert images on the fly with URL parameters.</p>
            </div>
            
            <div class="feature-card">
                <div class="w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center mb-4">
                    <i class="fas fa-shield-alt text-2xl text-primary-500"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Enterprise Security</h3>
                <p class="text-gray-600 dark:text-gray-400">End-to-end encryption, DDoS protection, and secure token authentication.</p>
            </div>
            
            <div class="feature-card">
                <div class="w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center mb-4">
                    <i class="fas fa-chart-line text-2xl text-primary-500"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Real-time Analytics</h3>
                <p class="text-gray-600 dark:text-gray-400">Track bandwidth usage, storage, and request metrics in real-time.</p>
            </div>
            
            <div class="feature-card">
                <div class="w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center mb-4">
                    <i class="fas fa-globe text-2xl text-primary-500"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Global CDN</h3>
                <p class="text-gray-600 dark:text-gray-400">Fast delivery worldwide with edge caching and optimization.</p>
            </div>
            
            <div class="feature-card">
                <div class="w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center mb-4">
                    <i class="fas fa-key text-2xl text-primary-500"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">API Tokens</h3>
                <p class="text-gray-600 dark:text-gray-400">Granular permissions for secure API access and integration.</p>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section class="py-20 bg-gray-50 dark:bg-gray-900/50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Simple, Transparent Pricing</h2>
            <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                Choose the perfect plan for your needs. Upgrade anytime.
            </p>
        </div>
        
        <div class="grid md:grid-cols-4 gap-6 max-w-6xl mx-auto">
            <!-- Free Plan -->
            <div class="pricing-card">
                <h3 class="text-xl font-bold mb-2">Free</h3>
                <div class="text-3xl font-bold mb-4">$0<span class="text-sm font-normal text-gray-500">/month</span></div>
                <ul class="space-y-3 mb-6">
                    <li class="flex items-center gap-2"><i class="fas fa-check-circle text-green-500"></i> 1 GB Storage</li>
                    <li class="flex items-center gap-2"><i class="fas fa-check-circle text-green-500"></i> 20 GB Bandwidth</li>
                    <li class="flex items-center gap-2"><i class="fas fa-check-circle text-green-500"></i> API Access</li>
                    <li class="flex items-center gap-2"><i class="fas fa-check-circle text-green-500"></i> Image Processing</li>
                </ul>
                <a href="/auth/signup" class="btn-outline w-full text-center block">Get Started</a>
            </div>
            
            <!-- Plus Plan -->
            <div class="pricing-card popular">
                <div class="popular-badge">Most Popular</div>
                <h3 class="text-xl font-bold mb-2">Plus</h3>
                <div class="text-3xl font-bold mb-4">$10<span class="text-sm font-normal text-gray-500">/month</span></div>
                <ul class="space-y-3 mb-6">
                    <li class="flex items-center gap-2"><i class="fas fa-check-circle text-green-500"></i> 5 GB Storage</li>
                    <li class="flex items-center gap-2"><i class="fas fa-check-circle text-green-500"></i> 50 GB Bandwidth</li>
                    <li class="flex items-center gap-2"><i class="fas fa-check-circle text-green-500"></i> Priority Support</li>
                    <li class="flex items-center gap-2"><i class="fas fa-check-circle text-green-500"></i> Custom Domain</li>
                </ul>
                <a href="/contact?plan=plus" class="btn-primary w-full text-center block">Upgrade Now</a>
            </div>
            
            <!-- Pro Plan -->
            <div class="pricing-card">
                <h3 class="text-xl font-bold mb-2">Pro</h3>
                <div class="text-3xl font-bold mb-4">$49<span class="text-sm font-normal text-gray-500">/month</span></div>
                <ul class="space-y-3 mb-6">
                    <li class="flex items-center gap-2"><i class="fas fa-check-circle text-green-500"></i> 50 GB Storage</li>
                    <li class="flex items-center gap-2"><i class="fas fa-check-circle text-green-500"></i> 200 GB Bandwidth</li>
                    <li class="flex items-center gap-2"><i class="fas fa-check-circle text-green-500"></i> 24/7 Support</li>
                    <li class="flex items-center gap-2"><i class="fas fa-check-circle text-green-500"></i> Advanced Analytics</li>
                </ul>
                <a href="/contact?plan=pro" class="btn-outline w-full text-center block">Upgrade Now</a>
            </div>
            
            <!-- Business Plan -->
            <div class="pricing-card">
                <h3 class="text-xl font-bold mb-2">Business</h3>
                <div class="text-3xl font-bold mb-4">Custom</div>
                <ul class="space-y-3 mb-6">
                    <li class="flex items-center gap-2"><i class="fas fa-check-circle text-green-500"></i> Unlimited Storage</li>
                    <li class="flex items-center gap-2"><i class="fas fa-check-circle text-green-500"></i> Unlimited Bandwidth</li>
                    <li class="flex items-center gap-2"><i class="fas fa-check-circle text-green-500"></i> SLA Guarantee</li>
                    <li class="flex items-center gap-2"><i class="fas fa-check-circle text-green-500"></i> Dedicated Support</li>
                </ul>
                <a href="/contact?plan=business" class="btn-outline w-full text-center block">Contact Sales</a>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-20">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Loved by developers worldwide</h2>
            <p class="text-gray-600 dark:text-gray-400">Join thousands of satisfied users</p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-6">
            <div class="testimonial-card">
                <div class="flex items-center gap-2 text-yellow-500 mb-4">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p class="text-gray-600 dark:text-gray-400 mb-4">"Best CDN service I've ever used. The image processing feature saves us hours of manual work."</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-r from-primary-500 to-primary-600 flex items-center justify-center text-white font-bold">JD</div>
                    <div>
                        <p class="font-semibold">John Doe</p>
                        <p class="text-sm text-gray-500">CTO at TechCorp</p>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <div class="flex items-center gap-2 text-yellow-500 mb-4">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p class="text-gray-600 dark:text-gray-400 mb-4">"Incredible performance and amazing support. The API is clean and well-documented."</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-r from-primary-500 to-primary-600 flex items-center justify-center text-white font-bold">SM</div>
                    <div>
                        <p class="font-semibold">Sarah Miller</p>
                        <p class="text-sm text-gray-500">Lead Developer</p>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <div class="flex items-center gap-2 text-yellow-500 mb-4">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p class="text-gray-600 dark:text-gray-400 mb-4">"The free tier is generous and the paid plans are very affordable. Highly recommended!"</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-r from-primary-500 to-primary-600 flex items-center justify-center text-white font-bold">MC</div>
                    <div>
                        <p class="font-semibold">Michael Chen</p>
                        <p class="text-sm text-gray-500">Startup Founder</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20">
    <div class="container mx-auto px-4">
        <div class="cta-section text-center relative">
            <div class="relative z-10">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Ready to accelerate your content delivery?
                </h2>
                <p class="text-lg text-gray-700 dark:text-gray-300 mb-8 max-w-2xl mx-auto">
                    Join thousands of developers who trust CDN App for their digital assets.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/auth/signup" class="btn-primary text-lg px-8 py-3">
                        <i class="fas fa-rocket mr-2"></i> Start Free Trial
                    </a>
                    <a href="/contact" class="btn-outline text-lg px-8 py-3">
                        <i class="fas fa-headset mr-2"></i> Contact Sales
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php View::endSection(); ?>