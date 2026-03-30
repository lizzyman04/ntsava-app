<?php
/**
 * Plans Grid Component
 * 
 * @var array $plans
 *   - name: string
 *   - slug: string
 *   - price_mzn: float
 *   - price_usd: float
 *   - storage_gb: string|float
 *   - bandwidth_gb: string|float
 *   - is_popular: bool
 *   - description: string|null
 */
?>
<div class="grid md:grid-cols-4 gap-6 max-w-6xl mx-auto">
    <?php foreach ($plans as $plan): ?>
        <div
            class="glass-card p-6 text-center transition-all duration-300 hover:shadow-2xl <?= $plan['is_popular'] ? 'ring-2 ring-primary-500 ring-offset-2 ring-offset-white relative shadow-glow' : '' ?>">
            <?php if ($plan['is_popular']): ?>
                <div class="popular-badge">
                    <svg class="popular-badge-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span>Most Popular</span>
                </div>
            <?php endif; ?>

            <h3 class="text-xl font-bold mb-2"><?= htmlspecialchars($plan['name']) ?></h3>

            <?php if ($plan['slug'] === 'business'): ?>
                <div class="mb-4">
                    <span class="text-2xl font-bold text-primary-600">Custom</span>
                    <span class="text-sm text-gray-500">/month</span>
                </div>
                <div class="text-sm text-gray-500 mb-4">Contact us for pricing</div>
            <?php else: ?>
                <div class="mb-2">
                    <span class="text-3xl font-bold text-primary-600"><?= number_format($plan['price_mzn'], 0) ?> MZN</span>
                    <span class="text-sm text-gray-500">/month</span>
                </div>
                <?php if ($plan['price_usd'] > 0): ?>
                    <div class="text-sm text-gray-500 mb-4">
                        ≈ <?= number_format($plan['price_usd'], 2) ?> USD
                    </div>
                <?php else: ?>
                    <div class="text-sm text-gray-500 mb-4">&nbsp;</div>
                <?php endif; ?>
            <?php endif; ?>

            <ul class="space-y-2 mb-6 text-left">
                <li class="flex items-center gap-2">
                    <i class="fas fa-check-circle text-green-500"></i>
                    <span><?= $plan['storage_gb'] === 'Unlimited' ? 'Unlimited Storage' : $plan['storage_gb'] . ' GB Storage' ?></span>
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-check-circle text-green-500"></i>
                    <span><?= $plan['bandwidth_gb'] === 'Unlimited' ? 'Unlimited Bandwidth' : $plan['bandwidth_gb'] . ' GB Bandwidth' ?></span>
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-check-circle text-green-500"></i>
                    <span>API Access</span>
                </li>
                <?php if ($plan['slug'] !== 'free' && $plan['slug'] !== 'business'): ?>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>Priority Support</span>
                    </li>
                <?php endif; ?>
                <?php if ($plan['slug'] === 'business'): ?>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>Dedicated Support</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>Custom Domain</span>
                    </li>
                <?php endif; ?>
            </ul>

            <?php if ($plan['slug'] === 'free'): ?>
                <a href="/auth/signup" class="btn-outline w-full block">Get Started</a>
            <?php elseif ($plan['slug'] === 'business'): ?>
                <a href="/contact?plan=business" class="btn-outline w-full block">Contact Sales</a>
            <?php else: ?>
                <a href="/contact?plan=<?= $plan['slug'] ?>" class="btn-primary w-full block">Upgrade Now</a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>