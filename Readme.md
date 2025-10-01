# Objednávkový systém (KIV/WEB – semestrální projekt)

## Popis
Webová aplikace pro správu objednávek a produktů s podporou více rolí uživatelů (admin, dodavatel, zákazník).  
Projekt je vytvořen v PHP (bez frameworku), s využitím **MVC architektury** a **OOP přístupu**.  
Data jsou ukládána do databáze MySQL/MariaDB přes PDO.

---

## Funkcionalita

### Autentizace a role
- Registrace a přihlášení uživatelů (hesla hashovaná pomocí `password_hash`/`bcrypt`).
- Role:
    - **Admin** – spravuje všechny objednávky, vidí všechno.
    - **Dodavatel** – spravuje své produkty, vidí objednávky obsahující jeho zboží.
    - **Zákazník** – prohlíží produkty, vkládá do košíku a vytváří objednávky.
- Logout (správně ukončuje session).

### Produkty
- Dodavatel i admin mohou přidávat/mazat/upravovat produkty.
- Každý produkt má:
    - název,
    - popis,
    - cenu,
    - **počet kusů skladem**,
    - dodavatele (vlastník produktu).
- **Kontrola skladu**:
    - uživatel nemůže přidat do košíku vyprodaný produkt,
    - nelze přidat více kusů, než je skladem,
    - dodavatel může editovat počet kusů skladem.
- V seznamu produktů se zobrazuje, kolik kusů je skladem.

### Košík
- Jen pro přihlášené uživatele.
- Přidávání/odebírání produktů v rámci omezení skladu.
- Vytvoření objednávky z obsahu košíku.

### Objednávky
- Uživatelé (zákazníci) vytvářejí objednávky z košíku.
- Admin vidí a spravuje všechny objednávky (změna stavu).
- Dodavatel vidí pouze objednávky, kde se nachází jeho produkty.

---

## Technické požadavky
Projekt splňuje požadavky semestrální práce:
- **HTML5, CSS, PHP, SQL (PDO)**.
- **MVC architektura** a **OOP** (modely, controllery).
- Uživatelské role a autentizace.
- Ošetření proti **SQL injection** a **XSS**.
- **Responzivní design** (základní CSS, detailní design se bude dopracovávat).
- Připraveno na **upload obrázků** k produktům.

---

## Instalace
1. Naklonujte projekt:
   ```bash
   git clone https://github.com/USERNAME/objednavkovy-system.git
