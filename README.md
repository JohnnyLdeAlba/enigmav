# Enigma V
A prototype social network created in 2013 that uses PHP5 and MySQL. 

# Features

- Create an account with an avatar or profile picture.
- Post messages with image or video preview.
- Users can up and down vote their favorite content.
- Directories can be added to organize content into categories.

# Requirements

- PHP 5.6
- MySQL (or MariaDB)

# Installing PHP5.6 on Debian

Because PHP5.6 is no longer supported by the main repository, you'll need an alternate reposititory that supports it.
Below are instructions taken from https://packages.sury.org/php/README.txt that explain how to add one such possible repository.

```bash
apt-get update
apt-get -y install apt-transport-https lsb-release ca-certificates curl
curl -sSLo /usr/share/keyrings/deb.sury.org-php.gpg https://packages.sury.org/php/apt.gpg
sh -c 'echo "deb [signed-by=/usr/share/keyrings/deb.sury.org-php.gpg] https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list'
apt-get update
```

Next you'll need to install PHP5.6 and all the additional dependancies needed for EnigmaV to run.

```bash
sudo apt install php5.6 php5.6-mysql
sudo apt install libapache2-mod-php
```

Once PHP is installed you'll need to enable it in Apache2. The below commands disable PHP7.4 and enables PHP5.6 (The latest version of PHP is enabled by default).
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

```
sudo mysql
```

```mysql
CREATE USER 'username'@'localhost' identified by 'username';
```

```mysql
GRANT ALL PRIVILEGES on * TO 'username'@'localhost';
```

```mysql
CREATE DATABASE DATABASE_NAME;
```

```bash
sudo mysql -uusername -p database_name < database.sql
```

```bash
sudo mysqldump --databases database_name > database.sql
```

# Known Issues

- Recover password by email does not currently work and needs to be updated.
