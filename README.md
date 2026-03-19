# Multi-Tenant Flat & Bill Management System

A Laravel 12 application for **building/flat management** with **multi-tenant billing**.  
Supports **Super Admin → House Owners → Tenants** roles, flat assignments, bill generation, payments, and due/adjustment tracking.

---

## Business Goals

1. **Streamline property billing** — Replace manual spreadsheets and paper records with a single platform for building owners to manage flats, tenants, and bills.
2. **Reduce payment delays** — Clear visibility of dues, carry-forward, and payment history helps owners chase payments and tenants stay informed.
3. **Scale for multiple owners** — Multi-tenant SaaS model allows one platform to serve many property owners, each with isolated data.
4. **Improve audit & compliance** — Ledger-style records (bills, payments, adjustments) provide a traceable history for accounting and disputes.
5. **Save time on repetitive tasks** — Automated bill generation, carry-forward, and email notifications cut down manual data entry and follow-ups.
6. **Enable future growth** — Tenant portal, exports (CSV/PDF), and APIs set the foundation for mobile apps and integrations.

---

## Features

- **Super Admin**
  - Manage House Owners
  - Manage Buildings
  - Manage Tenants
  - Assign Tenants to Buildings

- **House Owner**
  - Manage Buildings & Flats
  - Assign Tenants Flats
  - Manage Bill Categories (Electricity, Gas, Water, etc.)
  - Generate Flat-wise Bills (per Category, per Month)
  - Track Dues & Carry Forward
  - Add **Manual Adjustments** (late fees, discounts, waivers)
  - Record **Payments**
  - Email notifications for Bill Created / Bill Paid

- **Tenant**
  - (Future scope) login and view bills/payments

---

## Setup Instructions

### 1. Clone & Install
```bash
git clone https://github.com/rifatcse09/multi-tenant-flat-bill
cd multi-tenant-flat-bill
composer install
npm install && npm run dev
```

### 2. Environment
```bash
cp .env.example .env
php artisan key:generate
```
Update `.env` for DB, mail, and subdomain if needed.

### 3. Database
```bash
php artisan migrate --seed
```
Seeds include:
- Super Admin user
- Sample House Owner & Tenant

### 4. Run locally
```bash
php artisan serve
```
- Open: http://localhost:8000

