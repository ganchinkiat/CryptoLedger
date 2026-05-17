@extends('admin.layout')

@section('title', 'Asset List')

@section('content')
    <div class="content">
        <h2>Asset List</h2>

        <table>
            <thead>
                <tr>
                    <th>Asset</th>
                    <th>Total Quantity</th>
                    <th>Total Value</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($assetSummary as $asset)
                    <tr>
                        <td>{{ $asset->asset }}</td>
                        <td>{{ number_format($asset->total_quantity, 4) }}</td>
                        <td>${{ number_format($asset->total_value, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">No assets found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
