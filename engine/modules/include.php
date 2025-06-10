<?php

# Connect configs
include ($_SERVER["DOCUMENT_ROOT"] . "/engine/config/DB.php");
include ($_SERVER["DOCUMENT_ROOT"] . "/engine/config/kernelConfig.php");

# Connect Classes
include ($_SERVER["DOCUMENT_ROOT"] . "/engine/Classes/Database/Database.php");
include ($_SERVER["DOCUMENT_ROOT"] . "/engine/Classes/IO/IO.php");
include ($_SERVER["DOCUMENT_ROOT"] . "/engine/Classes/Immunity/Immunity.php");

# Connect Exceptions
include ($_SERVER["DOCUMENT_ROOT"] . "/engine/Classes/Exceptions/db_config_exception.php");
include ($_SERVER["DOCUMENT_ROOT"] . "/engine/Classes/Exceptions/db_connect_error.php");
include ($_SERVER["DOCUMENT_ROOT"] . "/engine/Classes/Exceptions/immunity_warning_sql_injection.php");

# Connect Modules
include ($_SERVER["DOCUMENT_ROOT"] . "/engine/modules/functions.php");
include ($_SERVER["DOCUMENT_ROOT"] . "/engine/modules/messages.php");
?>