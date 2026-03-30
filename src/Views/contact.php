<?php
// Load helpers
require_once __DIR__ . '/components/helpers.php';

/**
 * @var string $title
 * @var string|null $plan
 * @var bool $success
 * @var array $plans
 */
use Fluxor\View;
?>
<?php View::extend('layouts/main'); ?>

<?php View::section('title'); ?>
<?= View::e($title ?? 'Contact Ntsava') ?>
<?php View::endSection(); ?>

<?php View::section('content'); ?>
<!-- Hero -->
<section class="relative overflow-hidden py-16 bg-gradient-to-br from-primary-50 to-white">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Get in Touch</h1>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">Have questions? We'd love to hear from you</p>
    </div>
</section>

<!-- Contact Form -->
<section class="py-16">
    <div class="container mx-auto px-4">
        <div class="grid lg:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <!-- Info Cards -->
            <div class="space-y-6">
                <div class="glass-card p-6 text-center">
                    <div class="w-12 h-12 rounded-full bg-primary-100 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-envelope text-primary-600 text-xl"></i>
                    </div>
                    <h3 class="font-semibold mb-1">Email</h3>
                    <p class="text-sm text-gray-500">cdn@tudocomlizzyman.com</p>
                    <p class="text-sm text-gray-500">sales@tudocomlizzyman</p>
                </div>
                <div class="glass-card p-6 text-center">
                    <div class="w-12 h-12 rounded-full bg-primary-100 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-clock text-primary-600 text-xl"></i>
                    </div>
                    <h3 class="font-semibold mb-1">Support Hours</h3>
                    <p class="text-sm text-gray-500">24/7 Email Support</p>
                    <p class="text-sm text-gray-500">Live Chat: Mon-Fri, 9am-6pm</p>
                </div>
            </div>

            <!-- Form -->
            <div class="lg:col-span-2">
                <div class="glass-card p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Send us a message</h2>

                    <?php if ($success): ?>
                        <div
                            class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
                            <i class="fas fa-check-circle"></i>
                            <span>Thank you! We'll get back to you within 24 hours.</span>
                        </div>
                    <?php endif; ?>

                    <form id="contactForm" method="POST" action="/contact">
                        <?= csrf_field() ?>

                        <div class="grid md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                                <input type="text" name="name" class="input-field" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                                <input type="email" name="email" class="input-field" required>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                                <select name="subject" class="input-field" required>
                                    <option value="general">General Inquiry</option>
                                    <option value="support">Technical Support</option>
                                    <option value="billing">Billing Question</option>
                                    <option value="upgrade">Plan Upgrade</option>
                                    <option value="enterprise" <?= $plan === 'business' ? 'selected' : '' ?>>Enterprise
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Plan Interest</label>
                                <select name="plan" class="input-field">
                                    <option value="">Not sure yet</option>
                                    <?php foreach ($plans as $p): ?>
                                        <?php if ($p['slug'] === 'free'): ?>
                                            <option value="free" <?= $plan === 'free' ? 'selected' : '' ?>>Free</option>
                                        <?php elseif ($p['slug'] === 'plus'): ?>
                                            <option value="plus" <?= $plan === 'plus' ? 'selected' : '' ?>>
                                                Plus (<?= number_format($p['price_mzn'], 0) ?> MZN /
                                                <?= number_format($p['price_usd'], 2) ?> USD)
                                            </option>
                                        <?php elseif ($p['slug'] === 'pro'): ?>
                                            <option value="pro" <?= $plan === 'pro' ? 'selected' : '' ?>>
                                                Pro (<?= number_format($p['price_mzn'], 0) ?> MZN /
                                                <?= number_format($p['price_usd'], 2) ?> USD)
                                            </option>
                                        <?php elseif ($p['slug'] === 'business'): ?>
                                            <option value="business" <?= $plan === 'business' ? 'selected' : '' ?>>Business
                                                (Custom)</option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                            <textarea name="message" rows="5" class="input-field" required
                                placeholder="Tell us how we can help..."></textarea>
                        </div>

                        <button type="submit" class="btn-primary w-full">
                            <i class="fas fa-paper-plane mr-2"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Reference Section -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Our Plans</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Choose the perfect plan for your needs</p>
        </div>

        <?php component('plans-grid', ['plans' => $plans]); ?>
    </div>
</section>

<!-- FAQ -->
<section class="py-16">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
            <p class="text-gray-600">Quick answers to common questions</p>
        </div>
        <div class="grid md:grid-cols-2 gap-6 max-w-4xl mx-auto">
            <div class="glass-card p-6">
                <h3 class="font-semibold text-gray-900 mb-2">How do I upgrade my plan?</h3>
                <p class="text-gray-500 text-sm">You can upgrade directly from your dashboard using credits. Contact us
                    for add credit or enterprise plans.</p>
            </div>
            <div class="glass-card p-6">
                <h3 class="font-semibold text-gray-900 mb-2">What payment methods do you accept?</h3>
                <p class="text-gray-500 text-sm">We accept credit cards, PayPal, and bank transfers for enterprise
                    customers.</p>
            </div>
            <div class="glass-card p-6">
                <h3 class="font-semibold text-gray-900 mb-2">Is there a free plan?</h3>
                <p class="text-gray-500 text-sm">Yes! Our free plan includes 1GB storage and 20GB bandwidth monthly. No
                    credit card required.</p>
            </div>
            <div class="glass-card p-6">
                <h3 class="font-semibold text-gray-900 mb-2">Do you offer custom domains?</h3>
                <p class="text-gray-500 text-sm">Yes, custom domains are available on Business plans.</p>
            </div>
        </div>
    </div>
</section>

<script src="<?= asset('js/contact.js') ?>"></script>
<?php View::endSection(); ?>