# Melody Masters Online Store 🎵

## Overview
Melody Masters is a full-featured, academic E-Commerce web application built using PHP and MySQL. It simulates a premium musical instrument and digital sheet music store, complete with shopping cart logic, verified customer reviews, and a fully functional Admin dashboard.

This project was built to meet robust business requirements while maintaining a clean, well-commented ("student submission" style) codebase. The application is fully localized for Sri Lanka, with all prices and shipping thresholds operating in Sri Lankan Rupees (Rs.).

---

## Key Features

### User Experience
- **Premium UI/UX:** A modern, visually stunning design featuring glassmorphism, dynamic animations, custom gradients, and the Google font "Outfit".
- **AI-Generated Imagery:** High-quality placeholder images generated exclusively for the store.
- **Shopping Cart & Checkout:** Complete e-commerce flow. Includes conditional shipping rules (Free shipping on orders over Rs. 15,000, otherwise standard Rs. 500 shipping).
- **Digital Downloads:** Supports digital products (e.g., PDF sheet music) which can only be downloaded by users who have successfully purchased them.
- **Verified Reviews Network:** Custom business logic explicitly prevents anonymous or non-purchasing users from leaving product reviews. Only verified buyers can submit ratings.
- **Forgot Password Flow:** A simulated, mock email password-reset system allowing users to regain access securely without requiring live SMTP server configurations.

### Admin Dashboard Capabilities
- **Complete CRUD Operations:** Administrators can add, edit, or delete products directly from the `admin_manage_products.php` dashboard.
- **Local Image Uploads:** Admins can natively upload physical image files (e.g., `.jpg`, `.png`) directly from their device. The system automatically secures the file with a unique ID and saves it to the local `assets/images/` directory.

---

## 🛠️ Installation & Setup (Local Development)

This application is designed to run on **XAMPP** (or a similar AMP stack).

1. **Clone/Move the Directory**: 
   Ensure this entire project folder (`Melody-Master-Online-Store`) is placed inside your XAMPP `htdocs` directory (e.g., `C:\xampp\htdocs\Melody-Master-Online-Store`).

2. **Start the Servers**:
   Open your XAMPP Control Panel and start **Apache** and **MySQL**.

3. **Database Configuration**:
   - Open phpMyAdmin in your browser (`http://localhost/phpmyadmin`).
   - You do NOT need to create a database manually; simply import the provided `database.sql` file.
   - The `.sql` file will automatically `CREATE DATABASE melody_masters`, build the tables, and seed it with realistic Sri Lankan Dummy Data.

4. **Verify Connection Scripts**:
   Ensure `config/db.php` has the correct credentials. By default, it uses the standard XAMPP configuration:
   - Host: `localhost`
   - User: `root`
   - Password: `''` (Blank)

5. **Launch the Application**:
   Navigate to the entry point in your browser:
   `http://localhost/Melody-Master-Online-Store/public/index.php`

---

## 📝 Account Information
Since the database includes a fresh setup script, you can easily register a new user account to test the buying flow. To test Administrative features, you can alter your registered user's `role` to `'admin'` directly inside the `users` table via phpMyAdmin.

---
