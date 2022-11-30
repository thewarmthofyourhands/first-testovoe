## Инструкция по запуску (debian/ubuntu)
1. ```
   docker-compose up -d 
   docker-compose exec api composer i
   docker-compose exec api php ./bin/console generate.tree.json --input-file='/task/input.csv'
   ```
2. Смотрим файл output.json в папке task

Для проверки генерации дерева на 20 тысяч строк используйте файл /task/largeinput.csv

### Тесты
``` 
docker-compose exec api vendor/bin/phpunit
```
