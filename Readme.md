# Order Management System (KIV/WEB semester project)

## Overview
- PHP 8 without a framework, MVC + PSR-4 autoloading (`app/autoload.php`).
- MySQL/MariaDB via PDO (`config/Database.php`).
- Frontend: Bootstrap 5 (CDN), Font Awesome icons, custom theme in `public/css/app.css`.
- No AJAX/Twig: classic PHP templates in `app/Views`, minimal JS (hamburger/sidebar) + Bootstrap bundle.

## Features
- Authentication: registration, login, logout; passwords hashed (`password_hash`).
- Roles:
  - Admin: user management, all orders/products overview, order status changes.
  - Supplier: manage own products, see orders containing their products.
  - Customer: cart, create orders, see own orders.
- Products: add/edit, archive/reactivate, image upload, table overviews.
- Cart and orders: cart stored in session, create order with items and prices, statuses pending/confirmed/shipped/delivered/canceled; cancel returns stock.
- Security: prepared statements, escaped output (`htmlspecialchars`), CSRF tokens in forms.

## API (REST-like)
- Entrypoint: `public/api.php?action=...`
  - `action=products` (GET) — product list; filter `supplier_id`; detail via `id`.
  - `action=orders` (GET) — requires login; returns orders by role (admin all, supplier own items, customer own orders).
  - `action=orders&id=XYZ` (GET) — order detail; same role restrictions; JSON `success`/`order` or `error` with HTTP 401/403/404/500.
- Responses are JSON. No POST/PUT/DELETE (read-only, not full CRUD REST).

## Structure (main folders/files)
```
app/
  Controllers/ (ProductController, OrderController, ApiController, UserController, ...)
  Models/ (ProductModel, OrderModel, UserModel, ...)
  Views/ (partials/header.php, footer.php + view files for dashboard, cart, orders...)
  autoload.php
config/Database.php
public/
  index.php
  api.php
  routers/ (admin_router.php, auth_router.php, cart_router.php, order_router.php, ...)
  css/app.css
  uploads/ (product images)
sql/objednavkovy_system.sql
```

## Install & run (local)
1) Clone the repo into your webroot (e.g. `htdocs/objednavkovy-system`).  
2) Create DB and import `sql/objednavkovy_system.sql`.  
3) Set DB creds in `config/Database.php` (host, dbname, user, pass).  
4) Open `http://localhost/objednavkovy-system/public/index.php`.  
5) API smoke test: `http://localhost/objednavkovy-system/public/api.php?action=products` (products), `...?action=orders` (after login).

## Test account
- Customer: `customer@local.test` / `Customer123!`

## Deployment
- PHP/MySQL app — GitHub Pages cannot run PHP (static only). For production use PHP hosting or your own server (Apache/Nginx+PHP+MySQL). GitHub can serve as remote repo/deploy scripts, but the app itself must run on a PHP backend.

## Author
Tomas Klepac, FAV ZCU — KIV/WEB
