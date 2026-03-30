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
<?= View::e($title ?? 'Privacy Policy') ?>
<?php View::endSection(); ?>

<?php View::section('styles'); ?>
<style>
    .warning-box {
        background: linear-gradient(135deg, #fff3e0 0%, #ffe0b3 100%);
        border-left: 4px solid #f59e0b;
        border-radius: 0.5rem;
    }
    .warning-box i {
        color: #f59e0b;
    }
</style>
<?php View::endSection(); ?>

<?php View::section('content'); ?>
<section class="py-16">
    <div class="container mx-auto px-4 max-w-4xl">
        <h1 class="text-4xl font-bold text-gray-900 mb-6">Privacy Policy</h1>
        <p class="text-gray-500 mb-8">Last updated: March 29, 2026</p>

        <!-- Warning Box - Public Files -->
        <div class="warning-box p-6 mb-8">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-triangle text-2xl flex-shrink-0 mt-1"></i>
                <div>
                    <h3 class="font-bold text-amber-800 mb-2">Important: Public Files Notice</h3>
                    <p class="text-amber-700 text-sm leading-relaxed mb-2">
                        <strong>All files uploaded to Ntsava are publicly accessible by default.</strong> 
                        Any file you upload receives a public URL that can be accessed by anyone who has the link.
                    </p>
                    <p class="text-amber-700 text-sm leading-relaxed mb-2">
                        Your files may be indexed by search engines (Google, Bing, etc.) and can be discovered 
                        if the URL is shared or linked from public websites.
                    </p>
                    <p class="text-amber-700 text-sm leading-relaxed">
                        <strong>Recommendation:</strong> Do not upload sensitive, confidential, or private information 
                        to Ntsava. This service is designed for public content delivery, not private file storage. 
                        If you need private file storage, please consider alternative solutions or contact us about 
                        enterprise options with restricted access.
                    </p>
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-3">1. Information We Collect</h2>
                <p class="text-gray-600 leading-relaxed">We collect account information (name, email), usage data, and files you upload. Remember that uploaded files become publicly accessible via their URLs.</p>
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-3">2. How We Use Your Information</h2>
                <p class="text-gray-600 leading-relaxed">We use your information to provide and improve our services, communicate with you, and ensure security. Your public files are delivered globally via our CDN network.</p>
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-3">3. Data Storage</h2>
                <p class="text-gray-600 leading-relaxed">Your files are stored on secure servers. However, by the nature of this service, all uploaded files have public URLs and can be accessed by anyone with the link.</p>
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-3">4. Public File Access</h2>
                <p class="text-gray-600 leading-relaxed mb-2">Ntsava is a content delivery network (CDN) designed for public file distribution. This means:</p>
                <ul class="list-disc list-inside text-gray-600 leading-relaxed space-y-1 ml-4">
                    <li>All uploaded files receive publicly accessible URLs</li>
                    <li>Files may be indexed by search engines</li>
                    <li>We do not provide private file access controls in the standard plans</li>
                    <li>Anyone with the file URL can download or view your file</li>
                    <li>Links can be shared and may become public through social media or other websites</li>
                </ul>
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-3">5. Third-Party Services</h2>
                <p class="text-gray-600 leading-relaxed">We may use third-party services for analytics, payments, and infrastructure. These services have their own privacy policies.</p>
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-3">6. Your Rights</h2>
                <p class="text-gray-600 leading-relaxed">You have the right to access, correct, or delete your personal data. You may also delete your files at any time. Contact us to exercise these rights.</p>
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-3">7. Cookies</h2>
                <p class="text-gray-600 leading-relaxed">We use cookies for authentication and analytics. You can disable cookies in your browser settings.</p>
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-3">8. Data Retention</h2>
                <p class="text-gray-600 leading-relaxed">We retain your data while your account is active. After account deletion, your files are permanently removed from our servers within 30 days.</p>
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-3">9. Security</h2>
                <p class="text-gray-600 leading-relaxed">We implement industry-standard security measures to protect our infrastructure. However, due to the public nature of file URLs, you should not rely on Ntsava for storing sensitive or private information.</p>
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-3">10. Changes to This Policy</h2>
                <p class="text-gray-600 leading-relaxed">We may update this policy. We will notify you of significant changes via email or notice on our website.</p>
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-3">11. Contact Us</h2>
                <p class="text-gray-600 leading-relaxed">Questions about privacy? Contact us at <a href="mailto:cdn+privacy@tudocomlizzyman.com" class="text-primary-600 hover:underline">cdn+privacy@tudocomlizzyman.com</a></p>
            </div>
        </div>
    </div>
</section>
<?php View::endSection(); ?>