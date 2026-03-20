
## What this project is

- Laravel application (current setup uses Laravel 11+, PHP 8.2+)
- Role-based user administration (Admin, Super Admin, User, secondary user)
- Dealer and manufacturer management
- File/media uploads, categories and tags, user relation mappings
- Search and API endpoints for countries/states/cities/dealers/manufacturers

## Setup instructions

1. Clone repository:
   ```bash
   git clone <your-url>
   ```
2. Copy environment file:
   ```bash
   cp .env.example .env
   ```
3. Update `.env` values with your own database and API settings
4. Install dependencies:
   ```bash
   composer install
   npm install
   npm run build
   ```
5. Generate app key:
   ```bash
   php artisan key:generate
   ```
6. Run migrations and seed sample data:
   ```bash
   php artisan migrate --seed
   ```
7. Start local server:
   ```bash
   php artisan serve
   ```

## Notes for a portfolio repository

- Keep no real client info: names, domains, company emails, internal systems
- Prefer default demo users and factories for content
- Add tests for critical operations and endpoints before sharing

## Git commands to push

```bash
git init
git add .
git commit -m "Sanitize and prepare demo project"
git branch -M main
git remote add origin <your-git-url>
git push -u origin main
```

## License

MIT

- Secrets removed from source and now stored in `.env`
- `.env.example` contains all required keys with placeholders
- Client/brand names replaced with generic values
- No hardcoded passwords or production API keys
- Logs and compiled view caches cleaned

## Tech Stack

- PHP 8.2+ and Laravel
- MySQL
- Mailgun/SMTP (config via `.env`)

## Setup

1. `git clone <repo>`
2. `cd directoy`
3. `cp .env.example .env`
4. Fill placeholders in `.env`
5. `composer install`
6. `npm install && npm run build`
7. `php artisan key:generate`
8. `php artisan migrate --seed`
9. `php artisan serve`

## Configuration

- `APP_NAME` set to `DemoApp`
- `DB_*` values in `.env`
- `MAILGUN_*`, `RECAPTCHA_*`, `WEB_URL` in `.env`

