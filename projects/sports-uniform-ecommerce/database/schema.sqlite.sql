-- ============================================================
--  VANTAGE Apparel — SQLite schema (zero-setup mode)
--  Auto-loaded on first run when DB_DRIVER = 'sqlite'.
--  Mirrors database/schema.sql (MySQL) with the same seed data.
-- ============================================================
PRAGMA foreign_keys = ON;

-- ---- Admin users ----  (admin / admin123)
CREATE TABLE admin_users (
  id            INTEGER PRIMARY KEY AUTOINCREMENT,
  name          TEXT NOT NULL,
  username      TEXT NOT NULL UNIQUE,
  password_hash TEXT NOT NULL,
  created_at    TEXT NOT NULL DEFAULT (datetime('now'))
);
INSERT INTO admin_users (name, username, password_hash) VALUES
('Store Administrator', 'admin', '$2y$10$VSe/8Fg2Zo65xQ1sGkaQI.OntLLilEQXKgst5h3ITdA9VXifV/O.G');

-- ---- Settings ----
CREATE TABLE settings (
  id     INTEGER PRIMARY KEY AUTOINCREMENT,
  skey   TEXT NOT NULL UNIQUE,
  svalue TEXT
);
INSERT INTO settings (skey, svalue) VALUES
('site_name',        'VANTAGE'),
('site_tagline',     'Custom Sports Uniforms & Apparel Manufacturing'),
('logo_text',        'VANTAGE'),
('topbar_text',      'Trusted by 500+ teams & brands worldwide — Low minimums, factory-direct pricing.'),
('topbar_phone',     '+1 (800) 555-0147'),
('topbar_email',     'sales@vantage-apparel.com'),
('marquee_text',     'FREE DESIGN MOCKUPS ★ 100% CUSTOM SUBLIMATION ★ TEAM DISCOUNTS ★ WORLDWIDE SHIPPING ★ LOW MINIMUM ORDER ★ FACTORY-DIRECT PRICING'),
('contact_address',  '240 Industrial Way, Unit 12, Portland, OR 97201, USA'),
('contact_phone',    '+1 (800) 555-0147'),
('contact_email',    'sales@vantage-apparel.com'),
('contact_whatsapp', '+1 (800) 555-0147'),
('social_facebook',  '#'),
('social_instagram', '#'),
('social_linkedin',  '#'),
('social_youtube',   '#'),
('footer_about',     'VANTAGE is a vertically-integrated apparel manufacturer producing custom sports uniforms, active wear, casual and street wear for teams, clubs and brands. Ethical factory, fast turnaround, no compromise on quality.'),
('currency_symbol',  '$'),
('enable_parallax',  '1'),
('primary_color',    '#e11d48'),
('accent_color',     '#0f172a');

-- ---- Sliders ----
CREATE TABLE sliders (
  id          INTEGER PRIMARY KEY AUTOINCREMENT,
  title       TEXT NOT NULL,
  subtitle    TEXT,
  image       TEXT NOT NULL,
  button_text TEXT,
  button_link TEXT,
  sort_order  INTEGER NOT NULL DEFAULT 0,
  is_active   INTEGER NOT NULL DEFAULT 1
);
INSERT INTO sliders (title, subtitle, image, button_text, button_link, sort_order, is_active) VALUES
('Built For The Game.',   'Pro-grade custom sports uniforms, engineered for performance and made to order.', 'assets/images/slides/slide-1.svg', 'Explore Uniforms', 'category.php?cat=sports-uniforms', 1, 1),
('Own The Street.',       'Premium street & casual wear, manufactured to your brand specs.',                  'assets/images/slides/slide-2.svg', 'Shop Street Wear', 'category.php?cat=street-wear', 2, 1),
('Train Without Limits.', 'Moisture-wicking active wear built for every rep, every mile.',                     'assets/images/slides/slide-3.svg', 'See Active Wear', 'category.php?cat=active-wear', 3, 1);

