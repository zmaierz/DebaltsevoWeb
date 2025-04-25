<?php

class Immunity {
    private ?array $defaultDeniedSymbols = array(
        '$', '%', ';', '\'', '"', '`', '#', '@', '&', '~'
    );
    private ?array $deniedSymbols;
    public function __construct(?array $deniedSymbols) {
        if ($deniedSymbols == null)
            $this->deniedSymbols = $this->defaultDeniedSymbols;
        else
            $this->deniedSymbols = $deniedSymbols;
    }
    public function validateString(?string $temp): bool {
        $temp = trim($temp);
        foreach ($this->deniedSymbols as $i) {
            if (str_contains($temp, $i)) {
                return false;
            }
        }
        return true;
    }
}

?>