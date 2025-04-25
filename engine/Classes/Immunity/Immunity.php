<?php

class Immunity {
    private ?array $defaultDeniedSymbols = array(
        '$', '%', ';', '\'', '"', '`', '#', '@', '&', '~'
    );
    private ?array $deniedSymbols;

    private ?array $kernelConfigList = array(
        "debug", "useCache", "modulePath", "templatePath", "mediaPath", "cachePath", "deniedSymbols"
    );
    private ?array $kernelConfigTypes = array(
        "debug" => "boolean",
        "useCache" => "boolean",
        "modulePath" => "string",
        "templatePath" => "string",
        "mediaPath" => "string",
        "cachePath" => "string",
        "deniedSymbols" => "array"
    );

    private ?array $DBConfigList = array("username", "password", "hostname", "database", "port");
    private ?array $DBConfigTypes = array(
        "username" => "string",
        "password" => "string",
        "hostname" => "string",
        "database" => "string",
        "port" => "integer"
    );

    public function validateString(?string $temp): bool {
        $temp = trim($temp);
        foreach ($this->deniedSymbols as $i) {
            if (str_contains($temp, $i)) {
                return false;
            }
        }
        return true;
    }
    public function validateConfigs(?array $configs): int {
        $status = 0;
        $kernelConfig = $configs["kernelConfig"];
        $DBConfig = $configs["DBConfig"];

        foreach ($this->kernelConfigList as $i) {
            if (gettype($kernelConfig[$i]) != $this->kernelConfigTypes[$i]) {
                $status = 1;
            }
        }

        foreach ($this->DBConfigList as $i) {
            if (gettype($DBConfig[$i]) != $this->DBConfigTypes[$i]) {
                $status = 2;
            }
        }

        return $status;
    }
    public function setDeniedSymbols(?array $deniedSymbols): void {
        if ($deniedSymbols == null)
            $this->deniedSymbols = $this->defaultDeniedSymbols;
        else
            $this->deniedSymbols = $deniedSymbols;
    }
}

?>