-- ---- Categories ----
CREATE TABLE categories (
  id          INTEGER PRIMARY KEY AUTOINCREMENT,
  name        TEXT NOT NULL,
  slug        TEXT NOT NULL UNIQUE,
  tagline     TEXT,
  description TEXT,
  image       TEXT,
  color       TEXT NOT NULL DEFAULT '#e11d48',
  sort_order  INTEGER NOT NULL DEFAULT 0,
  is_active   INTEGER NOT NULL DEFAULT 1
);
INSERT INTO categories (name, slug, tagline, description, image, color, sort_order, is_active) VALUES
('Sports Uniforms', 'sports-uniforms', 'Team kits engineered to win', 'Fully custom sublimated kits for football, basketball, cricket, baseball, rugby and more.', 'assets/images/cats/cat-sports.svg',  '#e11d48', 1, 1),
('Active Wear',     'active-wear',     'Move. Sweat. Repeat.',        'Performance tees, leggings, shorts and training tops with moisture-wicking fabric.',       'assets/images/cats/cat-active.svg',  '#0891b2', 2, 1),
('Casual Wear',     'casual-wear',     'Everyday comfort, your brand','Soft hoodies, tees, polos and joggers for lifestyle and corporate lines.',                  'assets/images/cats/cat-casual.svg',  '#ca8a04', 3, 1),
('Street Wear',     'street-wear',     'Bold fits for the culture',   'Oversized tees, cargo pants, windbreakers and statement pieces built to turn heads.',      'assets/images/cats/cat-street.svg',  '#db2777', 4, 1),
('Training Gear',   'training-gear',   'Kit out the whole squad',     'Bibs, base layers, tracksuits and warm-ups for teams and academies.',                      'assets/images/cats/cat-training.svg','#7c3aed', 5, 1),
('Accessories',     'accessories',     'Finish the look',             'Caps, socks, bags and headwear to complete your lineup.',                                  'assets/images/cats/cat-access.svg',  '#16a34a', 6, 1);

