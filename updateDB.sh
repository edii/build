#!/usr/bin/env bash

php app/console doctrine:database:drop --force
php app/console doctrine:database:create
php app/console doctrine:schema:update --force
php app/console doctrine:fixtures:load -n
php app/console doctrine:fixtures:load --fixtures=src/Apissystem/Base/Tests/Fixtures/User --append