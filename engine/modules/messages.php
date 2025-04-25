<?php

function getFatalMessages(): array {
    return array(
        "database_no_connect_error" => "Внимание!<br><b>Ошибка при инициализации подключения к БД!</b><br>Пожалуйста, обратитесь к вашему системному администратору!<br>",
        "page_no_found_in_database" => "Внимание!<br>Не найдена таблица страницы в базе данных!<br>Пожалуйста, проверьте целостность БД или обратитесь к администратору!",
        "page_block_not_allowed" => "Внимание!<br>Обнаружен недопустимый блок страницы в БД!<br>Пожалуйста, проверьте целостность БД или обратитесь к администратору!",
        "immunity_warning_sql_injection" => "<div>Внимание!<br><b>Обнаружена попытка SQL-инъекции!</b><br>Инцидент зафиксирован</div>",
    );
}

function getImmunityMessages(): array {
    return array(
        "sql-injection-from-url-name" => "Попытка SQL-инъекции через адресную строку браузера",
        "sql-injection-from-url-description" => "Была попытка SQL-инъекции через адресную строку браузера.\nСтрока: #1#",
    );
}

?>