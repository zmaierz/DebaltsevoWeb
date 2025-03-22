<?php

class Database {
    private ?string $username;
    private ?string $password;
    private ?string $hostname;
    private ?string $database;
    private ?int $port;

    public function __construct(?array $config) {
        $this->username = $config["username"];
        $this->password = $config["password"];
        $this->hostname = $config["hostname"];
        $this->database = $config["database"];
        $this->port = $config["port"];
    }
}

?>