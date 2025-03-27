<?php

class Database {
    private ?string $username;
    private ?string $password;
    private ?string $hostname;
    private ?string $database;
    private ?int $port;
    
    private ?string $errorMSG;

    public function __construct(?array $config, ?bool $checkConnection) {
        $this->username = $config["username"];
        $this->password = $config["password"];
        $this->hostname = $config["hostname"];
        $this->database = $config["database"];
        $this->port = $config["port"];
        
        $this->errorMSG = "";

        try {
            $conn = $this->getConn();
            if (!$conn) {
                throw new db_connect_error;
            }
        }
        catch (db_config_exception $ex) {
            $this->errorMSG = $ex;
        }
        catch (db_connect_error $ex) {
            $this->errorMSG = $ex;
        }
    }

    public function getDataForMenuWithCategory(?string $categoryName): ?array {
        $out = array();

        try {
            $query = "SELECT * FROM `debaltsevo-web`.`pageList` WHERE `category` = '$categoryName'";
            $conn = $this->getConn();

            try {
                if ($result = $conn->query($query)) {
                    $j = 0;
                    foreach ($result as $row) {
                        $out[$j]["name"] = $row["name"];
                        $out[$j]["alias"] = $row["alias"];
                        $j++;
                    }
                    return $out;
                }
                else {
                    throw new db_connect_error;
                }
            }
            catch (mysqli_sql_exception $ex) {
                $this->errorMSG = $ex;
                return null;
            }
            catch (db_connect_error $ex) {
                $this->errorMSG = $ex;
                return null;
            }
        }
        catch (db_config_exception $ex) {
            $this->errorMSG = $ex;
            return null;
        }
        catch (db_connect_error $ex) {
            $this->errorMSG = $ex;
            return null;
        }
    }

    public function getData(?string $table, ?array $rows): ?array {
        $out = array();
        try {
            $query = "SELECT * FROM $table";
            $conn = $this->getConn();

            try {
                if ($result = $conn->query($query)) {
                    $j = 0;
                    foreach ($result as $row) {
                        foreach ($rows as $i) {
                            $out[$j][$i] = $row[$i];
                        }
                        $j += 1;
                    }
                    return $out;
                }
                else {
                    throw new db_connect_error;
                }
            }
            catch (mysqli_sql_exception $ex) {
                $this->errorMSG = $ex;
                return null;
            }
            catch (db_connect_error $ex) {
                $this->errorMSG = $ex;
                return null;
            }
        }
        catch (db_config_exception $ex) {
            $this->errorMSG = $ex;
            return null;
        }
        catch (db_connect_error $ex) {
            $this->errorMSG = $ex;
            return null;
        }
    }

    public function getErrorMSG(): string {
        return $this->errorMSG;
    }

    private function getConn(): ?mysqli {
        try {
            $conn = new mysqli(
                $this->hostname,
                $this->username,
                $this->password,
                $this->database,
            );
        }
        catch (mysqli_sql_exception $ex) {
            throw new db_connect_error;
        }

        if ($conn->connect_error)
            return null;
        return $conn;
    }
}

?>