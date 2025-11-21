# REST API Dokumentace

## Přehled

REST API vrací JSON data místo HTML. Umožňuje programům (JavaScriptu, mobilním aplikacím atd.) přístup k datům aplikace.

## Base URL
```
http://localhost/objednavkovy-system/public/api.php
```

## Dostupné Endpointy

### 1. Všechny produkty
**Cesta:** `?action=products`
**Metoda:** GET
**Vrací:** JSON array všech produktů

**Příklad:**
```
http://localhost/objednavkovy-system/public/api.php?action=products
```

**Odpověď:**
```json
{
  "success": true,
  "count": 5,
  "products": [
    {
      "id": 1,
      "name": "Jablko",
      "price": "19.90",
      "stock": 10,
      "image": "uploads/prod_123.jpg"
    },
    {
      "id": 2,
      "name": "Hrušky",
      "price": "24.90",
      "stock": 5,
      "image": "uploads/prod_456.jpg"
    }
  ]
}
```

### 2. Jeden konkrétní produkt
**Cesta:** `?action=products&id=ID`
**Metoda:** GET
**Vrací:** JSON object jednoho produktu

**Příklad:**
```
http://localhost/objednavkovy-system/public/api.php?action=products&id=1
```

**Odpověď:**
```json
{
  "success": true,
  "product": {
    "id": 1,
    "name": "Jablko",
    "description": "Sladké červené jablko z farmy",
    "price": "19.90",
    "stock": 10,
    "image": "uploads/prod_123.jpg"
  }
}
```

### 3. Filtrování produktů po ceně
**Cesta:** `?action=products&price_min=X&price_max=Y`
**Metoda:** GET
**Vrací:** JSON array produktů v cenovém rozmezí

**Příklad:**
```
http://localhost/objednavkovy-system/public/api.php?action=products&price_min=10&price_max=50
```

**Odpověď:**
```json
{
  "success": true,
  "count": 3,
  "products": [
    {
      "id": 1,
      "name": "Jablko",
      "price": "19.90"
    },
    {
      "id": 2,
      "name": "Hrušky",
      "price": "24.90"
    }
  ]
}
```

### 4. Všechny objednávky (vyžaduje přihlášení)
**Cesta:** `?action=orders`
**Metoda:** GET
**Vyžaduje:** Přihlášený uživatel (session)
**Vrací:** JSON array objednávek (obsah závisí na roli)

**Příklad:**
```
http://localhost/objednavkovy-system/public/api.php?action=orders
```

**Odpověď (admin vidí všechny):**
```json
{
  "success": true,
  "count": 3,
  "orders": [
    {
      "id": 1,
      "user_id": 5,
      "total_price": "64.70",
      "status": "confirmed"
    }
  ]
}
```

**Chyba (není přihlášen):**
```json
{
  "success": false,
  "error": "Unauthorized: Please log in"
}
```

### 5. Detail jedné objednávky (vyžaduje přihlášení)
**Cesta:** `?action=orders&id=ID`
**Metoda:** GET
**Vyžaduje:** Přihlášený uživatel (session)
**Vrací:** JSON object s detailem objednávky

**Příklad:**
```
http://localhost/objednavkovy-system/public/api.php?action=orders&id=1
```

## HTTP Status Kódy

| Kód | Chyba | Případ |
|-----|-------|--------|
| 200 | OK | Úspěšný request |
| 400 | Bad Request | Chybný action |
| 401 | Unauthorized | Není přihlášen (pro orders) |
| 404 | Not Found | Produkt/objednávka neexistuje |
| 500 | Server Error | Chyba na serveru |

## Implementace

**Soubory:**
- `public/api.php` - Entry point, router
- `app/Controllers/ApiController.php` - Logika, vrací JSON

**Klíčové funkce:**
- `ApiController::getAllProducts()` - Vrátí všechny produkty
- `ApiController::getProductById(int $id)` - Vrátí jeden produkt
- `ApiController::getAllOrders()` - Vrátí objednávky podle role
- `ApiController::getOrderById(int $id)` - Vrátí detail objednávky
- `jsonResponse(array $data)` - Pomocná funkce pro vrácení JSON
- `jsonError(string $message)` - Pomocná funkce pro vrácení chyby

## Použití s JavaScriptem (AJAX)

```javascript
// Načíst všechny produkty
fetch('/objednavkovy-system/public/api.php?action=products')
    .then(response => response.json())
    .then(data => {
        console.log('Produkty:', data.products);
        // Aktualizuj UI bez refreshu stránky
    });

// Filtrovat produkty po ceně
fetch('/objednavkovy-system/public/api.php?action=products&price_max=50')
    .then(response => response.json())
    .then(data => {
        console.log('Lacné produkty:', data.products);
    });
```

## Bodování

- REST API (volitelné technologie): **2 body**
  - Kategorie: "z části (např. jedna URL a POST pro všechno)"
  - Implementováno: GET endpointy pro products a orders
  - Vrací: JSON data v správném formátu

---

**Aktuální stav:** 52/60 bodů (86%)