### 4b. Run with Laravel Sail (Docker)
This project includes [Laravel Sail](https://laravel.com/docs/sail) for a Docker-based development environment.

**First-time setup:**
```bash
composer install
cp .env.example .env
php artisan key:generate
```

**Configure `.env` for Sail** (MySQL, Redis, MailHog use Docker service hostnames):
```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password

REDIS_HOST=redis

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
```

**Optional:** Add Sail user/group IDs (Linux) to avoid permission issues:
```env
WWWUSER=1000
WWWGROUP=1000
APP_PORT=80
```

**Start Sail:**
```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
```

- App: http://localhost (or http://localhost:80)
- MailHog: http://localhost:8025

**Useful Sail commands:**
```bash
./vendor/bin/sail up -d          # Start containers (detached)
./vendor/bin/sail down           # Stop containers
./vendor/bin/sail artisan ...    # Run Artisan commands
./vendor/bin/sail composer ...   # Run Composer
./vendor/bin/sail npm run dev     # Build frontend assets
```

### 5. Subdomain (if enabled)
This project supports **subdomain-based tenant isolation** (optional).  
- Set `SESSION_DOMAIN=.local.test` in `.env`
- Add `/etc/hosts` entries like:
  ```
  127.0.0.1 admin.local.test
  127.0.0.1 owner1.local.test
  ```
- Access via http://admin.local.test:8000

> For the assessment, **column-based tenant scoping** (`owner_id` on models) is used by default. Subdomain routing is optional.

> We used Gates and Global Scopes for role + tenant isolation. Policies could be introduced later if we need per-model granular checks.

---

## Multi-Tenant Implementation

- **Column-based tenant isolation**  
  Every resource (`buildings`, `flats`, `bill_categories`, `bills`) has an `owner_id`.  
  Global scopes (`OwnerScope`) ensure owners only see their own data.

- **Super Admin** is scoped separately and can manage all house owners/buildings/tenants.

- **Pivot tables** (`building_tenant`, `flat_tenant`) manage assignments:
  - Admin approves tenants for buildings.
  - Owners assign tenants to specific flats with start/end dates (no overlaps).

- **Carry Forward vs Adjustments**
  - Carry Forward: auto-applied when generating new bills if previous bills unpaid.
  - Adjustments: manual interventions (late fees, discounts, corrections).

---

## Optimization & Query Notes

- **Eager Loading** (`with()`) used to avoid N+1 queries (e.g., bills with flat, category, tenant).
- **Database Indexes**:
  - Unique `(owner_id, flat_id, bill_category_id, month)` on `bills` to prevent duplicate bills.
  - Indexes on `flat_id, tenant_id` in pivot tables for fast joins.
- **Accessors** on `Bill` model (`gross`, `paid`, `due`) centralize financial calculations.
- **Soft Deletes**: Used for major entities (Users, Buildings, Flats, Bills) to preserve audit history.
- **Services Layer** (`BillService`, `PaymentService`, `AdjustmentService`) keeps controllers thin and reusable.
- **Validation** via FormRequests ensures tenant assignment doesn’t overlap and prevents overpayments.

---

## Design Decisions

1. **Multi-Tenant Isolation**
   - Chose **column-based** isolation (via `owner_id`) for simplicity and quick local testing.
   - Subdomain routing supported but optional.

2. **Ledger-based Billing**
   - Bills, Payments, and Adjustments are append-only (audit-friendly).
   - Dues are always recalculated, not overwritten.

3. **Extensible**
   - Easy to extend with APIs (e.g., expose bills/payments to tenants).
   - Notifications abstracted, so can swap mail for SMS later.

4. **UI**
   - Blade + TailwindCSS for lightweight, assessment-friendly frontend.

---

## Development Notes

- Built with **Laravel Breeze (Blade)** auth scaffold.
- Queue-ready (for sending bill/payment emails asynchronously).
- Modular service layer for testability.
- Resource controllers with `only([...])` where partial CRUD is required.

---

## Credentials (for testing)

After `php artisan migrate --seed` (or `sail artisan migrate --seed`):

| Role | Email | Password |
|------|-------|----------|
| **Super Admin** | admin@example.com | password |
| **House Owner 1** | owner1@example.com | password |
| **House Owner 2** | owner2@example.com | password |

The login page shows **Demo Login** buttons — click any role to auto-fill and sign in. Seed data includes buildings, flats, tenants, bills, and sample payments for demos.

---

## Next Steps / Improvements

- Dashboard (Total Building, Flats, Unpaid Bills, Payment This Month)
- Tenant portal (login & view bills)
- Export bills/payments (CSV, PDF)
- Role-based API (for mobile app integration)
- Real-time notifications (Websockets)

## Screenshots

### Login Page
![Login Screenshot](docs/screenshots/login.png)

### Super Admin Dashboard
![Super Admin Dashboard Screenshot](docs/screenshots/super_admin_dashboard.png)

### Owers
![Super Admin Dashboard Screenshot](docs/screenshots/owers.png)

### Buildings
![Super Admin Dashboard Screenshot](docs/screenshots/super_admin_buildings.png)

### Tenants House Owners
![Tenants House Owners](docs/screenshots/super_admin_tenants.png)

### Bills House Owners
![Bills House Owners Screenshot](docs/screenshots/bills.png)

### Payment House Owners
![Payment House Owners Screenshot](docs/screenshots/payments.png)

### Bill Adjustment House Owners
![Bill Adjustment House Owners Screenshot](docs/screenshots/adjustments.png)