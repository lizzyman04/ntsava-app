<?php
// Load helpers
require_once __DIR__ . '/../components/helpers.php';

/**
 * @var \Source\Models\User $user
 * @var \Source\Models\Plan[] $plans
 * @var \Source\Models\CreditTransaction[] $transactions
 * @var float $credits
 * @var string $title
 * @var string $page_title
 * @var string $active_menu
 */
use Fluxor\View;
?>
<?php View::extend('layouts/dashboard'); ?>

<?php View::section('title'); ?>
Credits & Upgrades
<?php View::endSection(); ?>

<?php View::section('active_menu'); ?>
credits
<?php View::endSection(); ?>

<?php View::section('content'); ?>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="glass-card p-6 text-center">
        <div
            class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-yellow-100 to-yellow-200 dark:from-yellow-900/30 dark:to-yellow-800/30 flex items-center justify-center">
            <i class="fas fa-coins text-3xl text-yellow-500"></i>
        </div>
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white"><?= number_format($credits, 2) ?></h2>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Available Credits</p>
        <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Use credits to upgrade your plan</p>
    </div>

    <div class="lg:col-span-2">
        <div class="glass-card p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Available Plans</h2>
            <div class="grid md:grid-cols-3 gap-4">
                <?php foreach ($plans as $plan): ?>
                    <?php /** @var \Source\Models\Plan $plan */ ?>
                    <div
                        class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 text-center hover:shadow-lg transition">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white"><?= $plan->getName() ?></h3>
                        <div class="mt-2">
                            <span class="text-2xl font-bold text-primary-500"><?= $plan->getFormattedPrice() ?></span>
                        </div>
                        <div class="mt-3 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                            <p><?= $plan->getStorageLimitBytes() > 0 ? round($plan->getStorageLimitBytes() / 1073741824, 2) . ' GB Storage' : 'Unlimited Storage' ?>
                            </p>
                            <p><?= $plan->getBandwidthLimitBytes() > 0 ? round($plan->getBandwidthLimitBytes() / 1073741824, 2) . ' GB Bandwidth' : 'Unlimited Bandwidth' ?>
                            </p>
                        </div>
                        <?php if ($plan->getSlug() !== 'business'): ?>
                            <button onclick="upgradePlan('<?= $plan->getSlug() ?>')"
                                class="mt-4 btn-primary w-full <?= $credits < $plan->getPrice() ? 'opacity-50 cursor-not-allowed' : '' ?>"
                                <?= $credits < $plan->getPrice() ? 'disabled' : '' ?>>
                                <?= $credits >= $plan->getPrice() ? 'Upgrade' : 'Insufficient Credits' ?>
                            </button>
                        <?php else: ?>
                            <a href="mailto:contact@cdn.tudocomlizzyman.com"
                                class="mt-4 btn-secondary w-full inline-block text-center">
                                Contact Us
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="glass-card p-6">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Transaction History</h2>

    <?php if (empty($transactions)): ?>
        <div class="text-center py-12">
            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                <i class="fas fa-receipt text-3xl text-gray-400"></i>
            </div>
            <p class="text-gray-500 dark:text-gray-400">No transactions yet.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left py-3 text-gray-500 dark:text-gray-400 font-medium">Date</th>
                        <th class="text-left py-3 text-gray-500 dark:text-gray-400 font-medium">Type</th>
                        <th class="text-left py-3 text-gray-500 dark:text-gray-400 font-medium">Amount</th>
                        <th class="text-left py-3 text-gray-500 dark:text-gray-400 font-medium">Description</th>
                        <th class="text-left py-3 text-gray-500 dark:text-gray-400 font-medium">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $transaction): ?>
                        <?php /** @var \Source\Models\CreditTransaction $transaction */ ?>
                        <tr
                            class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                            <td class="py-3 text-gray-600 dark:text-gray-400">
                                <?= $transaction->getCreatedAt()->format('d/m/Y H:i') ?></td>
                            <td class="py-3 capitalize">
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-700">
                                    <?= str_replace('_', ' ', $transaction->getType()) ?>
                                </span>
                            </td>
                            <td class="py-3 <?= $transaction->getAmount() > 0 ? 'text-green-600' : 'text-red-600' ?>">
                                <?= $transaction->getCurrency() ?>         <?= number_format($transaction->getAmount(), 2) ?>
                            </td>
                            <td class="py-3 text-gray-600 dark:text-gray-400"><?= $transaction->getDescription() ?: '-' ?></td>
                            <td class="py-3 text-gray-600 dark:text-gray-400"><?= $transaction->getCurrency() ?>
                                <?= number_format($transaction->getBalanceAfter(), 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script src="<?= asset('js/dashboard/credits.js') ?>"></script>
<?php View::endSection(); ?>