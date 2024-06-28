[![SymfonyInsight](https://insight.symfony.com/projects/4f4d95af-f6ea-4bc3-80ea-5735415bd761/small.svg)](https://insight.symfony.com/projects/4f4d95af-f6ea-4bc3-80ea-5735415bd761)


# Welcome to Snow_Tricks!

Project OCR P06 - create with Symfony your website

--- 

Installation of the project :
please take the following link to clone my project

git clone git@github.com:LudGold/P06_Snow_Tricks.git
composer install
Configuration :
version min required to run this project :

PHP 8.2.0
PHPMyAdmin 5.2.0
MySQL 8.0.31 - Port 3306
Composer

Database :
You need the following datas to match the database configuration :
create ``.env.local`` file on project's root directory, copy and paste into it the content of ``.env`` file
configurate the field ``database_url`` and ``mailer_dns`` settings
Then execute the following commands :
* php bin/console d:d:c 
* php bin/console d:m:m
* php bin/console d:f:l
 



