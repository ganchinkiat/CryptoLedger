<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

$argvFile = $argv[1] ?? null;
if (! $argvFile) {
    echo "Usage: php scripts/import_binance.php <xlsx-file-path>\n";
    exit(1);
}

$filePath = base_path($argvFile);
if (! file_exists($filePath)) {
    echo "File not found: {$filePath}\n";
    exit(1);
}

echo "Loading spreadsheet {$filePath}\n";
$spreadsheet = IOFactory::load($filePath);
$sheet = $spreadsheet->getActiveSheet();

// read rows, then filter data rows and reverse so top-of-sheet (newest) is inserted last
$allRows = $sheet->toArray(null, true, true, true);
$dataRows = [];
foreach ($allRows as $r => $row) {
    if ($r >= 11) {
        $dataRows[$r] = $row;
    }
}

if (! empty($dataRows)) {
    // truncate to ensure fresh import
    DB::table('transactions')->truncate();
}

$imported = 0;
$errors = [];

// process rows from bottom to top so newest (top of sheet) becomes last in DB
$rowKeys = array_reverse(array_keys($dataRows));
foreach ($rowKeys as $r) {
    $row = $dataRows[$r];
    // map columns: C=Time, E=Pair, G=Side, I=Price, K=Executed, M=Amount, O=Fee
    $time = trim((string)($row['C'] ?? ''));
    $pair = trim((string)($row['E'] ?? ''));
    $side = trim((string)($row['G'] ?? ''));
    $price = trim((string)($row['I'] ?? ''));
    $executed = trim((string)($row['K'] ?? ''));
    $amount = trim((string)($row['M'] ?? ''));
    $fee = trim((string)($row['O'] ?? ''));

    if ($time === '' && $pair === '') {
        continue;
    }

    try {
        // parse time
        try {
            $dt = Carbon::createFromFormat('y-m-d H:i:s', $time);
        } catch (Exception $e) {
            $dt = Carbon::parse($time);
        }

        // parse executed (quantity + asset)
        $execQty = 0;
        $execAsset = null;
        if (preg_match('/([0-9\.,]+)\s*([A-Za-z0-9%]*)/', $executed, $m)) {
            $execQty = (float) str_replace(',', '', $m[1]);
            $execAsset = $m[2] ?: null;
        }

        // parse amount (numeric and currency)
        $totalAmount = 0;
        $amountCurrency = null;
        if (preg_match('/([0-9\.,]+)\s*([A-Za-z0-9%]*)/', $amount, $m2)) {
            $totalAmount = (float) str_replace(',', '', $m2[1]);
            $amountCurrency = $m2[2] ?: null;
        }

        // parse fee
        $feeAmount = 0;
        $feeAsset = null;
        if (preg_match('/([0-9\.,]+)\s*([A-Za-z0-9%]*)/', $fee, $m3)) {
            $feeAmount = (float) str_replace(',', '', $m3[1]);
            $feeAsset = $m3[2] ?: null;
        }

        $priceVal = $price === '' ? 0 : (float) str_replace(',', '', $price);

        $record = [
            'pair' => $pair,
            'asset' => $execAsset,
            'side' => $side,
            'price' => $priceVal,
            'quantity' => $execQty,
            'amount' => $totalAmount,
            'currency' => $amountCurrency,
            'fee' => $feeAmount,
            'fee_currency' => $feeAsset,
            'transaction_date' => $dt->toDateTimeString(),
        ];

        Transaction::create($record);

        $imported++;
    } catch (Throwable $e) {
        $errors[] = "Row {$r}: " . $e->getMessage();
    }
}

echo "Imported: {$imported}\n";
if (! empty($errors)) {
    echo "Errors:\n" . implode("\n", $errors) . "\n";
}

return 0;
