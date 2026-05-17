<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TransactionImportService;
use App\Services\InventoryAnalysisService;
use App\Models\Transaction;

class TransactionImportController extends Controller
{
    protected TransactionImportService $importService;
    protected InventoryAnalysisService $analysisService;

    public function __construct(TransactionImportService $importService, InventoryAnalysisService $analysisService)
    {
        $this->importService = $importService;
        $this->analysisService = $analysisService;
    }

    public function index()
    {
        $transactions = Transaction::orderByDesc('transaction_date')->get();
        $analysis = $this->analysisService->analyze($transactions);

        return view('welcome', [
            'transactions' => $transactions,
            'analysis' => $analysis,
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'transactions' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('transactions');
        $summary = $this->importService->importFile($file->getRealPath());

        return redirect()->route('transactions.index')->with('summary', $summary);
    }
}
