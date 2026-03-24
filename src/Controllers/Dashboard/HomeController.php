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
        // Get user stats
        $fileRepo = ORMHelper::getRepository(File::class);
        $files = $fileRepo->findAll(['userId' => $this->user->getId(), 'deletedAt' => null]);
        $totalFilesCount = count($files);
        $totalSize = array_sum(array_map(fn($f) => $f->getSizeBytes(), $files));

        // Get credits
        $creditRepo = ORMHelper::getRepository(Credit::class);
        $credits = $creditRepo->findOne(['userId' => $this->user->getId()]);

        // Get recent files (limit 5)
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