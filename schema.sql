CREATE DATABASE IF NOT EXISTS optms_db;
USE optms_db;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'Teacher', 'Parent') NOT NULL,
    status ENUM('Pending', 'Active') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Students Table
CREATE TABLE IF NOT EXISTS Students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    registration_no VARCHAR(50) UNIQUE NOT NULL,
    class_name VARCHAR(50) NOT NULL,
    dob DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES Users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Payments Table
CREATE TABLE IF NOT EXISTS Payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    jazzcash_trx_id VARCHAR(100) DEFAULT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_date DATETIME DEFAULT NULL,
    status ENUM('Paid', 'Unpaid') DEFAULT 'Unpaid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES Students(student_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. DMCs Table
CREATE TABLE IF NOT EXISTS DMCs (
    dmc_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    teacher_id INT NOT NULL,
    total_marks INT NOT NULL,
    obtained_marks INT NOT NULL,
    pdf_file_path VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES Students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES Users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Meetings Table
CREATE TABLE IF NOT EXISTS Meetings (
    meeting_id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT NOT NULL,
    parent_id INT NOT NULL,
    slot_time DATETIME NOT NULL,
    duration INT DEFAULT 15,
    meet_link VARCHAR(255) DEFAULT NULL,
    status ENUM('Scheduled', 'Completed', 'Cancelled') DEFAULT 'Scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES Users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Feedback Table
CREATE TABLE IF NOT EXISTS Feedback (
    feedback_id INT AUTO_INCREMENT PRIMARY KEY,
    meeting_id INT NOT NULL,
    rating_stars INT CHECK (rating_stars BETWEEN 1 AND 5),
    comments TEXT,
    needs_admin_action BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (meeting_id) REFERENCES Meetings(meeting_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- PTM Events (for event scheduling metadata)
CREATE TABLE IF NOT EXISTS PtmEvents (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    event_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    slot_duration INT DEFAULT 15,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Settings Table
CREATE TABLE IF NOT EXISTS Settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. TimeSlots Table
CREATE TABLE IF NOT EXISTS TimeSlots (
    slot_id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT NOT NULL,
    slot_time DATETIME NOT NULL,
    duration INT DEFAULT 15,
    status ENUM('Available', 'Booked', 'Blocked') DEFAULT 'Available',
    FOREIGN KEY (teacher_id) REFERENCES Users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Expenses Table
CREATE TABLE IF NOT EXISTS Expenses (
    expense_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    category VARCHAR(100) DEFAULT 'General',
    expense_date DATE NOT NULL,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. AcademicRecords Table
CREATE TABLE IF NOT EXISTS AcademicRecords (
    record_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    teacher_id INT NOT NULL,
    record_type ENUM('Test', 'Exam', 'Assignment', 'Behaviour', 'Participation', 'Document') NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    total_marks INT DEFAULT NULL,
    obtained_marks INT DEFAULT NULL,
    file_path VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES Students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES Users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Settings
INSERT INTO Settings (setting_key, setting_value) VALUES ('restrict_dues_booking', '1') ON DUPLICATE KEY UPDATE setting_value=setting_value;

-- Seed initial data
-- Password hash for 'password123'
INSERT INTO Users (name, email, password, role, status) VALUES
('System Admin', 'admin@optms.com', '$2y$10$tZ9sD3r6uWwX6eD7aT1aEu0yVex7DkSwW0N5k6t1rXF2V2aHq0N92', 'Admin', 'Active'),
('Sir Ahmed', 'teacher@optms.com', '$2y$10$tZ9sD3r6uWwX6eD7aT1aEu0yVex7DkSwW0N5k6t1rXF2V2aHq0N92', 'Teacher', 'Active'),
('Sir Kamran', 'kamran@optms.com', '$2y$10$tZ9sD3r6uWwX6eD7aT1aEu0yVex7DkSwW0N5k6t1rXF2V2aHq0N92', 'Teacher', 'Active'),
('John Doe Parent', 'parent@optms.com', '$2y$10$tZ9sD3r6uWwX6eD7aT1aEu0yVex7DkSwW0N5k6t1rXF2V2aHq0N92', 'Parent', 'Active');

INSERT INTO Students (parent_id, full_name, registration_no, class_name, dob) VALUES
(4, 'Alex Doe', 'REG-2026-001', 'Class 10-A', '2010-05-15');

INSERT INTO Payments (student_id, jazzcash_trx_id, amount, payment_date, status) VALUES
(1, NULL, 5000.00, NULL, 'Unpaid');