-- ---- Products ----
CREATE TABLE products (
  id          INTEGER PRIMARY KEY AUTOINCREMENT,
  name        TEXT NOT NULL,
  slug        TEXT NOT NULL UNIQUE,
  category_id INTEGER REFERENCES categories(id) ON DELETE SET NULL,
  sku         TEXT,
  short_desc  TEXT,
  description TEXT,
  main_image  TEXT,
  price_from  REAL NOT NULL DEFAULT 0,
  min_order   INTEGER NOT NULL DEFAULT 10,
  fabric      TEXT,
  is_featured INTEGER NOT NULL DEFAULT 0,
  is_active   INTEGER NOT NULL DEFAULT 1,
  created_at  TEXT NOT NULL DEFAULT (datetime('now'))
);
INSERT INTO products (name, slug, category_id, sku, short_desc, description, main_image, price_from, min_order, fabric, is_featured, is_active) VALUES
('Pro Sublimated Football Kit', 'pro-sublimated-football-kit', 1, 'VN-FB-001', 'Full sublimation, breathable mesh, unlimited design.', 'Our flagship football kit built from 150gsm interlock polyester with full-color sublimation that will never crack or fade. Ergonomic cut, breathable mesh panels and a reinforced collar. Includes jersey + shorts; socks optional.', 'assets/images/products/p-football.svg', 24.00, 10, '150gsm Interlock Polyester', 1, 1),
('Elite Basketball Jersey Set', 'elite-basketball-jersey-set', 1, 'VN-BB-002', 'Reversible option, quick-dry, pro fit.', 'Lightweight quick-dry basketball jersey and shorts with tackle-twill or sublimated numbers. Available reversible. Loose athletic fit for full range of motion.', 'assets/images/products/p-basketball.svg', 28.00, 10, '140gsm Micro-Mesh', 1, 1),
('Cricket Team Whites & Colours', 'cricket-team-kit', 1, 'VN-CR-003', 'ODI & test styles, UV-protect fabric.', 'Professional cricket uniforms in classic whites or full-color ODI styling. UV-protective, moisture-wicking fabric keeps players cool through long innings.', 'assets/images/products/p-cricket.svg', 26.00, 10, '160gsm Honeycomb Poly', 0, 1),
('Baseball Full Button Jersey', 'baseball-full-button-jersey', 1, 'VN-BS-004', 'Authentic cut, twill or sublimated.', 'Full-button baseball jersey with authentic raglan sleeves, available in tackle-twill lettering or full sublimation. Matching pants and caps available.', 'assets/images/products/p-baseball.svg', 30.00, 10, '180gsm Double-Knit', 0, 1),
('Pro Compression Base Layer', 'pro-compression-base-layer', 2, 'VN-AW-005', 'Second-skin fit, 4-way stretch.', 'Muscle-supporting compression top with 4-way stretch and flatlock seams to prevent chafing. Ideal as a base layer under any kit.', 'assets/images/products/p-compression.svg', 16.00, 15, '88% Poly / 12% Spandex', 1, 1),
('Performance Running Tee', 'performance-running-tee', 2, 'VN-AW-006', 'Ultralight, anti-odor, quick-dry.', 'Ultralight performance tee with laser-cut ventilation and an anti-odor finish. Weighs under 100g. Your logo, your colors.', 'assets/images/products/p-runtee.svg', 12.00, 20, '120gsm Recycled Poly', 0, 1),
('Sculpt High-Waist Leggings', 'sculpt-high-waist-leggings', 2, 'VN-AW-007', 'Squat-proof, hidden pocket.', 'Squat-proof high-waist leggings with a buttery-soft hand feel, hidden waistband pocket and contour seaming. A best-seller for studios and gyms.', 'assets/images/products/p-leggings.svg', 18.00, 15, 'Nylon / Spandex Blend', 1, 1),
('Heavyweight Pullover Hoodie', 'heavyweight-pullover-hoodie', 3, 'VN-CW-008', '380gsm brushed fleece, premium feel.', 'Premium 380gsm brushed-fleece hoodie with double-layer hood, kangaroo pocket and ribbed cuffs. Perfect blank for embroidery or print.', 'assets/images/products/p-hoodie.svg', 22.00, 25, '380gsm Cotton Fleece', 1, 1),
('Classic Pique Polo', 'classic-pique-polo', 3, 'VN-CW-009', 'Corporate-ready, color-fast.', 'Breathable pique polo with a clean three-button placket, perfect for teams, staff and corporate lines. Embroidery or print.', 'assets/images/products/p-polo.svg', 14.00, 25, '200gsm Cotton Pique', 0, 1),
('Oversized Boxy Street Tee', 'oversized-boxy-street-tee', 4, 'VN-SW-010', 'Drop shoulder, heavyweight cotton.', 'Heavyweight 240gsm boxy tee with a drop-shoulder silhouette — the blank every streetwear label wants. Garment-dyed options available.', 'assets/images/products/p-streettee.svg', 15.00, 25, '240gsm Combed Cotton', 1, 1),
('Tech Cargo Pants', 'tech-cargo-pants', 4, 'VN-SW-011', 'Utility pockets, tapered fit.', 'Tapered tech cargo pants with bellowed pockets, adjustable hem cords and a water-repellent finish. A statement bottom for any drop.', 'assets/images/products/p-cargo.svg', 27.00, 20, 'Nylon Ripstop', 0, 1),
('Coaches Tracksuit Set', 'coaches-tracksuit-set', 5, 'VN-TG-012', 'Jacket + pant, team-branded.', 'Full tracksuit with zip jacket and tapered pant, fully customizable with team crests, sponsor logos and piping. Warm, light and sharp on the touchline.', 'assets/images/products/p-tracksuit.svg', 34.00, 10, '240gsm Poly Tricot', 1, 1),
('Training Bib Set (Pack of 10)', 'training-bib-set', 5, 'VN-TG-013', 'Color-coded, numbered.', 'Lightweight mesh training bibs in color-coded sets, optionally numbered. Breathable and quick to wash and dry.', 'assets/images/products/p-bibs.svg', 6.00, 10, 'Polyester Mesh', 0, 1),
('Structured 6-Panel Cap', 'structured-6-panel-cap', 6, 'VN-AC-014', 'Embroidered, adjustable.', 'Classic structured 6-panel cap with embroidered front logo and an adjustable strap. Finish off any uniform or lifestyle line.', 'assets/images/products/p-cap.svg', 9.00, 25, 'Cotton Twill', 0, 1),
('Performance Crew Socks (3-Pack)', 'performance-crew-socks', 6, 'VN-AC-015', 'Cushioned, arch support.', 'Cushioned performance crew socks with arch compression and a seamless toe. Knit-in logos and stripes available.', 'assets/images/products/p-socks.svg', 7.00, 30, 'Combed Cotton Blend', 0, 1);

