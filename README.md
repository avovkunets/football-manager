# Football Manager API

This project is a RESTful API for managing football teams and players, built with Symfony and API Platform.

## Features

- **CRUD Operations:** Manage Teams and Players via a RESTful API.
- **Business Logic:**
    - A team can have a maximum of 11 players.
    - Cascade deletion: deleting a team removes all its players.
    - Notification system: when a team's city is updated, notifications are logged.
- **Testing:** Includes unit tests and functional tests to ensure reliability.
- **API Documentation:** Automatically generated via API Platform (available at `/api/docs`).

## Installation

### Requirements

- PHP >= 8.2
- Composer

### Setup Steps

1. Clone the repository:
   - git clone <repository_url>
   - cd football-manager

2. Configure the environment: Ensure your `.env` file is set correctly, for example:

        ###> symfony/framework-bundle ###
        APP_ENV=dev
        APP_SECRET=secret
        APP_RUNTIME_ENV=dev
        APP_RUNTIME_MODE=web
        ###< symfony/framework-bundle ###
        
        ###> doctrine/doctrine-bundle ###
        DATABASE_URL="pgsql://root:root@db:5432/football_manager"
        ###< doctrine/doctrine-bundle ###
        
        ###> symfony/messenger ###
        MESSENGER_TRANSPORT_DSN=doctrine://default
        ###< symfony/messenger ###

3. Install dependencies:
   - composer install

4. Run Database Migrations:
   - bin/console doctrine:database:create
   - bin/console doctrine:migrations:migrate

## Usage
### API Endpoints
    Teams: /api/teams
    Players: /api/players

You can test these endpoints using the provided `api.http` file or any REST client.

_Note: The {{baseUrl}} variable should be set to your API URL (e.g. http://football-manager.local)._

## Testing
To run unit and functional tests, execute: `vendor/bin/phpunit`

## Logging
General Logs: Located in var/log/dev.log
Notifications Logs: Dedicated logs for notifications are written to var/log/notifications.log 

