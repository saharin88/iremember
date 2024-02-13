## Увічнення пам'яті загиблих Захисників України

Laravel застосунок для створення та організації проєктів, що присвячені пам'яті загиблих Захисників України. 

### Створення користувача

*php artisan users:create*

### Панель

Панель доступна за посиланням *[/admin](http://platform.iremember.org.ua/admin)*.

Більше інформації про панель [filament](https://filamentphp.com/). 

### Заповнювач бази даних

PersonSeeder заповнює базу даних зі зведеної таблиці Google Sheets. 
[Приклад зведеної таблиці](https://docs.google.com/spreadsheets/d/1SR5-7gx23mAYxNfN1IrmThh0VqQZfICeAYnSYXorg_M) проєкту [Книга пам'яті полеглих за Україну](https://memorybook.org.ua/).

Детальніше про налаштування доступу до Google Sheets можна прочитати [тут](https://drivemarketing.ca/en/blog/connecting-laravel-to-a-google-sheet/).

### API документація

Документація API доступна за посиланням *[/docs](http://platform.iremember.org.ua/docs)*.

Файл конфігурації для створення документації знаходиться в *config/scribe.php*.

Команда для генерації документації *php artisan scribe:generate*.

Детальніша інформація про створення документації доступна [тут](https://scribe.knuckles.wtf/).
