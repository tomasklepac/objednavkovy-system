# Objednávkový systém (KIV/WEB – semestrální projekt)

## Popis
Webová aplikace pro správu objednávek a produktů s podporou více rolí uživatelů (**admin, dodavatel, zákazník**).  
Projekt je napsán v čistém **PHP 8.2+**, bez frameworku, s využitím principů **MVC architektury** a **OOP přístupu**.  
Data jsou ukládána do databáze **MySQL/MariaDB** pomocí PDO.

---

## Funkcionalita

### Autentizace a role
- **Registrace a přihlášení uživatelů**
    - hesla jsou ukládána hashovaně pomocí `password_hash` (bcrypt),
    - zákazník se aktivuje okamžitě, dodavatel čeká na schválení adminem.
- **Role uživatelů**:
    - **Admin** – spravuje uživatele, vidí všechny produkty a objednávky, může měnit stavy objednávek.
    - **Dodavatel** – spravuje své produkty, vidí objednávky obsahující jeho položky.
    - **Zákazník** – prohlíží produkty, spravuje košík a vytváří objednávky.
- **Logout** – kompletně ukončí session.

### Produkty
- Dodavatel i admin mohou **přidávat, mazat a upravovat** produkty.
- Každý produkt obsahuje:
    - název,
    - popis,
    - cenu (v centech, zobrazovanou v Kč),
    - počet kusů skladem,
    - vlastníka (dodavatele).
- **Validace skladu**:
    - zákazník nemůže přidat do košíku vyprodaný produkt,
    - nelze přidat více kusů, než je dostupné,
    - dodavatel může kdykoliv upravit počet kusů skladem.
- Zobrazení produktů:
    - všichni přihlášení vidí seznam produktů,
    - admin/dodavatel vidí tlačítka pro úpravu/smazání,
    - zákazník tlačítko „Přidat do košíku“.

### Košík
- Přístupný pouze přihlášeným uživatelům.
- Funkce:
    - přidání produktu do košíku,
    - změna množství (zvýšení/snížení),
    - odebrání celého produktu z košíku,
    - výpočet celkové ceny.
- Po potvrzení → přesměrování na formulář s adresou a vytvoření objednávky.

### Objednávky
- **Zákazník**:
    - vytváří objednávky z košíku,
    - vidí seznam svých objednávek a jejich stav,
    - detail objednávky se seznamem položek.
- **Admin**:
    - vidí všechny objednávky,
    - potvrzuje objednávky (odečtení kusů ze skladu),
    - mění stav (`pending → confirmed → shipped → delivered`),
    - může objednávku zrušit.
- **Dodavatel**:
    - vidí jen objednávky obsahující jeho produkty,
    - v detailu objednávky vidí pouze své položky + zákazníka.

---

## Architektura projektu

- **index.php** – hlavní router, který přesměrovává na dílčí routery podle `?action=...`.
- **Routers** (adresář `public/routers/`):
    - `auth_router.php` – login, logout, registrace,
    - `admin_router.php` – správa uživatelů,
    - `product_router.php` – CRUD operace s produkty,
    - `cart_router.php` – logika košíku,
    - `order_router.php` – tvorba a správa objednávek,
    - `dashboard_router.php` – úvodní přehled a produkty.
- **Controllers** – business logika (`user_controller`, `product_controller`, `order_controller`).
- **Models** – komunikace s DB (`user_model`, `product_model`).
- **Views** – šablony pro jednotlivé stránky (login, registrace, produkty, košík, objednávky, dashboard, správa uživatelů atd.).
- **config/db.php** – připojení k databázi (Singleton, PDO).

---

## Požadavky
- PHP **8.2+**
- MySQL / MariaDB
- Webserver (např. Apache přes XAMPP / LAMP)
- Povolené sessions

---

## Spuštění projektu
1. Naklonuj repozitář:
   ```bash
   git clone <repo_url>
   ```
2. Vytvoř databázi `objednavkovy_system` a importuj schéma (soubor `schema.sql`).
3. Nastav připojení v `config/db.php` podle svého prostředí (host, user, heslo).
4. Spusť projekt přes webserver (např. `http://localhost/objednavkovy-system/public`).
5. Registruj uživatele a přihlaš se.

---

## Další kroky
- Přidat **design/šablonu** (CSS, responzivita).
- Vylepšit UX (bootstrap, validace formulářů, chybové hlášky).
- Přidat upload obrázků k produktům.
- Logování akcí (např. změny stavu objednávek).
- Možnost exportu objednávek.
