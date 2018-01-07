#!/usr/bin/env bash

php bin/console doctrine:database:drop --env=test --no-debug --force --if-exists
php bin/console doctrine:database:create --env=test --no-debug --no-interaction --if-not-exists
php bin/console doctrine:migrations:migrate --env=test --no-debug --no-interaction
php bin/console doctrine:fixtures:load --env=test --no-debug --no-interaction
php bin/console fos:user:create testuser test@test.test p@ssword --env=test --no-debug
php bin/console fos:user:promote testuser ROLE_ADMIN --env=test --no-debug
php bin/console cache:clear --env=test --no-debug --no-warmup

phpunit
