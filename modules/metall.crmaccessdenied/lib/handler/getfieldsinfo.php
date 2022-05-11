<?
namespace Access\Fields;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");


use Bitrix\Main\HttpApplication;
use Bitrix\Crm\Service;
use Bitrix\Ui\EntityForm\EntityFormConfigTable;
use Bitrix\Main\Localization\Loc;
use Metall\Crmaccessdenied\Entity\AccessdeniedTable;

\Bitrix\Main\Loader::includeModule('crm');
\Bitrix\Main\Loader::includeModule('metall.crmaccessdenied');
Loc::loadMessages(__FILE__);
class GetFields
{
    protected object $request;
    protected array $unicFields = [];
    protected array $tableFields = [];
    protected array $exceptionFields = ['UTM', 'EXPORT', 'OPENED', 'IM', 'HONORIFIC'];
    protected array $departaments = [];
    protected array $result;


    public function __construct()
    {
        $this->request = HttpApplication::getInstance()->getContext()->getRequest();
        $this->unicFields = $this->getDefaultFieldContact();
        $this->tableFields = $this->getResFromTable();
        $this->departaments = $this->getDepartament();
    }

    public function getRequest()
    {
        if($this->request->isPost() &&
            ($this->request->getPost('save') || $this->request->getPost('apply')) &&
            check_bitrix_sessid()
        ) {
            return $this->request->getPostList()->toArray();
        } else {
            return false;
        }
    }

    protected function getDefaultFieldContact()
    {
        $fieldsFromDb = EntityFormConfigTable::query()
        ->where('CATEGORY', '=', 'CRM')
        ->addSelect('ENTITY_TYPE_ID')
        ->addSelect('CONFIG')
        ->addSelect('NAME')
        ->addOrder('ENTITY_TYPE_ID', 'ASC')
        ->exec()
        ->fetchAll();

        $resultFirst = [];
        $resultSecond = [];
        $resultThird = [];
        $nameEntity = '';

        foreach ($fieldsFromDb as $field) {
            if($field['ENTITY_TYPE_ID'] !== $nameEntity) {
                $nameEntity = $field['ENTITY_TYPE_ID'];
            }
            $resultFirst[$nameEntity][$field['NAME']] = $field['CONFIG'];
        }

        foreach ($resultFirst as $key1 => $item1) {
            foreach ($item1 as $key2 => $item2) {
                foreach ($item2 as $key3 => $item3) {
                    $resultSecond[$key1][] = $this->getUnicFields($item3);
                }
            }
        }

        foreach ($resultSecond as $key1 => $item1) {
            switch ($key1) {
                case 'contact_details':
                    $entityName = 'CONTACT';
                    break;
                case 'company_details':
                    $entityName = 'COMPANY';
                    break;
            }
            foreach ($item1 as $key2 => $item2) {
                foreach ($item2 as $key3 => $item3) {
                    if(in_array($key3, $this->exceptionFields)) continue;
                    $resultThird[$key1][$item3['TITLE']] = $item3;
                    $resultThird[$key1][$item3['TITLE']]['TITLE_RU'] = Loc::getMessage('ACCESS_'.$entityName.'_'.$item3['TITLE']) ?? Loc::getMessage('ACCESS_CONTACT_USER_FIELDS');
                }
            }

        }

        return $resultThird;
    }

    protected function getUnicFields($arr)
    {
        $unicFields = [];
        foreach ($arr['elements'] as $key1 => $item1) {
            foreach ($item1['elements'] as $item2) {
                $unicFields[$item2['name']] = ['TITLE' => $item2['name']];
            }
        }
        return $unicFields;

    }

    protected function getResFromTable(): array
    {
        $arResultFromTable = [];
        $arRes = AccessdeniedTable::query()
            ->addSelect('*')
            ->addOrder('ENTITY_TYPE', 'ASC')
            ->exec()
            ->fetchCollection();

        foreach ($arRes as $item) {
            $arResultFromTable[$item->getEntityType()][$item->getFieldName()]['ID'] = $item->getId();
            $arResultFromTable[$item->getEntityType()][$item->getFieldName()]['TITLE'] = $item->getFieldTitle();
            $arResultFromTable[$item->getEntityType()][$item->getFieldName()]['USERS'] = unserialize($item->getAccessId());
            $arResultFromTable[$item->getEntityType()][$item->getFieldName()]['IS_VIEW'] = $item->getIsView();
            $arResultFromTable[$item->getEntityType()][$item->getFieldName()]['IS_EDIT'] = $item->getIsEdit();
        }
        return $arResultFromTable;
    }
    protected function getDepartament()
    {
        $departments= [];
        $rsDepartments = \CIBlockSection::GetTreeList(array(
            "IBLOCK_ID"=>intval(\COption::GetOptionInt('intranet', 'iblock_structure', false)),
        ));

        while($arDepartment = $rsDepartments->GetNext()) {
            $departments[$arDepartment['ID']] = str_repeat(" . ", $arDepartment["DEPTH_LEVEL"]).$arDepartment['NAME'];
        }
        return $departments;
    }



