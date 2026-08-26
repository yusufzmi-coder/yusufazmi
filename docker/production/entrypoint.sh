#!/bin/sh
set -e

echo "Menunggu pangkalan data…"
until pg_isready -h "${DB_HOST:-postgres}" -p "${DB_PORT:-5432}" -U "${DB_USERNAME:-jadual}" >/dev/null 2>&1; do
    sleep 2
done

php artisan migrate --force

# Base data only -- never the demo seeder in production.
php artisan db:seed --class=Database\\Seeders\\RoleSeeder --force
php artisan db:seed --class=Database\\Seeders\\AcademicSessionSeeder --force
php artisan db:seed --class=Database\\Seeders\\TimeSlotSeeder --force
php artisan db:seed --class=Database\\Seeders\\RuleSeeder --force

# Creates the first Super Admin from ADMIN_EMAIL/ADMIN_PASSWORD, but only while the
# users table is empty -- a redeploy must never resurrect a removed account.
php artisan db:seed --class=Database\\Seeders\\FirstAdminSeeder --force

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link || true

exec "$@"
