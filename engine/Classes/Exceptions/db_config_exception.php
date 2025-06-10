<?php

class db_config_exception extends Exception {
    function __contruct($message) {
        $this->message = "Некорректная настройка конфигурации БД!<br>Пожалуйста, обратитесь к администратору.";
    }
}

?>