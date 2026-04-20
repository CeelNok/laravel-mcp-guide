# 01 – Az alap Laravel projekt felépítése

> Ebben a részben felépítjük azt a Laravel webshop alapot, amelyre később az MCP szervert ráépítjük.

---

## Miről szól ez a rész?

Mielőtt MCP szervert építünk, szükségünk van egy működő Laravel projektre, amellyel dolgozhatunk. Ez a rész egy egyszerű webshop adatbázis struktúráját mutatja be:

- **Termékek** – névvel, árral, készlettel, státusszal
- **Vásárlók** – kapcsolattartási adatokkal
- **Rendelések** – vásárlóhoz kötve, tételekkel

Ez nem egy teljes webshop – nincsenek kontrollerek, nézetek vagy API végpontok. Csak az adatbázis réteg, amelyre az MCP tool-ok épülnek majd.

> ⚠️ **Fontos:** Ez csak egy sablon, nem egy kötelezően követendő struktúra. Ha neked már van egy meglévő Laravel projekted – más táblanevekkel, más modellekkel, más kapcsolatokkal – akkor **a saját projektedhez igazítva** használd ezt az útmutatót. Az MCP integráció szempontjából mindegy, hogy webshopról, blog rendszerről vagy bármi másról van szó. A lényeg a koncepció, nem a konkrét adatbázis struktúra.

---

## Előfeltételek

