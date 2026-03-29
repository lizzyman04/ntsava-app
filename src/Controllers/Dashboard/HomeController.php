<?php

namespace Source\Controllers\Dashboard;

use Fluxor\Response;
use App\Core\ORMHelper;
use Source\Models\File;
use Source\Models\Credit;

class HomeController extends DashboardController
{
    public function index()
    {
        $allFiles = ORMHelper::findAll(File::class);
        $files = [];
        $totalSize = 0;
        
        foreach ($allFiles as $file) {
            if ($file->getUserId() === $this->user->getId() && !$file->isDeleted()) {
                $files[] = $file;
                $totalSize += $file->getSizeBytes();
            }
        }
        
        $totalFilesCount = count($files);
        
        $allCredits = ORMHelper::findAll(Credit::class);
        $credits = null;
        foreach ($allCredits as $c) {
            if ($c->getUserId() === $this->user->getId()) {
                $credits = $c;
                break;
            }
        }
        
        usort($files, function ($a, $b) {
            return $b->getCreatedAt() <=> $a->getCreatedAt();
        });
        $recentFiles = array_slice($files, 0, 5);

        return Response::view('dashboard/index', [
            'title' => 'Dashboard',
            'page_title' => 'Dashboard',
            'active_menu' => 'dashboard',
            'user' => $this->user,
            'total_files' => $totalFilesCount,
            'total_size' => $totalSize,
            'storage_used_gb' => round($this->user->getStorageUsedBytes() / 1073741824, 2),
            'storage_limit_gb' => round($this->user->getStorageLimitBytes() / 1073741824, 2),
            'storage_percent' => $this->user->getStorageUsagePercent(),
            'bandwidth_used_gb' => round($this->user->getBandwidthUsedBytes() / 1073741824, 2),
            'bandwidth_limit_gb' => round($this->user->getBandwidthLimitBytes() / 1073741824, 2),
            'bandwidth_percent' => $this->user->getBandwidthUsagePercent(),
            'credits' => $credits ? $credits->getAmount() : 0,
            'recent_files' => $recentFiles
        ]);
    }
}