<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ClientUserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function __construct(private readonly ClientUserService $clientUserService) {}

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'subscription_tier' => ['nullable', 'in:free,pro'],
            'status' => ['nullable', 'in:active,locked'],
        ]);

        $clients = $this->clientUserService->getClients($filters);

        return view('admin.clients.index', compact('clients', 'filters'));
    }

    public function show(Request $request, User $client): View
    {
        $filters = $request->validate([
            'period_days' => ['nullable', 'integer', 'in:7,30,90,180,365'],
            'per_page' => ['nullable', 'integer', 'in:10,15,20,50'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        return view('admin.clients.show', $this->clientUserService->getClientDetail($client, $filters));
    }

    public function updateStatus(Request $request, User $client): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:active,locked'],
        ]);

        $this->clientUserService->updateStatus($client, $data['status']);

        $query = $this->sanitizeShowFilters($request);

        return redirect()->route('admin.clients.show', array_merge(['client' => $client], $query))
            ->with('success', 'Trạng thái tài khoản học viên đã được cập nhật.');
    }

    public function updateSubscription(Request $request, User $client): RedirectResponse
    {
        $data = $request->validate([
            'subscription_tier' => ['required', 'in:free,pro'],
            'subscription_expires_at' => ['nullable', 'date', 'required_if:subscription_tier,pro', 'after_or_equal:today'],
        ]);

        $this->clientUserService->updateSubscription(
            $client,
            $data['subscription_tier'],
            $data['subscription_expires_at'] ?? null,
        );

        $query = $this->sanitizeShowFilters($request);

        return redirect()->route('admin.clients.show', array_merge(['client' => $client], $query))
            ->with('success', 'Gói cước học viên đã được cập nhật.');
    }

    private function sanitizeShowFilters(Request $request): array
    {
        $periodDays = (int) $request->input('period_days');
        $perPage = (int) $request->input('per_page');
        $page = (int) $request->input('page');

        return array_filter([
            'period_days' => in_array($periodDays, [7, 30, 90, 180, 365], true) ? $periodDays : null,
            'per_page' => in_array($perPage, [10, 15, 20, 50], true) ? $perPage : null,
            'page' => $page > 0 ? $page : null,
        ]);
    }
}
