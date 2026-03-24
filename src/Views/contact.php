<?php
// Load helpers
require_once __DIR__ . '/components/helpers.php';

/**
 * @var string $title
 * @var string|null $plan
 * @var string|null $success
 */
use Fluxor\View;
?>
<?php View::extend('layouts/main'); ?>

<?php View::section('title'); ?>
<?= View::e($title ?? 'Contact Us') ?>
<?php View::endSection(); ?>

<?php View::section('styles'); ?>
<style>
    .contact-info-card {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 1rem;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
    }

    .contact-info-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: var(--text-secondary);
    }

    .form-input,
    .form-textarea,
    .form-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        background: var(--bg-primary);
        color: var(--text-primary);
        transition: all 0.2s;
    }

    .form-input:focus,
    .form-textarea:focus,
    .form-select:focus {
        outline: none;
        border-color: var(--primary-500);
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    }

    .success-message {
        background: var(--success);
        color: white;
        padding: 1rem;
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
<?php View::endSection(); ?>

<?php View::section('content'); ?>
<!-- Hero Section -->
<section class="relative overflow-hidden py-16">
    <div class="container mx-auto px-4">
        <div class="text-center max-w-3xl mx-auto">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Get in Touch</h1>
            <p class="text-xl text-gray-600 dark:text-gray-300">
                Have questions about CDN App? We're here to help.
            </p>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Contact Info -->
            <div class="space-y-6">
                <div class="contact-info-card">
                    <div
                        class="w-12 h-12 mx-auto mb-4 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                        <i class="fas fa-envelope text-2xl text-primary-500"></i>
                    </div>
                    <h3 class="font-semibold mb-2">Email Us</h3>
                    <p class="text-gray-600 dark:text-gray-400">support@tudocomlizzyman.com</p>
                    <p class="text-gray-600 dark:text-gray-400">sales@tudocomlizzyman.com</p>
                </div>

                <div class="contact-info-card">
                    <div
                        class="w-12 h-12 mx-auto mb-4 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                        <i class="fas fa-phone text-2xl text-primary-500"></i>
                    </div>
                    <h3 class="font-semibold mb-2">Call Us</h3>
                    <p class="text-gray-600 dark:text-gray-400">+1 (555) 123-4567</p>
                    <p class="text-gray-600 dark:text-gray-400">Mon-Fri, 9am-6pm EST</p>
                </div>

                <div class="contact-info-card">
                    <div
                        class="w-12 h-12 mx-auto mb-4 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                        <i class="fas fa-clock text-2xl text-primary-500"></i>
                    </div>
                    <h3 class="font-semibold mb-2">Support Hours</h3>
                    <p class="text-gray-600 dark:text-gray-400">24/7 Email Support</p>
                    <p class="text-gray-600 dark:text-gray-400">Live Chat: 9am-9pm EST</p>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="lg:col-span-2">
                <div class="glass-card p-8">
                    <h2 class="text-2xl font-bold mb-6">Send us a message</h2>

                    <?php if (isset($success) && $success): ?>
                        <div class="success-message">
                            <i class="fas fa-check-circle mr-2"></i>
                            Thank you for your message! We'll get back to you within 24 hours.
                        </div>
                    <?php endif; ?>

                    <form id="contactForm" method="POST" action="/contact">
                        <input type="hidden" name="csrf_token" value="<?= csrf_field() ?>">

                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="name" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email Address *</label>
                                <input type="email" name="email" class="form-input" required>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="form-label">Subject *</label>
                                <select name="subject" class="form-select" required>
                                    <option value="general">General Inquiry</option>
                                    <option value="support">Technical Support</option>
                                    <option value="billing">Billing Question</option>
                                    <option value="upgrade">Plan Upgrade</option>
                                    <option value="enterprise" <?= isset($plan) && $plan === 'business' ? 'selected' : '' ?>>Enterprise Inquiry</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Plan Interest</label>
                                <select name="plan" class="form-select">
                                    <option value="">Not sure yet</option>
                                    <option value="plus" <?= isset($plan) && $plan === 'plus' ? 'selected' : '' ?>>Plus
                                        ($10/mo)</option>
                                    <option value="pro" <?= isset($plan) && $plan === 'pro' ? 'selected' : '' ?>>Pro
                                        ($49/mo)</option>
                                    <option value="business" <?= isset($plan) && $plan === 'business' ? 'selected' : '' ?>>
                                        Business (Custom)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Message *</label>
                            <textarea name="message" rows="5" class="form-textarea" required
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

<!-- FAQ Section -->
<section class="py-16 bg-gray-50 dark:bg-gray-900/50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-4">Frequently Asked Questions</h2>
            <p class="text-gray-600 dark:text-gray-400">Quick answers to common questions</p>
        </div>

        <div class="grid md:grid-cols-2 gap-6 max-w-4xl mx-auto">
            <div class="glass-card p-6">
                <h3 class="font-semibold mb-2">How do I upgrade my plan?</h3>
                <p class="text-gray-600 dark:text-gray-400">You can upgrade directly from your dashboard using credits.
                    Contact us for enterprise plans.</p>
            </div>
            <div class="glass-card p-6">
                <h3 class="font-semibold mb-2">What payment methods do you accept?</h3>
                <p class="text-gray-600 dark:text-gray-400">We accept credit cards, PayPal, and bank transfers for
                    enterprise customers.</p>
            </div>
            <div class="glass-card p-6">
                <h3 class="font-semibold mb-2">Is there a free trial?</h3>
                <p class="text-gray-600 dark:text-gray-400">Yes! Our free plan includes 1GB storage and 20GB bandwidth
                    monthly.</p>
            </div>
            <div class="glass-card p-6">
                <h3 class="font-semibold mb-2">Do you offer custom domains?</h3>
                <p class="text-gray-600 dark:text-gray-400">Yes, custom domains are available on Plus and higher plans.
                </p>
            </div>
        </div>
    </div>
</section>

<script src="<?= asset('js/contact.js') ?>"></script>
<?php View::endSection(); ?>