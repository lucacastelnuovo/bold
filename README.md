<h1 align="center">
  lucacastelnuovo/bold
</h1>

### Installation

```sh
# Clone repository
git clone https://github.com/lucacastelnuovo/bold && cd bold

# Configure Herd
herd secure

# Install dependencies
npm install && npm run build
composer install

# Copy .env.example to .env
cp .env.example .env

# Generate a new application key
php artisan key:generate

# Run migrations and seeders
php artisan migrate:fresh --seed

# Run dev
composer dev

# Open app
herd open
```

### How to get an Bold Bearer token?

1. Start BurpSuite
2. Add `https://boldsmartlock.com` (including subdomain) to scope
3. Filter to only show scope
4. Start Burp Browser
5. Open: https://portal.boldsmartlock.com/
6. Login with your Bold credentials
7. From "HTTP History" copy `access_token` & `refresh_token`
<img width="1429" alt="burpsuite" src="https://github.com/user-attachments/assets/3ef038fe-f33c-4dd5-a0d5-ad010c407a33" />
