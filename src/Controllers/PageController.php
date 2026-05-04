<?php

namespace Source\Controllers;

use Fluxor\Response;
use App\Core\Mailer;
use Fluxor\Controller;
use App\Core\ORMHelper;
use Source\Models\User;
use Source\Models\File;
use Source\Models\Plan;

class PageController extends Controller
{
    public function home()
    {
        $totalUsers = count(ORMHelper::getRepository(User::class)->findAll());
        $allFiles = ORMHelper::getRepository(File::class)->findAll();

        $totalFiles = 0;
        foreach ($allFiles as $file) {
            if (!$file->isDeleted()) {
                $totalFiles++;
            }
        }

        $planRepo = ORMHelper::getRepository(Plan::class);
        $plans = $planRepo->findAll(['is_active' => true]);

        usort($plans, function ($a, $b) {
            return $a->getSortOrder() <=> $b->getSortOrder();
        });

        $formattedPlans = [];
        $exchangeRate = 65;

        foreach ($plans as $plan) {
            $priceMZN = $plan->getPrice();
            $priceUSD = $priceMZN > 0 ? round($priceMZN / $exchangeRate, 2) : 0;

            $formattedPlans[] = [
                'name' => $plan->getName(),
                'slug' => $plan->getSlug(),
                'price_mzn' => $priceMZN,
                'price_usd' => $priceUSD,
                'storage_gb' => $plan->getStorageLimitBytes() > 0 ? round($plan->getStorageLimitBytes() / 1073741824, 2) : 'Unlimited',
                'bandwidth_gb' => $plan->getBandwidthLimitBytes() > 0 ? round($plan->getBandwidthLimitBytes() / 1073741824, 2) : 'Unlimited',
                'max_file_size_bytes' => $plan->getMaxFileSizeBytes(),
                'allowed_mime_types' => $plan->getAllowedMimeTypes(),
                'is_popular' => $plan->getSlug() === 'plus',
                'description' => $plan->getDescription()
            ];
        }

        $stats = [
            'uptime' => '99.9%',
            'active_users' => $this->formatNumber($totalUsers),
            'total_files' => $this->formatNumber($totalFiles),
            'storage_used' => $this->getTotalStorageUsed($allFiles)
        ];

        return Response::view('home', [
            'title' => 'Ntsava App',
            'stats' => $stats,
            'plans' => $formattedPlans
        ]);
    }

    public function about()
    {
        $totalUsers = count(ORMHelper::getRepository(User::class)->findAll());
        $allFiles = ORMHelper::getRepository(File::class)->findAll();

        $totalFiles = 0;
        $totalStorage = 0;

        foreach ($allFiles as $file) {
            if (!$file->isDeleted()) {
                $totalFiles++;
                $totalStorage += $file->getSizeBytes();
            }
        }

        $stats = [
            'total_users' => $this->formatNumber($totalUsers),
            'total_files' => $this->formatNumber($totalFiles),
            'total_storage' => $this->formatBytes($totalStorage),
            'founded' => '2026',
            'mission' => 'To provide fast, secure, and affordable content delivery for developers and businesses.'
        ];

        return Response::view('about', [
            'title' => 'About Ntsava App',
            'stats' => $stats
        ]);
    }

    public function contact()
    {
        $plan = $this->request->input('plan');
        $success = $this->request->input('success') === '1';

        $planRepo = ORMHelper::getRepository(Plan::class);
        $plans = $planRepo->findAll(['is_active' => true]);

        usort($plans, function ($a, $b) {
            return $a->getSortOrder() <=> $b->getSortOrder();
        });

        $formattedPlans = [];
        $exchangeRate = 65;

        foreach ($plans as $p) {
            $priceMZN = $p->getPrice();
            $priceUSD = $priceMZN > 0 ? round($priceMZN / $exchangeRate, 2) : 0;

            $formattedPlans[] = [
                'name' => $p->getName(),
                'slug' => $p->getSlug(),
                'price_mzn' => $priceMZN,
                'price_usd' => $priceUSD,
                'is_popular' => $p->getSlug() === 'plus',
                'storage_gb' => $p->getStorageLimitBytes() > 0 ? round($p->getStorageLimitBytes() / 1073741824, 2) : 'Unlimited',
                'bandwidth_gb' => $p->getBandwidthLimitBytes() > 0 ? round($p->getBandwidthLimitBytes() / 1073741824, 2) : 'Unlimited',
                'max_file_size_bytes' => $p->getMaxFileSizeBytes(),
                'allowed_mime_types' => $p->getAllowedMimeTypes(),
            ];
        }

        return Response::view('contact', [
            'title' => 'Contact Ntsava',
            'plan' => $plan,
            'success' => $success,
            'plans' => $formattedPlans
        ]);
    }

    public function sendContact()
    {
        $name = $this->request->input('name');
        $email = $this->request->input('email');
        $subject = $this->request->input('subject');
        $plan = $this->request->input('plan');
        $message = $this->request->input('message');

        if (empty($name) || empty($email) || empty($message)) {
            return Response::error('All fields are required', 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return Response::error('Invalid email address', 400);
        }

        $mailer = new Mailer();

        $emailBody = "
            <h2>New Contact Form Submission - Ntsava App</h2>
            <p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>
            <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
            <p><strong>Subject:</strong> " . htmlspecialchars($subject) . "</p>
            <p><strong>Plan Interest:</strong> " . htmlspecialchars($plan ?: 'Not specified') . "</p>
            <p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>
        ";

        $mailer->send('ntsava@tudocomlizzyman.com', 'New Contact: ' . $subject, $emailBody);

        $confirmationBody = "
            <h2>Thank you for contacting Ntsava!</h2>
            <p>We've received your message and will get back to you within 24 hours.</p>
            <p>Here's a copy of your message:</p>
            <hr>
            <p>" . nl2br(htmlspecialchars($message)) . "</p>
            <hr>
            <p><strong>Ntsava App</strong><br>Everything you need, in one basket.</p>
        ";

        $mailer->send($email, 'Thank you for contacting Ntsava', $confirmationBody);

        return Response::success([
            'redirect' => '/contact?success=1'
        ], 'Message sent successfully');
    }

    public function docs()
    {
        return Response::view('docs', [
            'title' => 'API Documentation'
        ]);
    }

    public function terms()
    {
        return Response::view('terms', [
            'title' => 'Terms of Service'
        ]);
    }

    public function privacy()
    {
        return Response::view('privacy', [
            'title' => 'Privacy Policy'
        ]);
    }

    private function formatNumber($number): string
    {
        if ($number < 100) {
            return '~ 100';
        }

        if ($number < 1000) {
            $rounded = ceil($number / 100) * 100;
            return $rounded . '+';
        }

        if ($number < 5000) {
            $rounded = ceil($number / 1000) * 1000;
            return $rounded . '+';
        }

        $rounded = ceil($number / 1000);
        return $rounded . 'k+';
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    private function getTotalStorageUsed($files): string
    {
        $totalBytes = 0;
        foreach ($files as $file) {
            if (!$file->isDeleted()) {
                $totalBytes += $file->getSizeBytes();
            }
        }

        return $this->formatBytes($totalBytes);
    }
}