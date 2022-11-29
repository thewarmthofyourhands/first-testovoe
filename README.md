Инструкция по запуску (debian/ubuntu)
1. docker-compose up -d
2. docker-compose exec api composer i
3. docker-compose exec api php ./bin/console generate.tree.json --input-file='/task/input.csv'
4. Смотрим файл output.json в папке task

Для проверки генерации дерева на 20 тысяч строк используйте файл /task/largeinput.csv

Тесты написать не успеваю, так что пока что так. Если потребуется, то сообщите, напишу за пару часиков
