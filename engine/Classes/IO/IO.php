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
    public static function putFileContent(?string $path, ?string $filename, ?string $content, ?bool $append = false): void {
        if (is_dir($path)) {
            if ($append)
                file_put_contents($path . "/" . $filename, $content, FILE_APPEND | LOCK_EX);
            else {
                file_put_contents($path . "/" . $filename, $content, LOCK_EX);
            }
        }
        else if (is_dir($_SERVER["DOCUMENT_ROOT"] . "/" . $path)) {
            if ($append)
                file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/" . $path . "/" . $filename, $content, LOCK_EX);
            else
                file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/" . $path . "/" . $filename, $content, FILE_APPEND | LOCK_EX);
        }
        else
            return;
    }
}

?>