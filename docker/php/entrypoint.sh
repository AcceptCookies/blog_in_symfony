#!/bin/sh
set -e
  if ! [ "$( find ./migrations -iname '*.php' -print -quit )" ]; then
    php bin/console doctrine:database:create --no-interaction --if-not-exists
    php bin/console make:migration --no-interaction
    php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration --all-or-nothing
    php bin/console doctrine:fixtures:load --no-interaction
  fi
exec "$@"
