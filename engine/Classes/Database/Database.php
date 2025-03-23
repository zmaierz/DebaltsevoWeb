<?php

class Database {
    private ?string $username;
    private ?string $password;
    private ?string $hostname;
    private ?string $database;
    private ?int $port;

    public function __construct(?array $config, ?bool $checkConnection) {
        $this->username = $config["username"];
        $this->password = $config["password"];
        $this->hostname = $config["hostname"];
        $this->database = $config["database"];
        $this->port = $config["port"];

        try {
            if ($checkConnection) {
                $conn = $this->getConn();
                if (!$conn) {
                    throw new db_connect_error;
                }
            }
        }
        catch (db_config_exception $ex) {
            echo "Какая то параша...";
        }
        catch (db_connect_error $ex) {
            echo "Какая то параша...";
            echo $ex;
        }
    }

    private function getConn(): ?mysqli {
        $conn = new mysqli(
            $this->hostname,
            $this->username,
            $this->password,
            $this->database,
        );

        if ($conn->connect_error)
            return false;
        return $conn;
    }
}

?>