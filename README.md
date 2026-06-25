# Online Parent-Teacher Meeting System (OPTMS)

**Project IDs:** BC250219905, BC230212937  
**Tech Stack:** PHP (MVC), MySQL, HTML5, CSS3, JavaScript, Bootstrap

---

## 📌 Project Overview
The **Online Parent-Teacher Meeting System (OPTMS)** is a comprehensive web application designed to bridge the communication gap between parents and teachers. It automates scheduling, report sharing (DMCs), slot booking, payment verifications (via JazzCash), and meeting orchestration (via Google Meet integration).

---

## 🛠️ Tech Stack & Architecture
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla JS & Bootstrap)
- **Backend:** PHP (structured using MVC pattern)
- **Database:** MySQL
- **Integrations:**
  - **JazzCash API Sandbox** (for parent dues/fee verification)
  - **Google Calendar/Meet API** (for automated meeting links)

---

## 🗂️ Database Architecture
The project database (`optms_db`) consists of the following key tables:
1. **`Users`**: Holds credentials and roles (`Admin`, `Teacher`, `Parent`) and status (`Pending`, `Active`).
2. **`Students`**: Details for students linked to parent profiles.
3. **`Payments`**: Tracks JazzCash transaction ID, amount, and payment status (`Paid`, `Unpaid`).
4. **`DMCs`**: Stores student test marks, total marks, and path to uploaded DMC PDF reports.
5. **`Meetings`**: Stores booked slots, duration, Google Meet link, and meeting status.
6. **`Feedback`**: Parent ratings and comments left after meetings, with optional forwarding to Admin.

---

## 🌟 Key Features

### 👑 1. Admin Panel
- **Teacher Management:** Add, update, and delete teacher profiles.
- **Parent Verification:** Review parent registrations and approve/reject based on student registration details.
- **Event Scheduling:** Create Parent-Teacher Meeting events and specify open time slots.
- **Financial Reporting:** View and export profit/loss summaries (expenses vs. fee payments) to **PDF** and **CSV** formats.

### 👨‍🏫 2. Teacher Panel
- **Academic Records:** View students, enter exam marks, or upload scanned PDF DMC files.
- **Schedule Management:** See booked slots with parent/student details.
- **Meeting room:** Joint interface displaying the Google Meet call alongside the student's DMC report for convenient viewing during meetings.
- **Feedback Feed:** View parent ratings and forward critical issues to the Admin.

### 👩‍👦 3. Parent Panel
- **Registration:** Secure form to request profile creation and link students by registration number, class, and DOB.
- **Financial Gatekeeper:** Instant payment validation. Scheduler is locked until outstanding dues are paid online via the simulated JazzCash checkout.
- **Slot Booking:** Book available 15-minute PTM slots 1-2 days prior to the event.
- **Live Meeting:** Access Google Meet links and easily join active sessions.
- **Feedback:** Seamless post-meeting feedback form to rate teachers immediately after a meeting ends.

---

## 🚀 Installation & Setup

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) (Apache & MySQL)
- Git

### Steps
1. **Clone/Copy Project:**
   Clone this repository or place the `OPTMS` folder into your XAMPP web directory (typically `C:\xampp\htdocs\`).
   ```bash
   git clone https://github.com/Umair6085/OPTMS.git
   ```

2. **Configure Database:**
   - Open phpMyAdmin (`http://localhost/phpmyadmin`).
   - Create a new database named `optms_db`.
   - Import the `schema.sql` file located in the root directory.

3. **Check Configuration:**
   - Verify connection details in [db.php](file:///c:/xampp/htdocs/OPTMS/db.php):
     ```php
     $host = 'localhost';
     $db_name = 'optms_db';
     $username = 'root';
     $password = '';
     ```
   - Make sure your base URL matches in [config.php](file:///c:/xampp/htdocs/OPTMS/config.php):
     ```php
     define('BASE_URL', 'http://localhost/OPTMS');
     define('USE_MOCK_APIS', true); // Runs APIs in simulation mode for local testing
     ```

4. **Run Application:**
   - Open your browser and navigate to `http://localhost/OPTMS`.

---

## 📂 Directory Structure
```text
OPTMS/
│
├── assets/               # CSS, JS, and Images/icons
├── controllers/          # MVC Controllers (Admin, Auth, Parent, Teacher)
├── models/               # Database Models
├── views/                # Frontend Views/Templates (layout headers, footer, etc.)
│   ├── admin/
│   ├── auth/
│   ├── parent/
│   ├── teacher/
│   └── error.php
│
├── config.php            # Global configurations, URLs, & mock toggles
├── db.php                # Database PDO connection instance
├── schema.sql            # MySQL Database SQL setup script
└── README.md             # Project documentation (this file)
```
