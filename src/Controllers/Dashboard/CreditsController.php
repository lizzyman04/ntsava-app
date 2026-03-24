<?php

namespace Source\Controllers\Dashboard;

use Fluxor\Response;
use App\Core\ORMHelper;
use Source\Models\Credit;
use Source\Models\CreditTransaction;
use Source\Models\Plan;

class CreditsController extends DashboardController
{
    public function index()
    {
        $creditRepo = ORMHelper::getRepository(Credit::class);
        $credits = $creditRepo->findOne(['userId' => $this->user->getId()]);

        $transactionRepo = ORMHelper::getRepository(CreditTransaction::class);
        $transactions = $transactionRepo->findAll(['userId' => $this->user->getId()]);

        // Sort by created_at DESC manually
        usort($transactions, function ($a, $b) {
            return $b->getCreatedAt() <=> $a->getCreatedAt();
        });

        // Limit to 20
        $transactions = array_slice($transactions, 0, 20);

        $planRepo = ORMHelper::getRepository(Plan::class);
        $plans = $planRepo->findAll(['isActive' => true]);

        // Sort by sort_order
        usort($plans, function ($a, $b) {
            return $a->getSortOrder() <=> $b->getSortOrder();
        });

        return Response::view('dashboard/credits', [
            'title' => 'Credits',
            'page_title' => 'Credits & Upgrades',
            'active_menu' => 'credits',
            'user' => $this->user,
            'credits' => $credits ? $credits->getAmount() : 0,
            'transactions' => $transactions,
            'plans' => $plans
        ]);
    }

    public function upgrade($planSlug)
    {
        $planRepo = ORMHelper::getRepository(Plan::class);
        $plan = $planRepo->findOne(['slug' => $planSlug, 'isActive' => true]);

        if (!$plan) {
            return Response::error('Plan not found', 404);
        }

        $creditRepo = ORMHelper::getRepository(Credit::class);
        $credits = $creditRepo->findOne(['userId' => $this->user->getId()]);

        if (!$credits || $credits->getAmount() < $plan->getPrice()) {
            return Response::error('Insufficient credits', 402);
        }

        // Deduct credits
        $oldAmount = $credits->getAmount();
        $credits->subtract($plan->getPrice());

        // Create transaction
        $transaction = new CreditTransaction(
            $this->user->getId(),
            'upgrade_plan',
            -$plan->getPrice(),
            $credits->getAmount(),
            $plan->getCurrency()
        );
        $transaction->setDescription("Upgraded to {$plan->getName()} plan");
        $transaction->setMetadata([
            'old_plan_id' => $this->user->getPlanId(),
            'new_plan_id' => $plan->getId(),
            'old_plan_name' => $this->getPlanName($this->user->getPlanId()),
            'new_plan_name' => $plan->getName()
        ]);

        // Update user plan
        $this->user->setPlanId($plan->getId())
            ->setStorageLimitBytes($plan->getStorageLimitBytes())
            ->setBandwidthLimitBytes($plan->getBandwidthLimitBytes());

        $entityManager = ORMHelper::getManager();
        $entityManager->persist($credits);
        $entityManager->persist($transaction);
        $entityManager->persist($this->user);
        $entityManager->run();

        return Response::success([
            'new_balance' => $credits->getAmount(),
            'plan' => $plan->getName()
        ], 'Plan upgraded successfully');
    }

    private function getPlanName($planId)
    {
        $planRepo = ORMHelper::getRepository(Plan::class);
        $plan = $planRepo->findByPK($planId);
        return $plan ? $plan->getName() : 'Unknown';
    }
}