<?php

include $_SERVER["DOCUMENT_ROOT"] . "/engine/modules/include.php";

class Kernel {
    private ?Database $DB;
    
    private ?array $DBConfig;
    private ?array $kernelConfig;

    private ?string $modulesPath = "";
    private ?string $defaultModulesPath = "/engine/templates/modules";
    
    public function __construct() {
        $this->DBConfig = getDBConfig();
        $this->kernelConfig = getKernelConfig();

        if ($this->kernelConfig["modulePath"] == "") {
            $this->modulesPath = $_SERVER["DOCUMENT_ROOT"] . $this->defaultModulesPath;
        }

        $this->DB = new Database($this->DBConfig, $this->kernelConfig["debug"]);
        $dbError = $this->DB->getErrorMSG();
        if ($dbError != "") {
            $this->showWarning($dbError, isException: true);
        }
    }

    public function showWarning(?string $exceptionMessage, ?bool $isException = false): void {
        if ($isException)
            $path = $this->modulesPath . "/showException//";
        else
            $path = $this->modulesPath . "/showError//";
        
        $html = IO::getFileContent($path . "content.html");
        $css = IO::getFileContent($path . "style.css");
        $cssMobile = IO::getFileContent($path . "style-mobile.css");
        $script = IO::getFileContent($path . "script.css");

        $html = str_replace("#~#", $exceptionMessage, $html);

        $outHtml = $html . "<style>" . $css . $cssMobile . "</style>" . "<script>" . $script . "</script>";

        echo $outHtml;
    }
}

?>