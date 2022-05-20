# Enigma V
A prototype social network created in 2012 that uses PHP 5 and MySQL. 

<img style="height: 400px;" src="https://raw.githubusercontent.com/JohnnyLdeAlba/enigmav/master/enigmav-landing.png" /> <img style="height: 400px;" src="https://raw.githubusercontent.com/JohnnyLdeAlba/enigmav/master/enigmav-edit.png" />

# Features

- Create an account with an avatar/profile picture.
- Post messages with an image or video preview.
- Users can up and down vote their favorite content.
- Add Directories to organize content into categories.

# [Example](https://enigmav.nexusultima.com)

This is a live version of the repo.

# Requirements

- PHP 5.6
- MySQL (or MariaDB)

# Known Issues

- Recovering passwords by email no longer works and needs to be updated.
- Youtube links do not show up when attached to messages due to using obsolete embed code.

# Installing Enigma V

Getting Enigma V working is a simple easy process, the only issue is getting PHP 5.6 installed
as well as ImageMaick as most repositories do not support, or properly support them. If you're
using Debian, there are instructions included in this text on how to get them
running.

## Setting Up config.php

You're going to need to edit config.php in order for Enigma V to connect to the database.
The first thing you'll want to do is rename the file _config.php (included in this repo) to config.php. 
Next open the file with a text editor and edit the variables below:

- `$NetworkName` Name of the Website Enigma V will be running on.
- `$NetworkUrl` URL of your website.
- `$NetworkDomain` Domain of your website.

- `$MySqlHost` Host where the MySQL (or MariaDB) database is located.
- `$MySqlUsername` Username used to access the database.
- `$MySqlPassword` Password associated with the above username.
- `$MySqlDatabase` The name of the database.

## Installing database.sql

A file called database.sql was included in this repo that provides the initial setup needed
to get Enigma V operating. Below is the command to do this:

```bash
sudo mysql -uusername -p database_name < database.sql
```

The command above installs `database.sql` (included in this repository)
into your newly created database. Be sure to replace username with your
username, for example if your username is `root` then you will need to type in
`-uroot`. You will also need to replace database_name with the name of your database.

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

Next you'll need to install PHP 5.6 and all the additional dependancies needed for Enigma V to run.

```bash
sudo apt install php5.6 php5.6-mysql
sudo apt install libapache2-mod-php
```

Once PHP is installed you'll need to enable it in Apache 2. The below commands disable PHP7.4 and enables PHP5.6 (The latest version of PHP is enabled by default).
After that you will need to restart Apache.

```bash
sudo a2dismod php7.4
sudo a2enmod php5.6
apache2ctl restart
```

# Installing MariaDB (MySQL) Server

MariaDB is the successor of MySQL and works great with Enigma V. Below are instructions on how to install it on Debian.

```bash
sudo apt install mariadb-server
sudo mysql_secure_installation
```

Next you'll want to bring up the MySQL prompt.

```
sudo mysql
```

We need to create a user for our database, below is the command to do so. Just be sure to replace
username with your desired username.

```mysql
CREATE USER 'username'@'localhost' identified by 'username';
```

The next step is optional, here we are granting our newly created user all privileges. 
This could be dangerous on a public server. As with the previous step, replace username
with your username.

```mysql
GRANT ALL PRIVILEGES on * TO 'username'@'localhost';
```

Now to create the database, replace DATABASE_NAME with the name
of your database.

```mysql
CREATE DATABASE DATABASE_NAME;
```

The following command installs the database file included in this repository
into your newly created database. Be sure to replace username with your
username, for example if your username is `root` then you need to type in
`-uroot`. Also be sure to replace database_name with the name of your database.

```bash
sudo mysql -uusername -p database_name < database.sql
```

Below is the command for backing up your database, remember
to replace database_name with your database.

```bash
sudo mysqldump --databases database_name > database.sql
```
