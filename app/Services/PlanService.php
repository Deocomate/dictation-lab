<?php

namespace App\Services;

use App\Models\Plan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PlanService
{
    public function getPlans(int $perPage = 15): LengthAwarePaginator
    {
        return Plan::query()->latest()->paginate($perPage);
    }

    public function createPlan(array $data): Plan
    {
        return Plan::create($data);
    }

    public function updatePlan(Plan $plan, array $data): Plan
    {
        $plan->update($data);

        return $plan->fresh();
    }

    public function deletePlan(Plan $plan): void
    {
        $plan->delete();
    }
}
