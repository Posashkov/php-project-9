install:
	composer install

lint:
	composer exec --verbose phpcs -- --standard=PSR12 public src tests
	composer exec --verbose phpstan -- --level=8 analyse public src tests

lint-fix:
	composer exec --verbose phpcbf -- --standard=PSR12 public src tests

PORT ?= 8000
start:
	PHP_CLI_SERVER_WORKERS=5 php -S 0.0.0.0:$(PORT) -t public
