devlogger
=========

A tool that I use to create automatic devlogs from a TODO.txt.

Stuff required:
  * Dropbox account
  * PHP, MySQL
  * Cronjobs

Devlogger uses dropboxes ability to automatically publish a file on the internets as it's basis. 
The script downloads the TODO.txt and parses it and puts the results into an SQL database. Cronjob
is used to run the script on certain intervals (10 minutes or so). Devlogger also comes with an 
ability to download a daily screenshot from Dropbox and synch it with things you've gotten done 
that day. For that to work you need to either take a screenshot manually or program an automatic 
screenshotting functionality into your program (that you're developing). I went with the program 
your own automagical screenshotter way and it was worth the trouble :)


Installing
----------

First order of business is to download the devlogger onto your webserver.

SQL database
------------

You need to create a MYSQL database for devlogger. Here's how you can do it. 
TODO_DATABASE 	is the name of database
TODO_USERNAME	is the username for that database
PASSWORD	is the password

CREATE DATABASE TODO_DATABASE;
GRANT ALL PRIVILEGES ON TODO_DATABASE.* TO "TODO_USERNAME"@localhost IDENTIFIED BY "PASSWORD";
FLUSH PRIVILEGES;
USE TODO_DATABASE;

After creating the database, we need to also create the tables for devlogger. Here's the code to do that.

CREATE TABLE todo_tasks (
id INT AUTO_INCREMENT,
task_name VARCHAR(2000),
task_status TINYINT DEFAULT 0,
task_priority TINYINT DEFAULT 10,
line_priority INT DEFAULT 256,
project_name VARCHAR(200),
date_created DATETIME DEFAULT NULL,
date_completed DATETIME DEFAULT NULL,
PRIMARY KEY (id)
);


config.php
----------

Now setup things in config.php. First of all type in your database setup in here:

define(db_host, "localhost");
define(db_user, "TODO_USERNAME");
define(db_pass, "TODO_PASSWORD");
define(db_name, "TODO_DATABASE");

Also define your dropbox setup in here.

define(dropbox_todo_txt, "http://dl.dropbox.com/YOUR_PUBLIC_DROPBOX_LINK/TODO.txt");
define(dropbox_daily_shot, "http://dl.dropbox.com/YOUR_PUBLIC_DROPBOX_LINK/daily_screenshot.png" );
define(default_project, 'TODO PARSER' );


Cronjobs
--------
Remember to to chmod 755 .sh files so that they can be run...

chmod 755 cronjobs/fetch_daily_screenshot.sh
chmod 755 cronjobs/fetch_todo.sh

Create file called crontab.txt if you don't have one and add these with correct path to it:

*/10 * * * * /var/www/vhosts/yoursite.com/httpdocs/devlogger/cronjobs/fetch_todo.sh
50 */2 * * * /var/www/vhosts/yoursite.com/httpdocs/devlogger/cronjobs/fetch_daily_screenshot.sh

To use it run
crontab crontab.txt

To list things:
crontab -l 

Remove everything
crontab -r


