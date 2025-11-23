Завдання 1

Використовуючи nginx налаштувати проксі сервер на свій сайт з першого тестового (якщо немає, то створити заново будь який сайт).
Має бути 2 списки:
Whitelist - дозволяє доступ
Blacklist - має віддавати 403 код для усіх IP зі списку
Умови:
   - для /admin/ запитати пароль, і окремо писати лог файл хто заходить по паролю, а хто по whitelist
   - проксі має віддавати існуючі файли (окрім PHP файлів)
   - неіснуючі та PHP файли мають проксуватися на бекенд сервер

# установка апаче
apt install apache2
systemclt status apache2
# меняем порт на 8080
vim /etc/apache2/ports.conf   
# меняем виртуальный хост на 127.0.0.1:8080 и указываем рут файл /var/www/php          
vim /etc/apache2/sites-available/000-default.conf    
# качаем моды для апаче 
apt install php libapache2-mod-php
# включаем модуль
a2enmod php8.1
# меняем конфиг nginx   
vim /etc/nginx/sites-available/nginx.conf
# перезагрузка 
sudo systemctl restart apache2
# установка модуля для генерации пароля
sudo apt install apache2-utils
# создаем пароль для юзера по паролю
htpasswd -c /etc/nginx/.htpasswd danit
# если добавить еще юзера без записи 
sudo htpasswd /etc/nginx/.htpasswd anotheruser

