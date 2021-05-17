chgrp -R www-data /var/www/html/project/storage;
chgrp -R www-data /var/www/html/project/bootstrap/cache;
chmod -R 777 /var/www/html/project/storage;
echo "Apache server is running ..., browser your project using 'localhost' ";
apache2 -DFOREGROUND;