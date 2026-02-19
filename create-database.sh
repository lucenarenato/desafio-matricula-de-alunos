#!/usr/bin/env bash

mysql --user=root --password="$MYSQL_ROOT_PASSWORD" <<-EOSQL
    CREATE DATABASE IF NOT EXISTS desafio;
    GRANT ALL PRIVILEGES ON \`desafio%\`.* TO '$MYSQL_USER'@'%';

    FLUSH PRIVILEGES;

EOSQL
