Инструкция по запуску (debian/ubuntu)
1. docker-compose up -d
2. docker-compose exec api php ./bin/console generate.tree.json --input-file='/task/input.csv'
3. Смотрим файл output.json в папке task
