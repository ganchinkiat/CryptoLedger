@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
    <div class="cards-grid">
        <div class="card primary">
            <div class="card-title">Total Transactions</div>
            <div class="card-value">{{ number_format($totalTransactions) }}</div>
        </div>

        <div class="card success">
            <div class="card-title">Total Asset Types</div>
            <div class="card-value">{{ number_format($totalAssets) }}</div>
        </div>

        <div class="card warning">
            <div class="card-title">Total Quantity</div>
            <div class="card-value">{{ number_format($totalValue, 2) }}</div>
        </div>
    </div>

    <div class="content">
        <h2>Recent Transactions</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Asset</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentTransactions as $transaction)
                    <tr>
                        <td>{{ $transaction->date }}</td>
                        <td>{{ $transaction->asset }}</td>
                        <td>{{ ucfirst($transaction->type) }}</td>
                        <td>{{ number_format($transaction->quantity, 4) }}</td>
                        <td>{{ number_format($transaction->price, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No recent transactions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
