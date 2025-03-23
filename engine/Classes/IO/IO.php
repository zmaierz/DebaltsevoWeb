<?php

class IO {
    public static function getFileContent(?string $path): ?string {
        if (file_exists($path)) {
            $file = file_get_contents($path);

            return $file;
        }
        else if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/" . $path)) {
            $file = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/" . $path);

            return $file;
        }
        return null;
    }
}

?>