<?php

namespace App\Services;

use Illuminate\Support\Collection;
use App\Models\Transaction;

class InventoryAnalysisService
{
    public function analyze(Collection $transactions): array
    {
        $grouped = $transactions->groupBy('item_name');

        $items = $grouped->map(function (Collection $records, $itemName) {
            $purchases = $records->where('transaction_type', 'purchase');
            $sales = $records->where('transaction_type', 'sale');

            $totalPurchased = $purchases->sum('quantity');
            $totalSold = $sales->sum('quantity');
            $stockOnHand = $totalPurchased - $totalSold;

            $purchaseCost = $purchases->sum(fn ($record) => $record->quantity * $record->unit_price);
            $averageCost = $totalPurchased > 0 ? $purchaseCost / $totalPurchased : 0;
            $revenue = $sales->sum(fn ($record) => $record->quantity * $record->unit_price);
            $cogs = min($totalSold, $totalPurchased) * $averageCost;
            $profitLoss = $revenue - $cogs;

            return [
                'item_name' => $itemName,
                'stock_on_hand' => $stockOnHand,
                'total_purchased' => $totalPurchased,
                'total_sold' => $totalSold,
                'average_cost' => round($averageCost, 4),
                'revenue' => round($revenue, 4),
                'cogs' => round($cogs, 4),
                'profit_loss' => round($profitLoss, 4),
            ];
        })->values()->all();

        $totals = [
            'stock_on_hand' => collect($items)->sum('stock_on_hand'),
            'revenue' => collect($items)->sum('revenue'),
            'cogs' => collect($items)->sum('cogs'),
            'profit_loss' => collect($items)->sum('profit_loss'),
        ];

        return [
            'items' => $items,
            'totals' => $totals,
        ];
    }
}
