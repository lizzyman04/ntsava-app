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
        $allCredits = ORMHelper::findAll(Credit::class);
        $credits = null;
        foreach ($allCredits as $c) {
            if ($c->getUserId() === $this->user->getId()) {
                $credits = $c;
                break;
            }
        }

        $allTransactions = ORMHelper::findAll(CreditTransaction::class);
        $transactions = [];
        foreach ($allTransactions as $t) {
            if ($t->getUserId() === $this->user->getId()) {
                $transactions[] = $t;
            }
        }

        usort($transactions, function ($a, $b) {
            return $b->getCreatedAt() <=> $a->getCreatedAt();
        });

        $transactions = array_slice($transactions, 0, 20);

        $allPlans = ORMHelper::findAll(Plan::class);
        $plans = [];
        foreach ($allPlans as $p) {
            if ($p->isActive()) {
                $plans[] = $p;
            }
        }

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
        $allPlans = ORMHelper::findAll(Plan::class);
        $plan = null;
        foreach ($allPlans as $p) {
            if ($p->getSlug() === $planSlug && $p->isActive()) {
                $plan = $p;
                break;
            }
        }

        if (!$plan) {
            return Response::error('Plan not found', 404);
        }

        $allCredits = ORMHelper::findAll(Credit::class);
        $credits = null;
        foreach ($allCredits as $c) {
            if ($c->getUserId() === $this->user->getId()) {
                $credits = $c;
                break;
            }
        }

        if (!$credits || $credits->getAmount() < $plan->getPrice()) {
            return Response::error('Insufficient credits', 402);
        }

        $credits->subtract($plan->getPrice());

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
        $allPlans = ORMHelper::findAll(Plan::class);
        foreach ($allPlans as $plan) {
            if ($plan->getId() === $planId) {
                return $plan->getName();
            }
        }
        return 'Unknown';
    }
}