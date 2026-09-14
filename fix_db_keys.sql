-- ============================================================
-- TTMS DATABASE FIX SCRIPT
-- Run this in phpMyAdmin > ttms database > SQL tab
-- ============================================================

USE ttms;

-- ─────────────────────────────────────────────────────────────
-- STEP 1: Fix itineraries table (id=0 problem)
-- ─────────────────────────────────────────────────────────────

-- Add temp column to assign unique ids
ALTER TABLE itineraries ADD COLUMN new_id INT AUTO_INCREMENT PRIMARY KEY FIRST;

-- Remove the duplicate id=0 issue by copying new_id into id
UPDATE itineraries SET id = new_id;

-- Drop the temp column
ALTER TABLE itineraries DROP COLUMN new_id;

-- Now add PRIMARY KEY + AUTO_INCREMENT on id
ALTER TABLE itineraries MODIFY id INT(11) NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (id);

-- ─────────────────────────────────────────────────────────────
-- STEP 2: Drop hotels_old backup table (not needed)
-- ─────────────────────────────────────────────────────────────

DROP TABLE IF EXISTS hotels_old;

-- ─────────────────────────────────────────────────────────────
-- STEP 3: Fix bookings.hotel_id = 0 → NULL
--         (0 is not a valid hotel id, needed for FK)
-- ─────────────────────────────────────────────────────────────

UPDATE bookings SET hotel_id = NULL WHERE hotel_id = 0;

-- ─────────────────────────────────────────────────────────────
-- STEP 4: Add FOREIGN KEYS
-- ─────────────────────────────────────────────────────────────

-- FK 1: itineraries.package_id → packages.id
ALTER TABLE itineraries
  ADD CONSTRAINT fk_itin_package
  FOREIGN KEY (package_id) REFERENCES packages(id)
  ON DELETE CASCADE ON UPDATE CASCADE;

-- FK 2: hotels.package_id → packages.id
ALTER TABLE hotels
  ADD CONSTRAINT fk_hotel_package
  FOREIGN KEY (package_id) REFERENCES packages(id)
  ON DELETE CASCADE ON UPDATE CASCADE;

-- FK 3: bookings.user_id → users.id
ALTER TABLE bookings
  ADD CONSTRAINT fk_book_user
  FOREIGN KEY (user_id) REFERENCES users(id)
  ON DELETE CASCADE ON UPDATE CASCADE;

-- FK 4: bookings.package_id → packages.id
ALTER TABLE bookings
  ADD CONSTRAINT fk_book_package
  FOREIGN KEY (package_id) REFERENCES packages(id)
  ON DELETE CASCADE ON UPDATE CASCADE;

-- FK 5: bookings.hotel_id → hotels.id (SET NULL because hotel_id can be NULL)
ALTER TABLE bookings
  ADD CONSTRAINT fk_book_hotel
  FOREIGN KEY (hotel_id) REFERENCES hotels(id)
  ON DELETE SET NULL ON UPDATE CASCADE;

-- ─────────────────────────────────────────────────────────────
-- DONE! Verify with these queries:
-- ─────────────────────────────────────────────────────────────

-- Check Primary Keys:
SELECT TABLE_NAME, CONSTRAINT_TYPE, CONSTRAINT_NAME
FROM information_schema.TABLE_CONSTRAINTS
WHERE TABLE_SCHEMA = 'ttms'
ORDER BY TABLE_NAME, CONSTRAINT_TYPE;
