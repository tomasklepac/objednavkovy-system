# Objednávkový systém (KIV/WEB – semestrální projekt)

## Popis
Webová aplikace pro správu objednávek a produktů s podporou více rolí uživatelů.  
Projekt je vyvinut v **PHP** bez použití frameworku, s využitím **MVC architektury** a **OOP přístupu**.  
Databáze je řešena pomocí **MySQL/MariaDB** a připojení přes **PDO**.  
Design aplikace využívá **Bootstrap**.

---
Login
<img width="512" height="580" alt="Snímek obrazovky 2025-10-12 214047" src="https://github.com/user-attachments/assets/7eac6606-cf8b-4515-8e6c-8fdddf48b8c3" />

Dashboard
<img width="1903" height="1054" alt="Snímek obrazovky 2025-10-12 211901" src="https://github.com/user-attachments/assets/d740c801-fab1-4b93-9fa0-7124272e33dc" />

Košík
<img width="1447" height="544" alt="Snímek obrazovky 2025-10-12 213459" src="https://github.com/user-attachments/assets/722ab1fe-fc72-4d18-aa32-6f9d405731b5" />

Objednávky
<img width="1436" height="598" alt="Snímek obrazovky 2025-10-12 211910" src="https://github.com/user-attachments/assets/aa6e53e0-3c83-4c11-9d98-272be435bbfc" />

Správa uživatelů
<img width="1453" height="593" alt="Snímek obrazovky 2025-10-12 213546" src="https://github.com/user-attachments/assets/000ad41d-35f5-49ec-9e26-53310385caac" />


## Funkcionalita

- **Autentizace uživatelů**
    - Registrace, přihlášení, odhlášení
    - Hesla ukládána bezpečně (bcrypt hash)

- **Role**
    - **Admin** – správa uživatelů (nelze blokovat adminy), přehled všech objednávek a produktů
    - **Dodavatel** – správa vlastních produktů a objednávek
    - **Zákazník** – vytváření objednávek, správa košíku

- **Správa produktů**
    - Přidávání, editace a mazání produktů
    - Náhled produktů včetně detailu

- **Objednávky**
- Vytváření objednávek z košíku
    - Přehled objednávek podle role

- **Bezpečnostní opatření**
    - Prepared statements (PDO) proti SQL Injection
    - Ošetření výstupů (htmlspecialchars) proti XSS
    - Session pro správu přihlášení

---

## Struktura projektu
```
objednavkovy-system/
├── app
│   ├── Controllers
│   │   ├── order_controller.php
│   │   ├── product_controller.php
│   │   └── user_controller.php
│   ├── Models
│   │   ├── product_model.php
│   │   └── user_model.php
│   └── Views
│       ├── partials
│       │   ├── footer.php
│       │   └── header.php
│       ├── add_product_view.php
│       ├── cart_view.php
│       ├── confirm_order_view.php
│       ├── dashboard_view.php
│       ├── edit_product_view.php
│       ├── login_view.php
│       ├── my_products_view.php
│       ├── order_detail_view.php
│       ├── orders_view.php
│       ├── products_view.php
│       ├── register_view.php
│       ├── supplier_order_detail_view.php
│       ├── supplier_orders_view.php
│       └── users_view.php
├── config
│   └── db.php
├── public
│   ├── css
│   │   └── app.css
│   ├── routers
│   │   ├── admin_router.php
│   │   ├── auth_router.php
│   │   ├── cart_router.php
│   │   ├── dashboard_router.php
│   │   ├── order_router.php
│   │   └── product_router.php
│   ├── uploads
│   │   ├── prod_68e00fcb62d193.29855128.jpg
│   │   ├── prod_68e011c80f0430.53612348.jpg
│   │   ├── prod_68e01155a59415.52305916.jpg
│   │   ├── prod_68e0114125b083.52865953.jpg
│   │   └── prod_68e0303668b371.91767251.jpg
│   ├── index.php
├── sql
│   └── objednavkovy_system.sql
├── dokumentace.pdf
└── Readme.md
```

---

## Instalace a spuštění

1. Nakopírujte projekt do root složky serveru (např. `htdocs/` pro XAMPP).
2. Naimportujte databázi z `sql/objednavkov_system.sql`.
3. Upravte přístupové údaje k databázi v `config/db.php`.
4. Spusťte projekt přes `http://localhost/objednavkovy-system/public/index.php`.

---

## Testovací účty

- **Admin**
    - Email: `admin@local.test`
    - Heslo: `Admin123!`

- **Dodavatel**
    - Email: `supplier@local.test`
    - Heslo: `Supplier123!`

- **Zákazník**
    - Email: `customer@local.test`
    - Heslo: `Customer123!`

---

## Autor
Tomáš Klepač, FAV ZČU – KIV/WEB
