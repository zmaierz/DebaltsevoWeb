<?php

include $_SERVER["DOCUMENT_ROOT"] . "/engine/modules/include.php";

class Kernel {
    private ?Database $DB;
    private ?Immunity $immunity;
    
    private ?array $DBConfig;
    private ?array $kernelConfig;

    private ?array $fatalMessages;

    private ?string $modulesPath = "";
    private ?string $templatesPath = "";
    private ?string $cachePath = "";
    private ?string $mediaPath = "";
    private ?string $defaultModulesPath = "/engine/templates/modules";
    private ?string $defaultTemplatesPath = "/engine/templates";
    private ?string $defaultMediaPath = "/engine/templates/media";
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
        
        if ($this->kernelConfig["mediaPath"] == "") {
            $this->mediaPath = $this->defaultMediaPath;
        }

        if ($this->kernelConfig["cachePath"] == "") {
            $this->cachePath = $_SERVER["DOCUMENT_ROOT"] . $this->defaultCachePath;
        }

        $this->immunity = new Immunity($this->kernelConfig["deniedSymbols"]);
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

    public function getPageContent(?string $category, ?string $page): string {
        $out = "";

        # try to check fast-get from cache
        $pageGeneralData = $this->DB->getData('pageList', array(
            'name',
            'alias',
            'category',
            'tableName',
            'cacheName',
        ), " WHERE alias = \"$page\";");
        if ($pageGeneralData['cacheName'] != NULL) {
            echo "Выводим страницу из кэша";
        }
        else {
            $pageTable = $pageGeneralData[0]["tableName"] . "_Page";
            $pageContent = $this->DB->getData($pageTable, array(
                'type',
                'subdata',
                'data'
            ));
            if ($pageContent == NULL) {
                $this->showWarning($this->fatalMessages["page_no_found_in_database"], true);
            }

            $useDocStyle = false;
            $useBlockStyle = false;
            $usePhotoBlockStyle = false;
            for ($pageBlock = 0; $pageBlock < count($pageContent); $pageBlock++) {
                $blockType = $pageContent[$pageBlock]["type"];
                $blockSubData = $pageContent[$pageBlock]["subdata"];
                $blockData = $pageContent[$pageBlock]["data"];

                switch ($blockType) {
                    case "block": {
                        $html = $this->getBlock("pageInfoTextBlock", getStyle: false);
                        $css = $this->getBlock("pageInfoTextBlock", getOnlyStyle: true);
                        $html = str_replace("#!#", $pageContent[$pageBlock]["subdata"], $html);
                        $html = str_replace("#~#", $pageContent[$pageBlock]["data"], $html);
                        if (!$useBlockStyle) {
                            $html .= $css;
                            $useBlockStyle = true;
                        }
                        $out .= $html;
                            break;
                    }
                    case "customCode": {
                        $out .= $pageContent[$pageBlock]["data"];
                            break;
                    }
                    case "doc": {
                        $fileArray = array();
                        $j = 0;
                        for ($i = $pageBlock; $i < count($pageContent); $i++) {
                            if ($pageContent[$i]["type"] == "doc" || $pageContent[$i]["type"] == "file") { # CHECK MY WORK. WHY "$file"
                                $fileArray[$j]["name"] = $pageContent[$i]["subdata"];
                                if ($pageContent[$i]["type"] == "doc")
                                    $subPath = "docs";
                                else if ($pageContent[$i]["type"] == "file")
                                    $subPath = "files";
                                $fileArray[$j]["path"] = $this->mediaPath . "/" . $subPath . "/" . $pageContent[$i]["data"];
                                $j+=1;
                            }
                            else {
                                break;
                            }
                        }
                        $pageBlock = $i;

                        $docHtml = $this->getFileBlock($fileArray);

                        $out .= $docHtml[0];
                        if (!$useDocStyle) {
                            $out .= $docHtml[1];
                            $useDocStyle = true;
                        }
                        
                            break;
                    }
                    case "file": {
                        $fileArray = array();
                        $j = 0;
                        for ($i = $pageBlock; $i < count($pageContent); $i++) {
                            if ($pageContent[$i]["type"] == "doc" || $pageContent[$i]["type"] == $file) {
                                $fileArray[$j]["name"] = $pageContent[$i]["subdata"];
                                if ($pageContent[$i]["type"] == "doc")
                                    $subPath = "docs";
                                else if ($pageContent[$i]["type"] == "file")
                                    $subPath = "files";
                                $fileArray[$j]["path"] = $this->mediaPath . "/" . $subPath . "/" . $pageContent[$i]["data"];
                                $j+=1;
                            }
                            else {
                                break;
                            }
                        }
                        $pageBlock = $i;

                        $docHtml = $this->getFileBlock($fileArray);

                        $out .= $docHtml[0];
                        if (!$useDocStyle) {
                            $out .= $docHtml[1];
                            $useDocStyle = true;
                        }
                            break;
                    }
                    case "link": {
                        $linkArray = array();

                        $j = 0;
                        for ($i = $pageBlock; $i < count($pageContent); $i++) {
                            if ($pageContent[$i]["type"] == "link") {
                                $linkArray[$j] = $pageContent[$i]["data"];
                                $j++;
                            }
                            else {
                                $pageBlock = $i - 1;
                                break;
                            }
                        }

                        $content = "";

                        foreach ($linkArray as $link) {
                            $content .= "<a href=\"$link\">$link</a><br>";
                        }

                        $html = $this->getBlock("pageInfoTextBlock", getStyle: false);
                        $css = $this->getBlock("pageInfoTextBlock", getOnlyStyle: true);
                        
                        $html = str_replace("#!#", "Ссылки", $html);
                        $html = str_replace("#~#", $content, $html);

                        $out .= $html;
                        if (!$useBlockStyle) {
                            $out .= $css;
                            $useBlockStyle = true;
                        }
                            break;
                    }
                    case "photo": {
                        $html = $this->getBlock("pageInfoPhotoBlock", getStyle: false);
                        $css = $this->getBlock("pageInfoPhotoBlock", getOnlyStyle: true);

                        $content = "<img src=\"$this->mediaPath\images\\$blockData\" alt=\"\">";
                        $html = str_replace("#!#", $blockSubData, $html);
                        $html = str_replace("#~#", $content, $html);

                        $out .= $html;
                        if (!$usePhotoBlockStyle) {
                            $out .= $css;
                            $usePhotoBlockStyle = true;
                        }
                            break;
                    }
                    default: {
                        $this->showWarning($this->fatalMessages["page_block_not_allowed"]);
                        echo "Block not found! Name: $blockType";
                        echo "Array: <br><br>";
                        showArray($pageContent[$pageBlock]);
                        echo "<br><br> Array end. <br>";
                            break;
                    }
                }
            }
            echo $out;
        }

        return $out;
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

    private function getFileBlock(?array $files): array {
        $out = array("", "");
        
        $content_1 = IO::getFileContent($this->modulesPath . "/pageInfoFileBlock/content_1.html");
        $content_2 = IO::getFileContent($this->modulesPath . "/pageInfoFileBlock/content_2.html");
        $style = IO::getFileContent($this->modulesPath . "/pageInfoFileBlock/style.css");
        $styleMobile = IO::getFileContent($this->modulesPath . "/pageInfoFileBlock/style-mobile.css");
        $script = IO::getFileContent($this->modulesPath . "/pageInfoFileBlock/script.js");
        include_once($this->modulesPath . "/pageInfoFileBlock/module.php");

        $out[0] = $content_1 . getPageInfoFileBlock_HTML($files) . $content_2;
        $out[1] = "<style>$style $styleMobile </style><script>$script</script>";

        return $out;
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