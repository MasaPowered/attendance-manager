FROM richarvey/nginx-php-fpm:3.1.6
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY . /var/www/html

# イメージ専用の環境変数で Laravel 用の設定をオンにする
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# ★ここが重要！URL書き換えを有効にする設定
ENV ERRORS_404_PAGE /index.php
ENV nginx_conf_file /etc/nginx/sites-available/default.conf
ENV APP_ENV production

# 権限の付与
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 必要なパッケージのインストール
RUN composer install --no-dev --optimize-autoloader

# 起動コマンド（db:seedは一度成功していれば外してもOKですが、念のため継続）
CMD php artisan migrate:fresh --force && php artisan db:seed --force && /start.sh