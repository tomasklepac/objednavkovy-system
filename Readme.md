# Objednavkovy system (KIV/WEB semestralni projekt)

## Prehled
- PHP 8 bez frameworku, MVC + PSR-4 autoloading (viz `app/autoload.php`).
- Databaze MySQL/MariaDB pres PDO (`config/Database.php`).
- Frontend: Bootstrap 5 (CDN), Font Awesome ikony, vlastni tema v `public/css/app.css`.
- Bez AJAXu/Twig: klasicke PHP sablony v `app/Views`, minimum JS (hamburger/sidebar) + Bootstrap bundle.

## Funkcionalita
- Autentizace: registrace, prihlaseni, odhlaseni; hesla hashovana (password_hash).
- Role:
  - Admin: sprava uzivatelu, prehled vsech objednavek a produktu, zmena stavu objednavek.
  - Dodavatel: sprava vlastnich produktu, prehled objednavek obsahujicich jeho zbozi.
  - Zakaznik: kosik, vytvoreni objednavky, prehled vlastnich objednavek.
- Produkty: pridani/editace, archivace/reaktivace, upload obrazku, tabulkove prehledy.
- Kosik a objednavky: kosik v session, vytvoreni objednavky s polozkami a cenami, stavy pending/confirmed/shipped/delivered/canceled; storno vraci zasoby.
- Bezpecnost: prepared statements, htmlspecialchars na vystupu, CSRF tokeny ve formularech.

## API (REST-like)
- Entrypoint: `public/api.php?action=...`
  - `action=products` (GET) — seznam produktu; filtr `supplier_id`; detail pres `id`.
  - `action=orders` (GET) — vyzaduje login; vraci objednavky podle role (admin vse, dodavatel svoje polozky, zakaznik svoje objednavky).
  - `action=orders&id=XYZ` (GET) — detail objednavky; role omezeni stejne jako vyse, vraci JSON `success`, `order` nebo `error` s HTTP 401/403/404/500.
- Odpovedi JSON, zadne POST/PUT/DELETE (neni full CRUD REST, jen cteni).

## Struktura (hlavni slozky/soubory)
```
app/
  Controllers/ (ProductController, OrderController, ApiController, UserController, ...)
  Models/ (ProductModel, OrderModel, UserModel, ...)
  Views/ (partials/header.php, footer.php + view soubory pro dashboard, kosik, objednavky...)
  autoload.php
config/Database.php
public/
  index.php
  api.php
  routers/ (admin_router.php, auth_router.php, cart_router.php, order_router.php, ...)
  css/app.css
  uploads/ (obrazky produktu)
sql/objednavkovy_system.sql
```

## Instalace a spusteni (lokalne)
1) Naklonuj repozitar do webrootu (napr. `htdocs/objednavkovy-system`).  
2) Vytvor DB a naimportuj `sql/objednavkovy_system.sql`.  
3) Nastav DB pristupy v `config/Database.php` (host, dbname, user, pass).  
4) Spust `http://localhost/objednavkovy-system/public/index.php`.  
5) API test: `http://localhost/objednavkovy-system/public/api.php?action=products` (produkty), `...?action=orders` (po prihlaseni).

## Testovaci ucty
- Admin: `admin@local.test` / `Admin123!`
- Dodavatel: `supplier@local.test` / `Supplier123!`
- Zakaznik: `customer@local.test` / `Customer123!`

## Nasazeni
- Aplikace je PHP/MySQL, GitHub Pages neumi PHP spoustet (jen staticky obsah). Pro ostry provoz je potreba PHP hosting nebo vlastni server (Apache/Nginx+PHP+MySQL).  
- GitHub muzes pouzit jako git remote a pro deployment skripty, ale beh samotne aplikace na Pages nejde bez dalsiho backendu.

## Autor
Tomas Klepac, FAV ZCU — KIV/WEB
