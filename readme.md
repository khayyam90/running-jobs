#PHP Running jobs#

This project aims to provide a web based process launching micro application. 

##For what purpose ?##
It is efficient if you are running very long jobs such as 3d rendering or machine learning training. The running jobs are stored into database so you can retrieve your jobs even several hours later. 
The out/err content are stored in files until you delete them. 

I implemented this project for my own needs, it is running on a Linux platform with MariaDB with a specific user authentication.

##Installation##

Create a database and one table such as

```
CREATE TABLE `process` (
  `id` int(11) NOT NULL,
  `start` datetime NOT NULL,
  `pid` int(11) NOT NULL DEFAULT '0',
  `hash` varchar(32) NOT NULL,
  `cmd` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

* clone the current repository in your server
* run composer update
* create a new config in your webserver (nginx?) to link the location containing the cloned repo
* edit the .env file, especially the DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD options

##Security considerations##
This project may be dangerous for your webserver if the access is not correctly protected. 
**Warning** This project does not implement user management features. If you want to protect the access to this page, feel free to implement user authentication (through php or in your web server config).

Keep in mind that the processes will run as your webserver user (so you cannot run commands as root, or you need to activate even more dangerous config).   
It might have the rights to delete your web apps. If you want to run them as a different user, you may need to implement a cron based job watching the database and running the requested commands.
If someone can access this project in your webserver, it will able to run whatever he wants on your server, depending the rights given to your webserver user. 

In the same way, the commands to run are not checked before running. Nothing will prevent you for running a rm -rf *. For a more specific usage, you may need a strongly protected web shell.

Enjoy ! 