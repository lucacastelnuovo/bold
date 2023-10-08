<h1 align="center">
  lucacastelnuovo/bold
</h1>

### Todo

-   [ ] Get user locks from bold API
-   [ ] Only display logs for the locks the user has access to
-   [ ] Specify which locks the link has access to
-   [ ] Specify which locks the api token has access to

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

echo
echo "📂 Go to project directory"
cd {SITE_DIRECTORY}

echo
echo "🚧 Enable maintenance mode"
php artisan down --refresh 30 || true

echo
echo "📚 Pull the repository"
git pull origin {BRANCH}

echo
echo "💾 Install back-end dependencies"
# Remove --no-dev for staging environment
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

echo
echo "💾 Install front-end dependencies"
yarn install --immutable
yarn run build

echo
echo "💣 Restart FPM"
{RELOAD_PHP_FPM}

echo
echo "🧹 Clear caches"
php artisan cache:clear
php artisan config:clear
php artisan event:clear
php artisan route:clear
php artisan view:clear

echo
echo "🪛 Build caches"
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

echo
echo "🔀 Run migrations"
php artisan migrate --force
php artisan db:seed --force --class=PermissionAndRoleSeeder
php artisan db:seed --force --class=CountrySeeder

echo
echo "🏁 Disable maintenance mode"
php artisan up

echo
echo "🚀 Deployment completed!"
php artisan about
```
