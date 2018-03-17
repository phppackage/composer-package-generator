#!/bin/bash

#
# Dependencies installer for debians.
#

# Set envioroment, for cloud-init
set -e
export DEBIAN_FRONTEND=noninteractive
export PATH='/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin'
export HOME='/root'

# Setup & Install Dependencys
setup_system() {
    #
    # Update package lists
    sudo apt-get update
    #
    # Install system packages
    sudo apt-get -yq install curl wget
    sudo apt-get -yq install unzip
    sudo apt-get -yq install nano
    sudo apt-get -yq install git
}

# Install PHP
install_php() {
    #
    # Import distibution variables 
    . /etc/lsb-release
    #
    # Is PHP5?
    if [ $DISTRIB_RELEASE = "12.04" ] || [ $DISTRIB_RELEASE = "14.04" ] || [ $DISTRIB_RELEASE = "15.04" ]; then
        phpver="5"
    fi
    #
    # Is PHP7?
    if [ $DISTRIB_RELEASE = "16.04" ] || [ $DISTRIB_RELEASE = "16.10" ] || [ $DISTRIB_RELEASE = "17.04" ] || [ $DISTRIB_RELEASE = "17.10" ] || [ $DISTRIB_RELEASE = "18.04" ] || [ $DISTRIB_RELEASE = "18.10" ]; then
        phpver="7"
    fi
    #
    # Install PHP7 from ppa for old systems
    if [ $phpver = "5" ]; then
        #
        #echo "Installing PHP5.5.9"
        #sudo apt-get -yq install php5 php5-cli
        #sudo apt-get -yq install php5-{curl,gd,mcrypt,json,mysql,sqlite}
        #
        #sudo apt-get -yq install libapache2-mod-php5 
        #
        # enable mods
        #sudo php5enmod mcrypt
        
        # add ppa and update
        sudo add-apt-repository ppa:ondrej/php -y
        sudo apt-get update
        
        # install deps
        sudo apt-get install -y php7.0-{dev,curl,gd,intl,mcrypt,json,mysql,opcache,bcmath,mbstring,soap,xml}
        sudo apt-get install -y libapache2-mod-php7.0
        
        # disable PHP5 enable PHP7
        sudo a2dismod php5
        sudo a2enmod php7.0
        
    fi
    #
    # Install PHP7
    if [ $phpver = "7" ]; then
        #
        echo "Installing PHP7.0"
        sudo apt-get -yq install php7.0 php7.0-cli
        sudo apt-get -yq install php7.0-{mbstring,curl,gd,mcrypt,json,xml,mysql,sqlite}
        #
        sudo apt-get -yq install libapache2-mod-php7.0
    fi
}

# Install composer (globally)
install_composer() {
    #
    # Install composer
    sudo curl -sS https://getcomposer.org/installer | sudo php
    sudo mv composer.phar /usr/local/bin/composer
    sudo ln -s /usr/local/bin/composer /usr/bin/composer
}

# Install inotify-tools
install_inotify() {
    #
    # Install nodejs
    sudo apt-get install inotify-tools
}

#
# Main 
#
main() {
    #
    setup_system
    #
    install_php
    #
    install_composer
    #
    install_inotify

    echo "Install finished."
}

main