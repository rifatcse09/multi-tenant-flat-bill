# Case Study: Multi-Tenant Flat & Bill Management System

## Project Overview

| Metric | Value |
|--------|-------|
| **Project** | Multi-Tenant Flat & Bill Management System |
| **Type** | SaaS, B2B |
| **Framework** | Laravel 12 |
| **Auth** | Laravel Breeze (Blade) |

## Demo Data (After Seed)

| Entity | Count |
|--------|-------|
| Users | 3 (1 Admin, 2 Owners) |
| Tenants | 5 (Alice, Bob, Charlie, Diana, Evan) |
| Buildings | 2 (1 per owner) |
| Flats | 8 (4 per building, A-1 to A-4) |
| Bill Categories | 8 (4 per owner: Electricity, Gas, Water, Service Charge) |
| Bills | ~24 (2 flats × 2 categories × 3 months × 2 owners) |
| Payments | Up to 5 (partial/full) |
| Occupancies | Tenants assigned to buildings and flats |

## Technical Scope

| Category | Count |
|----------|-------|
| Models | 8+ (User, Building, Flat, Tenant, Bill, Payment, BillCategory, BillAdjustment) |
| Controllers | 15+ (Admin + Owner) |
| Services | 10+ (BillService, PaymentService, AdjustmentService, etc.) |
| Blade Views | 40+ |
| Form Requests | 15+ |
| Database Tables | 15+ (with pivots) |

## Features

- Role-based access (Super Admin, House Owner, Tenant)
- Tenant-based data isolation (OwnerScope)
- Buildings & flats management
- Tenant-to-flat assignments (with date ranges)
- Bill categories (Electricity, Gas, Water, Service Charge)
- Monthly billing with carry-forward
- Payments with status (unpaid/partial/paid)
- Adjustments (late fees, discounts)
- Email notifications (Bill Created, Bill Paid)
- Dashboard with live stats
- Demo login (one-click for each role)

## Case Study Summary

**Challenge:** Property owners needed a single system to manage buildings, flats, tenants, and billing instead of spreadsheets.

**Solution:** Multi-tenant SaaS app with role-based access. Owners manage properties and billing; admins manage owners and tenants. Tenant-based isolation keeps data separate.

**Results:**

- One platform for multiple property owners
- Automated billing with carry-forward
- Payment tracking and status updates
- Audit trail via bills, payments, and adjustments
- Email notifications for bills and payments

**Tech:** Laravel 12, PHP, MySQL, TailwindCSS, Blade, Docker/Sail.

## Screenshots

Located in `docs/screenshots/`:

- Login Page
- Super Admin Dashboard
- Owners
- Buildings
- Tenants (House Owners)
- Bills (House Owners)
- Payments (House Owners)
- Bill Adjustments (House Owners)
