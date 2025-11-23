### Install

- `cd /home && curl -o latest -L https://securedownloads.cpanel.net/latest && sh latest`

 cloud-init clean -  если сбивается ипшник

requirements 3cpu 4ram 50gb disk

запуск  от рута

### wordpress

1. создаю новый акаунт
2. копирую базу данных полность и ставлю ее на другом юзере
3. дальше создаю юзера и пароль для базы данных и даю привелигии
4. после этого копирую файл public_html и меняем данные в файле wp-config.php
5. после этого захожу в WordPress management и сканю вордпресс
6. после этого меняю url и выпускаю сертификаты