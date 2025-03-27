<?php

include $_SERVER["DOCUMENT_ROOT"] . "/engine/modules/include.php";

class Kernel {
    private ?Database $DB;
    
    private ?array $DBConfig;
    private ?array $kernelConfig;

    private ?array $fatalMessages;

    private ?string $modulesPath = "";
    private ?string $templatesPath = "";
    private ?string $cachePath = "";
    private ?string $defaultModulesPath = "/engine/templates/modules";
    private ?string $defaultTemplatesPath = "/engine/templates";
    private ?string $defaultCachePath = "/engine/cache";
    
    public function __construct() {
        $this->DBConfig = getDBConfig();
        $this->kernelConfig = getKernelConfig();
        
        $this->fatalMessages = getFatalMessages();

        if ($this->kernelConfig["modulePath"] == "") {
            $this->modulesPath = $_SERVER["DOCUMENT_ROOT"] . $this->defaultModulesPath;
        }

        if ($this->kernelConfig["templatePath"] == "") {
            $this->templatesPath = $_SERVER["DOCUMENT_ROOT"] . $this->defaultTemplatesPath;
        }
        if ($this->kernelConfig["cachePath"] == "") {
            $this->cachePath = $_SERVER["DOCUMENT_ROOT"] . $this->defaultCachePath;
        }

        $this->DB = new Database($this->DBConfig, $this->kernelConfig["debug"]);
        $dbError = $this->DB->getErrorMSG();
        if ($dbError != "") {
            if ($this->kernelConfig["debug"] == true) {
                $fatalMessage = $this->fatalMessages["database_no_connect_error"] . "<br><br>" . $dbError;
            }
            else {
                $fatalMessage = $this->fatalMessages["database_no_connect_error"];
            }
            $this->showFatal($fatalMessage);
        }
    }

    public function showHeader(): void {
        $cacheHeader = IO::getFileContent($this->cachePath . "/system/header.html");
        if ($cacheHeader == null || $this->kernelConfig["debug"] == true) {
            $modulePath = $this->templatesPath . "/modules/headerMenu";
            $header1 = IO::getFileContent($modulePath . "/header_1.html");
            $header2 = IO::getFileContent($modulePath . "/header_2.html");
            include_once($modulePath . "/module.php");

            $categoryList = $this->DB->getData("categoryList", array("number", "name", "url"));
            $pagesList = array();

            $j = 0;
            foreach($categoryList as $i) {
                $pagesList[$j]["name"] = $i["name"];
                $pagesList[$j]["url"] = $i["url"];
                $pagesList[$j]["pages"] = $this->DB->getDataForMenuWithCategory($i["name"]);
                $j++;
            }

            $menuCode = getHeaderMenu(array($pagesList));
            
            echo $header1;
            echo $menuCode;
            echo $header2;

            $cacheHeader = $header1 . $menuCode . $header2;
            IO::putFileContent(path: $this->cachePath . "/system/", filename:  "header.html", content: $cacheHeader);
        }
        else {
            echo $cacheHeader;
        }
    }

    public function showFooter(): void {
        $cacheFooter = IO::getFileContent($this->cachePath . "/system/footer.html");
        if ($cacheFooter == null || $this->kernelConfig["debug"] == true) {
            $block = $this->getSystemBlock("systemFooter");

            echo $block;

            IO::putFileContent(path: $this->cachePath . "/system/", filename:  "footer.html", content: $block);
        }
        else {
            echo $cacheFooter;
        }
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

    public function showMainPageNews(?int $showCount = 6): void {
        $htmlTemplate = $this->getBlock("mainPageNewsInnerContainer", getStyle: false);
        $htmlStyleTemplate = $this->getBlock("mainPageNewsInnerContainer", getOnlyStyle: true);
        $htmlOut = "";

        $data = $this->DB->getData("news", array("ID", "title", "short-descr"));

        if ($data == null) {
            echo "Странно! Ничего нет :(";
            $this->showWarning("Внимание!<br>Нет никаких новостей!");
        }
        else {
            for ($i = count($data) - $showCount; $i < count($data); $i++) {
                $temp = $htmlTemplate;
                $temp = str_replace("#title#", $data[$i]["title"], $temp);
                $temp = str_replace("#content#", $data[$i]["short-descr"], $temp);
                $htmlOut .= $temp;
            }
        }

        $htmlOut .= $htmlStyleTemplate;
        
        echo $htmlOut;
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

    private function showFatal(?string $message): void {
        echo $message;
        die();
    }
}

?>