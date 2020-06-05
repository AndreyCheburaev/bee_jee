#!/bin/bash

SERVER_NAME=$1
USER_NAME=$2
SUDO_NEED=$3
SCRIPTS_DIR=$4
SWAP_MEMORY=$5

if [ "$SUDO_NEED" == true ]
    then COMMAND_PREFIX="sudo"
else
    COMMAND_PREFIX=""
fi

# Изменяем hostname:
OLD_HOST_NAME=$(cat /etc/hostname)
"$COMMAND_PREFIX" hostnamectl set-hostname "$SERVER_NAME"
"$COMMAND_PREFIX" sed -i "s/${OLD_HOST_NAME}/${SERVER_NAME}/g" /etc/hosts
"$COMMAND_PREFIX" sed -i "s/${OLD_HOST_NAME}/${SERVER_NAME}/g" /etc/hostname
echo -e "\e[1;42m>>> Установили hostname: $SERVER_NAME"


# Устанавливаем locale:
"$COMMAND_PREFIX" echo "LC_ALL=en_US.UTF-8" | "$COMMAND_PREFIX" tee -a /etc/default/locale
. /etc/default/locale
"$COMMAND_PREFIX" locale-gen en_US
"$COMMAND_PREFIX" locale-gen en_US.UTF-8
"$COMMAND_PREFIX" update-locale
"$COMMAND_PREFIX" apt update
echo -e "\e[1;42m>>> Обновили файлы локализации. \e[0m"

# Создаем необходимые директории:
"$COMMAND_PREFIX" mkdir  /var/log/php/
echo -e "\e[1;42m>>> Создали базовые каталоги. \e[0m"

# Ставим базовое ПО:
"$COMMAND_PREFIX" apt install -y git htop curl software-properties-common vim nginx ncdu ntp acl unzip build-essential
echo -e "\e[1;42m>>> Установили базовое ПО. \e[0m"

if [ "$VAGRANT_ONLY" == true ]
    then USER_ZSH_NAME="vagrant"
else
    USER_ZSH_NAME="root"
fi

# Устанавливаем zsh для root:
"$COMMAND_PREFIX" apt install -y zsh
"$COMMAND_PREFIX" mkdir ~/.dotfiles
"$COMMAND_PREFIX" wget https://github.com/robbyrussell/oh-my-zsh/raw/master/tools/install.sh -O - | zsh
"$COMMAND_PREFIX" chsh -s `which zsh` ${USER_ZSH_NAME}
"$COMMAND_PREFIX" cp -rp "$SCRIPTS_DIR"dotfiles/*  ~/.dotfiles/
"$COMMAND_PREFIX" ln -fs ~/.dotfiles/zshrc ~/.zshrc
"$COMMAND_PREFIX" ln -fs ~/.dotfiles/curlrc ~/.curlrc
"$COMMAND_PREFIX" ln -fs ~/.dotfiles/inputrc ~/.inputrc
"$COMMAND_PREFIX" chown -R "${USER_ZSH_NAME}:${USER_ZSH_NAME}" ~/.oh-my-zsh/cache/
echo -e "\e[1;42m>>> Установили zsh. \e[0m"

# Настроим ядро под высокую нагрузку:
"$COMMAND_PREFIX" echo "
vm.swappiness=10
vm.vfs_cache_pressure=50

net.ipv4.conf.all.accept_redirects = 0
net.ipv4.conf.all.secure_redirects = 0
net.ipv4.conf.all.send_redirects = 0

net.ipv4.tcp_max_orphans = 65536
net.ipv4.tcp_fin_timeout = 10

net.ipv4.conf.default.accept_redirects = 0
net.ipv4.conf.eth0.accept_redirects = 0
net.ipv4.conf.lo.accept_redirects = 0
net.ipv6.conf.all.accept_redirects = 0
net.ipv6.conf.default.accept_redirects = 0
net.ipv6.conf.eth0.accept_redirects = 0
net.ipv6.conf.lo.accept_redirects = 0

net.ipv4.tcp_syncookies = 0

net.ipv4.conf.all.accept_source_route = 0
net.ipv4.conf.lo.accept_source_route = 0
net.ipv4.conf.eth0.accept_source_route = 0
net.ipv4.conf.default.accept_source_route = 0

net.core.rmem_max = 16777216
net.core.wmem_max = 16777216

net.ipv4.tcp_rfc1337 = 1

net.core.somaxconn = 65535
net.core.netdev_max_backlog = 2000

net.ipv4.tcp_mem = 50576   64768   98152

net.ipv4.tcp_keepalive_intvl = 15

net.ipv4.tcp_max_syn_backlog = 2048

net.ipv4.tcp_synack_retries = 1

kernel.msgmnb = 65536
kernel.msgmax = 65536

fs.file-max = 100000" | "$COMMAND_PREFIX" tee -a /etc/sysctl.conf

"$COMMAND_PREFIX" sysctl -p /etc/sysctl.conf
echo -e "\e[1;42m>>> Добавили настройки ядра под высокие нагрузки. \e[0m"

# Добавляем/убираем Swap:
swapon -s | grep -i swapfile > /dev/null
SWAP_STATUS=$? # 0:enabled

if [[ ${SWAP_MEMORY} != "0" && ${SWAP_STATUS} != "0" ]]; then
    echo -e "\e[1;42m>>> Установили Swap (${SWAP_MEMORY} MB) \e[0m"

    "$COMMAND_PREFIX" fallocate -l ${SWAP_MEMORY}M /swapfile # Create the Swap file
    "$COMMAND_PREFIX" chmod 600 /swapfile # Set the correct Swap permissions
    "$COMMAND_PREFIX" mkswap /swapfile # Setup Swap space
    "$COMMAND_PREFIX" swapon /swapfile # Enable Swap space

    # Make the Swap file permanent
    echo "/swapfile   none    swap    sw    0   0" | "$COMMAND_PREFIX" tee -a /etc/fstab

    # Swap settings
    # vm.swappiness=10: Means that there wont be a Swap file until memory hits 90% useage
    # vm.vfs_cache_pressure=50: http://rudd-o.com/linux-and-free-software/tales-from-responsivenessland-why-linux-feels-slow-and-how-to-fix-that
    echo "vm.swappiness=10" | "$COMMAND_PREFIX" tee -a /etc/sysctl.conf
    echo "vm.vfs_cache_pressure=50" | "$COMMAND_PREFIX" tee -a /etc/sysctl.conf
    "$COMMAND_PREFIX" sysctl -p
fi

if [[ ${SWAP_MEMORY} == "0" && ${SWAP_STATUS} == "0" ]]; then
    echo -e "\e[1;42m>>> Убираем Swap \e[0m"

    "$COMMAND_PREFIX" swapoff -a
    "$COMMAND_PREFIX" perl -pi -e "s#/swapfile.*\n##" /etc/fstab
    "$COMMAND_PREFIX" perl -pi -e "s#vm\.swappiness.*\n##" /etc/sysctl.conf
    "$COMMAND_PREFIX" perl -pi -e "s#vm\.vfs_cache_pressure.*\n##" /etc/sysctl.conf
    "$COMMAND_PREFIX" sysctl -p
fi

swapon -s
