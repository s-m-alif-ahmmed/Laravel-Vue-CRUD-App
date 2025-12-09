### This repository contains a CRUD application with:

Backend: Laravel (default branch: backend)

Frontend: Vue 3 (branch: frontend)

### Laravel Project — Setup & Installation Guide

This guide explains how to install, configure, and run the Laravel project after cloning it from GitHub.

1. Clone the Repository
```bash
git clone -b backend --single-branch https://github.com/s-m-alif-ahmmed/Laravel-Vue-CRUD-App.git 
```

2. Install PHP Dependencies

Make sure PHP 8+ and Composer are installed.
```bash
composer install
```
3. Create Environment File

Copy .env.example to .env:

Then update your .env with your database credentials and other settings.

4. Generate Application Key
```bash
php artisan key:generate
```

5. Run Migrations & Seeders
```bash
php artisan migrate --seed
```


### Default Login Credentials after seed:

Email: admin@admin.com

Password: 12345678
