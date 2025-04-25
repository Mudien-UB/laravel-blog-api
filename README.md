# Blog Application – RESTful API Backend

A simple yet powerful REST API for a Blog Application built with Laravel 12, featuring JWT authentication and cloud image storage.


## Tech Stack

- **Framework:** Laravel 12  
- **Authentication:** Tymon JWT  
- **Image Storage:** Cloudinary  


## Features

- JWT-based Authentication (Login, Register, Logout)
- CRUD Blog Posts with image upload support
- Cloudinary integration for seamless image storage
- JSON responses for all API endpoints


## Requirements

- PHP >= 8.2  
- Composer  
- Laravel 12  
- Cloudinary Account  
- MySQL / PostgreSQL  
- Postman or any API testing tool


## Installation

```bash
# Clone the repository
git clone https://github.com/Mudien-UB/laravel-blog-api.git
cd blog-api-laravel

# Install PHP dependencies
composer install

# Install JWT authentication package
composer require tymon/jwt-auth

# Copy environment file and generate app key
cp .env.example .env
php artisan key:generate

# Generate JWT secret key
php artisan jwt:secret

# Install Cloudinary service provider
composer require cloudinary-labs/cloudinary-laravel

# Run database migrations
php artisan migrate

# Start the development server
php artisan serve
```

## Cloudinary Configuration
Tambahkan konfigurasi berikut ke dalam file .env:
```env
CLOUDINARY_CLOUD_NAME=your_cloud_name  
CLOUDINARY_API_KEY=your_api_key  
CLOUDINARY_API_SECRET=your_api_secret
```
## Penutup / Closing

This repository is made for learning purposes and exploring how to build a RESTful API with Laravel 12.
Hope it helps and serves as a good reference for fellow learners.

Terima kasih dan selamat belajar!  
Thank you and happy coding!

**Dont forget a cup coffee for today**

___
> **NB:** If you want to add Postman Collection or Swagger documentation later, just tell me, I can help generate or structure it too.


---