- PHP 8.2+
- Composer
- Laravel 11 projekt (`composer create-project laravel/laravel webshop`)
- Konfigurált adatbázis (`.env` fájlban beállított `DB_*` értékek)
- **Alap Laravel ismeret** – érteni kell a migrations, modellek és Eloquent kapcsolatok fogalmát. Ha ezek még újak, először ajánlott a [Laravel hivatalos dokumentációját](https://laravel.com/docs) átolvasni.

---

## A fájlok struktúrája

```
├── app/
│   └── Models/
│       ├── Customer.php
│       ├── Order.php
│       ├── OrderItem.php
│       └── Product.php
├── database/
│   ├── factories/
│   │   ├── CustomerFactory.php
│   │   ├── OrderFactory.php
│   │   ├── OrderItemFactory.php
│   │   └── ProductFactory.php
│   ├── migrations/
│   │   ├── 1_create_products_table.php
│   │   ├── 2_create_customers_table.php
│   │   ├── 3_create_orders_table.php
│   │   └── 4_create_order_items_table.php
│   └── seeders/
│       ├── CustomerSeeder.php
│       ├── DatabaseSeeder.php
│       ├── OrderSeeder.php
│       └── ProductSeeder.php
```

---

## Migrations

A migrációk definiálják az adatbázis táblastruktúráját. Laravel ezeket sorban futtatja le, és nyilvántartja melyik futott már le. A fájlnevekben lévő sorszám határozza meg a futtatási sorrendet – ez fontos, mert az `orders` tábla csak akkor jöhet létre, ha a `customers` már létezik (idegen kulcs miatt).

### `1_create_products_table.php`

Létrehozza a `products` táblát az alábbi mezőkkel:

| Mező | Típus | Leírás |
|------|-------|--------|
| `id` | bigint | Automatikus elsődleges kulcs |
| `name` | string | A termék neve |
| `slug` | string, unique | URL-barát azonosító (pl. `piros-cipo`) |
| `description` | text | Hosszabb leírás |
| `price` | decimal(10,2) | Ár, két tizedesjeggyel |
| `stock` | integer | Készleten lévő darabszám |
| `status` | enum | Csak `active`, `inactive` vagy `draft` lehet |
| `timestamps` | - | `created_at` és `updated_at` automatikusan |

Az `enum` típus azért hasznos, mert adatbázis szinten garantálja, hogy érvénytelen státusz nem kerülhet be – nem kell ezt külön validálni a kódban.

### `2_create_customers_table.php`

Létrehozza a `customers` táblát az alábbi mezőkkel:

| Mező | Típus | Leírás |
|------|-------|--------|
| `id` | bigint | Automatikus elsődleges kulcs |
| `name` | string | Teljes név |
| `email` | string, unique | Email cím – egyedi, kétszer nem szerepelhet |
| `phone` | string, nullable | Telefonszám – nem kötelező |
| `address` | text, nullable | Cím – nem kötelező |
| `timestamps` | - | `created_at` és `updated_at` automatikusan |

### `3_create_orders_table.php`

Létrehozza az `orders` táblát az alábbi mezőkkel:

| Mező | Típus | Leírás |
|------|-------|--------|
| `id` | bigint | Automatikus elsődleges kulcs |
| `customer_id` | bigint, foreign | Melyik vásárlóhoz tartozik |
| `status` | enum | `pending`, `processing`, `completed`, `cancelled` |
| `total_price` | decimal(10,2) | A rendelés végösszege |
| `notes` | text, nullable | Megjegyzés a rendeléshez |
| `timestamps` | - | `created_at` és `updated_at` automatikusan |

A `customer_id` idegen kulcs `cascadeOnDelete`-tel van beállítva – ha egy vásárlót törlünk, az összes rendelése automatikusan törlődik vele együtt.

### `4_create_order_items_table.php`

Létrehozza az `order_items` táblát (a rendelés sorait). Egy rendeléshez több tétel is tartozhat – például: 2 db piros cipő + 1 db fekete öv.

| Mező | Típus | Leírás |
|------|-------|--------|
| `id` | bigint | Automatikus elsődleges kulcs |
| `order_id` | bigint, foreign | Melyik rendeléshez tartozik |
| `product_id` | bigint, foreign | Melyik termékről van szó |
| `quantity` | integer | Darabszám |
| `unit_price` | decimal(10,2) | Az egységár a rendelés pillanatában |
| `timestamps` | - | `created_at` és `updated_at` automatikusan |

Két fontos különbség a kapcsolatoknál:
- `order_id` → `cascadeOnDelete`: ha a rendelést töröljük, a tételek is törlődnek
- `product_id` → `restrictOnDelete`: nem törölhetünk olyan terméket, amelyhez még van rendelési sor – ez védi az adatok integritását

---

## Models

A modellek az adatbázis táblákat reprezentálják PHP osztályként. Ezeken keresztül olvassuk és írjuk az adatokat, és itt vannak definiálva a kapcsolatok (hogy melyik tábla melyikhez kapcsolódik).

### `Product.php`

A `products` tábla modellje.

```php
// Automatikus típuskonverzió
protected $casts = [
    'price' => 'decimal:2',
    'stock' => 'integer',
    'status' => ProductStatus::class, // enum cast
];

// Egy termékhez sok rendelési sor tartozhat
public function orderItems(): HasMany
{
    return $this->hasMany(OrderItem::class);
}
```

A `$casts` gondoskodik arról, hogy a `price` mező ne stringként érkezzen vissza az adatbázisból, hanem valódi számként. Ez különösen fontos ha számítást végzünk az árakkal.

### `Customer.php`

A `customers` tábla modellje.

```php
// Egy vásárlóhoz sok rendelés tartozhat
public function orders(): HasMany
{
    return $this->hasMany(Order::class);
}
```

### `Order.php`

Az `orders` tábla modellje. Két kapcsolata van más táblák felé:

```php
// A rendelés gazdája
public function customer(): BelongsTo
{
    return $this->belongsTo(Customer::class);
}

// A rendelés tételei
public function orderItems(): HasMany
{
    return $this->hasMany(OrderItem::class);
}
```

### `OrderItem.php`

Az `order_items` tábla modellje. Egy rendelési sor két táblához is kapcsolódik:

```php
// Melyik rendeléshez tartozik ez a sor
public function order(): BelongsTo
{
    return $this->belongsTo(Order::class);
}

// Melyik termékről van szó
public function product(): BelongsTo
{
    return $this->belongsTo(Product::class);
}
```

Az `unit_price` mezőt azért tároljuk el a rendelési sorban (és nem csak a termék aktuális árát használjuk), mert a termék ára változhat – de a rendelés pillanatában érvényes árat meg kell őrizni.

---

## Factories

A factory-k fake (teszt) adatokat generálnak fejlesztés és seedelés során. A `fake()` helper véletlenszerű, de valósnak tűnő adatokat ad – neveket, emaileket, számokat.

### `ProductFactory.php`

Véletlenszerű termékadatokat generál. Három state metódust tartalmaz, amelyekkel célzottan adott státuszú termékeket lehet létrehozni:

```php
// Ezeket a seeder használja a pontos arány eléréséhez
Product::factory()->active()->count(8)->create();
Product::factory()->inactive()->count(6)->create();
Product::factory()->draft()->count(6)->create();
```

### `CustomerFactory.php`

Véletlenszerű vevőadatokat generál. Nem minden mezőt tölt ki minden esetben – ez realisztikusabbá teszi az adatokat:
- 75% eséllyel lesz telefonszám
- 80% eséllyel lesz cím

### `OrderFactory.php`

Rendeléseket generál. A `total_price` alapból `0`-val jön létre, mert a seeder utólag számítja ki a valós összeget a tételekből – így mindig konzisztens lesz az adat.

### `OrderItemFactory.php`

Rendelési sorokat generál. Elsősorban unit teszteléshez hasznos – a seeder nem ezt használja közvetlenül, hanem manuálisan hozza létre a tételeket.

---

## Seeders

A seederek töltik fel az adatbázist tesztadatokkal. A sorrendjük fontos: nem lehet rendelést létrehozni vásárló nélkül, és rendelési sort sem termék nélkül.

### `DatabaseSeeder.php`

A belépési pont – ez hívja meg a többi seedert a helyes sorrendben:

```php
$this->call([
    ProductSeeder::class,  // 1. először termékek
    CustomerSeeder::class, // 2. majd vásárlók
    OrderSeeder::class,    // 3. végül rendelések (mert hivatkoznak az előző kettőre)
]);
```

### `ProductSeeder.php`

20 terméket hoz létre a factory state metódusaival:
- 8 db `active` (aktív, látható termék)
- 6 db `inactive` (inaktív, nem látható)
- 6 db `draft` (vázlat, még nem publikált)

### `CustomerSeeder.php`

50 vevőt hoz létre a `CustomerFactory` segítségével.

### `OrderSeeder.php`

Ez a legösszetettebb seeder. 100 rendelést hoz létre az alábbi logikával:

1. Minden rendeléshez véletlenszerűen kiválaszt egy meglévő vásárlót
2. Létrehoz 1–5 rendelési tételt, minden tételhez egy véletlenszerű terméket
3. Az `unit_price`-t a termék aktuális árából veszi
4. A rendelés végén összeadja az összes tétel `mennyiség × egységár` értékét, és beírja a `total_price` mezőbe

Ez biztosítja, hogy a `total_price` mindig konzisztens legyen a tényleges tételekkel.

---

## Mi jön következőnek?

A következő részben erre az alapra építjük rá az MCP szervert:

- Route-ok regisztrálása
- CSRF kivétel beállítása
- `McpServer.php` – a szerver belépési pontja
- Az első Tool-ok: termékek, rendelések, vásárlók kezelése

→ [02 – MCP szerver integrálása](../02-mcp-integracio/)