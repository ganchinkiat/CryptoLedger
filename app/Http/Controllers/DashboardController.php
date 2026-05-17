<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the dashboard.
     */
    public function index()
    {
        $totalTransactions = Transaction::count();
        $totalAssets = Transaction::select('asset')->distinct()->count();
        $totalValue = Transaction::sum('quantity');
        
        $recentTransactions = Transaction::latest('transaction_date')->take(5)->get();

        return view('admin.dashboard', compact('totalTransactions', 'totalAssets', 'totalValue', 'recentTransactions'));
    }

    /**
     * Show transaction history.
     */
    public function transactions()
    {
        $transactions = Transaction::latest('transaction_date')->paginate(15);
        return view('admin.transactions', compact('transactions'));
    }

    /**
     * Show asset list.
     */
    public function assets()
    {
        $assetSummary = Transaction::selectRaw('asset, SUM(quantity) as total_quantity, SUM(amount) as total_value')
            ->groupBy('asset')
            ->get();

        return view('admin.assets', compact('assetSummary'));
    }

    /**
     * Show profit and loss report.
     */
    public function profitLoss()
    {
        $transactions = Transaction::all();
        
        $profitLoss = [];
        foreach ($transactions as $transaction) {
            if (!isset($profitLoss[$transaction->asset])) {
                $profitLoss[$transaction->asset] = [
                    'asset' => $transaction->asset,
                    'quantity' => 0,
                    'buy_cost' => 0,
                    'sell_revenue' => 0,
                    'profit_loss' => 0,
                ];
            }
            
            if ($transaction->side === 'buy') {
                $profitLoss[$transaction->asset]['quantity'] += $transaction->quantity;
                $profitLoss[$transaction->asset]['buy_cost'] += $transaction->amount;
            } else {
                $profitLoss[$transaction->asset]['quantity'] -= $transaction->quantity;
                $profitLoss[$transaction->asset]['sell_revenue'] += $transaction->amount;
            }
        }

        foreach ($profitLoss as &$item) {
            $item['profit_loss'] = $item['sell_revenue'] - $item['buy_cost'];
        }

        return view('admin.profit-loss', compact('profitLoss'));
    }
}
