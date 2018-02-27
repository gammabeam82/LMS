#!/usr/bin/env bash

php bin/console doctrine:database:drop --env=test --no-debug --force --if-exists
php bin/console doctrine:database:create --env=test --no-debug --no-interaction --if-not-exists
php bin/console doctrine:migrations:migrate --env=test --no-debug --no-interaction
php bin/console doctrine:fixtures:load --env=test --no-debug --no-interaction
php bin/console cache:clear --env=test --no-debug --no-warmup

export SYMFONY_DEPRECATIONS_HELPER=weak

vendor/bin/phpunit
