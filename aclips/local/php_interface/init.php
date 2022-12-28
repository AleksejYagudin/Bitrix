<?php
use Bitrix\Main\Context;
spl_autoload_register(function($sClassName)
{
    $sClassFile = __DIR__.'/classes';

    if ( file_exists($sClassFile.'/'.str_replace('\\', '/', $sClassName).'.php') )
    {
        require_once($sClassFile.'/'.str_replace('\\', '/', $sClassName).'.php');
        return;
    }

    $arClass = explode('\\', strtolower($sClassName));
    foreach($arClass as $sPath )
    {
        $sClassFile .= '/'.ucfirst($sPath);
    }

    $sClassFile .= '.php';
    if (file_exists($sClassFile))
    {
        require_once($sClassFile);
    }
});

require_once(__DIR__.'/override.php');

foreach( [
             __DIR__.'/kernel.php',
             __DIR__.'/events.php',
             __DIR__.'/vendor/autoload.php',
         ]
         as $filePath ) {
    if ( file_exists($filePath) )
    {
        require_once($filePath);
    }
}

$envFilePath = '/home/bitrix/.env';

if (file_exists($envFilePath)) {
    $env = Context::getCurrent()->getEnvironment();

    $iniParams = \parse_ini_file($envFilePath, true, INI_SCANNER_TYPED);

    foreach ($iniParams as $key => $value) {
        $env->set($key, $value);
    }
    unset($iniParams);
}
unset($envFilePath);