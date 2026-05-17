@extends('admin.layout')

@section('title', 'Transaction History')

@section('content')
    <div class="content">
        <h2>Transaction History</h2>

        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Pair</th>
                    <th>Asset</th>
                    <th>Side</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->transaction_date }}</td>
                        <td>{{ $transaction->pair }}</td>
                        <td>{{ $transaction->asset }}</td>
                        <td>{{ ucfirst($transaction->side) }}</td>
                        <td>{{ number_format($transaction->quantity, 4) }}</td>
                        <td>{{ number_format($transaction->price, 2) }}</td>
                        <td>{{ number_format($transaction->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">No transactions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination">
            {{ $transactions->links() }}
        </div>
    </div>
@endsection
