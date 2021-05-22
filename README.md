## DEV

### PHP

php:7.4-fpm-buster

composer:1.10

node:14

### nginx

nginx:1.18-alpine

### mysql

mysql:5.7

### ngrok

こちらのサイトで authtoken を取得してください。
https://dashboard.ngrok.com/get-started/setup

そのトークンを、`.env`に入れてください。

## Usage

```
$ git clone git@github.com:ssk9597/Docker-php-node-nginx-mysql-ngrok.git
$ cd Docker-php-node-nginx-mysql-ngrok
$ make install
```

http://127.0.0.1:10080/

## Connect Sequel Pro

Often uses 3306 ports.

【Change】docker-compose.yml

```
# MySQL
  db:
    build: ./docker/mysql
    volumes:
      - db-data:/var/lib/mysql
    ports:
      <!-- To any number -->
      - 3306:3306
```
