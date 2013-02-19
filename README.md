devlogger
=========

A tool that I use to create automatic devlogs from a TODO.txt.

Stuff required:
  * Dropbox account
  * PHP, MySQL
  * Cronjobs

Devlogger uses dropboxes ability to automatically publish a file on the internets as it's basis. The script downloads the TODO.txt and parses it and puts the results into an SQL database. Cronjob is used to run the script on certain intervals (10 minutes or so). Devlogger also comes with an ability to download a daily screenshot from Dropbox and synch it with things you've gotten done that day. For that work you need to either take a screenshot manually or program an automatic screenshotting functionality into your program (that you're developing). I went with the program your own auto screenshotter way and it was worth the trouble :)
