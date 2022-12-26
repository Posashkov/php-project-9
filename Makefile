install:
	composer install

lint:
	composer exec --verbose phpcs -- --standard=PSR12 public
	composer exec --verbose phpstan -- --level=8 analyse public

lint-fix:
	composer exec --verbose phpcbf -- --standard=PSR12 public src tests

PORT ?= 8000
start:
	php -S 0.0.0.0:$(PORT) -t public
