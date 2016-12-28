#!/bin/bash

#Ubuntu 14.04
#Tested on Digital Ocean's PHPMyAdimin Pre Installed Ubuntu 14.04 server

##IMPORTANT: Inorder to initialize MySQL server load at least one page of the website
##after the installation.

#https://github.com/kaustubh-karkare/aurora-online-judge
echo "Enter your MySql's root user password: (If it's not installed yet, it will be installed.)"
read SQLPass
sudo apt-get update
apt-get install -y git
sudo apt-get -y install mysql-server
sudo mysql_secure_installation
sudo mysql_install_db
service mysql status
sudo apt-get -y install lamp-server^
sudo apt-get -y install phpmyadmin
sudo apt-get -y install apache2
cd /var/www/html
git clone https://github.com/kaustubh-karkare/aurora-online-judge.git
#Revert back to the version script was tested on.
#You can remove it to get later features on your own.
git checkout 71f5d685531daa5d62b24ab2e5de922cef3f2bb7
mv aurora-online-judge/ aurora/
cd aurora
#So under the root account in SQL it creates the Database.
#The admin & password is the username and password for the root
#account in your judge.
cat >> sys/system_config.php << EOF
<?php
\$mysql_hostname = "127.0.0.1";
\$mysql_username = "root";
\$mysql_password = "$SQLPass";
\$mysql_database = "aurora";
\$admin_teamname = "admin";
\$admin_password = "password";
?>
EOF
cd sys
mkdir temp
chmod 777 temp/
cd ../
sudo apt-get -y install bf g++ fpc mono-gmcs openjdk-6-jdk perl php5 python python-mysqldb rhino ruby

#Put your own sql_hostname, sql_username and ... in judge.py.
#You may create new users in SQL but simplest would be using your root username
#and password.
#Run python judge.py -judge -unsafe
#(-unsafe to support more than C, C++; if you're only going to run C/C++ codes you can omit it.)
#Preferably to be run on another server, or if they're on the same system at least one
#featuring a multicore processor.
