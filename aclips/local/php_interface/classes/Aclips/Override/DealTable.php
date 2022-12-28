<?php

namespace Aclips\Override;

/**
 * Класс для переопределения поведения Bitrix\Crm\DealTable
 */
class DealTable extends \Bitrix\Crm\DealTable
{
    public static function getRow($params)
    {
        print 'Метод переопределён';
        return parent::getRow($params);
    }


    public static function delete($id)
    {
        throw new \Exception('Метод больше не используется');
    }

    public static function getTableName()
    {
        return 'b_crm_deal';
    }
}