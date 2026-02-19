# Discount BG

Discount BG is a full-stack Bulgarian e-commerce aggregation platform that centralizes discounted products from multiple local online stores into one clean shopping experience.

It combines:
- Automated product ingestion from external spiders
- Category normalization across different source websites
- Real-time catalog updates with product/variant lifecycle cleanup
- Customer storefront (search, filters, cart, favourites, checkout)
- Back-office operations panel for order and catalog management

The crawling microservice lives here: [Discount-bg-spiders](https://github.com/Radinski341/Discount-bg-spiders)

## Why This Project Is Strong

- Real business logic, not a demo CRUD app
- Multi-source product consolidation with category/subcategory mapping
- Variant support (`Product` + `ProductChoice`) and dynamic option URLs
- Admin ingestion workflow with processing logs and cleanup stages
- Role-based admin operations (`ROLE_ADMIN`, `ROLE_HEAD_ADMIN`, `ROLE_OWNER`)
- Stripe payment integration
- S3-backed ingestion file storage via Flysystem

## Tech Stack

- Backend: PHP 8.2, Symfony 7, Doctrine ORM, EasyAdmin, Twig
- Database: PostgreSQL
- Frontend: Webpack Encore, Stimulus, Bootstrap, JS controllers
- Storage: AWS S3 (via Flysystem)
- Payments: Stripe
- Email: Symfony Mailer (SendGrid DSN supported)
- Infra: Docker (PHP-FPM + Nginx), optional native run

## Architecture Overview

### 1. Ingestion Path

1. External spider service collects product JSON from Bulgarian stores.
2. JSON files are uploaded in Admin -> `Data Upload` (stored in S3 under `data/{website}/`).
3. Processing pipeline runs in sequence:
   - `process-categories`: normalize and create categories/subcategories
   - `process-data`: create/update products and collect variant rows
   - `process-choices`: create/update product variants (`ProductChoice`)
   - `delete-missing-products-and-choices`: remove stale products and variants

### 2. Catalog Domain

- `Product`: canonical item in Discount BG catalog
- `ProductChoice`: variant/option entries (size, color, etc.)
- `Category`, `SubCategory`, `MainCategory`: storefront taxonomy
- `BaseCategory`, `BaseSubcategory`: mapping layers to unify external website taxonomies
- `Website`, `WebsiteDeliveryRole`: source metadata and delivery pricing rules

### 3. Customer Flow

- Browse all products or by main/category/subcategory routes
- Search with transliteration support for Bulgarian/Latin queries
- Anonymous + authenticated cart and favourites (session merge on login)
- Stripe checkout -> order creation with order transactions snapshot

### 4. Admin Flow

- EasyAdmin dashboard for catalog and config entities
- Dedicated order operations views by status
- Per-order and per-item status transitions with audit-like descriptions
- Data upload and ingestion processing UI with progress logs

## Key Features

- Advanced filtering: price range, sorting, per-page pagination
- Search preview endpoint for responsive UX
- Per-source delivery cost logic and free-delivery thresholds
- Product lifecycle strategy (`forDelete` mark-and-sweep)
- Homepage visual merchandising with banners and carousels

## Run Locally

Two practical options are supported.

## Option A: Docker (recommended for parity)

This repo includes PHP-FPM + Nginx containers. PostgreSQL is expected as a separate container/service.

### 1) Prerequisites

- Docker + Docker Compose
- Node.js 18+ (or compatible LTS)
- Composer

### 2) Clone and install dependencies

```bash
git clone <your-fork-or-this-repo-url>
cd discount-bg
composer install
npm install
```

### 3) Configure environment

Create or edit `.env.local`:

```env
APP_ENV=dev
APP_SECRET=change_me
DATABASE_URL="postgresql://<user>:<password>@<host>:5432/<db>?sslmode=disable"

STRIPE_SECRET_KEY=sk_test_xxx
STRIPE_PUBLIC_KEY=pk_test_xxx

MAILER_DSN=null://null

AWS_ACCESS_KEY_ID=xxx
AWS_SECRET_ACCESS_KEY=xxx
AWS_REGION=eu-north-1
AWS_BUCKET_NAME=your-bucket-name

MERCURE_PUBLIC_URL=http://localhost/.well-known/mercure
```

Important:
- Do not commit real secrets to Git.
- If any keys were exposed previously, rotate them before public deployment.

### 4) Start PostgreSQL

Example:

```bash
docker run --name discount-bg-postgres \
  -e POSTGRES_USER=discount \
  -e POSTGRES_PASSWORD=discount \
  -e POSTGRES_DB=discount_bg \
  -p 5432:5432 -d postgres:14
```

### 5) Start app containers

```bash
docker compose up -d --build
```

App will be available at:
- `http://localhost:8080`

### 6) Database setup

```bash
docker compose exec php php bin/console doctrine:migrations:migrate -n
docker compose exec php php bin/console doctrine:fixtures:load -n
```

### 7) Build frontend assets

```bash
npm run dev
```

For active frontend development:

```bash
npm run watch
```

## Option B: Native (without Docker)

### 1) Prerequisites

- PHP 8.2+
- Composer
- PostgreSQL 14+
- Node.js 18+

### 2) Install dependencies

```bash
composer install
npm install
```

### 3) Configure `.env.local`

Use the same environment variables as Option A.

### 4) Prepare DB + assets

```bash
php bin/console doctrine:migrations:migrate -n
php bin/console doctrine:fixtures:load -n
npm run dev
```

### 5) Start Symfony server

```bash
symfony server:start
```

If Symfony CLI is not installed:

```bash
php -S 127.0.0.1:8000 -t public
```

## Default Fixture Users

After loading fixtures, these users are created:

- `radinskikire@gmail.com` -> `ROLE_OWNER`
- `headadmin@gmail.com` -> `ROLE_HEAD_ADMIN`
- `admin@gmail.com` -> `ROLE_ADMIN`

Default password: `Apokalipsa2`

## How To Process Crawled Data

1. Login as owner/admin.
2. Open `/admin`.
3. Go to `Data Upload`.
4. Upload spider-generated JSON files for a website.
5. Click `Process {website}` to run the full ingestion pipeline.

Expected row fields for product ingestion include:

- `is-product-choice`
- `website`
- `website-url`
- `website-id`
- `product-url`
- `title`
- `old-price`
- `new-price`
- `discount-percent`
- `images`
- `categories`
- `description-html`

## Notes

- The Docker Compose file does not include a database container by default.
- S3 storage is required for ingestion files in the current implementation.
- Stripe keys are needed only if you want full checkout flow testing.

---

Built for high-volume discounted product aggregation in the Bulgarian market, with a practical split between ingestion automation and commerce operations.
