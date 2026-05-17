@extends('admin.layout')

@section('title', 'Profit & Loss Report')

@section('content')
    <div class="content">
        <h2>Profit & Loss Report</h2>

        <table>
            <thead>
                <tr>
                    <th>Asset</th>
                    <th>Quantity</th>
                    <th>Buy Cost</th>
                    <th>Current Cost</th>
                    <th>Profit/Loss</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($profitLoss as $item)
                    <tr>
                        <td>{{ $item['asset'] }}</td>
                        <td>{{ number_format($item['quantity'], 4) }}</td>
                        <td>${{ number_format($item['buy_cost'], 2) }}</td>
                        <td>${{ number_format($item['current_cost'], 2) }}</td>
                        <td>${{ number_format($item['profit_loss'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No profit/loss data available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
