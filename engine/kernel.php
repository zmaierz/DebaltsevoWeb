<?php

include $_SERVER["DOCUMENT_ROOT"] . "/engine/modules/include.php";

class Kernel {
    private ?Database $DB;
    
    private ?array $DBConfig;
    private ?array $kernelConfig;
    
    public function __construct() {
        $this->DBConfig = getDBConfig();
        $this->kernelConfig = getKernelConfig();

        $this->DB = new Database($this->DBConfig);
    }
}

?>