#!/bin/sh -x

##GPG key
apt-get install debian-archive-keyring
apt-get update

## lighttpd
sudo apt-get install -y lighttpd

## mysql
echo "mysql-server-5.5 mysql-server/root_password password raspberry" | debconf-set-selections
echo "mysql-server-5.5 mysql-server/root_password_again password raspberry" | debconf-set-selections
sudo apt-get -y install mysql-server-5.5 \
  heirloom-mailx libaio1 libdbd-mysql-perl libdbi-perl libhtml-template-perl\
  mysql-client-5.5 mysql-server-core-5.5

## php
sudo apt-get install php5-common php5-cgi php5 php5-mysql

## fastcgi
sudo lighty-enable-mod fastcgi-php
sudo service lighttpd force-reload

# www-data permissions
sudo chown www-data:www-data /var/www
sudo chmod 775 /var/www
sudo usermod -a -G www-data pi

## phpmyadmin 
APP_PASS="raspberry"
ROOT_PASS="raspberry"
APP_DB_PASS="raspberry"

echo "phpmyadmin phpmyadmin/dbconfig-install boolean true" | debconf-set-selections
echo "phpmyadmin phpmyadmin/app-password-confirm password $APP_PASS" | debconf-set-selections
echo "phpmyadmin phpmyadmin/mysql/admin-pass password $ROOT_PASS" | debconf-set-selections
echo "phpmyadmin phpmyadmin/mysql/app-pass password $APP_DB_PASS" | debconf-set-selections
echo "phpmyadmin phpmyadmin/reconfigure-webserver multiselect lighttpd" | debconf-set-selections

sudo apt-get install dbconfig-common libmcrypt4 php5-gd php5-mcrypt phpmyadmin
sudo ln -s /usr/share/phpmyadmin /var/www/phpmyadmin 

zcat /usr/share/doc/phpmyadmin/examples/create_tables.sql.gz > /tmp/create_tables.sql

echo ' show databases;'>/tmp/pma.sql
echo ' source /tmp/create_tables.sql;'>/tmp/pma.sql
echo ' show databases;'>>/tmp/pma.sql
echo ' use phpmyadmin;'>>/tmp/pma.sql
echo ' show tables;'>>/tmp/pma.sql
echo ' GRANT SELECT, INSERT, UPDATE, DELETE ON phpmyadmin.* TO pma@localhost IDENTIFIED BY "pmapass";'>>/tmp/pma.sql
echo ' select host,user,password from mysql.user;'>>/tmp/pma.sql
echo ' flush privileges;'>>/tmp/pma.sql

mysql -u root -p < /tmp/pma.sql

# /etc/phpmyadmin/config-db.php
# dbuser = root  







#
