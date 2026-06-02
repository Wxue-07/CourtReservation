-- ==============================================================
-- NAMA DATABASE  : CourtReservation
-- DESKRIPSI      : DDL & DML Script untuk Sistem Reservasi Lapangan
-- ==============================================================

-- ==========================================
-- BAGIAN 0: MEMBUAT DATABASE
-- ==========================================

-- Membuat database baru
CREATE DATABASE CourtReservation;
GO

-- Menggunakan database yang baru saja dibuat
USE CourtReservation;
GO

-- ==========================================
-- BAGIAN 1: DDL (DATA DEFINITION LANGUAGE)
-- Membuat struktur tabel
-- ==========================================

-- 1. Membuat Tabel Users
CREATE TABLE Users (
    UserID INT IDENTITY(1,1) PRIMARY KEY,
    Username VARCHAR(100) NOT NULL,
    Password VARCHAR(255) NOT NULL,
    Role VARCHAR(20) NOT NULL, -- 'Customer' atau 'Admin'
    Email VARCHAR(100) NOT NULL UNIQUE,
    PhoneNumber VARCHAR(20) NOT NULL
);
GO

-- 2. Membuat Tabel Court
CREATE TABLE Court (
    CourtID INT IDENTITY(1,1) PRIMARY KEY,
    CourtNumber VARCHAR(50) NOT NULL,
    PricePerSession DECIMAL(10,2) NOT NULL
);
GO

-- 3. Membuat Tabel Reservation
CREATE TABLE Reservation (
    ReservationID INT IDENTITY(1,1) PRIMARY KEY,
    UserID INT NOT NULL,
    CourtID INT NOT NULL,
    BookingDate DATE NOT NULL,
    SessionTime VARCHAR(50) NOT NULL,
    TotalToPaid DECIMAL(10,2) NOT NULL,
    Status VARCHAR(20) DEFAULT 'Pending',
    FOREIGN KEY (UserID) REFERENCES Users(UserID) ON DELETE CASCADE,
    FOREIGN KEY (CourtID) REFERENCES Court(CourtID) ON DELETE CASCADE
);
GO


-- ==========================================
-- BAGIAN 2: CRUD (CREATE, READ, UPDATE, DELETE)
-- Memanipulasi data di dalam tabel
-- ==========================================

-- ------------------------------------------
-- A. CREATE (INSERT DATA)
-- ------------------------------------------

-- INSERT 5 Data ke tabel Users
INSERT INTO Users (Username, Password, Role, Email, PhoneNumber) VALUES
('Winson Xue', 'winson123', 'Customer', 'winson@badmincourt.com', '081234567890'),
('Budi Santoso', 'budi321', 'Customer', 'budi@gmail.com', '085678901234'),
('Siti Aminah', 'siti456', 'Customer', 'siti@yahoo.com', '089876543210'),
('Admin Utama', 'admin123', 'Admin', 'admin@badmincourt.com', '081112223333'),
('Admin Dua', 'admin456', 'Admin', 'admin2@badmincourt.com', '082223334444');

-- INSERT 8 Data ke tabel Court (Harga Fixed 150000)
INSERT INTO Court (CourtNumber, PricePerSession) VALUES
('Court 1', 150000.00),
('Court 2', 150000.00),
('Court 3', 150000.00),
('Court 4', 150000.00),
('Court 5', 150000.00),
('Court 6', 150000.00),
('Court 7', 150000.00),
('Court 8', 150000.00);

-- INSERT 5 Data ke tabel Reservation
-- (Format SessionTime: Interval 2 jam, TotalToPaid menyesuaikan harga fixed)
INSERT INTO Reservation (UserID, CourtID, BookingDate, SessionTime, TotalToPaid, Status) VALUES
(1, 1, '2026-06-05', '10:00 - 12:00', 150000.00, 'Pending'),
(2, 2, '2026-06-06', '14:00 - 16:00', 150000.00, 'Approved'),
(3, 8, '2026-06-07', '18:00 - 20:00', 150000.00, 'Pending'),
(1, 4, '2026-06-10', '12:00 - 14:00', 150000.00, 'Approved'),
(2, 7, '2026-06-12', '20:00 - 22:00', 150000.00, 'Canceled');


-- ------------------------------------------
-- B. READ (SELECT DATA)
-- ------------------------------------------

-- Menampilkan seluruh data pengguna
SELECT * FROM Users;

-- Menampilkan seluruh data lapangan
SELECT * FROM Court;

-- Menampilkan daftar reservasi lengkap dengan nama pengguna dan nomor lapangan (Untuk Dashboard Admin)
SELECT 
    r.ReservationID, 
    u.Username, 
    c.CourtNumber, 
    r.BookingDate, 
    r.SessionTime, 
    r.TotalToPaid, 
    r.Status
FROM Reservation r
JOIN Users u ON r.UserID = u.UserID
JOIN Court c ON r.CourtID = c.CourtID
ORDER BY r.BookingDate DESC;


-- ------------------------------------------
-- C. UPDATE (MENGUBAH DATA)
-- ------------------------------------------

-- Admin mengubah status reservasi menjadi 'Approved' untuk ReservationID 1
UPDATE Reservation 
SET Status = 'Approved' 
WHERE ReservationID = 1;

-- Pengguna mengupdate nomor teleponnya
UPDATE Users 
SET PhoneNumber = '089998887776' 
WHERE UserID = 2;


-- ------------------------------------------
-- D. DELETE (MENGHAPUS DATA)
-- ------------------------------------------

-- Menghapus data reservasi yang sudah dibatalkan (misal ReservationID 5)
DELETE FROM Reservation 
WHERE ReservationID = 5;

-- Menghapus lapangan yang mungkin sedang direnovasi (misal CourtID 3)
DELETE FROM Court 
WHERE CourtID = 3;