# Enigma V
A prototype social network created in 2013 that uses PHP5 and MySQL. 

# Requirements

# Installing PHP5.6 on Debian

Because PHP5.6 is no longer supported by the main repository, you'll need an alternate reposititory that supports it.
Below are instructions taken from https://packages.sury.org/php/README.txt that explain how to add one such possible repository.

```bash
#!/bin/sh
# To add this repository please do:

if [ "$(whoami)" != "root" ]; then
    SUDO=sudo
fi

${SUDO} apt-get update
${SUDO} apt-get -y install apt-transport-https lsb-release ca-certificates curl
${SUDO} curl -sSLo /usr/share/keyrings/deb.sury.org-php.gpg https://packages.sury.org/php/apt.gpg
${SUDO} sh -c 'echo "deb [signed-by=/usr/share/keyrings/deb.sury.org-php.gpg] https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list'
${SUDO} apt-get update
```

Next you'll need to install PHP5.6 and all the additional dependancies needed for EnigmaV to run.

```bash
sudo apt install php5.6 php5.6-mysql
sudo apt install libapache2-mod-php
```

Once PHP is installed you'll need to enable it in Apache2. The below commands disable PHP7.4 (The latest version as of this writing), and enables PHP5.6.
After that we need to restart Apache.

```bash
sudo a2dismod php7.4
sudo a2enmod php5.6
apache2ctl restart
```

# Installing MariaDB (MySQL) Server

```bash
sudo apt install mariadb-server
sudo mysql_secure_installation
```

