<?php

use Bitrix\Main\DI\ServiceLocator;

if (class_exists('\Bitrix\Main\DI\ServiceLocator')) {
    $serviceLocator = ServiceLocator::getInstance();

    $serviceLocator->addInstanceLazy('aclips.bookrental.entityManager', [
        'constructor' => static function () use ($serviceLocator) {

            $entitiesConfig = [
                \Aclips\BookRental\Entity\Edition::class => [
                    'tableClass' => \Aclips\BookRental\Internal\EditionTable::class,
                ],
            ];

            $config['entitiesConfig'] = $entitiesConfig;

            return new \enoffspb\BitrixEntityManager\BitrixEntityManager($config);

        }
    ]);
}