<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function __construct(private readonly TransactionService $transactionService) {}

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', 'in:pending,success,failed'],
        ]);

        $transactions = $this->transactionService->getTransactions($filters);

        return view('admin.transactions.index', compact('transactions', 'filters'));
    }

    public function updateStatus(Request $request, Transaction $transaction): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,success,failed'],
        ]);

        $this->transactionService->updateStatus($transaction, $data['status']);

        return redirect()->route('admin.transactions.index')
            ->with('success', 'Trạng thái giao dịch đã được cập nhật.');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $this->transactionService->deleteTransaction($transaction);

        return redirect()->route('admin.transactions.index')
            ->with('success', 'Giao dịch đã được xóa.');
    }
}
