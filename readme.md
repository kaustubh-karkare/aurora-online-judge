## Aurora Online Judge

### Setup
* Copy all files into a directory named "aurora" inside your Apache's Document Root.
* Open (create, if required) the file "<path-to-document-root>/aurora/sys/system_config.php" and set the variables (with appropriate values) as shown below:

```php
<?php
$mysql_hostname = "127.0.0.1";
$mysql_username = "username";
$mysql_password = "password";
$mysql_database = "aurora";
$admin_teamname = "admin";
$admin_password = "password";
?>
```

* Create an empty subdirectory called "temp" in the "sys" directory (with 777 permissions).
* Using a browser, open "https://hostname/aurora/?display=doc" to read further instructions on how to use this software.
* To judge sumissions, run the script "judge.py" on (preferably) a virtual machine that satisfies the server configuration specified in the FAQ.
* An upgraded version of this project can be found at: https://github.com/pushkar8723/Aurora

### Created by: Kaustubh Karkare
