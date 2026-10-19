# VANTAGE — Sports Uniform & Apparel Ecommerce + CMS

A complete, custom-built **PHP + MySQL** ecommerce storefront **and** admin CMS for a
sports uniform manufacturing company (also active wear, casual & street wear).
It is an **inquiry / quote–based** B2B store (no online payments) — customers build an
inquiry list and request a custom quote.

No frameworks, no Composer — plain PHP (PDO), ready to drop onto any PHP 7.4+ / 8.x host.

> **Two database modes, same codebase:**
> - **SQLite (default) — zero setup.** No database server needed. The database file is
>   created and seeded automatically on first page load. Perfect for testing online and
>   for free hosts without MySQL.
> - **MySQL** — for production. Flip one setting and import the SQL file.

---

## ⚡ Fastest way to run (SQLite — zero setup)

You only need PHP (7.4+). **No MySQL, no import, nothing to configure.**

```bash
cd sports-uniform-ecommerce
php -S localhost:8000
```
Open **http://localhost:8000/** — the store is live and the database seeds itself on the
first visit. CMS at **http://localhost:8000/admin** (`admin` / `admin123`).

To host it online, just upload the folder to **any PHP host** and make sure the
`database/` folder is writable. That's it. (Requires the `pdo_sqlite` PHP extension,
which is enabled by default almost everywhere.)

---

## ✨ Features

### Storefront
- **Top bar** (message, phone, email, socials) + **sticky header** with mega-menu
- **Hero slider** (auto-play, arrows, dots) managed from the CMS
- **Marquee** ticker below the slider
- **Magazine-style landing page** (editorial intro, drop-cap, stats)
- **Tall category cards** (2:3 ratio) with **vertical hollow category text**
- **Featured** & **Recent** product rows
- **Quick View** modal + **Add to Inquiry** on every product
- **Single product page** with a custom **cloud zoom**, image gallery, variations (size/color/…), tabbed description, size chart & shipping info
- **Categories page** with a **colorful vertical category menu** + filters (search, price range, featured, sort)
- **Search** in the header *and* a dedicated search band on the landing page
- **FAQs** (accordion) — teaser on home + full page
- **Parallax** section
- **Rich footer** with CTA band
- **Inquiry / quote system** — session cart → quote request saved to the database
- **Size charts** (per category, rendered as tables)
- Fully **responsive**

### Admin CMS (`/admin`)
- Secure login (`password_hash` / `password_verify`), CSRF-protected forms
- **Dashboard** with stats & recent inquiries
- Manage **Sliders**, **Categories**, **Products** (with gallery + variations), **Size Charts**, **Shipping methods**, **FAQs**
- **Quotes / Inquiries** inbox with status workflow (new → in review → quoted → won → closed)
- **Settings** (brand name, colors, top bar, marquee, contact, socials, footer, parallax toggle, currency) + change admin password
- Image **upload** *or* paste an image path for every image field

---

## 🚀 Installation

### 1. Requirements
- PHP **7.4+** (8.x recommended) with PDO MySQL
- MySQL **5.7+** / MariaDB **10.3+**
- Any web server (Apache, Nginx) **or** the built-in PHP server

Easiest local setup: **XAMPP**, **Laragon**, or **MAMP**.

### 2. Get the files
Copy this folder into your web root, e.g.
```
XAMPP:   C:/xampp/htdocs/sports-uniform-ecommerce
Laragon: C:/laragon/www/sports-uniform-ecommerce
```

> **Prefer MySQL?** Set `define('DB_DRIVER', 'mysql');` in `config/config.php`, then
> follow steps 3–4 below. Otherwise skip them — SQLite needs no setup.

### 3. Create the database (MySQL mode only)
Import `database/schema.sql` — it creates the `vantage_shop` database, all tables and demo data.
```bash
mysql -u root -p < database/schema.sql
```
Or use **phpMyAdmin → Import → choose `database/schema.sql`**.

### 4. Configure the connection (MySQL mode only)
Edit `config/config.php` if your MySQL credentials differ from the defaults
(`host 127.0.0.1`, `db vantage_shop`, `user root`, empty password):
```php
define('DB_DRIVER', 'mysql');
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'vantage_shop');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 5. Run
- **With XAMPP/Laragon:** open `http://localhost/sports-uniform-ecommerce/`
- **With the built-in server:**
  ```bash
  cd sports-uniform-ecommerce
  php -S localhost:8000
  ```
  then open `http://localhost:8000/`

### 6. Log in to the CMS
```
URL:      /admin
Username: admin
Password: admin123
```
> **Change the password immediately** in **Admin → Settings → Admin Password.**

---

## 🖼️ Images
Demo artwork is generated as lightweight SVG placeholders in `assets/images/`.
Replace them with real photos, or upload your own through the CMS (stored in
`assets/uploads/`). Make sure `assets/uploads/` is **writable** by the web server
for uploads to work; otherwise paste an image path in the "image path" field instead.

---

## 📁 Structure
```
config/           config.php (DB + base URL)
database/         schema.sql (tables + seed data)
includes/         db, functions, header, footer, product-card
admin/            the CMS (login, dashboard, CRUD modules)
assets/
  css/            style.css (store), admin.css (CMS)
  js/             main.js (slider, cloud zoom, quick view, tabs…)
  images/         slides, cats, products (SVG placeholders)
  uploads/        CMS image uploads land here
index.php         magazine landing page
category.php      catalog + vertical menu + filters
product.php       single product + cloud zoom
quickview.php     quick-view AJAX fragment
inquiry.php       inquiry cart + quote request
search.php  faq.php  size-chart.php
```

---

## 🔒 Notes for production
- Set `APP_DEBUG` to `false` in `config/config.php`.
- Change the admin password and the demo contact details in **Settings**.
- Put the site behind HTTPS; the session cookie flags can then be hardened.
- Back up the `vantage_shop` database regularly.

Built as a starter you can extend — add payments, email notifications, user
accounts, or product reviews on top of this foundation.
