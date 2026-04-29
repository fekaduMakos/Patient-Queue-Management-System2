-- COMPLETE SQL SETUP FOR NIGIST ELIN HOSPITAL QUEUE SYSTEM
CREATE DATABASE IF NOT EXISTS hospital_queue_db;
USE hospital_queue_db;

-- 1. Table for persistent patient profiles
CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id VARCHAR(50) UNIQUE NOT NULL, -- Unique Medical Record Number
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    age INT,
    gender ENUM('Male', 'Female', 'Other'),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Updated Queue Table (Linked to Patients)
CREATE TABLE IF NOT EXISTS queue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token_number VARCHAR(10) NOT NULL,
    patient_id VARCHAR(50) NOT NULL, -- Link to patients table
    department VARCHAR(50) NOT NULL,
    urgency ENUM('Normal', 'Urgent', 'Emergency') DEFAULT 'Normal',
    payment_status ENUM('Pending', 'Paid', 'Exempt') DEFAULT 'Pending',
    status ENUM('waiting', 'calling', 'completed', 'skipped') DEFAULT 'waiting',
    window_number INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    called_at TIMESTAMP NULL,
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id)
);

-- 2. Table for current serving status
CREATE TABLE IF NOT EXISTS counters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    counter_name VARCHAR(50) NOT NULL,
    window_number INT NOT NULL,
    current_token VARCHAR(10) DEFAULT NULL
);

-- 3. Table for Role-Based Access Control
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'doctor', 'receptionist', 'pharmacist', 'cashier', 'lab_tech') NOT NULL,
    window_number INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Initial Seed Data
INSERT INTO counters (counter_name, window_number, prefix) VALUES 
('Reception', 1, 'R'),
('Triage', 2, 'T'),
('OPD', 3, 'O'),
('Laboratory', 4, 'L'),
('Pharmacy', 5, 'P'),
('Radiology', 6, 'X'),
('Cashier', 7, 'C');

INSERT INTO users (username, password, full_name, role, window_number) VALUES 
('admin', 'admin123', 'System Administrator', 'admin', 0),
('reception', 'rec123', 'Selamawit Kebede', 'receptionist', 1),
('pharmacy', 'phar123', 'Dawit Girma', 'pharmacist', 5),
('doctor', 'doc123', 'Dr. Bruck Ayele', 'doctor', 3),
('lab', 'lab123', 'Abebe Tadesse', 'lab_tech', 4);
