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
}

?>