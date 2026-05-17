<?php

namespace App\Services;

use App\Models\Transaction;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

class TransactionImportService
{
    public function importFile(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $imported = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            if ($index === 1) {
                continue;
            }

            if ($this->isEmptyRow($row)) {
                continue;
            }

            try {
                $data = $this->normalizeRow($row);

                Transaction::create($data);
                $imported++;
            } catch (\Throwable $e) {
                $errors[] = "Row {$index}: {$e->getMessage()}";
            }
        }

        return [
            'imported' => $imported,
            'errors' => $errors,
        ];
    }

    protected function isEmptyRow(array $row): bool
    {
        return empty(array_filter($row, fn ($value) => $value !== null && trim((string)$value) !== ''));
    }

    protected function normalizeRow(array $row): array
    {
        $date = trim((string) ($row['A'] ?? ''));
        $item = trim((string) ($row['B'] ?? ''));
        $type = strtolower(trim((string) ($row['C'] ?? '')));
        $quantity = trim((string) ($row['D'] ?? ''));
        $unitPrice = trim((string) ($row['E'] ?? ''));
        $totalAmount = trim((string) ($row['F'] ?? ''));

        if ($date === '' || $item === '' || $type === '' || $quantity === '') {
            throw new \RuntimeException('Required transaction columns are missing.');
        }

        $transactionType = $this->normalizeType($type);
        $quantity = $this->parseDecimal($quantity);
        $unitPrice = $unitPrice !== '' ? $this->parseDecimal($unitPrice) : 0;
        $totalAmount = $totalAmount !== '' ? $this->parseDecimal($totalAmount) : $quantity * $unitPrice;

        return [
            'transaction_date' => $this->parseDate($date),
            'item_name' => $item,
            'transaction_type' => $transactionType,
            'quantity' => $transactionType === 'sale' ? abs($quantity) : abs($quantity),
            'unit_price' => $unitPrice,
            'total_amount' => $totalAmount,
        ];
    }

    protected function normalizeType(string $type): string
    {
        if (in_array($type, ['sale', 'sold', 'out', 'sell'], true)) {
            return 'sale';
        }

        if (in_array($type, ['purchase', 'buy', 'in', 'received'], true)) {
            return 'purchase';
        }

        throw new \RuntimeException('Unknown transaction type: ' . $type);
    }

    protected function parseDecimal(string $value): float
    {
        $value = str_replace([',', '$', '€', '£'], ['', '', '', ''], $value);

        if (!is_numeric($value)) {
            throw new \RuntimeException('Invalid numeric value: ' . $value);
        }

        return (float) $value;
    }

    protected function parseDate(string $value): string
    {
        if (is_numeric($value)) {
            $timestamp = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp((float) $value);
            return Carbon::createFromTimestamp($timestamp)->toDateString();
        }

        return Carbon::parse($value)->toDateString();
    }
}
