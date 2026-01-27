Reborn Rentals

Reborn Rentals is a full-stack rental management web application built with Laravel that allows users to rent products and synchronizes data with Odoo ERP.
The project follows Laravel’s MVC architecture and is designed to be scalable, clean, and production-ready.

🚀 Overview

Product rental platform

Shopping cart & checkout flow

User authentication

Order management

Integration with Odoo for product, customer, and operational data

SQLite by default for fast local setup

✨ Features

✅ Laravel 12 MVC Architecture

✅ User Authentication (Login / Register)

✅ Product Catalog

✅ Rental Cart (Session-based)

✅ Checkout System

✅ Order History & Details

✅ Database Migrations & Seeders

✅ Odoo ERP Integration

✅ Tailwind CSS (via CDN)

🧰 Tech Stack

Backend: Laravel 12, PHP 8.2+

Frontend: Blade + Tailwind CSS

Database: SQLite (default)

Sessions / Cache / Queue: Database

ERP Integration: Odoo (API)

Maps: Google Maps API (optional)

📋 Requirements

PHP >= 8.2

Composer

SQLite

Node.js (optional, only if extending frontend tooling)

🔧 Environment Configuration

Create a .env file based on .env.example and configure the following:

APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

ODOO_URL=https://yourcompany.odoo.com
ODOO_DB=yourcompany
ODOO_USER=youruser@email.com
ODOO_API_KEY=your_api_key


⚠️ Never commit real API keys or credentials to GitHub.

🛠️ Installation & Setup

Follow these steps to run the project locally:

# 1. Install dependencies
composer install

# 2. Create environment file
cp .env.example .env

# 3. Generate application key
php artisan key:generate

# 4. Run migrations
php artisan migrate

# 5. Seed database with demo data
php artisan migrate --seed

# 6. Start development server
php artisan serve


The app will be available at:

http://localhost:8000

👤 Demo Users

Admin

Email: admin@rebornrentals.com

Password: password

Standard User

Email: john@example.com

Password: password

📁 Project Structure
app/
├── Http/Controllers/
│   ├── Auth/                  # Authentication
│   ├── CartController.php     # Cart logic
│   ├── CheckoutController.php # Checkout flow
│   ├── HomeController.php     # Landing page
│   ├── OrderController.php    # Orders
│   └── ProductController.php # Products
├── Models/                    # Eloquent models
└── ...

resources/views/
├── layouts/
│   └── app.blade.php
├── auth/
├── products/
├── checkout/
├── orders/
└── home.blade.php

routes/
└── web.php

🛒 Core Functionality
Cart

Add / remove products

Update quantities

Persisted using Laravel sessions

Sidebar-style cart UI

Checkout

Rental date selection

Delivery information

Order validation

Tax calculations

Orders

User order list

Order detail view

Full order history

🔐 Authentication

Laravel session-based authentication

Protected routes via middleware

Login & registration flows

🔗 Odoo Integration

The application connects to Odoo via API to enable:

Product synchronization

Customer data management

Operational consistency with ERP workflows

Configuration is handled via .env variables.

🎨 UI / Design

Tailwind CSS via CDN

Dark-themed UI

Modular Blade layouts

📝 Notes

The cart uses Laravel sessions, not localStorage

Images are stored in public/

SQLite is used by default for simplicity

Tailwind is loaded via CDN (no build step required)

🚀 Roadmap

Admin dashboard

Real payment gateways (Stripe / PayPal)

Email notifications

Advanced Odoo sync (orders & inventory)

Improved mobile responsiveness

Role-based access control

📄 License

This project is private and proprietary.
All rights reserved.
