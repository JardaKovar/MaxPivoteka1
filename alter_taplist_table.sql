-- Alter existing taplist table to remove unused columns
ALTER TABLE taplist DROP COLUMN ibu;
ALTER TABLE taplist DROP COLUMN ebc;
ALTER TABLE taplist DROP COLUMN price_03l;
