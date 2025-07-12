# PHP ERP System - Purewood

A comprehensive ERP system built with PHP for managing leads, quotations, customers, and proforma invoices.

## Features

- **Lead Management** - Create and manage business leads
- **Quotation System** - Generate quotations with approval workflow
- **Customer Management** - Track customer interactions and history
- **PI (Proforma Invoice)** - Automatic PI generation from locked quotations
- **Multi-user Access** - Different admin roles (Super, Sales, Accounts, Operations, Production)
- **Export Functionality** - PDF and Excel export for quotations and PIs
- **Email Integration** - Send quotations and PIs via email

## Installation

1. Clone the repository
2. Copy `config/config.example.php` to `config/config.php`
3. Update database credentials in `config/config.php`
4. Import the database schema
5. Configure your web server to point to the project directory

## Database Setup

Create the following tables in your MySQL database:
- leads
- quotations
- quotation_products
- pi
- quotation_status

## Admin Access

Default admin credentials will be set up during installation.

## Modules

- **Lead Module** - `/modules/lead/`
- **Quotation Module** - `/modules/quotation/`
- **Customer Module** - `/modules/customer/`
- **PI Module** - `/modules/pi/`

## Requirements

- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx web server

## License

Proprietary - Purewood Company