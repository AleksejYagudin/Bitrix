<?php

namespace Aclips\Components;

use Bitrix\Main\Grid;
use Bitrix\Main\UI;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Error;
use Bitrix\Main\Errorable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\ErrorableImplementation;

use Bitrix\Main\Engine\ActionFilter\Csrf;
use Bitrix\Main\Engine\ActionFilter\Authentication;
use Bitrix\Main\Engine\ActionFilter\HttpMethod;


class BaseGridComponent extends \CBitrixComponent implements Controllerable, Errorable
{
    const GRID_ID = 'BASE_GRID';

    const PAGE_SIZE = 15;

    use ErrorableImplementation;

    public function executeComponent()
    {
        $grid_id = self::GRID_ID;
        $grid_options = new Grid\Options($grid_id);

        $grid_filter = $this->getFilterFields();

        $entityRepository = $this->getEntityRepository();

        $filter = $this->getEntityFilter($grid_id, $grid_filter);

        $select = $this->getEntitySelect();

        $sort = $this->getSorting($grid_options);

        $page_size = $this->arParams['PAGE_SIZE'] ?? self::PAGE_SIZE;
        $nav = $this->initNav($grid_options, $page_size);

        $elements = $entityRepository::getList([
            'filter' => $filter,
            'select' => $select,
            "order" => $sort,
            "count_total" => true,
            "offset" => $nav->getOffset(),
            "limit" => $nav->getLimit()
        ]);

        $nav->setRecordCount($elements->getCount());

        $grid_rows = [];

        foreach ($elements as $element) {
            $prepared_element = $this->getPreparedElement($element);

            $actions = $this->getElementActions($element);

            $row = [
                'id' => $element['ID'],
                'data' => $element,
                'columns' => $prepared_element,
                'editable' => 'Y',
                'actions' => $actions
            ];

            $grid_rows[] = $row;
        }

        $this->arResult['NAV'] = $nav;

        $this->arResult['GRID_ID'] = $grid_id;
        $this->arResult['GRID_FILTER'] = $grid_filter;
        $this->arResult['GRID_COLUMNS'] = $this->getGridColumns();
        $this->arResult['ROWS'] = $grid_rows;

        $this->includeComponentTemplate();
    }

    public function getEntityRepository()
    {
        $entityReporitory = new \Bitrix\Main\UserTable();

        return $entityReporitory;
    }

    public function initNav($grid_options, $page_size)
    {
        $grid_id = $grid_options->getid();

        $nav = new UI\PageNavigation($grid_id);

        $nav->allowAllRecords(true)
            ->setPageSize($page_size)
            ->initFromUri();

        return $nav;
    }

    public function getSorting($grid)
    {
        $sort = $grid->GetSorting([
            'sort' => [
                'ID' => 'DESC'
            ],
            'vars' => [
                'by' => 'by',
                'order' => 'order'
            ]
        ]);

        return $sort['sort'];
    }

    public function getEntityFilter($grid_id, $grid_filter)
    {
        return $this->prepareFilter($grid_id, $grid_filter);
    }

    public function getEntitySelect()
    {
        return ['*'];
    }

    public function getPreparedElement($fields)
    {
        return $fields;
    }

    public function getElementActions($fields)
    {
        $actions = [];

        $actions[] = [
            'text' => 'Открыть',
            'onclick' => "BX.Aclips.BaseGrid.List.openProfile({$fields['ID']})",
            'default' => true
        ];

        $actions[] = [
            'text' => 'Удалить',
            'onclick' => "BX.Aclips.BaseGrid.List.showRemoveUserConfirmation({$fields['ID']})",
        ];

        return $actions;
    }

    private function getFilterFields(): array
    {
        $filterFields = [
            [
                'id' => 'NAME',
                'name' => 'Имя',
                'default' => true
            ],
            [
                'id' => 'DATE_REGISTER',
                'name' => 'Дата регистрации',
                'type' => 'date',
                'default' => true
            ]
        ];

        return $filterFields;
    }

    private function getGridColumns(): array
    {
        $columns = [
            [
                'id' => 'NAME',
                'name' => 'Имя',
                'sort' => 'NAME',
                'default' => true
            ],
            [
                'id' => 'DATE_REGISTER',
                'name' => 'Дата регистрации',
                'sort' => 'DATE_REGISTER',
                'default' => true
            ]
        ];

        return $columns;
    }

    private function prepareFilter($grid_id, $grid_filter): array
    {
        $filter = [];

        $filterOption = new \Bitrix\Main\UI\Filter\Options($grid_id);
        $filterData = $filterOption->getFilter([]);

        foreach ($filterData as $k => $v) {
            $filter[$k] = $v;
        }

        $filterPrepared = \Bitrix\Main\UI\Filter\Type::getLogicFilter($filter, $grid_filter);

        if (!empty($filter['FIND'])) {
            $findFilter = [
                'LOGIC' => 'OR',
                [
                    '%NAME' => $filter['FIND']
                ]
            ];

            if (!empty($filterPrepared)) {
                $filterPrepared[] = $findFilter;
            } else {
                $filterPrepared = $findFilter;
            }
        }

        return $filterPrepared;
    }

    public function configureActions()
    {
        return [
            'deleteUserAction' => [
                'prefilters' => [
                    new Authentication(true),
                    new HttpMethod(
                        array(HttpMethod::METHOD_POST)
                    ),
                    new Csrf(),
                ],
                'postfilters' => []
            ]
        ];

    }

    public function deleteUserAction(int $userId): bool
    {
        // Инициализация коллекции ошибок
        $this->errorCollection = new ErrorCollection();

        $result = \CUser::Delete($userId);

        if(!$result) {
            $this->errorCollection->setError(new Error('Произошла ошибка при удалении пользователя'));
            return false;
        }

        return true;
    }


}
