# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Environment
- Project runs in Docker containers
- All services (web, database, etc.) are containerized
- Run commands using Docker Compose: `docker compose exec laravel.test <command>`
- Tests use Pest PHP testing framework (https://pestphp.com/)
- Use .env.testing file for test configuration
- Test database credentials are configured in .env.testing file

## Commands
- **Install**: `composer install && npm install`
- **Build**: `npm run dev` or `npm run prod`
- **Test (all)**: `./vendor/bin/pest`
- **Test (single)**: `./vendor/bin/pest tests/Path/To/TestFile.php`
- **Test (filter)**: `./vendor/bin/pest --filter=TestName`
- **Lint**: `./vendor/bin/phpstan analyse`

## Code Style
- **PSR-12** standard for PHP formatting
- Models: Use builders for complex object creation
- Controllers: Keep thin, delegate to services
- Services: Namespace by feature (e.g., `PaymentService/`)
- Type hints: Use everywhere (properties, parameters, return types)
- Use repositories for database interaction logic
- DTOs for data transfer between layers
- Namespaced Enums with string values (`SmsTypeEnum::class`)
- Meaningful exception handling with specific exception classes
- Use facades for service access (`SettingsServiceFacade::class`)
- Dependency injection through constructors
- Follow Laravel conventions for route naming and controller methods