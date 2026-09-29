# Fyndora — E-commerce Web Application

Fyndora is a full-stack e-commerce web application built with Laravel and PHP. It provides a customer-facing shopping experience with product browsing, product search, shopping cart management, checkout, and M-Pesa payment integration through Safaricom's Daraja API.

The project also includes an authenticated administration area for managing products, customers, and sales.

## Features

### Customer Shopping

- Browse available products
- Search products by name
- Case-insensitive product search
- Multi-word product search
- Add products to the shopping cart
- Update cart quantities
- Remove products from the cart
- Review the cart before checkout
- Customer checkout form
- Responsive storefront interface
- Payment success and payment failure feedback

### M-Pesa Payment Integration

Fyndora integrates with Safaricom's Daraja API to demonstrate an M-Pesa STK Push payment workflow.

The payment flow includes:

1. Customer proceeds to checkout.
2. Customer provides their name and M-Pesa phone number.
3. The application validates and formats the phone number.
4. Fyndora requests an M-Pesa STK Push.
5. The customer completes the payment on their phone.
6. The application receives the Daraja callback.
7. Payment transaction details are stored in the database.
8. The application checks the payment status and displays the appropriate result.
9. The cart is cleared after a successful payment.

> **Note:** The application uses the Daraja sandbox environment for payment testing. Production credentials should be configured separately and must never be committed to the repository.

### Administration

The application includes an authenticated administration area with functionality for:

- Admin dashboard
- Product management
- Creating products
- Editing products
- Deleting products
- Viewing customers
- Viewing sales

Public user registration is disabled, while administrative functionality is protected by authentication.

## Technology Stack

| Technology | Purpose |
|---|---|
| PHP 8.2+ | Backend programming language |
| Laravel 12 | Application framework |
| Laravel Eloquent | Database ORM |
| Laravel UI | Authentication scaffolding |
| Blade | Server-side templating |
| Bootstrap 5 | User interface and responsive layout |
| Tailwind CSS | Frontend styling utilities |
| Vite | Frontend asset development and build tooling |
| JavaScript | Client-side interactions |
| PostgreSQL / MySQL | Database support |
| Safaricom Daraja API | M-Pesa payment integration |
| Git & GitHub | Version control |

## Application Architecture

Fyndora follows Laravel's MVC architecture.

```text
Browser
   │
   ▼
Laravel Routes
   │
   ▼
Controllers
   │
   ├── ProductController
   │       ├── Product browsing
   │       ├── Search
   │       ├── Cart
   │       └── Checkout
   │
   ├── AdminController
   │       ├── Product management
   │       ├── Customer management
   │       └── Sales
   │
   └── DarajaController
           ├── STK Push
           ├── Payment callback
           └── Payment status
   │
   ▼
Eloquent Models
   │
   ▼
Database
```

## Main Application Routes

### Storefront

```text
GET  /                    Product listing and search
GET  /cart                Shopping cart
POST /add-to-cart/{id}    Add product to cart
GET  /remove-from-cart/{id}
GET  /checkout             Checkout
POST /update-cart/{id}     Update cart quantity
```

### Administration

```text
GET     /admin
GET     /admin/products
GET     /admin/customers
GET     /admin/sales
GET     /admin/create
POST    /admin/store
GET     /admin/edit/{id}
POST    /admin/update/{id}
DELETE  /admin/delete/{id}
```

### M-Pesa / Daraja

```text
POST /initiate-stk
POST /mpesa/callback
GET  /check-payment-status
```

## Project Structure

```text
fyndora/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AdminController.php
│   │       ├── DarajaController.php
│   │       └── ProductController.php
│   └── Models/
│
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
│
├── resources/
│   └── views/
│       ├── admin/
│       ├── auth/
│       ├── layouts/
│       └── shop/
│
├── routes/
│   └── web.php
│
├── public/
├── config/
├── tests/
├── composer.json
├── package.json
└── README.md
```

## Local Installation

### Requirements

Before installing Fyndora, make sure the following are available:

- PHP 8.2 or later
- Composer
- Node.js and npm
- MySQL or PostgreSQL
- Git

### Clone the repository

```bash
git clone https://github.com/engugi254/fyndora.git

cd fyndora
```

### Install PHP dependencies

```bash
composer install
```

### Configure the environment

Create the environment file:

```bash
cp .env.example .env
```

Generate the Laravel application key:

```bash
php artisan key:generate
```

Configure the database credentials in `.env`.

Then run the migrations:

```bash
php artisan migrate
```

### Install frontend dependencies

```bash
npm install
```

Build the frontend assets:

```bash
npm run build
```

### Start the application

```bash
php artisan serve
```

For frontend development, Vite can be started with:

```bash
npm run dev
```

The application will then be available through the local Laravel development server.

## M-Pesa Configuration

To use the Daraja sandbox integration, configure the required credentials in `.env`.

Example:

```env
MPESA_CONSUMER_KEY=
MPESA_CONSUMER_SECRET=
MPESA_SHORTCODE=
MPESA_PASSKEY=
MPESA_CALLBACK_URL=
```

**Never commit real API credentials, passwords, or other secrets to GitHub.**

The application currently uses the Safaricom Daraja sandbox endpoints for development and testing.

## Security Considerations

The project demonstrates several basic application security practices:

- Authentication for administrative functionality
- Public registration disabled
- Environment variables for API credentials
- Laravel request validation
- Protected admin routes
- Database-backed transaction records
- Separation of application secrets from source code

For a production deployment, additional hardening would be required, including comprehensive authorization policies, rate limiting, monitoring, secure production payment credentials, HTTPS enforcement, automated testing, and production-grade error handling.

## Future Improvements

Potential improvements for a production-ready version include:

- Customer accounts and order history
- Product categories
- Product images and multiple product images
- Product variants such as size and color
- Stock/inventory management
- Advanced product filtering
- Customer dashboards
- Order tracking
- Email notifications
- Improved administrative reporting
- Automated tests
- Role-based administrative permissions
- Production payment configuration
- Enhanced security monitoring

## What This Project Demonstrates

Fyndora was developed as a practical full-stack project to demonstrate the ability to build and connect multiple parts of a web application.

The project demonstrates experience with:

- Laravel MVC development
- PHP backend development
- REST-style application routes
- Database-driven applications
- Eloquent ORM
- Authentication and protected routes
- CRUD operations
- Shopping cart functionality
- Server-side product search
- Form validation
- Session-based application state
- Payment API integration
- M-Pesa STK Push
- Payment callbacks
- Transaction persistence
- Responsive web interfaces
- Git and GitHub version control
- Application deployment and environment configuration

## Author

**Edward Mutahi Ngugi**

Software Engineer / Full-Stack Developer

GitHub: [https://github.com/engugi254](https://github.com/engugi254)

Portfolio: [https://engugi254.github.io/Portfolio](https://engugi254.github.io/Portfolio)

---

