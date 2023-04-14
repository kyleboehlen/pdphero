#!/usr/bin/env bash

# Sync static assets to S3

php artisan storage:link &&
php artisan assets:sync-static &&
php artisan purge:profile-picture

# php artisan assets:purge-deleted