    public function getResult()
    {
        $result = [];
        if(count($this->tableFields['contact_details']) > 0) {
            foreach ($this->unicFields as $key => $item) {
                if($key === 'contact_details') {
                    $result[$key] = array_merge($this->unicFields[$key], $this->tableFields[$key]);
                }

            }
        } else {
            $result['contact_details'] = $this->unicFields['contact_details'];
        }

        if(count($this->tableFields['company_details']) > 0) {
            foreach ($this->unicFields as $key => $item) {
                if($key === 'company_details') {
                    $result[$key] = array_merge($this->unicFields[$key], $this->tableFields[$key]);
                }
            }
        } else {
            $result['company_details'] = $this->unicFields['company_details'];

        }
        $result['DEPARTAMENTS'] = $this->departaments;
        return $result;
    }


    public function creatField($fieldName, $item, $entity)
    {
        switch ($entity) {
            case 'contact_details':
                $entityName = 'CONTACT';
                break;
            case 'company_details':
                $entityName = 'COMPANY';
                break;
        }

        $userGroupSerialize = [];
        foreach ($item as $key => $arItem) {
            if(is_array($arItem)) {
                $userGroupSerialize[$key] = $arItem;
            }
        }

        $fields = AccessdeniedTable::createObject();
        $fields->setEntityType($entity);
        $fields->setFieldName($fieldName);
        $fields->setFieldTitle(Loc::getMessage('ACCESS_'.$entityName.'_'.$fieldName) ?? Loc::getMessage('ACCESS_CONTACT_USER_FIELDS'));
        $fields->setAccessId(serialize($userGroupSerialize));
        $fields->setIsView(($item['is_view'] === 'on' ? 'Y':'N'));
        $fields->setIsEdit(($item['is_edit'] === 'on' ? 'Y':'N'));
        $fields->save();
    }

    public function updateField($item)
    {
        $userGroupSerialize = [];
        if(isset($item['is_view']) || isset($item['is_edit'])) {
            foreach ($item as $key => $arItem) {
                if(is_array($arItem)) {
                    $userGroupSerialize[$key] = $arItem;
                }
            }
        }


        $data = [
            'IS_VIEW' => ($item['is_view'] == 'on') ? 'Y':'N',
            'IS_EDIT' => ($item['is_edit'] == 'on') ? 'Y':'N',
            'ACCESS_ID' => serialize($userGroupSerialize)
        ];
        AccessdeniedTable::update($item['ID'], $data);
    }

    public static function getFieldInfo($userDepartament, $entity)
    {
        $result = [];

        $arRes = AccessdeniedTable::query()
            ->where('ENTITY_TYPE', '=', $entity)
            ->addSelect('*')
            ->addOrder('FIELD_NAME', 'ASC')
            ->exec()
            ->fetchAll();


        foreach ($arRes as $arField) {
            $groupAccess = unserialize($arField['ACCESS_ID']);
            foreach ($groupAccess['UF_DEPARTMENT_VIEW'] as $access) {

                    if(in_array($access, $userDepartament)) {
                        if($arField['IS_VIEW'] === 'N' && $arField['IS_EDIT'] === 'N') {
                            continue;
                        }
                        $result['VIEW'][$entity][$arField['FIELD_NAME']]['ID'] = $arField['ID'];
                        $result['VIEW'][$entity][$arField['FIELD_NAME']]['TITLE'] = $arField['FIELD_TITLE'];
                    }

            }
            foreach ($groupAccess['UF_DEPARTMENT_EDIT'] as $access) {

                    if(in_array($access, $userDepartament)) {
                        if($arField['IS_VIEW'] === 'N' && $arField['IS_EDIT'] === 'N') {
                            continue;
                        }
                        $result['EDIT'][$entity][$arField['FIELD_NAME']]['ID'] = $arField['ID'];
                        $result['EDIT'][$entity][$arField['FIELD_NAME']]['TITLE'] = $arField['FIELD_TITLE'];
                    }
                }

        }

        return $result;
    }


}