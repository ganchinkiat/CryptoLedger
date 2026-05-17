<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CryptoLedger</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; background: #f5f7fb; color: #1f2937; }
        .card { background: #ffffff; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08); }
        .grid { display: grid; gap: 24px; }
        .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .button { background: #2563eb; color: white; border: none; padding: 12px 20px; border-radius: 8px; cursor: pointer; }
        .button:hover { background: #1d4ed8; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #e5e7eb; }
        th { background: #f8fafc; }
        .badge { display: inline-block; padding: 6px 12px; border-radius: 9999px; background: #e2e8f0; }
    </style>
</head>
<body>
    <div class="card">
        <h1>CryptoLedger transaction importer</h1>
        <p>Upload an Excel file with transaction history, then review stock on hand and profit/loss analysis.</p>
    </div>

    <div class="card">
        <h2>Import transaction history</h2>
        <form action="{{ route('transactions.import') }}" method="post" enctype="multipart/form-data">
            @csrf
            <label for="transactions">Select Excel file (.xlsx, .xls, .csv):</label><br>
            <input type="file" name="transactions" id="transactions" accept=".xlsx,.xls,.csv" required><br><br>
            <button class="button" type="submit">Import transactions</button>
        </form>
        <p style="margin-top: 16px; color: #475569;">Expected file columns in order: <strong>Date</strong>, <strong>Item</strong>, <strong>Type</strong>, <strong>Quantity</strong>, <strong>Unit Price</strong>, <strong>Total Amount</strong>.</p>

        @if(session('summary'))
            <div style="margin-top: 16px; padding: 16px; background: #ecfdf5; border-radius: 8px;">
                <strong>{{ session('summary.imported') }} transactions imported.</strong>
                @if(count(session('summary.errors')) > 0)
                    <div style="margin-top: 8px; color: #b91c1c;">
                        <p>Import errors:</p>
                        <ul>
                            @foreach(session('summary.errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif

        @if($errors->any())
            <div style="margin-top: 16px; padding: 16px; background: #fee2e2; border-radius: 8px; color: #991b1b;">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-2">
        <div class="card">
            <h2>Stock on hand</h2>
            @if(count($analysis['items']) > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Stock</th>
                            <th>Avg cost</th>
                            <th>Profit/Loss</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($analysis['items'] as $item)
                            <tr>
                                <td>{{ $item['item_name'] }}</td>
                                <td>{{ $item['stock_on_hand'] }}</td>
                                <td>{{ number_format($item['average_cost'], 4) }}</td>
                                <td>{{ number_format($item['profit_loss'], 4) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No transactions imported yet.</p>
            @endif
        </div>

        <div class="card">
            <h2>Profit and loss summary</h2>
            @if(count($analysis['items']) > 0)
                <table>
                    <tbody>
                        <tr>
                            <th>Total Revenue</th>
                            <td>{{ number_format($analysis['totals']['revenue'], 4) }}</td>
                        </tr>
                        <tr>
                            <th>Total Cost of Goods Sold</th>
                            <td>{{ number_format($analysis['totals']['cogs'], 4) }}</td>
                        </tr>
                        <tr>
                            <th>Total Profit/Loss</th>
                            <td>{{ number_format($analysis['totals']['profit_loss'], 4) }}</td>
                        </tr>
                    </tbody>
                </table>
            @else
                <p>No profit/loss data available until transactions are imported.</p>
            @endif
        </div>
    </div>

    <div class="card">
        <h2>Recent transactions</h2>
        @if(count($transactions) > 0)
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Item</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions->take(20) as $transaction)
                        <tr>
                            <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                            <td>{{ $transaction->item_name }}</td>
                            <td>{{ ucfirst($transaction->transaction_type) }}</td>
                            <td>{{ number_format($transaction->quantity, 4) }}</td>
                            <td>{{ number_format($transaction->unit_price, 4) }}</td>
                            <td>{{ number_format($transaction->total_amount, 4) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No imported transactions yet.</p>
        @endif
    </div>
</body>
</html>
