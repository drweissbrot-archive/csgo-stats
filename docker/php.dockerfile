FROM php:8.0-fpm
EXPOSE 9000

COPY . /var/www/html
COPY ./docker/php.ini $PHP_INI_DIR/conf.d/php.ini

WORKDIR /tmp
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
	&& curl -fsSL https://deb.nodesource.com/setup_17.x | bash - \
	&& apt-get update \
	&& apt-get upgrade -y \
	&& apt-get install libicu-dev nodejs -y \
	&& curl -s http://getcomposer.org/installer | php \
	&& echo "export PATH=${PATH}:/var/www/vendor/bin" >> ~/.bashrc \
	&& mv composer.phar /usr/local/bin/composer \
	&& docker-php-ext-install -j$(nproc) intl pdo_mysql \
	&& echo "alias art='php artisan'" >> ~/.bashrc \
	&& echo "alias tinker='php artisan tinker'" >> ~/.bashrc

WORKDIR /var/www/html
