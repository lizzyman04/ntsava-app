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
<?= View::e($title ?? 'Terms of Service') ?>
<?php View::endSection(); ?>

<?php View::section('content'); ?>
<section class="py-16">
    <div class="container mx-auto px-4 max-w-4xl">
        <h1 class="text-4xl font-bold text-gray-900 mb-6">Terms of Service</h1>
        <p class="text-gray-500 mb-8">Last updated: March 29, 2026</p>

        <div class="space-y-8">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-3">1. Acceptance of Terms</h2>
                <p class="text-gray-600 leading-relaxed">By accessing or using Ntsava's services, you agree to be bound by these Terms of Service and our Privacy Policy.</p>
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-3">2. Description of Service</h2>
                <p class="text-gray-600 leading-relaxed">Ntsava provides file storage, CDN delivery, image processing, and related services. All uploaded files become publicly accessible via unique URLs.</p>
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-3">3. User Accounts</h2>
                <p class="text-gray-600 leading-relaxed">You are responsible for maintaining the security of your account. You agree to provide accurate information and to notify us immediately of any unauthorized use.</p>
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-3">4. Acceptable Use</h2>
                <p class="text-gray-600 leading-relaxed">You agree not to upload illegal content, malware, or any material that violates third-party rights. We reserve the right to remove content and terminate accounts for violations.</p>
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-3">5. Content Ownership</h2>
                <p class="text-gray-600 leading-relaxed">You retain all ownership rights to your content. By using our service, you grant us permission to store and deliver your content publicly via our CDN.</p>
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-3">6. Public Nature of Content</h2>
                <p class="text-gray-600 leading-relaxed">Ntsava is designed for public content delivery. All files uploaded to the service receive public URLs. You acknowledge that your files may be accessed, downloaded, or indexed by search engines.</p>
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-3">7. Payment and Plans</h2>
                <p class="text-gray-600 leading-relaxed">Paid plans are billed in advance in Mozambican Metical (MZN). We reserve the right to change pricing with 30 days notice. Refunds are handled on a case-by-case basis.</p>
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-3">8. Service Level</h2>
                <p class="text-gray-600 leading-relaxed">We strive for 99.9% uptime but do not guarantee uninterrupted service. We are not liable for any damages from service interruptions.</p>
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-3">9. Termination</h2>
                <p class="text-gray-600 leading-relaxed">Either party may terminate the agreement at any time. Upon termination, your content will be deleted after 30 days.</p>
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-3">10. Limitation of Liability</h2>
                <p class="text-gray-600 leading-relaxed">Ntsava is not responsible for any damages arising from the use of our service, including unauthorized access to your public files or data breaches.</p>
            </div>

            <div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-3">11. Contact</h2>
                <p class="text-gray-600 leading-relaxed">Questions about these Terms? Contact us at <a href="mailto:legal@ntsava.space" class="text-primary-600 hover:underline">legal@ntsava.space</a></p>
            </div>
        </div>
    </div>
</section>
<?php View::endSection(); ?>