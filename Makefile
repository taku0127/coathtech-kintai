init:
	docker-compose up -d --build
	docker-compose exec php composer install
	docker-compose exec php cp .env.example .env
	docker-compose exec php php artisan key:generate
	@make fresh
	chmod -R 777 ./*
	@make npm-setup

fresh:
	docker compose exec php php artisan migrate:fresh --seed

npm-setup:
	cd src && npm install

npm-watch:
	cd src && nohup npm run watch > watch.log 2>&1 & echo $$! > watch.pid

stop-watch:
	kill `cat watch.pid` && rm watch.pid
