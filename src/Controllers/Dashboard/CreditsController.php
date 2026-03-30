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
            'plans' => $plans,
            'current_plan_name' => $this->getPlanName($this->user->getPlanId())
        ]);
    }

    public function upgrade($planSlug)
    {
        try {
            $plan = null;
            $allPlans = ORMHelper::findAll(Plan::class);
            foreach ($allPlans as $p) {
                if ($p->getSlug() === $planSlug && $p->isActive()) {
                    $plan = $p;
                    break;
                }
            }
            if (!$plan) {
                return Response::error('Plan not found', 404);
            }

            $currentPlanId = $this->user->getPlanId();
            if ($currentPlanId == $plan->getId()) {
                return Response::error('You are already on this plan', 400);
            }

            $isDowngrade = $this->isDowngrade($currentPlanId, $plan->getId());

            $credits = null;
            $allCredits = ORMHelper::findAll(Credit::class);
            foreach ($allCredits as $c) {
                if ($c->getUserId() === $this->user->getId()) {
                    $credits = $c;
                    break;
                }
            }
            
            if (!$isDowngrade && (!$credits || $credits->getAmount() < $plan->getPrice())) {
                return Response::error('Insufficient credits', 402);
            }

            $newAmount = $credits ? $credits->getAmount() : 0;
            if (!$isDowngrade) {
                $newAmount = $credits->getAmount() - $plan->getPrice();
                $credits->setAmount($newAmount);
            }

            $metadata = [
                'old_plan_id' => $currentPlanId,
                'new_plan_id' => $plan->getId(),
                'old_plan_name' => $this->getPlanName($currentPlanId),
                'new_plan_name' => $plan->getName()
            ];
            $metadataJson = json_encode($metadata);

            $transactionType = $isDowngrade ? 'downgrade_plan' : 'upgrade_plan';

            $db = ORMHelper::getDatabaseManager();
            $db->database('default')->insert('credit_transactions')->values([
                'user_id' => $this->user->getId(),
                'type' => $transactionType,
                'amount' => $isDowngrade ? 0 : -$plan->getPrice(),
                'currency' => $plan->getCurrency(),
                'balance_after' => $newAmount,
                'description' => ($isDowngrade ? "Downgraded to" : "Upgraded to") . " {$plan->getName()} plan",
                'metadata' => $metadataJson
            ])->run();

            $this->user->setPlanId($plan->getId())
                ->setStorageLimitBytes($plan->getStorageLimitBytes())
                ->setBandwidthLimitBytes($plan->getBandwidthLimitBytes());

            $entityManager = ORMHelper::getManager();
            if (!$isDowngrade && $credits) {
                $entityManager->persist($credits);
            }
            $entityManager->persist($this->user);
            $entityManager->run();

            return Response::success([
                'new_balance' => $newAmount,
                'plan' => $plan->getName(),
                'is_downgrade' => $isDowngrade
            ], ($isDowngrade ? 'Downgraded' : 'Upgraded') . " to {$plan->getName()} plan successfully");
            
        } catch (\Exception $e) {
            error_log('EXCEPTION in upgrade(): ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            return Response::error('Server error: ' . $e->getMessage(), 500);
        }
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

    private function isDowngrade($currentPlanId, $newPlanId): bool
    {
        $allPlans = ORMHelper::findAll(Plan::class);
        $currentSortOrder = 0;
        $newSortOrder = 0;
        
        foreach ($allPlans as $plan) {
            if ($plan->getId() === $currentPlanId) {
                $currentSortOrder = $plan->getSortOrder();
            }
            if ($plan->getId() === $newPlanId) {
                $newSortOrder = $plan->getSortOrder();
            }
        }
        
        return $newSortOrder < $currentSortOrder;
    }
}