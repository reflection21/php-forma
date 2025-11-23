Завдання 2 

Для посилання:

site2.com/text/args
де text може бути або 1 або 2 або 3
args - будь який набір букв і цифр

Якщо text = 1(site2.com/1/args), то 
посилання має бути переписане на посилання виду 
https://site2.com/aaa.php?var=args

Якщо text = 2(site2.com/2/args), то 
посилання має бути переписане на посилання виду 
https://site2.com/bbb.php?args=1

Якщо text = 3(site2.com/3/args), то 
посилання має бути переписане на посилання виду 
https://site2.com/text-args (301 redirect)



# создаем в корне файл .htaccess и записываем конфиг

RewriteRule ^1/([A-Za-z0-9]+)/?$ /aaa.php?var=$1 [R=302,L]

RewriteRule ^2/([A-Za-z0-9]+)/?$ /bbb.php?$1=1 [R=301,L]

RewriteRule ^3/([A-Za-z0-9]+)/?$ /test-$1 [R=301,L]

