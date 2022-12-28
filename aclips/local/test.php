<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>
<?php

use Bitrix\Main\Context;

$env = Context::getCurrent()->getEnvironment();

$param_1 = $env->get('param_1');
$param_2 = $env->get('param_2');

echo $param_1;


?>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>