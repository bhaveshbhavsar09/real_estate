# EstateFolio — Real Estate Website

A complete, ready-to-deploy real estate website: browse properties, filter by
type/price/city/bedrooms, view every listing on a Google Map, add new
property listings (with a click-to-pin map location), and collect customer
inquiries through a contact form.

## What's included

```
realestate/
├── index.html          Homepage: hero, filters, listings grid, map, contact form
├── add-property.html   "List Your Property" page with map pin-drop for lat/lng
├── style.css            All styling (single stylesheet, fully responsive)
├── uploads/              Property images uploaded via add-property.html land here
└── php/
    ├── config.php         Database connection settings — EDIT THIS FIRST
    ├── database.sql       Table schema + 8 sample listings
    ├── get_properties.php Returns listings as JSON (supports filters)
    ├── add_property.php   Handles new listing submissions + image upload
    └── contact.php         Handles the customer inquiry form
```

## 1. Requirements

- A web server with **PHP 7.4+** (Apache, Nginx, or PHP's built-in server)
- **MySQL** or **MariaDB**
- A **Google Maps JavaScript API key** (free tier is enough) — https://console.cloud.google.com/google/maps-apis

## 2. Set up the database

1. Create the database and tables by importing the schema:
   ```bash
   mysql -u root -p < php/database.sql
   ```
   This also inserts 8 sample properties so the site isn't empty.

2. Open `php/config.php` and set your credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'estatefolio');
   define('DB_USER', 'your_db_user');
   define('DB_PASS', 'your_db_password');
   ```

## 3. Add your Google Maps API key

Open both `index.html` and `add-property.html` and replace:
```js
const GOOGLE_MAPS_API_KEY = 'YOUR_GOOGLE_MAPS_API_KEY';
```
with your real key. In the Google Cloud Console, enable the **"Maps
JavaScript API"** for that key.

- On **index.html**, the map plots a pin for every property in the current
  filtered list.
- On **add-property.html**, clicking anywhere on the map drops a pin and
  fills in the Latitude/Longitude fields automatically (the marker can also
  be dragged to fine-tune the spot).

If you skip this step, both pages still work — the map area shows a message
instead, and coordinates on the "Add Property" page become manually
editable text fields.

## 4. Run it locally to test

From the `realestate/` folder:
```bash
php -S localhost:8000
```
Then open `http://localhost:8000/index.html` in your browser.

## 5. Deploy to a live server (make it available to the world)

1. Upload the entire `realestate/` folder to your web host (via FTP, cPanel
   File Manager, or `git`/`scp`), so it sits somewhere like
   `public_html/estatefolio/`.
2. Make sure `uploads/` is writable by the web server:
   ```bash
   chmod 755 uploads/
   ```
3. Create the MySQL database on your host (most shared hosts provide this
   through cPanel → MySQL Databases) and import `php/database.sql`.
4. Update `php/config.php` with the host's database credentials.
5. Point your domain at the folder, or move the contents to your web root.
6. Visit `https://yourdomain.com/` — your site is now live.

For HTTPS (recommended, and required by many browsers for some Maps
features), most hosts offer a free Let's Encrypt certificate — enable it in
your hosting control panel.

## How the pieces connect

- `index.html` loads properties by calling `php/get_properties.php` with
  `fetch()`. If that call fails (no server running yet), it falls back to
  built-in sample data so the front-end still looks complete.
- The **filter bar** re-calls `get_properties.php` with query parameters
  (`type`, `listing`, `city`, `bedrooms`, `max_price`).
- The **map** reads the same property list and drops one marker per
  listing; clicking a marker opens an info window with a "View details"
  link into the on-page modal.
- **"Add Property"** (`add-property.html`) posts a multipart form (including
  the photo) to `php/add_property.php`, which validates the data, stores
  the image in `uploads/`, and inserts a new row in the `properties` table.
- The **contact form** at the bottom of `index.html` posts JSON to
  `php/contact.php`, which validates and stores the inquiry in the
  `inquiries` table (there's a commented-out `mail()` call you can enable
  to also email your team).

## Customizing

- **Branding / colors / fonts**: all defined as CSS variables at the top of
  `style.css` under `:root`.
- **Sample data**: edit or remove the `INSERT INTO properties` block in
  `php/database.sql`, or delete rows directly once you have real listings.
- **Currency**: prices are stored as plain decimals; the `₹` symbol and
  formatting live in the `fmtPrice()` JS function in `index.html`.