# Project Name

User Management System API

## Description

The project entails developing a User Management System API using Laravel. This API will facilitate various operations related to user profiles within an application, including creating, updating, viewing, and deleting users.

## Table of Contents

- [Installation](#installation)
- [Database Setup](#database-setup)
- [Running the Server](#running-the-server)
- [Running Tests](#running-tests)

## Installation

1. Clone the repository to your local machine:

   `git clone https://github.com/cybernerdie/apexnetwork-user-management-system-api.git`

2. Navigate to the project directory:

    `cd apexnetwork-user-management-system-api`
   
3. Install dependencies:

   `composer install`

4. Copy the `.env.example` file to `.env` and configure your environment variables:
   
    `cp .env.example .env`

5. Generate application key:

    php artisan key:generate

6. Set up Passport:

   `php artisan passport:install`

## Database Setup

1. Create a new database.
   
2. Update the `.env` file with your database credentials:

   `DB_CONNECTION=mysql`  
   `DB_HOST=127.0.0.1`  
   `DB_PORT=3306`  
   `DB_DATABASE=your_database_name`  
   `DB_USERNAME=your_database_username`  
   `DB_PASSWORD=your_database_password`  
   
3. Run migrations to create the necessary tables:

    `php artisan migrate`

4. Seed the database with sample data:

    `php artisan db:seed`

## Running the Server

1. To start the server, run:
   
   `php artisan serve`

## Running Tests

1. To run tests, execute:

    `php artisan test`









