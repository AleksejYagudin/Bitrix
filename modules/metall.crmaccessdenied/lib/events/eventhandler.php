<?php
namespace Access\Fields\Events;
use Bitrix\Main\Event;

class EventHandler
{
    public function addString()
    {

        $engine = new \CComponentEngine();
        $page = $engine->guessComponentPath(
            '/crm/',
            ['detail' => '#entity_type#/details/#entity_id#/'],
            $variables
        );

        if ($page !== 'detail') {
            return;
        }

        $allowTypes = ['lead', 'deal', 'contact', 'company'];
        $variables['entity_type'] = strtolower($variables['entity_type']);

        if (!in_array($variables['entity_type'], $allowTypes, true)) {
            return;
        }

        $variables['entity_id'] = (int) $variables['entity_id'];
        if (0 >= $variables['entity_id']) {
            return;
        }

        $entity = $variables['entity_type'].'_details';

        $curUser = \Bitrix\Main\Engine\CurrentUser::get()->getId();
        $arRes = \Bitrix\Main\UserTable::query()
            ->where('ID', '=', $curUser)
            ->addSelect('UF_DEPARTMENT')
            ->exec()
            ->fetchObject();
        $userDepartamens = $arRes->getUfDepartment();
        if(isset($userDepartamens)) {
            \Bitrix\Main\Loader::includeModule('metall.crmaccessdenied');
            $fields = \Access\Fields\GetFields::getFieldInfo($userDepartamens, $entity);
            $fieldsJS = json_encode($fields);
            $entityJS = json_encode($entity);
            if($fieldsJS !=="null") {
            $asset = \Bitrix\Main\Page\Asset::getInstance();
            $asset->addString("<script type='text/javascript'>
          let fieldsFromTable = $fieldsJS,
              entity = $entityJS;             
               
             setTimeout(function (){
               let controlNode = BX.Crm.EntityEditor.getDefault().getAllControls();               
               
               for(let k in controlNode) {
                    let curKey = controlNode[k]._id;
                        if(typeof fieldsFromTable['EDIT'] !== 'undefined') {
                            if(curKey in fieldsFromTable['EDIT'][entity]) {                                                   
                            controlNode[k]._schemeElement._isEditable = false;
                        }    
                    }                    
                        if(typeof fieldsFromTable['VIEW'] !== 'undefined') {
                            if(curKey in fieldsFromTable['VIEW'][entity]) {                                
                            let curNode = controlNode[k]._wrapper;
                            curNode.style.display = 'none'
                        }                    
                    }                    

               }

     }, 1000)
            </script>");
            }



        }

    }
}