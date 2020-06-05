#!/bin/bash

# Для установки ПО на Vagrant следует:
# 1. Зайти в vagrant: `vagrant ssh`
# 2. Запустить скрипт: `/home/ubuntu/project/provision/vagrant.sh`

# Устанавливаем переменные для скриптов.
SERVER_NAME="beejee.ru"
SERVER_IP="192.168.10.130"
USER_NAME="vagrant"
SERVER_TIMEZONE="Etc/UTC"
SCRIPTS_DIR="/home/ubuntu/project/provision/scripts/"
PROVISION_DIR="/home/ubuntu/project/provision/"
PROJECT_DIR="/home/ubuntu/project/"
SUDO_NEED=true
AURORA_LOGIN="vagrant"
AURORA_PASSWORD="vagrant"
SWAP_MEMORY=1024

# 1. Подготовка виртуальной машины + установка базового ПО.
"$SCRIPTS_DIR"prepare.sh "$SERVER_NAME" "$USER_NAME" "$SUDO_NEED" "$SCRIPTS_DIR" "$SWAP_MEMORY";

# 2. PHP 7.3
"$SCRIPTS_DIR"php_7_3.sh "$SUDO_NEED";

# 3. MongoDB
"$SCRIPTS_DIR"mongodb_4_0.sh "$SUDO_NEED";

# 4. YAML
"$SCRIPTS_DIR"yaml.sh "$SUDO_NEED";

# 5. Composer
"$SCRIPTS_DIR"composer.sh "$SUDO_NEED";

# 6. Codecept
"$SCRIPTS_DIR"codecept.sh "$SUDO_NEED";

# 7. Xdebug
"$SCRIPTS_DIR"xdebug.sh "$SUDO_NEED";

# 8. Nginx conf
"$SCRIPTS_DIR"nginx_conf.sh "$SUDO_NEED";

# 9. Nginx sites-available
${SCRIPTS_DIR}nginx_vagrant.sh "$SERVER_NAME" "$USER_NAME" "$SUDO_NEED";
