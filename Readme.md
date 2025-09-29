# Objednávkový systém

Semestrální práce do předmětu **KIV/WEB**.

## ✅ Aktuálně hotovo
- ✅ Nastaven XAMPP (Apache + MySQL).
- ✅ Vytvořena databáze `objednavkovy_system` se 6 tabulkami (`users`, `roles`, `user_role`, `products`, `orders`, `order_item`).
- ✅ Přidána základní struktura projektu podle MVC (`app/`, `config/`, `public/`).
- ✅ Propojení s databází pomocí PDO (`config/db.php`).
- ✅ Login / Logout systém:
    - ověřování emailu a hesla,
    - hashování hesel (`password_hash`, `password_verify`),
    - session pro uchování přihlášeného uživatele.
- ✅ Registrace s výběrem role (`customer` = aktivní ihned, `supplier` = čeká na schválení adminem).
- ✅ Role uživatelů (customer, supplier, admin).
- ✅ Admin panel – přehled uživatelů, schvalování a blokace účtů.
- ✅ Produkty (CRUD):
    - přidání produktu,
    - výpis všech produktů,
    - editace produktu,
    - mazání produktu (jen vlastník nebo admin).

## 🔜 Co ještě zbývá
- Objednávky:
    - zákazník může vytvářet objednávky,
    - dodavatel / admin vidí přehled objednávek.
- Upload obrázků k produktům.
- Responzivní design (Bootstrap nebo Tailwind).
- Dokumentace (PDF, podle zadání).

## 👤 Autor
Tomáš Klepač  
FAV ZČU, 2025
