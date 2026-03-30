<?php
require_once __DIR__ . '/../components/helpers.php';

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
            class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-yellow-100 to-yellow-200 flex items-center justify-center">
            <i class="fas fa-coins text-3xl text-yellow-500"></i>
        </div>
        <h2 class="text-3xl font-bold text-gray-900"><?= number_format($credits, 2) ?></h2>
        <p class="text-gray-500 mt-1">Available Credits</p>
        <p class="text-sm text-gray-400 mt-2">Use credits to upgrade your plan</p>
    </div>

    <div class="lg:col-span-2">
        <div class="glass-card p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Available Plans</h2>
                <div class="text-sm text-gray-500">
                    Current Plan: <span class="font-semibold text-primary-600"><?= htmlspecialchars($current_plan_name) ?></span>
                </div>
            </div>
            <div class="grid md:grid-cols-3 gap-4">
                <?php foreach ($plans as $plan): ?>
                    <?php /** @var \Source\Models\Plan $plan */ ?>
                    <?php $isCurrentPlan = ($plan->getId() === $user->getPlanId()); ?>
                    <div class="border rounded-xl p-4 text-center transition <?= $isCurrentPlan ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:shadow-lg' ?>">
                        <h3 class="text-lg font-bold text-gray-900"><?= $plan->getName() ?></h3>
                        <div class="mt-2">
                            <span class="text-2xl font-bold text-primary-500"><?= $plan->getFormattedPrice() ?></span>
                        </div>
                        <div class="mt-3 space-y-1 text-sm text-gray-600">
                            <p><?= $plan->getStorageLimitBytes() > 0 ? round($plan->getStorageLimitBytes() / 1073741824, 2) . ' GB Storage' : 'Unlimited Storage' ?></p>
                            <p><?= $plan->getBandwidthLimitBytes() > 0 ? round($plan->getBandwidthLimitBytes() / 1073741824, 2) . ' GB Bandwidth' : 'Unlimited Bandwidth' ?></p>
                        </div>
                        <?php if ($isCurrentPlan): ?>
                            <button disabled class="mt-4 w-full bg-gray-300 text-gray-500 py-2 rounded-lg cursor-not-allowed">
                                Current Plan
                            </button>
                        <?php elseif ($plan->getSlug() !== 'business'): ?>
                            <button onclick="upgradePlan('<?= $plan->getSlug() ?>')"
                                class="mt-4 btn-primary w-full <?= $credits < $plan->getPrice() ? 'opacity-50 cursor-not-allowed' : '' ?>"
                                <?= $credits < $plan->getPrice() ? 'disabled' : '' ?>>
                                <?= $credits >= $plan->getPrice() ? 'Upgrade' : 'Insufficient Credits' ?>
                            </button>
                        <?php else: ?>
                            <a href="mailto:cdn@tudocomlizzyman.com"
                                class="mt-4 btn-secondary w-full inline-block text-center">
                                Contact Sales
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="glass-card p-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">Transaction History</h2>

    <?php if (empty($transactions)): ?>
        <div class="text-center py-12">
            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                <i class="fas fa-receipt text-3xl text-gray-400"></i>
            </div>
            <p class="text-gray-500">No transactions yet.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 text-gray-500 font-medium">Date</th>
                        <th class="text-left py-3 text-gray-500 font-medium">Type</th>
                        <th class="text-left py-3 text-gray-500 font-medium">Amount</th>
                        <th class="text-left py-3 text-gray-500 font-medium">Description</th>
                        <th class="text-left py-3 text-gray-500 font-medium">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $transaction): ?>
                        <?php /** @var \Source\Models\CreditTransaction $transaction */ ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="py-3 text-gray-600"><?= $transaction->getCreatedAt()->format('d/m/Y H:i') ?></td>
                            <td class="py-3 capitalize">
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100">
                                    <?= str_replace('_', ' ', $transaction->getType()) ?>
                                </span>
                            </td>
                            <td class="py-3 <?= $transaction->getAmount() > 0 ? 'text-green-600' : 'text-red-600' ?>">
                                <?= $transaction->getCurrency() ?> <?= number_format($transaction->getAmount(), 2) ?>
                            </td>
                            <td class="py-3 text-gray-600"><?= $transaction->getDescription() ?: '-' ?></td>
                            <td class="py-3 text-gray-600"><?= $transaction->getCurrency() ?> <?= number_format($transaction->getBalanceAfter(), 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script src="<?= asset('js/dashboard/credits.js') ?>"></script>
<?php View::endSection(); ?>