<?php


// here's how to setup your sql table
/*
CREATE DATABASE TODO_DATABASE;
GRANT ALL PRIVILEGES ON TODO_DATABASE.* TO "TODO_USERNAME"@localhost IDENTIFIED BY "PASSWORD";
FLUSH PRIVILEGES;
USE TODO_DATABASE;

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


CREATE TABLE todo_dates (
id INT AUTO_INCREMENT,
date_created DATE DEFAULT NULL,
PRIMARY KEY (id),
UNIQUE( date_created )
);

*/

// here's the config to your sql database
define(db_host, "localhost");
define(db_user, "TODO_USERNAME");
define(db_pass, "TODO_PASSWORD");
define(db_name, "TODO_DATABASE");

// dropbox urls + default project
define(dropbox_todo_txt, "http://dl.dropbox.com/YOUR_PUBLIC_DROPBOX_LINK/TODO.txt");
define(dropbox_daily_shot, "http://dl.dropbox.com/YOUR_PUBLIC_DROPBOX_LINK/daily_screenshot.png" );
define(default_project, 'TODO PARSER' );
?>