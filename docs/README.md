# Aybar System Documentation

## Overview
Aybar is a web application built with **Laravel 12** and **Livewire 4**. The system focuses on robust backend logic with a dynamic frontend using Livewire, styled with custom CSS.

## Technology Stack

### Backend
-   **Framework**: Laravel 12
-   **Language**: PHP 8.2+
-   **Authentication**: Laravel Fortify (Standard starter kit implementation)

### Frontend
-   **Interactive UI**: Livewire 4
-   **Scripting**: Alpine.js
-   **Styling**: Custom CSS (Vanilla)
    -   *Note*: The project does **not** use TailwindCSS or Flux UI. Any existing references to these in the initial setup are to be ignored or removed.

### Database
-   **Engine**: MySQL
-   **ORM**: Eloquent

## Key Features
-   **Authentication**: Complete flow for Login, Registration, Password Reset, and Email Verification.
-   **User Dashboard**: Protected area for authenticated users.
-   **User Settings**:
    -   Profile Information Update
    -   Password Management
    -   Two-Factor Authentication (2FA)

## Project Structure

### `app/Livewire`
Contains the frontend logic components.
-   `Settings/`: Components for user profile and security settings.
-   `Auth/`: Authentication related components.

### `resources/views`
Contains the Blade templates.
-   `livewire/`: Views corresponding to Livewire components.
-   `layouts/`: Main application layouts.

### `routes`
-   `web.php`: Main application routes.
-   `settings.php`: Routes specifically for user settings and account management.
