# PDPHero

© 2022 Kyle Boehlen, All Rights Reserved

This Laravel PWA is my personal development tool for tracking todos, habits, goals, addictions, journal entries, and more. Currently it's what I'd consider to be the MVP. Native applications, refreshed UI (using a proper front-end framework), and more features are on the roadmap for in the upcoming years.

## Local Development (Docker)

If on Windows verify you are doing the setup with WSL2. This is the bash shell you should be using, and also the Docker driver you should be using.

If it is not installed you can do so by simply running the following command in powershell:

`wsl --install -d "Ubuntu-20.04"`

Then make sure that you have [Docker Desktop](https://www.docker.com/products/docker-desktop) installed and running

Clone the codebase, preferably somewhere in your home directory

`git clone https://github.com/kyleboehlen/pdphero-core.git`

You'll then need to create your .env file by copying the example env file

`cp .env.example.local .env`

The rest of the instructions use the `sail` command. You should alias this command to the following:

`./vendor/bin/sail`

Generate an application key

`sail artisan key:generate`

And fill out the missing variables in the .env file

For logging:

- PAPERTRAIL_PORT=
- LOG_DISCORD_WEBHOOK_URL=""

For SMS testing:

- VONAGE_KEY=""
- VONAGE_SECRET=""
- VONAGE_SMS_FROM=""

For local email testing:

- MAILTRAP_USERNAME=
- MAILTRAP_PASSWORD=

For stripe testing, make sure you use the api keys and prices from the test console:

- STRIPE_KEY=
- STRIPE_SECRET=
- BASIC_STRIPE_PRICE_ID=
- BLACK_LABEL_STRIPE_PRICE_ID=

And generate a vapid key

`sail artisan webpush:vapid`

Before starting the Docker container install the composer packages, once you have the container running you may want to run a composer update if any of the local path packages didn't install correctly

```
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs
```

Finally go ahead and start the container

`sail up -d`

Once it's up you can migrate and seed the database

`sail artisan migrate && sail artisan db:seed`

Or alias the deploy unit test and run it

`alias vendor_phpunit=vendor/phpunit/phpunit/phpunit`

`vendor_phpunit --filter Deploy`

You'll also need to generate the assets

`sail npm run dev`

And create a user for the Nova dashboard

`sail artisan nova:user`

Lastly, you'll need to go to `localhost:9000` to access the MinIO console

Login using sail as the username and password as the password and create a bucket named `local` with a Public access policy

## Production (Digital Ocean App Platform)

The digital ocean app platform is set up to track the master branch, use the .env.example.production file to create the enviroment variables for the application.

For staging app platform is set up to track the staging branch, and the only difference for set up is to set the `APP_ENV` to 'staging' and you can enable `APP_DEBUG` if you'd like. For registration you may have to set `ALPHA_GUARD` to false.

You'll need to generate an `APP_KEY` elsewhere otherwise the build process would change it every time. You'll also want to generate the vapid keys `VAPID_PUBLIC_KEY` and `VAPID_PRIVATE_KEY` in the same way.

A managed database instance is also needed, in the DB .env variables be sure to replace `YOUR_DATABASE_COMPONENT` with the name of your managed DB

You'll also need to get the `PAPERTRAIL_PORT` from your papertrail account, and the `LOG_DISCORD_WEBHOOK_URL` from the integrations setting page in your discord server

You'll also need to create a S3 compatible space for assets, generate a space API key in the Digital Ocean web console to fill out `DO_ACCESS_KEY_ID` and `DO_SECRET_ACCESS_KEY`, as well as replace `YOUR_REGION` with whatever region the space is in

`DO_BUCKET` is simply the name of the space component

For SMS fill get the following enviroment variables from the Nexmo portal

- VONAGE_KEY=""
- VONAGE_SECRET=""
- VONAGE_SMS_FROM=""

For email you'll want to set up mailgun as primary, and SES as a fallback. You'll need to get the following enviroment variables

- MAILGUN_SECRET=""
- SES_ACCESS_KEY_ID=""
- SES_ACCESS_KEY_SECRET=""

And for stripe make sure you're using the production keys and prices

- STRIPE_KEY=
- STRIPE_SECRET=
- BASIC_STRIPE_PRICE_ID=
- BLACK_LABEL_STRIPE_PRICE_ID=

Once you set the build command to 

`npm install && npm run prod`

And the run command to 

```
php artisan migrate --force &&
php artisan db:seed --force &&
php artisan config:cache &&
php artisan route:cache &&
php artisan assets:sync-static &&
php artisan purge:profile-pictures &&
php artisan storage:link &&
heroku-php-apache2 public/
```

You're good to go👍