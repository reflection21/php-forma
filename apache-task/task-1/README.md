Завдання 1 

Налаштувати доступ за допомогою файлу .htaccess

site2.com/admin - доступ тільки по ІР адресі
site2.com/admin2 - доступ по паролю


# создать директорию /admin и файл .htccess
mkdir /home/user2/public_html/admin && cc $_
touch .htaccess 
# записать конфиг в .htaccess 
Order Deny,Allow
Deny from all
Allow from 91.123.154.142
# создать файл для отображения содержимого по пути /admin
touch index.html && echo "<h1>Admin Area</h1>" >> .htaccess 



# создать директорию /admin2 и файл .htccess
# записать конфиг в .htaccess 
AuthType Basic
AuthName "Restricted Area"
AuthUserFile /home/user2/public_html/admin2/.htpasswd
Require valid-user
# создать юзера с паролем
sudo htpasswd /home/user2/public_html/admin2/.htpasswd artem