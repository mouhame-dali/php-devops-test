# Php devops technical test...

This project provides a secure user authentication system using JWT (JSON Web Token). It includes API endpoints for user login, where users can login with their email and password and receive a JWT token for subsequent authentication.
To optimize routing, nikic/fast-route is used for efficient and fast route handling. For validation of user input, the illuminate/validation package is integrated to ensure data integrity and security when handling user registration and login forms. Error handling is managed through filp/whoops, which provides a detailed and user-friendly error reporting system, making it easier to debug during development.

## Features

- User authentication with JWT (JSON Web Token)
- Secure login and signup API endpoints
- Password hashing for security

## Prerequisites

Ensure you have the following installed before running the project:

- PHP 8+
- Composer
- Docker
## Installation

### Clone the Repository

```bash
git clone https://github.com/mouhame-dali/php-devops-test.git
cd php-devops-test
docker-compose up --build -d
# API Documentation

This project exposes the following API endpoints for user authentication.
---

## API Endpoints

### GET `http://127.0.0.1/api/uuid`
### POST `http://127.0.0.1/api/login`
#### Request Body
```json
{"username":"admin","password":"secret"}
### POST `http://127.0.0.1/api/protected`
#### Header Body
Authorization: Bearer <your-jwt-token-here>

