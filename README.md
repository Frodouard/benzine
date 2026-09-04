# MICKY SHOP Marketplace

A simple PHP/MySQL online marketplace for MICKY SHOP with customer and admin features.

## Setup
1. Create a MySQL database named `mickyshop`.
2. Import `db/schema.sql`.
3. Update `inc/db.php` with your database credentials.
4. Place the project in a PHP-enabled web server root.
5. Open `index.php` in your browser.

## Features
- Customer home, products, search, cart, checkout, account registration/login
- Admin login, product CRUD, order management, delivery status, messages, exports
- Secure password hashing with `password_hash()` and `password_verify()`

## Notes
- Use `admin/login.php` to access the admin area.
- The schema includes `users`, `products`, `orders`, `order_items`, `messages`, and `categories`.
