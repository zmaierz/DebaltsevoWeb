<?php

include ($_SERVER["DOCUMENT_ROOT"] . "/engine/config/DB.php");
include ($_SERVER["DOCUMENT_ROOT"] . "/engine/config/kernelConfig.php");

include ($_SERVER["DOCUMENT_ROOT"] . "/engine/Classes/Database/Database.php");
include ($_SERVER["DOCUMENT_ROOT"] . "/engine/Classes/IO/IO.php");
include ($_SERVER["DOCUMENT_ROOT"] . "/engine/Classes/ModuleManager/ModuleManager.php");

include ($_SERVER["DOCUMENT_ROOT"] . "/engine/Classes/Exceptions/db_config_exception.php");
include ($_SERVER["DOCUMENT_ROOT"] . "/engine/Classes/Exceptions/db_connect_error.php");

?>