# CryptoLedger

A Dockerized Laravel application scaffold for CryptoLedger.

## Quick start

1. Copy `.env.example` to `.env`
2. Adjust database credentials if needed
3. Run `docker-compose up --build`

## Services

- `app` - PHP 8.3 FPM
- `web` - Nginx
- `db` - MySQL 8.0
- `adminer` - Adminer database UI

## Transaction import

Upload transaction history in Excel format at the application homepage. The import expects columns in this order: `Date`, `Item`, `Type`, `Quantity`, `Unit Price`, `Total Amount`.

After import, the app calculates stock on hand and profit/loss automatically.
