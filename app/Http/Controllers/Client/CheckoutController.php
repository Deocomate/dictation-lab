<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\Client\CheckoutService;
use App\Services\Client\SePayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CheckoutService $checkoutService,
        private readonly SePayService $sePayService,
    ) {}

    public function index(Request $request): View
    {
        $planId = $request->query('plan');
        $plan = $planId ? $this->checkoutService->getPlan($planId) : null;

        return view('client.checkout.index', [
            'plan' => $plan,
            'plans' => $this->checkoutService->getActivePlans(),
        ]);
    }

    public function process(Request $request): RedirectResponse|View
    {
        $data = $request->validate([
            'plan_id' => ['required', 'exists:plans,id'],
        ]);

        try {
            $sePayData = $this->sePayService->createTransactionAndForm(
                auth()->user(),
                (int) $data['plan_id'],
            );

            return view('client.checkout.sepay-redirect', [
                'actionUrl' => $sePayData['actionUrl'],
                'formFields' => $sePayData['formFields'],
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return back()->withInput()->withErrors([
                'system' => 'Không thể tạo giao dịch lúc này. Vui lòng thử lại sau.',
            ]);
        }
    }

    public function success(int $transaction): View
    {
        $txn = auth()->user()->transactions()->with('plan')->findOrFail($transaction);

        return view('client.checkout.success', ['transaction' => $txn]);
    }

    public function failed(int $transaction): View
    {
        $txn = auth()->user()->transactions()->with('plan')->findOrFail($transaction);

        return view('client.checkout.failed', ['transaction' => $txn]);
    }

    public function pending(int $transaction): View|RedirectResponse
    {
        $txn = auth()->user()->transactions()->with('plan')->findOrFail($transaction);

        if ($txn->status === 'success') {
            return redirect()->route('client.checkout.success', $txn->id);
        }

        if ($txn->status === 'failed') {
            return redirect()->route('client.checkout.failed', $txn->id);
        }

        $qrImageUrl = null;
        if (! empty($txn->qr_code)) {
            $qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=320x320&data='.urlencode($txn->qr_code);
        }

        return view('client.checkout.pending', [
            'transaction' => $txn,
            'qrImageUrl' => $qrImageUrl,
        ]);
    }

    public function sepayIpn(Request $request): JsonResponse
    {
        try {
            $transaction = $this->sePayService->handleIpn(
                $request->all(),
                (string) $request->header('X-Secret-Key'),
            );
        } catch (HttpExceptionInterface $exception) {
            return response()->json([
                'message' => $exception->getMessage() ?: 'Invalid SePay IPN.',
            ], $exception->getStatusCode());
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'SePay IPN processing error.',
            ], 500);
        }

        return response()->json([
            'message' => 'SePay IPN processed.',
            'transaction_id' => $transaction?->id,
        ]);
    }
}
