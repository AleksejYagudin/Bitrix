<?php
/**
 * Перегрузка классов
 */

$classDirectoryPath = __DIR__ . '/classes';

/**
 * Конфигуратор переопределяемых классов
 */
$config = [
    'CCrmDeal' => [
        'classPath' => __DIR__ . '/../../bitrix/modules/crm/classes/mysql/crm_deal.php',
        'overrideClass' => 'Aclips\Override\CCrmDeal'
    ],
    'Bitrix\Crm\DealTable' => [
        'classPath' => __DIR__ . '/../../bitrix/modules/crm/lib/deal.php',
        'overrideClass' => 'Aclips\Override\DealTable'
    ]
];

/**
 * Регистрация автозагрузчика
 */

spl_autoload_register(function ($baseClassName) use ($config, $classDirectoryPath) {

    if (!empty($config[$baseClassName])) {

        $classParts = explode('\\', $baseClassName);
        $className = array_pop($classParts);
        $namespace = implode('\\', array_filter($classParts));

        $virtualClassName = "___Virtual{$className}";

        if (file_exists($config[$baseClassName]['classPath'])) {
            $classContent = file_get_contents($config[$baseClassName]['classPath']);
            $classContent = preg_replace('#^<\?(?:php)?\s*#', '', $classContent);
            $classContent = str_replace("class {$className}", "class {$virtualClassName}", $classContent);

            eval($classContent);
        }

        $classFilePath = $classDirectoryPath . '/' . str_replace('\\', '/', $config[$baseClassName]['overrideClass']) . '.php';

        if (file_exists($classFilePath)) {
            $overrideClassContent = file_get_contents($classFilePath);
            $overrideClassContent = preg_replace('#^<\?(?:php)?\s*#', '', $overrideClassContent);
            $overrideClassContent = preg_replace('#extends ([^\s]+)#', "extends {$virtualClassName}", $overrideClassContent);

            $overrideClassContent = preg_replace('#namespace ([^\s]+);#',
                $namespace ? "namespace {$namespace};" : "",
                $overrideClassContent);

            eval($overrideClassContent);
            return;
        }

    }
}, true, true);