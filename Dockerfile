FROM richarvey/nginx-php-fpm:3.1.6

COPY . /var/www/html

# 作成した nginx.conf をサーバーの設定場所に上書きします
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Laravelの設定
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# 権限の付与
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 必要なパッケージのインストール
RUN composer install --no-dev --optimize-autoloader

# 起動時にマイグレーションを自動実行するコマンドを追加
CMD php artisan migrate --force && php artisan db:seed --class=AdminSeeder --force && /start.sh