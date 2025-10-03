**MVC v kostce**
- **Controllers**: zpracují request, sáhnou do modelu a vyberou správné view
- **Models**: komunikace s databází přes PDO (CRUD operace pro uživatele, produkty, objednávky)
- **Views**: HTML šablony s Bootstrap 5 + vlastní CSS (`app.css`), oddělené do `partials/header.php` a `partials/footer.php`

---

## Funkce

- **Autentizace**: registrace (s rolí zákazník / dodavatel), přihlášení, odhlášení
- **Role**:
    - **Admin** – správa uživatelů (nelze blokovat adminy), správa produktů, přehled a správa všech objednávek
    - **Dodavatel** – vlastní produkty (CRUD), přehled objednávek obsahujících jeho položky
    - **Zákazník** – prohlížení produktů, přidávání do košíku, potvrzení objednávky a sledování stavu objednávek
- **Produkty**: přidání, úprava, smazání, možnost nahrát obrázek produktu (volitelné)
- **Košík**: přidání/odebrání položek, změna množství, zobrazení celkové ceny
- **Objednávky**: vytvoření z košíku, změna stavů (pending → confirmed → shipped → delivered / canceled), admin potvrzuje objednávky zákazníků
- **Bezpečnost**: PDO (ochrana proti SQL injection), `htmlspecialchars` (ochrana proti XSS), hesla ukládána přes `password_hash()` (bcrypt)
- **Responzivní UI**: Bootstrap 5 + minimalistické vlastní CSS (`public/css/app.css`)

---

## Důležité URL / akce

- `index.php?action=login` – přihlášení
- `index.php?action=register` – registrace (zákazník/dodavatel)
- `index.php?action=users` – správa uživatelů (admin)
- `index.php?action=products` – seznam všech produktů
- `index.php?action=my_products` – produkty aktuálního dodavatele
- `index.php?action=add_product` / `edit_product&id=...` / `delete_product&id=...` – správa produktů
- `index.php?action=view_cart` – košík
- `index.php?action=confirm_order` – potvrzení objednávky
- `index.php?action=orders` – seznam objednávek (role záleží na uživateli)
- `index.php?action=supplier_orders` / `supplier_order_detail&id=...` – objednávky mých položek (dodavatel)

> **Poznámka**: Header a footer jsou řešené jako sdílené partialy. Na stránce má být vždy jen jedno view, jinak se footer zobrazí dvakrát.

---

## Nahrávání obrázků

- Obrázky jsou volitelné při přidávání / úpravě produktu
- Formuláře `add_product` / `edit_product` používají `enctype="multipart/form-data"`
- Obrázky se ukládají do složky `public/uploads/` (musí být zapisovatelná)
- Do databáze se ukládá relativní cesta `uploads/<soubor>`
- Povolené typy: `image/jpeg`, `image/png`, `image/webp`
- Limit velikosti: **2 MB** (lze změnit v `product_controller::handleImageUpload()`)

```php
// product_controller.php (výřez)
private function handleImageUpload(array $file): ?string {
    if ($file['error'] === UPLOAD_ERR_NO_FILE) return null;
    if ($file['size'] > 2 * 1024 * 1024) throw new RuntimeException("Soubor je příliš velký (max 2 MB).");
    // ...
    return 'uploads/' . $filename;
}
