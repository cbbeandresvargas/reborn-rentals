# Reborn Rentals

A complete heavy machinery rental web application built with **Laravel 12**, **Tailwind CSS**, and **MVC architecture**.

## 🚀 Features

- **Full Backend**: Complete implementation of Models, Controllers, and Routes.
- **Integrated Frontend**: Blade templates styled with Tailwind CSS (via CDN).
- **Shopping Cart**: Session-based shopping cart system (no localStorage).
- **Authentication**: Secure Login/Registration using Laravel's native session authentication.
- **Checkout System**: Full checkout process with validation, date selection, and delivery address.
- **Order Management**: Users can view their order history and details.
- **Database**: SQLite configuration with comprehensive migrations and seeders.
- **QR Code Generation**: Integrated QR code functionality for orders/products.

## 📋 Requirements

- PHP >= 8.2
- Composer
- SQLite

## 🛠️ Installation

Follow these steps to set up the project locally:

1.  **Install PHP Dependencies**:
    ```bash
    composer install
    ```

2.  **Environment Configuration**:
    Copy the example environment file and generate the application key.
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

3.  **Database Setup**:
    Create the SQLite database file and run migrations with seeders.
    ```bash
    # Linux/Mac
    touch database/database.sqlite
    
    # Windows (PowerShell)
    New-Item -ItemType File -Path database/database.sqlite
    
    # Run migrations and seeds
    php artisan migrate --seed
    ```

4.  **Start the Server**:
    You can use the Laravel Artisan serve command:
    ```bash
    php artisan serve
    ```
    Or the built-in PHP server:
    ```bash
    php -S localhost:8000 -t public
    ```

    The application will be available at `http://localhost:8000`.

## 👤 Test Users

You can use the following credentials to test the application:

- **Admin User**:
  - Email: `admin@rebornrentals.com`
  - Password: `password`

- **Standard User**:
  - Email: `john@example.com`
  - Password: `password`

## 📁 Project Structure

```text
app/
├── Http/Controllers/
│   ├── Auth/               # Authentication (Login/Register)
│   ├── CartController.php  # Shopping Cart Logic
│   ├── CheckoutController.php # Checkout Process
│   ├── HomeController.php  # Landing Page
│   ├── OrderController.php # Order Management
│   └── ProductController.php # Product Listing/Details
├── Models/                 # Eloquent Models with Relationships
└── ...

resources/views/
├── layouts/
│   └── app.blade.php       # Main Layout Template
├── auth/                   # Authentication Views
├── checkout/               # Checkout Views
├── orders/                 # Order History Views
├── products/               # Product Views
└── home.blade.php          # Homepage

routes/
└── web.php                 # Web Routes Defined Here
```

## 🎨 Design & UI

- **Framework**: Tailwind CSS (loaded via CDN, no Node.js build step required for styles).
- **Color Palette**:
  - **Primary**: `#CE9704` (Gold/Amber)
  - **Dark Background**: `#4A4A4A`
  - **Cart Background**: `#2F2F2F`
  - **Light Gray**: `#BBBBBB`

## 🛒 Core Functionalities

### Shopping Cart
- Add products with quantity selection.
- Update quantities or remove items.
- Persistent cart state using Laravel Sessions.
- Sidebar view for quick access.

### Checkout
- **Rental Period**: Select start and end dates.
- **Delivery**: Input delivery address information.
- **Payment Methods**: Placeholder for payment method selection.
- **Summary**: Automatic calculation of taxes and totals.

### Orders
- **History**: View list of past rental orders.
- **Details**: Detailed view of specific orders including status and items.

## 📝 Notes

- **Sessions**: The cart uses server-side Laravel sessions, ensuring data persists across different devices if logged in (depending on session driver), and is more secure than client-side storage.
- **Assets**: Images are served from the `public/` directory.
- **Tailwind**: implementation uses the CDN script for simplicity in this demo environment. For production, a build step with Vite is recommended.


