-- ============================================================
--  VANTAGE Apparel — Ecommerce + CMS database schema
--  Sports Uniforms • Active Wear • Casual Wear • Street Wear
--  MySQL 5.7+ / 8.0 — utf8mb4
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Create the database (import this file into it, or let it create it)
CREATE DATABASE IF NOT EXISTS `vantage_shop`
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `vantage_shop`;

-- ------------------------------------------------------------
--  Admin users (CMS login)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE `admin_users` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(100) NOT NULL,
  `username`      VARCHAR(60)  NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default login -> username: admin  /  password: admin123
-- (hash generated with password_hash('admin123', PASSWORD_DEFAULT))
INSERT INTO `admin_users` (`name`, `username`, `password_hash`) VALUES
('Store Administrator', 'admin', '$2y$10$VSe/8Fg2Zo65xQ1sGkaQI.OntLLilEQXKgst5h3ITdA9VXifV/O.G');

-- ------------------------------------------------------------
--  Site settings (key/value) — editable from the CMS
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id`     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `skey`   VARCHAR(80)  NOT NULL,
  `svalue` TEXT         NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_skey` (`skey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` (`skey`, `svalue`) VALUES
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

-- ------------------------------------------------------------
--  Homepage slider
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `sliders`;
CREATE TABLE `sliders` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(160) NOT NULL,
  `subtitle`    VARCHAR(255) NULL,
  `image`       VARCHAR(255) NOT NULL,
  `button_text` VARCHAR(60)  NULL,
  `button_link` VARCHAR(255) NULL,
  `sort_order`  INT          NOT NULL DEFAULT 0,
  `is_active`   TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sliders` (`title`, `subtitle`, `image`, `button_text`, `button_link`, `sort_order`, `is_active`) VALUES
('Built For The Game.',      'Pro-grade custom sports uniforms, engineered for performance and made to order.', 'assets/images/slides/slide-1.svg', 'Explore Uniforms', 'category.php?cat=sports-uniforms', 1, 1),
('Own The Street.',          'Premium street & casual wear, manufactured to your brand specs.',                  'assets/images/slides/slide-2.svg', 'Shop Street Wear', 'category.php?cat=street-wear', 2, 1),
('Train Without Limits.',    'Moisture-wicking active wear built for every rep, every mile.',                     'assets/images/slides/slide-3.svg', 'See Active Wear', 'category.php?cat=active-wear', 3, 1);

-- ------------------------------------------------------------
--  Categories
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(120) NOT NULL,
  `slug`        VARCHAR(140) NOT NULL,
  `tagline`     VARCHAR(200) NULL,
  `description` TEXT         NULL,
  `image`       VARCHAR(255) NULL,
  `color`       VARCHAR(20)  NOT NULL DEFAULT '#e11d48',
  `sort_order`  INT          NOT NULL DEFAULT 0,
  `is_active`   TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cat_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`name`, `slug`, `tagline`, `description`, `image`, `color`, `sort_order`, `is_active`) VALUES
('Sports Uniforms', 'sports-uniforms', 'Team kits engineered to win', 'Fully custom sublimated kits for football, basketball, cricket, baseball, rugby and more.', 'assets/images/cats/cat-sports.svg',  '#e11d48', 1, 1),
('Active Wear',     'active-wear',     'Move. Sweat. Repeat.',        'Performance tees, leggings, shorts and training tops with moisture-wicking fabric.',       'assets/images/cats/cat-active.svg',  '#0891b2', 2, 1),
('Casual Wear',     'casual-wear',     'Everyday comfort, your brand','Soft hoodies, tees, polos and joggers for lifestyle and corporate lines.',                  'assets/images/cats/cat-casual.svg',  '#ca8a04', 3, 1),
('Street Wear',     'street-wear',     'Bold fits for the culture',   'Oversized tees, cargo pants, windbreakers and statement pieces built to turn heads.',      'assets/images/cats/cat-street.svg',  '#db2777', 4, 1),
('Training Gear',   'training-gear',   'Kit out the whole squad',     'Bibs, base layers, tracksuits and warm-ups for teams and academies.',                      'assets/images/cats/cat-training.svg','#7c3aed', 5, 1),
('Accessories',     'accessories',     'Finish the look',             'Caps, socks, bags and headwear to complete your lineup.',                                  'assets/images/cats/cat-access.svg',  '#16a34a', 6, 1);

-- ------------------------------------------------------------
--  Products
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`         VARCHAR(180) NOT NULL,
  `slug`         VARCHAR(200) NOT NULL,
  `category_id`  INT UNSIGNED NULL,
  `sku`          VARCHAR(60)  NULL,
  `short_desc`   VARCHAR(300) NULL,
  `description`  TEXT         NULL,
  `main_image`   VARCHAR(255) NULL,
  `price_from`   DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `min_order`    INT          NOT NULL DEFAULT 10,
  `fabric`       VARCHAR(160) NULL,
  `is_featured`  TINYINT(1)   NOT NULL DEFAULT 0,
  `is_active`    TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_prod_slug` (`slug`),
  KEY `idx_prod_cat` (`category_id`),
  CONSTRAINT `fk_prod_cat` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`name`, `slug`, `category_id`, `sku`, `short_desc`, `description`, `main_image`, `price_from`, `min_order`, `fabric`, `is_featured`, `is_active`) VALUES
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

-- ------------------------------------------------------------
--  Product gallery images (for cloud zoom / thumbnails)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `product_images`;
CREATE TABLE `product_images` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `image`      VARCHAR(255) NOT NULL,
  `sort_order` INT          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_pi_product` (`product_id`),
  CONSTRAINT `fk_pi_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A few extra angles reuse the category/product art for the demo gallery
INSERT INTO `product_images` (`product_id`, `image`, `sort_order`) VALUES
(1, 'assets/images/products/p-football.svg', 1),
(1, 'assets/images/products/p-basketball.svg', 2),
(1, 'assets/images/products/p-cricket.svg', 3),
(2, 'assets/images/products/p-basketball.svg', 1),
(2, 'assets/images/products/p-compression.svg', 2),
(8, 'assets/images/products/p-hoodie.svg', 1),
(8, 'assets/images/products/p-polo.svg', 2),
(10, 'assets/images/products/p-streettee.svg', 1),
(10, 'assets/images/products/p-cargo.svg', 2);

-- ------------------------------------------------------------
--  Product variations (attribute / value)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `product_variations`;
CREATE TABLE `product_variations` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `attribute`  VARCHAR(60)  NOT NULL,   -- e.g. Size, Color, Sleeve
  `value`      VARCHAR(80)  NOT NULL,   -- e.g. XL, Royal Blue
  `extra_cost` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `sort_order` INT          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_pv_product` (`product_id`),
  CONSTRAINT `fk_pv_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `product_variations` (`product_id`, `attribute`, `value`, `extra_cost`, `sort_order`) VALUES
(1,'Size','YS',0,1),(1,'Size','YM',0,2),(1,'Size','S',0,3),(1,'Size','M',0,4),(1,'Size','L',0,5),(1,'Size','XL',1.50,6),(1,'Size','2XL',3.00,7),
(1,'Color','Royal Blue',0,1),(1,'Color','Crimson',0,2),(1,'Color','Black / Gold',0,3),
(2,'Size','S',0,1),(2,'Size','M',0,2),(2,'Size','L',0,3),(2,'Size','XL',1.50,4),(2,'Size','2XL',3.00,5),
(2,'Style','Single Layer',0,1),(2,'Style','Reversible',4.00,2),
(5,'Size','S',0,1),(5,'Size','M',0,2),(5,'Size','L',0,3),(5,'Size','XL',1.00,4),
(8,'Size','S',0,1),(8,'Size','M',0,2),(8,'Size','L',0,3),(8,'Size','XL',2.00,4),(8,'Size','2XL',4.00,5),
(8,'Color','Heather Grey',0,1),(8,'Color','Black',0,2),(8,'Color','Forest',0,3),
(10,'Size','S',0,1),(10,'Size','M',0,2),(10,'Size','L',0,3),(10,'Size','XL',2.00,4),(10,'Size','2XL',4.00,5);

-- ------------------------------------------------------------
--  Size charts
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `size_charts`;
CREATE TABLE `size_charts` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(120) NOT NULL,
  `category_id` INT UNSIGNED NULL,
  `unit`        VARCHAR(20)  NOT NULL DEFAULT 'inches',
  `content`     MEDIUMTEXT   NULL,   -- stored as HTML table
  `is_active`   TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_sc_cat` (`category_id`),
  CONSTRAINT `fk_sc_cat` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `size_charts` (`name`, `category_id`, `unit`, `content`, `is_active`) VALUES
('Adult Tops — Standard Fit', 1, 'inches',
'<table><thead><tr><th>Size</th><th>Chest</th><th>Length</th><th>Sleeve</th></tr></thead><tbody><tr><td>S</td><td>36-38</td><td>28</td><td>8.5</td></tr><tr><td>M</td><td>39-41</td><td>29</td><td>9</td></tr><tr><td>L</td><td>42-44</td><td>30</td><td>9.5</td></tr><tr><td>XL</td><td>45-47</td><td>31</td><td>10</td></tr><tr><td>2XL</td><td>48-50</td><td>32</td><td>10.5</td></tr><tr><td>3XL</td><td>51-53</td><td>33</td><td>11</td></tr></tbody></table>', 1),
('Youth Tops', 1, 'inches',
'<table><thead><tr><th>Size</th><th>Chest</th><th>Length</th></tr></thead><tbody><tr><td>YS</td><td>26-28</td><td>20</td></tr><tr><td>YM</td><td>28-30</td><td>22</td></tr><tr><td>YL</td><td>30-32</td><td>24</td></tr></tbody></table>', 1),
('Bottoms — Pants & Joggers', 3, 'inches',
'<table><thead><tr><th>Size</th><th>Waist</th><th>Inseam</th><th>Hip</th></tr></thead><tbody><tr><td>S</td><td>28-30</td><td>30</td><td>36</td></tr><tr><td>M</td><td>31-33</td><td>31</td><td>39</td></tr><tr><td>L</td><td>34-36</td><td>32</td><td>42</td></tr><tr><td>XL</td><td>37-39</td><td>32</td><td>45</td></tr><tr><td>2XL</td><td>40-42</td><td>33</td><td>48</td></tr></tbody></table>', 1);

-- ------------------------------------------------------------
--  Shipping methods
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `shipping_methods`;
CREATE TABLE `shipping_methods` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(120) NOT NULL,
  `region`        VARCHAR(120) NOT NULL,
  `rate`          DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `free_over`     DECIMAL(10,2) NULL,
  `delivery_time` VARCHAR(80)  NULL,
  `is_active`     TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `shipping_methods` (`name`, `region`, `rate`, `free_over`, `delivery_time`, `is_active`) VALUES
('Standard Freight',  'North America', 35.00, 1500.00, '7-10 business days', 1),
('Express Air',       'North America', 90.00, NULL,    '3-5 business days',  1),
('International Sea', 'Worldwide',    120.00, 3000.00, '25-35 business days',1),
('International Air', 'Worldwide',    240.00, NULL,    '6-9 business days',  1);

-- ------------------------------------------------------------
--  Inquiries / Quotes
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `quotes`;
CREATE TABLE `quotes` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ref`        VARCHAR(20)  NOT NULL,
  `name`       VARCHAR(120) NOT NULL,
  `company`    VARCHAR(160) NULL,
  `email`      VARCHAR(160) NOT NULL,
  `phone`      VARCHAR(60)  NULL,
  `country`    VARCHAR(80)  NULL,
  `message`    TEXT         NULL,
  `status`     ENUM('new','in_review','quoted','won','closed') NOT NULL DEFAULT 'new',
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ref` (`ref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `quote_items`;
CREATE TABLE `quote_items` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `quote_id`     INT UNSIGNED NOT NULL,
  `product_id`   INT UNSIGNED NULL,
  `product_name` VARCHAR(200) NOT NULL,
  `variations`   VARCHAR(255) NULL,
  `quantity`     INT          NOT NULL DEFAULT 1,
  `notes`        VARCHAR(255) NULL,
  PRIMARY KEY (`id`),
  KEY `idx_qi_quote` (`quote_id`),
  CONSTRAINT `fk_qi_quote` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Demo quote so the CMS list isn't empty
INSERT INTO `quotes` (`ref`,`name`,`company`,`email`,`phone`,`country`,`message`,`status`) VALUES
('VN-240501','Jordan Blake','Northside Hawks FC','jordan@hawksfc.example','+1 503 555 0199','USA','Need 25 full football kits for the new season, royal blue & white.','new');
INSERT INTO `quote_items` (`quote_id`,`product_id`,`product_name`,`variations`,`quantity`,`notes`) VALUES
(1, 1, 'Pro Sublimated Football Kit', 'Color: Royal Blue', 25, 'Numbers 1-25, sponsor logo on chest');

-- ------------------------------------------------------------
--  FAQs
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `faqs`;
CREATE TABLE `faqs` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `question`   VARCHAR(255) NOT NULL,
  `answer`     TEXT         NOT NULL,
  `sort_order` INT          NOT NULL DEFAULT 0,
  `is_active`  TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `faqs` (`question`, `answer`, `sort_order`, `is_active`) VALUES
('What is your minimum order quantity (MOQ)?', 'Most custom items start at a MOQ of just 10 pieces per design. Accessories and blanks may vary — the MOQ is listed on each product page.', 1, 1),
('Do you provide design mockups?', 'Yes. Our in-house design team provides free 3D mockups for every custom order so you can approve the look before we produce anything.', 2, 1),
('How long does production take?', 'Standard production is 10-15 working days after artwork approval. Rush production is available on request.', 3, 1),
('Can I order with my own logo and sponsors?', 'Absolutely. Send us your artwork (AI, EPS, PDF or high-res PNG) and we will place logos, sponsors, names and numbers exactly where you want them.', 4, 1),
('What printing methods do you offer?', 'Full-dye sublimation, screen printing, embroidery, tackle twill, DTF and heat transfer — we recommend the best method for your fabric and design.', 5, 1),
('Do you ship worldwide?', 'Yes, we ship worldwide by air and sea. Shipping rates and timelines are shown at checkout and can be managed per region.', 6, 1),
('Can I get fabric samples?', 'Yes, we can send a swatch pack of our most popular performance and lifestyle fabrics on request.', 7, 1);

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
--  Done. Default CMS login:  admin / admin123
--  (Change it immediately from Admin → Settings / profile.)
-- ============================================================
