# Gelato Galery BackEnd

Backend application with Bramus Router and Controllers to handle API requests.
Data is saved and loaded from a MariaDB engine.

## Table of Contents

- [Introduction](#introduction)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
- [Routes](#routes)
- [License](#license)

## Introduction

This project is a PHP application built using the Bramus Router library. It provides a set of API endpoints for managing products, handling contacts, and store-related functionalities. The routes are organized under the `/api` prefix.

## Requirements

To run this project, you need to have the following:

- PHP >= 8.0
- Composer (for autoloading)

## Installation

1. Clone this repository to your local machine:

   ```bash
   git clone https://gitlab.com/ikdoeict/timon.claeys2/full-stack-introductory-project/2324fsip-gelatogallery-backend.git

   ## Running and stopping the Docker MCE

* Run the environment, using Docker, from your terminal/cmd
```shell
cd <your-project>
docker-compose up
```
* Stop the environment in your terminal/cmd by pressing <code>Ctrl+C</code>
* In order to avoid conflicts with your lab/slides environment, run from your terminal/cmd
```shell
docker-compose down
```

## Installing Twig, DBAL and bramus/router

The MCE is provided with a `composer.json`/`composer.lock` file, providing the Twig and DBAL libraries
* In order to install, run from your terminal/cmd
```shell
docker-compose exec php-web bash
$ composer install
$ exit
```

## About the autoloader

`composer.json` is configured such that the classes in "src/" (and subfolders), and the files "config/database.php" and "config/app.php" are autoloaded.
* This means there is no need to require these classes anymore in your `public/*.php` scripts.
* You can extend this list yourself in `composer.json`
* When you changed this list, or you created some new classes, let composer know from your terminal/cmd:
```shell
docker-compose exec php-web bash
$ composer dump-autoload
$ exit
```

## Recipes and troubleshooting

### <code>docker-compose up</code> does not start one or more containers
* Look at the output of <code>docker-compose up</code>. When a container (fails and) exits, it is shown as the last line of the container output (colored tags by container)
* Alternatively, start another terminal/cmd and inspect the output of <code>docker-compose ps -a</code>. You can see which container exited, exactly when.
* Probably one of the containers fails because TCP/IP port 8000, 8080 or 3307 is already in use on your system. Stop the environment, change the port in <code>docker-compose.yml</code> en rerun <code>docker-compose up</code>.

Usage
To use the application, make sure to configure your web server to point to the project's root directory. Ensure that the ../vendor/autoload.php file is correctly included.

## Usage

To use the application, make sure to configure your web server to point to the project's root directory. Ensure that the `../vendor/autoload.php` file is correctly included.

## Routes

### Products

- **GET /api/products/ice:** Get a list of ice creams.
- **GET /api/products/cake:** Get a list of ice cakes.
- **GET /api/products/{id}:** Get information about a specific product.
- **POST /api/products:** Add a new product.
- **PUT /api/products/{id}:** Edit an existing product.
- **DELETE /api/products/{id}:** Delete a product.

### Contact

- **POST /api/contact:** Send a contact message.
```typescript
fname   : String
lname   : String
email   : String
adress  : String | null
message : String

```
- **GET /api/order:** Get order information.
- **POST /api/order:** Place a new order.
- **GET /api/truck/order:** Get truck order information.
- **POST /api/truck/order:** Place a new truck order.

### Store

- **GET /api/dashboard:** View the store dashboard.
- **GET /api/calendar:** Get all events.
- **POST /api/calendar:** Update the store calendar.
- **GET /api/calendar/event:** Get details of a specific calendar event.
- **GET /api/popup:** Display a popup.

### Authentication

- **GET /api/login:** Show the login form.(next stage)
- **POST /api/login:** Handle login requests.
- **POST /api/logout:** Log out the user.

## License

This project is licensed under copyright
License - contact the maintainers for details.
