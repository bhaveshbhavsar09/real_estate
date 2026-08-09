-- ============================================================
-- EstateFolio Database Schema
-- Import this file in phpMyAdmin or via:
--   mysql -u root -p estatefolio < database.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS estatefolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE estatefolio;

-- ---------------------------------------------------------
-- Properties table
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS properties (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    title           VARCHAR(150)  NOT NULL,
    property_type   ENUM('House','Apartment','Villa','Plot','Commercial','Penthouse') NOT NULL DEFAULT 'House',
    listing_type    ENUM('Sale','Rent') NOT NULL DEFAULT 'Sale',
    price           DECIMAL(14,2) NOT NULL,
    bedrooms        INT DEFAULT 0,
    bathrooms       INT DEFAULT 0,
    area_sqft       INT DEFAULT 0,
    year_built      INT DEFAULT NULL,
    address         VARCHAR(255) NOT NULL,
    city            VARCHAR(100) NOT NULL,
    state           VARCHAR(100) DEFAULT NULL,
    country         VARCHAR(100) DEFAULT NULL,
    latitude        DECIMAL(10,7) NOT NULL,
    longitude       DECIMAL(10,7) NOT NULL,
    description     TEXT,
    amenities       VARCHAR(500) DEFAULT NULL COMMENT 'comma separated list',
    image           VARCHAR(255) DEFAULT NULL,
    featured        TINYINT(1) DEFAULT 0,
    status          ENUM('Available','Pending','Sold') DEFAULT 'Available',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------
-- Customer inquiries / contact form submissions
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS inquiries (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    property_id     INT DEFAULT NULL,
    name            VARCHAR(100) NOT NULL,
    email           VARCHAR(150) NOT NULL,
    phone           VARCHAR(30)  DEFAULT NULL,
    budget          VARCHAR(50)  DEFAULT NULL,
    message         TEXT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE SET NULL
);

-- ---------------------------------------------------------
-- Sample data so the site isn't empty on first run
-- ---------------------------------------------------------
INSERT INTO properties
(title, property_type, listing_type, price, bedrooms, bathrooms, area_sqft, year_built, address, city, state, country, latitude, longitude, description, amenities, image, featured, status)
VALUES
('Sunrise Meadow Villa', 'Villa', 'Sale', 485000, 4, 3, 3200, 2019, '221 Meadow Lane', 'Austin', 'Texas', 'USA', 30.2672, -97.7431,
 'A bright, airy villa with an open-plan living area, private garden, and a two-car garage. Close to schools and parks.',
 'Swimming Pool,Garden,Garage,Air Conditioning,Security', 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=900', 1, 'Available'),

('Downtown Skyline Apartment', 'Apartment', 'Rent', 2200, 2, 2, 1150, 2015, '88 Congress Ave', 'Austin', 'Texas', 'USA', 30.2669, -97.7428,
 'Modern high-rise apartment with panoramic city views, gym access, and 24/7 concierge.',
 'Gym,Elevator,Concierge,Balcony,Parking', 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=900', 1, 'Available'),

('Lakeview Family House', 'House', 'Sale', 620000, 5, 4, 4100, 2012, '15 Lakeview Drive', 'Seattle', 'Washington', 'USA', 47.6062, -122.3321,
 'Spacious family home with a private dock, finished basement, and mountain views.',
 'Dock,Fireplace,Basement,Garden,Garage', 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=900', 0, 'Available'),

('The Ivory Penthouse', 'Penthouse', 'Sale', 1250000, 3, 3, 2600, 2021, '900 Fifth Avenue', 'New York', 'New York', 'USA', 40.7736, -73.9566,
 'Ultra-luxury penthouse with a private rooftop terrace and floor-to-ceiling windows overlooking Central Park.',
 'Rooftop Terrace,Concierge,Gym,Wine Cellar,Smart Home', 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=900', 1, 'Available'),

('Riverside Commercial Plaza', 'Commercial', 'Rent', 8500, 0, 4, 6000, 2008, '45 River Street', 'Chicago', 'Illinois', 'USA', 41.8781, -87.6298,
 'Prime ground-floor commercial space suited for retail or office use, high foot traffic area.',
 'Parking,Loading Dock,Security,Signage', 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=900', 0, 'Available'),

('Golden Hills Building Plot', 'Plot', 'Sale', 150000, 0, 0, 8000, NULL, '3 Golden Hills Road', 'Denver', 'Colorado', 'USA', 39.7392, -104.9903,
 'Cleared residential plot with mountain views, ready for construction, utilities at the road.',
 'Mountain View,Utilities Ready', 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=900', 0, 'Available'),

('Maple Street Cottage', 'House', 'Rent', 1800, 3, 2, 1600, 2005, '12 Maple Street', 'Portland', 'Oregon', 'USA', 45.5152, -122.6784,
 'Charming cottage with a wraparound porch, updated kitchen, and a quiet tree-lined street.',
 'Porch,Garden,Fireplace,Pet Friendly', 'https://images.unsplash.com/photo-1570129477492-45c003edd2be?w=900', 0, 'Available'),

('Coral Bay Beach Villa', 'Villa', 'Sale', 980000, 4, 4, 3500, 2018, '77 Coral Bay Road', 'Miami', 'Florida', 'USA', 25.7617, -80.1918,
 'Beachfront villa with private beach access, infinity pool, and a rooftop lounge.',
 'Private Beach,Infinity Pool,Rooftop Lounge,Smart Home', 'https://images.unsplash.com/photo-1613977257363-707ba9348227?w=900', 1, 'Available');