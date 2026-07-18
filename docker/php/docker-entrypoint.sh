#!/bin/sh
set -e

php bin/console cache:warmup --env=prod

exec "$@"