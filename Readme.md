# Objednávkový systém

Semestrální práce do předmětu **KIV/WEB**.

## ✅ Aktuálně hotovo
- Nastaven XAMPP (Apache + MySQL).
- Vytvořena databáze `objednavkovy_system` se 6 tabulkami:
    - `users`, `roles`, `user_role`, `products`, `orders`, `order_item`.
- Základní struktura projektu podle MVC (`app/`, `config/`, `public/`).
- Propojení s databází pomocí PDO (`config/db.php`).
- Login / Logout systém:
    - ověřování emailu a hesla,
    - hashování hesel pomocí `password_hash` a `password_verify`,
    - session pro uchování přihlášeného uživatele.
- Registrace nových uživatelů:
    - **customer** = účet aktivní hned,
    - **supplier** = čeká na schválení administrátorem.
- Role uživatelů (`customer`, `supplier`, `admin`) – ukládají se do session.
- Admin sekce: přehled uživatelů, možnost schválit nebo blokovat účet.

## 🔜 Co ještě zbývá
- CRUD pro produkty:
    - dodavatel může přidávat, upravovat a mazat své produkty,
    - zákazník produkty pouze prohlíží.
- Objednávky:
    - zákazník může vytvořit objednávku,
    - dodavatel vidí objednávky svých produktů,
    - admin má přehled o všech objednávkách.
- Upload obrázků k produktům.
- Responzivní design (Bootstrap nebo Tailwind).
- Dokumentace (PDF podle zadání).

## 👤 Autor
**Tomáš Klepač**  
FAV ZČU, 2025
