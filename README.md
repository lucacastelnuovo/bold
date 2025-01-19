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

### How to get an API token?

1. `php artisan app:create-user "Luca Castelnuovo" luca@castelnuovo.dev`
2. `php artisan app:create-token 1 "iPhone van Luca" lock.activate share.create activity.view`
3. `php artisan app:create-token 1 "MacBook van Luca" user.tokens.update lock.sync lock.activate share.create activity.view`

### How to get an Bold token?

1. Start BurpSuite
2. Add `https://boldsmartlock.com` (including subdomain) to scope
3. Filter to only show scope
4. Start Burp Browser
5. Open: https://portal.boldsmartlock.com/
6. Login with your Bold credentials
7. From "HTTP History" copy `access_token` & `refresh_token`
   <img width="1429" alt="burpsuite" src="https://github.com/user-attachments/assets/3ef038fe-f33c-4dd5-a0d5-ad010c407a33" />

### How to store the Bold token using the API token?

> todo

### Forge

```sh
ENVIRONMENT=""

echo "============================"
echo "Branch: $FORGE_SITE_BRANCH"
echo "Commit: $FORGE_DEPLOY_COMMIT"
echo "============================"

echo
echo "--- Goto Directory ---"
cd $FORGE_SITE_PATH

echo
echo "--- Enable Maintenance Mode ---"
if [ -f artisan ]; then
    $FORGE_PHP artisan down --render errors.503 --refresh 30 || true
fi

echo
echo "--- Checkout Repository ---"
git fetch --all --tags --force
git reset --hard origin/$FORGE_SITE_BRANCH

echo
echo "--- Install Front-End Dependencies ---"
npm ci

echo
echo "--- Install Back-End Dependencies ---"
$FORGE_COMPOSER install --no-interaction --prefer-dist --optimize-autoloader --no-dev

echo
echo "--- Build Front-End ---"
npm run build

echo
echo "--- Setup .env ---"
$FORGE_PHP artisan env:decrypt --force --filename=.env --env=$ENVIRONMENT

echo
echo "--- Restart FPM ---"
(
    flock -w 10 9 || exit 1
    sudo -S service $FORGE_PHP_FPM reload
) 9>/tmp/fpmlock

echo
echo "--- Run Migrations ---"
$FORGE_PHP artisan migrate --force

echo
echo "--- Optimize ---"
$FORGE_PHP artisan optimize

echo
echo "--- Disable Maintenance Mode ---"
$FORGE_PHP artisan up

echo
echo "--- Deployment Complete ---"
$FORGE_PHP artisan about
```
