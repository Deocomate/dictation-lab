<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\PlanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function __construct(private readonly PlanService $planService) {}

    public function index(): View
    {
        $plans = $this->planService->getPlans();

        return view('admin.plans.index', compact('plans'));
    }

    public function create(): View
    {
        return view('admin.plans.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());
        $this->planService->createPlan($data);

        return redirect()->route('admin.plans.index')->with('success', 'Gói cước đã được tạo.');
    }

    public function edit(Plan $plan): View
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $data = $request->validate($this->rules());
        $this->planService->updatePlan($plan, $data);

        return redirect()->route('admin.plans.index')->with('success', 'Gói cước đã được cập nhật.');
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        $this->planService->deletePlan($plan);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Gói cước đã được xóa.');
    }

    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
