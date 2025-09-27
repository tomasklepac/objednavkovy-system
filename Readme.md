# Objednávkový systém

Semestrální práce do předmětu KIV/WEB.

## ✅ Aktuálně hotovo
- Nastaven XAMPP (Apache + MySQL).
- Vytvořena databáze `objednavkovy_system` se 6 tabulkami (`users`, `roles`, `user_role`, `products`, `orders`, `order_item`).
- Přidána základní struktura projektu podle MVC (`app/`, `config/`, `public/`).
- Propojení s databází pomocí PDO (`config/db.php`).
- Základní login systém:
    - ověřování emailu a hesla,
    - hashování hesel pomocí `password_hash` a `password_verify`,
    - session pro uchování přihlášeného uživatele.

## 🔜 Co ještě zbývá
- Logout (odhlášení uživatele).
- Role uživatelů (customer, supplier, admin) – odlišná práva.
- Správa produktů (CRUD – přidat, upravit, smazat).
- Vytváření objednávek zákazníkem.
- Přehled objednávek pro dodavatele a admina.
- Responzivní design (Bootstrap nebo Tailwind).
- Upload obrázků k produktům.
- Dokumentace (PDF, podle zadání).

## 👤 Autor
Tomáš Klepač  
FAV ZČU, 2025