-- ---- Product images ----
CREATE TABLE product_images (
  id         INTEGER PRIMARY KEY AUTOINCREMENT,
  product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
  image      TEXT NOT NULL,
  sort_order INTEGER NOT NULL DEFAULT 0
);
INSERT INTO product_images (product_id, image, sort_order) VALUES
(1, 'assets/images/products/p-football.svg', 1),
(1, 'assets/images/products/p-basketball.svg', 2),
(1, 'assets/images/products/p-cricket.svg', 3),
(2, 'assets/images/products/p-basketball.svg', 1),
(2, 'assets/images/products/p-compression.svg', 2),
(8, 'assets/images/products/p-hoodie.svg', 1),
(8, 'assets/images/products/p-polo.svg', 2),
(10, 'assets/images/products/p-streettee.svg', 1),
(10, 'assets/images/products/p-cargo.svg', 2);

-- ---- Product variations ----
CREATE TABLE product_variations (
  id         INTEGER PRIMARY KEY AUTOINCREMENT,
  product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
  attribute  TEXT NOT NULL,
  value      TEXT NOT NULL,
  extra_cost REAL NOT NULL DEFAULT 0,
  sort_order INTEGER NOT NULL DEFAULT 0
);
INSERT INTO product_variations (product_id, attribute, value, extra_cost, sort_order) VALUES
(1,'Size','YS',0,1),(1,'Size','YM',0,2),(1,'Size','S',0,3),(1,'Size','M',0,4),(1,'Size','L',0,5),(1,'Size','XL',1.50,6),(1,'Size','2XL',3.00,7),
(1,'Color','Royal Blue',0,1),(1,'Color','Crimson',0,2),(1,'Color','Black / Gold',0,3),
(2,'Size','S',0,1),(2,'Size','M',0,2),(2,'Size','L',0,3),(2,'Size','XL',1.50,4),(2,'Size','2XL',3.00,5),
(2,'Style','Single Layer',0,1),(2,'Style','Reversible',4.00,2),
(5,'Size','S',0,1),(5,'Size','M',0,2),(5,'Size','L',0,3),(5,'Size','XL',1.00,4),
(8,'Size','S',0,1),(8,'Size','M',0,2),(8,'Size','L',0,3),(8,'Size','XL',2.00,4),(8,'Size','2XL',4.00,5),
(8,'Color','Heather Grey',0,1),(8,'Color','Black',0,2),(8,'Color','Forest',0,3),
(10,'Size','S',0,1),(10,'Size','M',0,2),(10,'Size','L',0,3),(10,'Size','XL',2.00,4),(10,'Size','2XL',4.00,5);

-- ---- Size charts ----
CREATE TABLE size_charts (
  id          INTEGER PRIMARY KEY AUTOINCREMENT,
  name        TEXT NOT NULL,
  category_id INTEGER REFERENCES categories(id) ON DELETE SET NULL,
  unit        TEXT NOT NULL DEFAULT 'inches',
  content     TEXT,
  is_active   INTEGER NOT NULL DEFAULT 1
);
INSERT INTO size_charts (name, category_id, unit, content, is_active) VALUES
('Adult Tops — Standard Fit', 1, 'inches',
'<table><thead><tr><th>Size</th><th>Chest</th><th>Length</th><th>Sleeve</th></tr></thead><tbody><tr><td>S</td><td>36-38</td><td>28</td><td>8.5</td></tr><tr><td>M</td><td>39-41</td><td>29</td><td>9</td></tr><tr><td>L</td><td>42-44</td><td>30</td><td>9.5</td></tr><tr><td>XL</td><td>45-47</td><td>31</td><td>10</td></tr><tr><td>2XL</td><td>48-50</td><td>32</td><td>10.5</td></tr><tr><td>3XL</td><td>51-53</td><td>33</td><td>11</td></tr></tbody></table>', 1),
('Youth Tops', 1, 'inches',
'<table><thead><tr><th>Size</th><th>Chest</th><th>Length</th></tr></thead><tbody><tr><td>YS</td><td>26-28</td><td>20</td></tr><tr><td>YM</td><td>28-30</td><td>22</td></tr><tr><td>YL</td><td>30-32</td><td>24</td></tr></tbody></table>', 1),
('Bottoms — Pants & Joggers', 3, 'inches',
'<table><thead><tr><th>Size</th><th>Waist</th><th>Inseam</th><th>Hip</th></tr></thead><tbody><tr><td>S</td><td>28-30</td><td>30</td><td>36</td></tr><tr><td>M</td><td>31-33</td><td>31</td><td>39</td></tr><tr><td>L</td><td>34-36</td><td>32</td><td>42</td></tr><tr><td>XL</td><td>37-39</td><td>32</td><td>45</td></tr><tr><td>2XL</td><td>40-42</td><td>33</td><td>48</td></tr></tbody></table>', 1);

