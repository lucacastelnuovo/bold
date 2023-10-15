<h1 align="center">
  lucacastelnuovo/bold
</h1>

### Todo

-   [ ] Implement Jetstream Teams and call it Homes
-   [ ] Configure Bold Credentials on Homes
-   [ ] Get Locks in Home from Bold API

-   [ ] Show all Locks Activity in Home

-   [ ] Link is scoped per home
-   [ ] API token is scoped per home

-   [ ] Store a signing key per Home
-   [ ] Allow rotating the signing key per Home (and invalidate all links)
-   [ ] This action should also invalidate all API keys for that home

### How to get an Bold Bearer token?

1. Goto https://auth.boldsmartlock.com/
2. Login with your Bold account
3. Open the developer tools and goto the network tab
4. Click on the `account` request
5. Copy the `Authorization` header from the request headers

### Local Installation

```sh
# Clone repository
git clone https://github.com/lucacastelnuovo/bold && cd bold

# Configure Herd
herd secure

# Install dependencies
composer install && yarn install

# Copy .env.example to .env
cp .env.example .env

# Generate a new application key
php artisan key:generate

# Run migrations and seeders
php artisan migrate:fresh --seed

# Watch front-end assets
yarn run dev

# Open app
open https://bold.test
```

### Ploi Installation

```sh
echo "============================"
echo "Repo  : {REPOSITORY_NAME}"
echo "Branch: {BRANCH}"
echo "Commit: {COMMIT_HASH}"
echo "============================"

export APP_ENV="production"
# export LARAVEL_ENV_ENCRYPTION_KEY=""

echo
echo "--- Goto Directory ---"
cd {SITE_DIRECTORY}

echo
echo "--- Enable Maintenance Mode ---"
if [ -f artisan ]; then
    php artisan down --refresh 30 || true
fi

echo
echo "--- Pull Changes ---"
git pull origin {BRANCH}

echo
echo "--- Install Front-End Dependencies ---"
yarn install --immutable
yarn run build

echo
echo "--- Install Back-End Dependencies ---"
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

echo
echo "--- Restart FPM ---"
{RELOAD_PHP_FPM}

echo
echo "--- Decrypt .env ---"
php artisan env:decrypt --force --filename=.env --env=$APP_ENV

echo
echo "--- Clear Caches ---"
php artisan cache:clear
php artisan config:clear
php artisan event:clear
php artisan route:clear
php artisan view:clear

echo
echo "--- Rebuild Caches ---"
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

echo
echo "--- Run Migrations ---"
php artisan migrate --force

echo
echo "--- Disable Maintenance Mode ---"
php artisan up

echo
echo "--- Deployment Complete ---"
php artisan about
```
