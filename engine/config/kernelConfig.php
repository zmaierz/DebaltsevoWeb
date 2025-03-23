<?php

function getKernelConfig(): array {
    return [
        "debug" => true, # bool
        "modulePath" => "" # string. default: engine/templates/modules/
    ];
}

?>