-- ---- Shipping ----
CREATE TABLE shipping_methods (
  id            INTEGER PRIMARY KEY AUTOINCREMENT,
  name          TEXT NOT NULL,
  region        TEXT NOT NULL,
  rate          REAL NOT NULL DEFAULT 0,
  free_over     REAL,
  delivery_time TEXT,
  is_active     INTEGER NOT NULL DEFAULT 1
);
INSERT INTO shipping_methods (name, region, rate, free_over, delivery_time, is_active) VALUES
('Standard Freight',  'North America', 35.00, 1500.00, '7-10 business days', 1),
('Express Air',       'North America', 90.00, NULL,    '3-5 business days',  1),
('International Sea', 'Worldwide',    120.00, 3000.00, '25-35 business days',1),
('International Air', 'Worldwide',    240.00, NULL,    '6-9 business days',  1);

-- ---- Quotes ----
CREATE TABLE quotes (
  id         INTEGER PRIMARY KEY AUTOINCREMENT,
  ref        TEXT NOT NULL UNIQUE,
  name       TEXT NOT NULL,
  company    TEXT,
  email      TEXT NOT NULL,
  phone      TEXT,
  country    TEXT,
  message    TEXT,
  status     TEXT NOT NULL DEFAULT 'new',
  created_at TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE TABLE quote_items (
  id           INTEGER PRIMARY KEY AUTOINCREMENT,
  quote_id     INTEGER NOT NULL REFERENCES quotes(id) ON DELETE CASCADE,
  product_id   INTEGER,
  product_name TEXT NOT NULL,
  variations   TEXT,
  quantity     INTEGER NOT NULL DEFAULT 1,
  notes        TEXT
);
INSERT INTO quotes (ref,name,company,email,phone,country,message,status) VALUES
('VN-240501','Jordan Blake','Northside Hawks FC','jordan@hawksfc.example','+1 503 555 0199','USA','Need 25 full football kits for the new season, royal blue & white.','new');
INSERT INTO quote_items (quote_id,product_id,product_name,variations,quantity,notes) VALUES
(1, 1, 'Pro Sublimated Football Kit', 'Color: Royal Blue', 25, 'Numbers 1-25, sponsor logo on chest');

-- ---- FAQs ----
CREATE TABLE faqs (
  id         INTEGER PRIMARY KEY AUTOINCREMENT,
  question   TEXT NOT NULL,
  answer     TEXT NOT NULL,
  sort_order INTEGER NOT NULL DEFAULT 0,
  is_active  INTEGER NOT NULL DEFAULT 1
);
INSERT INTO faqs (question, answer, sort_order, is_active) VALUES
('What is your minimum order quantity (MOQ)?', 'Most custom items start at a MOQ of just 10 pieces per design. Accessories and blanks may vary — the MOQ is listed on each product page.', 1, 1),
('Do you provide design mockups?', 'Yes. Our in-house design team provides free 3D mockups for every custom order so you can approve the look before we produce anything.', 2, 1),
('How long does production take?', 'Standard production is 10-15 working days after artwork approval. Rush production is available on request.', 3, 1),
('Can I order with my own logo and sponsors?', 'Absolutely. Send us your artwork (AI, EPS, PDF or high-res PNG) and we will place logos, sponsors, names and numbers exactly where you want them.', 4, 1),
('What printing methods do you offer?', 'Full-dye sublimation, screen printing, embroidery, tackle twill, DTF and heat transfer — we recommend the best method for your fabric and design.', 5, 1),
('Do you ship worldwide?', 'Yes, we ship worldwide by air and sea. Shipping rates and timelines are shown at checkout and can be managed per region.', 6, 1),
('Can I get fabric samples?', 'Yes, we can send a swatch pack of our most popular performance and lifestyle fabrics on request.', 7, 1);
