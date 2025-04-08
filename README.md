# coathtech-kintai

## 環境構築

- Docker ビルド
  1. git clone git@github.com:taku0127/coathtech-kintai.git
  2. cd coathtech-kintai/
  3. make init
     ※MySQL は、OS によって起動しない場合があるのでそれぞれの PC に合わせて docker-compose.yml ファイルを編集してください。
- Laravel 環境構築
  1. 上記のmake initで設定済み
- sass の仕様
  1. make npm-watch
  4. src/resources/scss/配下で編集
- テストの実行
  1. docker-compose exec mysql mysql -u root -p
  2. CREATE DATABASE demo_test;
  3. テスト用のenvファイルは .env.testing.example ファイルから.env.testing をコピーしてください。
  4. docker-compose exec php bash
  5. php artisan key:generate --env=testing
  6. php artisan config:clear
  7. php artisan migrate --env=testing
  8. php artisan test --testsuite=Feature (全テストの実行)

## 使用技術(実行環境)

- Laravel 8.83
- PHP 7.4
- MySQL 8.0
- nginx 1.21.1
- mailhog
- sass 1.83.4


## ER 図
![ir drawio](https://github.com/user-attachments/assets/b7bd2db0-aa03-4781-a503-1268a8fc2c86)


## URL

- 開発環境：http://localhost/
- 開発環境(phpmyadmin)：http://localhost:8080/
- mailhog(メール受信テスト用): http://localhost:8025/
