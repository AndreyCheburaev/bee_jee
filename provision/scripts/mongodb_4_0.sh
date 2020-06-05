#!/bin/bash

SUDO_NEED=$1

if [ "$SUDO_NEED" == true ]
    then COMMAND_PREFIX="sudo"
else
    COMMAND_PREFIX=""
fi

echo -e "\e[1;42m>>> Устанавка MongoDB \e[0m"
PHP_VERSION=`php --ini | grep "Configuration File " | sed -e "s|.*:\s*||" | sed -e "s/[^.0-9]//g"`

# Установка MongoDB:
"$COMMAND_PREFIX" apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 9DA31620334BD75D9DCB49F368818C72E52529D4
"$COMMAND_PREFIX" echo "deb [ arch=amd64 ] https://repo.mongodb.org/apt/ubuntu bionic/mongodb-org/4.0 multiverse" | "$COMMAND_PREFIX" tee /etc/apt/sources.list.d/mongodb-org-4.0.list
"$COMMAND_PREFIX" apt update
"$COMMAND_PREFIX" apt install -y mongodb-org mongodb-org-server mongodb-org-shell mongodb-org-mongos mongodb-org-tools


# меняем стандартный порт на нестандартный т.к. мы его пробрасываем
# наружу и чтобы не было конфликтов с монгой на основной машине
"$COMMAND_PREFIX" sed -i "s/bindIp:.*/bindIp: 0.0.0.0/" /etc/mongod.conf


"$COMMAND_PREFIX" sed -i "s/#  mmapv1:*/  mmapv1: \n    smallFiles: true/" /etc/mongod.conf

"$COMMAND_PREFIX" systemctl enable mongod.service
"$COMMAND_PREFIX" systemctl restart mongod.service

# Установка драйвера для MongoDB:
echo -e "\e[1;42m>>> Устанавка драйвера для MongoDB \e[0m"
"$COMMAND_PREFIX" apt install -y pkg-config
"$COMMAND_PREFIX" pecl channel-update pecl.php.net
"$COMMAND_PREFIX" pecl install mongodb
"$COMMAND_PREFIX" echo "extension=mongodb.so" | "$COMMAND_PREFIX" tee -a `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||" | sed "s/.\{11\}$//" | sed "s/$/mods-available\/mongodb.ini/"`
"$COMMAND_PREFIX" phpenmod -s cli mongodb
"$COMMAND_PREFIX" phpenmod -s fpm mongodb
"$COMMAND_PREFIX" systemctl restart php"$PHP_VERSION"-fpm.service
