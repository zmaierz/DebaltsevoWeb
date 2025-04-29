<?php

function getKernelConfig(): array {
    return [
        "debug" => true, # bool. default: false
        "useCache" => true, # bool. It's not recommended to disable it. default: true
        "modulePath" => "", # string. default: engine/templates/modules
        "templatePath" => "", # string. default: engine/templates
        "mediaPath" => "", # string. default: engine/templates/media
        "cachePath" => "", # string. default: engine/cache
        "deniedSymbols" => array ( # array. if null, default: $, %, ;, ', ", `, #, @, &, ~, <, >
            '$', '%', ';', '\'', '"', '`', '#', '@', '&', '~', '<', '>',
        )
    ];
}

?>