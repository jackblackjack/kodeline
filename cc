#!/bin/sh

# Definition current execution dir.
EXEC_DIR=$(cd $(dirname "$0"); pwd)

# Definition path to php.
PHP_CMD=`which php`

${PHP_CMD} ${EXEC_DIR}/symfony cc
