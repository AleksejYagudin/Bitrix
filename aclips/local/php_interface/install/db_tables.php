<?php

/**
 * Консольная утилита для проверки существования и создание таблиц сущностей в бд
 *
 * Пример запуска:
 *      /usr/bin/php -f ~/www/local/php_interface/install/db_tables.php
 **/

if (php_sapi_name() != 'cli') {
    die();
}

// Абсолютный путь к корневой директории проекта
$_SERVER['DOCUMENT_ROOT'] = '/home/bitrix/www';

// Отключение ненужных проверок и действий
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define("NEED_AUTH", true);

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

// Классы сущностей проекта для создания таблиц в бд
$dataManagerClasses = [
    \Aclips\BookRental\Internal\EditionTable::class
];

foreach ($dataManagerClasses as $dataManagerClass) {
    try {
        // Получение объекта сущности
        $entity = $dataManagerClass::getEntity();

        // Получение названия таблицы бд
        $tableName = $entity->getDBTableName();

        // Проверка на отсутствие таблицы в бд
        $connection = Bitrix\Main\Application::getConnection();
        if (!$connection->isTableExists($tableName)) {
            // Создание бд
            $result = $entity->createDBTable();
            // log: Table $tableName created successfully
        } else {
            // log: Table $tableName already exists
        }
    } catch (\Throwable $e) {
        // log: Exception $e->getMessage()
    }
}