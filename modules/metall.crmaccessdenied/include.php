<?php
CModule::AddAutoloadClasses(
    "metall.crmaccessdenied",
    array(
        "Access\\Fields\\Events\\EventHandler" => "lib/events/eventhandler.php",
        "Access\\Fields\\GetFields" => "lib/handler/getfieldsinfo.php",
    )
);