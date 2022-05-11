<?php
defined('B_PROLOG_INCLUDED') || die;

use \Metall\Crmaccessdenied\Entity\AccessdeniedTable;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use \Bitrix\Main\Entity\Base;

class metall_crmaccessdenied extends CModule
{
    const MODULE_ID = 'metall.crmaccessdenied';
    var $MODULE_ID = self::MODULE_ID;
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;    var $strError = '';

    function __construct()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__) . '/version.php');
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];

        $this->MODULE_NAME = Loc::getMessage('METALL_CRM_ACCESS_DENIED.MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('METALL_CRM_ACCESS_DENIED.MODULE_DESC');

        $this->PARTNER_NAME = Loc::getMessage('METALL_CRM_ACCESS_DENIED.PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('METALL_CRM_ACCESS_DENIED.PARTNER_URI');
    }

    function DoInstall()
    {
        ModuleManager::registerModule(self::MODULE_ID);

        $this->InstallDB();
//        $this->InstallFiles();
        $this->InstallEvents();
    }

    function DoUninstall()
    {
        $this->UnInstallEvents();
//        $this->UnInstallFiles();
        $this->UnInstallDB();

        ModuleManager::unRegisterModule(self::MODULE_ID);
    }

    function InstallDB()
    {
        Loader::includeModule('metall.crmaccessdenied');

        $db = Application::getConnection();

         $application = AccessdeniedTable::getEntity();
         if (!$db->isTableExists($application->getDBTableName())) {
             $application->createDbTable();
          }
    }

    function UnInstallDB()
    {
        Loader::includeModule('metall.crmaccessdenied');

        Application::getConnection(\Metall\Crmaccessdenied\Entity\AccessdeniedTable::getConnectionName())->
        queryExecute('drop table if exists '.Base::getInstance('\Metall\Crmaccessdenied\Entity\AccessdeniedTable')->getDBTableName());

        Option::delete($this->MODULE_ID);
    }

    function InstallEvents()
    {
        EventManager::getInstance()->registerEventHandler(
            "main",
            "OnBeforeProlog",
            $this->MODULE_ID,
            "Access\\Fields\\Events\\EventHandler",
            "addString"
        );
    }

    function UnInstallEvents()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->unRegisterEventHandler(
            'main',
            'OnBeforeProlog',
            self::MODULE_ID,
            'Access\\Fields\\Events\\EventHandler',
            'addString'
        );

    }

//    function InstallFiles()
//    {
//        $documentRoot = Application::getDocumentRoot();
//
//        CopyDirFiles(
//            __DIR__ . '/files/components',
//            $documentRoot . '/local/components',
//            true,
//            true
//        );
//
//        CopyDirFiles(
//            __DIR__ . '/files/pub/crm',
//            $documentRoot . '/crm',
//            true,
//            true
//        );
//
//        CUrlRewriter::Add(array(
//            'CONDITION' => '#^/crm/stores/#',
//            'RULE' => '',
//            'ID' => 'academy.crmstores:stores',
//            'PATH' => '/crm/stores/index.php',
//        ));
//    }

//    function UnInstallFiles()
//    {
//        DeleteDirFilesEx('/crm/stores');
//        DeleteDirFilesEx('/local/components/academy.crmstores');
//
//        CUrlRewriter::Delete(array(
//            'ID' => 'academy.crmstores:stores',
//            'PATH' => '/crm/stores/index.php',
//        ));
//    }
}