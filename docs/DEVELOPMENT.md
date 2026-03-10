# Local Development Guide

## Environment Setup
1. **PHP**: 8.1+
2. **Database**: MySQL 8.0 / MariaDB 10.6
3. **Queue**: Redis (or MySQL for local dev)

## Setup Steps
1. Clone the repository.
2. Initialize environment:
   ```bash
   php init --env=Development
   ```
3. Configure `common/config/main-local.php` with your DB credentials.
4. Run migrations:
   ```bash
   php yii migrate
   ```
5. Seed initial data:
   ```bash
   php yii seed/students 50
   ```

## Running Daily Tasks
- **Stats Calculation**: `./yii stats/optimize`
- **Queue Worker**: `./yii queue/listen` or `make worker`

## Testing Workflow
### Coding Standards
Run the linter before every commit:
```bash
make lint
```
Auto-fix formatting:
```bash
make fix
```

### Static Analysis
Catch potential type errors and unreachable code:
```bash
make analyze
```

### Running Tests
Execute the full test suite:
```bash
make test
```
Or just functional tests for faster cycles:
```bash
make test-fast
```
