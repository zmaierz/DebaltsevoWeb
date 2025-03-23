<?php

include $_SERVER["DOCUMENT_ROOT"] . "/engine/modules/include.php";

class Kernel {
    private ?Database $DB;
    
    private ?array $DBConfig;
    private ?array $kernelConfig;

    private ?string $modulesPath = "";
    private ?string $defaultModulesPath = "/engine/templates/modules";
    private ?string $templatesPath = "";
    private ?string $defaultTemplatesPath = "/engine/templates";
    
    public function __construct() {
        $this->DBConfig = getDBConfig();
        $this->kernelConfig = getKernelConfig();

        if ($this->kernelConfig["modulePath"] == "") {
            $this->modulesPath = $_SERVER["DOCUMENT_ROOT"] . $this->defaultModulesPath;
        }

        if ($this->kernelConfig["templatePath"] == "") {
            $this->templatesPath = $_SERVER["DOCUMENT_ROOT"] . $this->defaultTemplatesPath;
        }

        $this->DB = new Database($this->DBConfig, $this->kernelConfig["debug"]);
        $dbError = $this->DB->getErrorMSG();
        if ($dbError != "") {
            $this->showWarning($dbError, isException: true);
        }
    }

    public function showHeader(): void {
        $block = $this->getSystemBlock("systemHeader");

        echo $block;
    }

    public function showFooter(): void {
        $block = $this->getSystemBlock("systemFooter");

        echo $block;
    }

    public function showGeneralLayout(): void {
        $layout = $this->getLayout($this->templatesPath . "/layout/general");

        echo "<style>" . $layout["style"] . $layout["style-mobile"] . "</style>" . "<script>" . $layout["script"] . "</script>";
    }

    public function showPageLayout(?string $page): void {
        $path = $this->templatesPath . "/layout/pages/" . $page;

        $layout = $this->getLayout($path);

        echo "<style>" . $layout["style"] . $layout["style-mobile"] . "</style>" . "<script>" . $layout["script"] . "</script>";
    }

    public function showMainPageBlocks() {
        $data = $this->DB->getData("mainPageContent", array("ID", "title", "image", "description"));
        $blockTemplate = $this->getBlock("mainPageBlock", getStyle: false);
        $blockPhotoTemplate = $this->getBlock("mainPagePhotoBlock", getStyle: false);
        $blockTemplateStyle = $this->getBlock("mainPageBlock", getOnlyStyle: true);
        
        $dbError = $this->DB->getErrorMSG();
        if ($dbError != "") {
            $this->showWarning($dbError, isException: true);
        }

        foreach ($data as $block) {
            // echo "<pre>"; print_r($block); echo "</pre>";
            if ($block["image"] == "") {
                $outHtml = $blockTemplate;
                $outHtml = str_replace("#title#", $block["title"], $outHtml);
                $outHtml = str_replace("#content#", $block["description"], $outHtml);
            }
            else {
                $outHtml = $blockPhotoTemplate;
                $outHtml = str_replace("#title#", $block["title"], $outHtml);
                $outHtml = str_replace("#image#", $block["image"], $outHtml);
                $outHtml = str_replace("#content#", $block["description"], $outHtml);
            }
            echo $outHtml;
        }
        echo $blockTemplateStyle;
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

    private function getSystemBlock(?string $name): string {
        switch ($name) {
            case "systemHeader": {
                $path = $this->templatesPath . "/header.html";
                    break;
            }
            case "systemFooter": {
                $path = $this->templatesPath . "/footer.html";
                    break;
            }
            default: {
                    break;
            }
        }
        $html = IO::getFileContent($path);

        return $html;
    }

    private function getBlock(?string $name, ?bool $getStyle = true, ?bool $getOnlyStyle = false): string {
        $html = IO::getFileContent("$this->modulesPath/$name/content.html");
        $css = IO::getFileContent("$this->modulesPath/$name/style.css");
        $cssMobile = IO::getFileContent("$this->modulesPath/$name/style-mobile.css");
        $script = IO::getFileContent("$this->modulesPath/$name/script.js");

        if (!$getStyle)
            $outHtml = $html;
        else if ($getOnlyStyle)
            $outHtml = "<style> $css $cssMobile </style> <script> $script </script>";
        else
            $outHtml = "$html <style> $css $cssMobile </style> <script> $script </script>";

        return $outHtml;
    }

    private function getLayout(?string $path): array {
        $layout = array();

        $layout["script"] = IO::getFileContent($path . "/script.js");
        $layout["style"] = IO::getFileContent($path . "/style.css");
        $layout["style-mobile"] = IO::getFileContent($path . "/style-mobile.css");

        return $layout;
    }
}

?>