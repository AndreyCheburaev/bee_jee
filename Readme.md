# Тестовое задание компании BeeJee 

Ссылка на сайт компании: https://beejee.org/career

Задача: разработать приложение-задачник.

Требования:
* В приложении нужно с помощью чистого PHP реализовать модель MVC. Фреймворки PHP использовать нельзя, библиотеки - можно. Этому приложению не нужна сложная архитектура, решите поставленные задачи минимально необходимым количеством кода. Верстка на bootstrap, к дизайну особых требований нет.
* Стартовая страница - список задач с возможностью сортировки по имени пользователя, email и статусу. Вывод задач нужно сделать страницами по 3 штуки (с пагинацией). Видеть список задач и создавать новые может любой посетитель без авторизации.
* Сделайте вход для администратора (логин "admin", пароль "123"). Администратор имеет возможность редактировать текст задачи и поставить галочку о выполнении. Выполненные задачи в общем списке выводятся с соответствующей отметкой.

# Описание приложения

Для начала стоит рассмотреть дерево директорий, которое прояснит ситуацию (для всех ключевых мест есть свои неймспейсы):

```
app/
    Controller/ - контроллеры;
    Model/ - общие модели используемые на этапе инициализации приложения;
        Tasks.php - основная модель задач;
    Core/ - общие классы для работы (логирование, роутинг, обработка\подготовка запросов, БД) 
    View/ - шаблоны html
        common/ - общие шаблоны и файлы css, js
    Bootstrap.php - основной загрузчик, 
   
resource/ - ресурсы, к которым доступаются напрямую без неймспейсов;
    config/ - конфигурационные файлы для инициализации приложения под различными приложениями и окружениями;
        cron - cron-задачи прокидываемые в crontab на продакшене;
        setting.php - файл с общими настройками;
        service.php - массив общих сервисов в di;
        paramter_dev.php - настройка под определённое окружение;
        paramter_test.php
        paramter_prod.php.sample - пример настройки приложения для продакшен окружения (реквизиты конектов к стореджам и др.);
public/
    index.php - точка входа для HTTP-запрсов;
composer.json - описание зависимостей, которые могут понадобится в процессе (подключаются они отдельно с помощью namespace когда надо)
log/ - папка логов
```

# Разработка

На основе Vagrant можно поднять виртуалку с требуемой средой. Для этого необходимо установить Vagrant и Virtualbox. 
Далее из корня проекта вызвать следующую команду:
```
vagrant up
```
После установки (≈10 минут) можно зайти на виртуальную машину:
```
vagrant ssh
```
После того как зайдём необходимо выполнить команду
```
/home/ubuntu/project/provision/vagrant.sh
```
Скрипт установит всё необходимое и настроит работу виртулаки

После этого перезапустить vagrant, зайти в виртуалку подтянуть зависимости `composer install`

# Логирование

Приложение записывает логи в `/log/log.txt` если этого файла нет, то лучше создать иначе будем ловить ошибки.

# Сторедж

Для хранения данных используется MongoDB, база *beejee*.

#### ЗЫ

К сожалению много что можно ещё улучшить прокачать, но в целом я кайфанул на этих выходных.