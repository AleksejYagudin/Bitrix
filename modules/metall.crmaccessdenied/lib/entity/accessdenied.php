<?php
namespace Metall\Crmaccessdenied\Entity;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\BooleanField;


class AccessdeniedTable extends DataManager
{
    public static function getTableName()
    {
        return 'mt_accessdenied';
    }

    public static function getMap()
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),
            (new StringField('ENTITY_TYPE'))
            ->configureRequired(),
            (new StringField('FIELD_NAME'))
            ->configureRequired(),
            (new StringField('FIELD_TITLE'))
                ->configureRequired(),
            (new StringField('ACCESS_ID'))
                ->configureRequired(),
            (new BooleanField('IS_VIEW'))
                ->configureRequired()
                ->configureStorageValues('N', 'Y')
                ->configureDefaultValue(true),
            (new BooleanField('IS_EDIT'))
                ->configureRequired()
                ->configureStorageValues('N', 'Y')
                ->configureDefaultValue(true),
        ];
    }
}