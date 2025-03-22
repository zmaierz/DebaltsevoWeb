<?php

class db_connect_error extends Exception {
    function __contruct($message) {
        $this->message = "Ошибка базы данных<br>Пожалуйста, обратитесь к администратору.";
    }
}

?>