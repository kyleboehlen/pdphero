# PDPHero (Alpha Dev)

## Installation
Before installing the site the following tools need to be installed:
- php7.4 or higher with the extensions (including php-memcached)
- apache2
- MySQL (MariaDB)
- git
- composer (added to the PATH)
- npm

<br/>
Create an ssh key (remember to copy it to root) and add it to bitbucket to clone the repo

`cd /var/www/html && git clone git@bitbucket.org:pdphero/pdphero.git`

<br/>
Install the required depdendencies

`cd /var/www/html/pdphero && composer install`

`npm install`

<br/>
Generate the .css and .js files

`npm run prod`

<br/>
Create a copy of the enviroment file from the template

`cp .env.example .env`

<br/>
Generate the application encryption key

`php artisan key:generate`

<br/>
Change the apache2 webroot to the laravel public folder
- Change to the apache2 root directory and open the configuration file

   `cd /etc/apache2/sites-available && sudo nano 000-default.conf`
- Edit the document root option to:

   `DocumentRoot /var/www/html/pdphero/public`
- Restart apache2

   `sudo service apache2 restart`

<br/>
In order to allow laravel to handle URLs, make sure the apache mod_rewrite extension is enabled and allow overrides
- Edit apache2.conf to allow overrides

   `cd /etc/apache2/ && sudo nano apache2.conf`
- Add the following to the directory settings

```
   <Directory /var/www/html/pdphero/public>

      Options Indexes FollowSymLinks

      AllowOverride All

      Require all granted

   </Directory>
```

- Enable mod_rewrite extension

   `sudo a2enmod rewrite`
- Restart apache2

   `sudo service apache2 restart`

<br/>
Allow apache to serve the files

`cd /var/www/html && sudo chown -R www-data:{your_user_group} pdphero`

<br/>
Create a symbolic link for the storage folder

`cd /var/www/html/pdphero && php artisan storage:link`

<br/>
Create a nysql database and create a new user to grant all privliages to the database on. Be sure to fill out the DB .env vars

- DB_DATABASE=
- DB_USERNAME=
- DB_PASSWORD=

<br/>
Add the mailgun api details

- MAILGUN_DOMAIN=
- MAILGUN_SECRET=

<br/>
Add the AWS Simple Email Service api details

- SES_ACCESS_KEY_ID=
- SES_ACCESS_KEY_SECRET=
- SES_REGION=

<br/>
Add the discord web hook url for logging

- LOG_DISCORD_WEBHOOK_URL=

<br/>
Add credentials for mailtrap if email testing is required

- MAILTRAP_USERNAME=
- MAILTRAP_PASSWORD=

<br/>
Set the super admin details

- SUPER_ADMIN_EMAIL="admin_email@domain.com"
- SUPER_ADMIN_PASSWORD=password

<br/>
Run the database migration, use the local phpunit

`alias vendor_phpunit=vendor/phpunit/phpunit/phpunit`

`vendor_phpunit --filter Deploy`

<br/>
Change the php.ini file to let Laravel handle file upload sizes

`upload_max_filesize = 0`
`post_max_size = 0`

<br/>
Run crontab -e and add the following line

`* * * * * cd /var/www/html/pdphero && php artisan schedule:run >> /dev/null 2>&1`

<br/><br/>
### _Make sure these steps are completed last_ 

Optimize the autoloader class

   `composer install --optimize-autoloader --no-dev`

<br/>
Cache the configuration

   `php artisan config:cache`


Optimize route loading

   `php artisan route:cache`

<br/><br/>
### Current External Service List:
- Cloudflare (CDN)
- Mailgun (primary email)
- SES (backup email)
- Mailtrap (email testing)
- Papertrail (logging)
- Jira Cloud (issue tracking)
- Bitbucket (code repo host)
- Stripe (payment processing)