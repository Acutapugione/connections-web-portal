# connections-web-portal
Проект веб портала.

## clients:
1. добавление/удаление контрактов, заказы подгружаются из БД 1С через html-service;
2. данные по заказам редактируются в 1С, при каждом обновлении происходит сравнение с 1С;
3. пользователь может написать обращение, выбрав тему, заказ и указав текст сообщения;
4. обращение попадает в 1С Документооборот через html-service, создается документ входящей корреспонденции и процесс рассмотрения;
5. исходя из работы в Документообороте обновляется и отображается состояние обращения;
7. по неоплаченным этапам заказа выводится PDF-файл с необходимыми реквизитами, который можно скачать. 

## what should do:
- ### "/functions/pdfExecutions.php/" - использовано наследование уже готовых классов, для изменения формы и т.д. используйте это.
- ### "your_db_name.sql" - переименовать в своё название.
- ### "config.php" - настройки БД. +definisions
- ### "signIn.php"/"signUp.php"/"index.php"/"messenger.php"/"printInvoice.php" - переделать под себя.
