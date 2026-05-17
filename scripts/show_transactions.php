<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = DB::select('SELECT id, transaction_date, transaction_time, item_name FROM transactions WHERE transaction_time IS NOT NULL ORDER BY transaction_time DESC LIMIT 10');
foreach ($rows as $r) {
    echo "{$r->id}\t{$r->transaction_date}\t{$r->transaction_time}\t{$r->item_name}\n";
}

return 0;
