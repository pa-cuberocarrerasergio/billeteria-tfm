<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        return response()->json(
            Transaction::with('category')->get()
        );
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:income,expense',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
        ]);
        $transaction = Transaction::create($validated);

        return response()->json($transaction, 201);
    }
    public function show(Transaction $transaction)
    {
        return response()->json(
            $transaction->load('category')
        );
    }

    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
        'category_id' => 'required|exists:categories,id',
        'type' => 'required|in:income,expense',
        'title' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0.01',
        'transaction_date' => 'required|date',
        ]);

        $transaction->update($validated);

        return response()->json($transaction);
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return response()->json([
            'message' => 'Transaction deleted successfully'
        ]);
    }
}
