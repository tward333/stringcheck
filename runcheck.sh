#! /bin/bash

echo '' > /home/httpd/html/content_checks.txt
date >> /home/httpd/html/content_checks.txt
/usr/local/sbin/php7.1 /opt/php-stringcheck/check.php >> /home/httpd/html/content_checks.txt
