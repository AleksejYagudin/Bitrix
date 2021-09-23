<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
global $USER;
if (!$USER->IsAdmin())
{
    die();
}
require 'vendor/autoload.php';
use Bitrix\Main\Loader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Bitrix\Main\Application;
use Bitrix\Main\UserTable;
use PhpOffice\PhpSpreadsheet\Style\{Font, Border, Alignment};
Loader::includeModule('socialnetwork');

$application = Application::getInstance();
$context = Application::getInstance()->getContext();
$request = $context->getRequest()->getPost('idGroup');

if($request !=='')
{
    $arResultUserList = array();
    $res = CSocNetUserToGroup::GetList(
        array("ID" => "DESC"),
        array("GROUP_ID" => $request, "=ROLE" => SONET_ROLES_USER
        ),
        false,
        false,
        array('USER_ID')
    );

    while ($ar_res = $res->GetNext())
    {
        $arResultUserList[]= $ar_res['USER_ID'];
    }

    if(!empty($arResultUserList))
    {
        function getDepartament($arr)
        {
            $result = array();
            $res = \Bitrix\Iblock\SectionTable::getList(array(
                'filter' => array('IBLOCK_ID' => 3, 'ID' => $arr),
                'select' => array('NAME')
            ));
            $resultTemp = $res->fetchAll();
            foreach ($resultTemp as $arItem)
            {
                $result[] = $arItem['NAME'];
            }
            return $result;
        }


        $res = UserTable::getList(array(
            'filter' => array('ID' => $arResultUserList),
            'select' => array('ID', 'LAST_LOGIN', 'NAME', 'SECOND_NAME', 'LAST_NAME', 'EMAIL', 'PERSONAL_PHONE', 'PERSONAL_MOBILE', 'WORK_POSITION', 'WORK_DEPARTMENT','UF_DEPARTMENT')
        ));
        
        
        while ($ar_res = $res->fetch())
        {
            $arResult[$ar_res['ID']] = $ar_res;
        }

    }
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->getColumnDimension('A')->setWidth(5);
    $sheet->getColumnDimension('B')->setWidth(20);
    $sheet->getColumnDimension('C')->setWidth(20);
    $sheet->getColumnDimension('D')->setWidth(20);
    $sheet->getColumnDimension('E')->setWidth(40);
    $sheet->getColumnDimension('F')->setWidth(20);
    $sheet->getColumnDimension('G')->setWidth(20);
    $sheet->getColumnDimension('H')->setWidth(50);
    $sheet->getColumnDimension('I')->setWidth(50);
    $sheet->getColumnDimension('J')->setWidth(50);
    $sheet->getColumnDimension('K')->setWidth(20);


    $sheet->getStyle('A1'.$count)->applyFromArray([
        'font' => [
            'bold' => true,
        ],
    ]);
    $sheet->getStyle('B1'.$count)->applyFromArray([
        'font' => [
            'bold' => true,
        ],
    ]);
    $sheet->getStyle('C1'.$count)->applyFromArray([
        'font' => [
            'bold' => true,
        ],
    ]);
    $sheet->getStyle('D1'.$count)->applyFromArray([
        'font' => [
            'bold' => true,
        ],
    ]);
    $sheet->getStyle('E1'.$count)->applyFromArray([
        'font' => [
            'bold' => true,
        ],
    ]);
    $sheet->getStyle('F1'.$count)->applyFromArray([
        'font' => [
            'bold' => true,
        ],
    ]);
    $sheet->getStyle('G1'.$count)->applyFromArray([
        'font' => [
            'bold' => true,
        ],
    ]);
    $sheet->getStyle('H1'.$count)->applyFromArray([
        'font' => [
            'bold' => true,
        ],
    ]);
    $sheet->getStyle('I1'.$count)->applyFromArray([
        'font' => [
            'bold' => true,
        ],
    ]);
    $sheet->getStyle('I1'.$count)->applyFromArray([
        'font' => [
            'bold' => true,
        ],
    ]);
    $sheet->getStyle('J1'.$count)->applyFromArray([
        'font' => [
            'bold' => true,
        ],
    ]);
    $sheet->getStyle('K1'.$count)->applyFromArray([
        'font' => [
            'bold' => true,
        ],
    ]);

    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'Имя');
    $sheet->setCellValue('C1', 'Отчество');
    $sheet->setCellValue('D1', 'Фамилия');
    $sheet->setCellValue('E1', 'Эл. почта');
    $sheet->setCellValue('F1', 'Телефон');
    $sheet->setCellValue('G1', 'Моб. телефон');
    $sheet->setCellValue('H1', 'Должность');
    $sheet->setCellValue('I1', 'Департамент');
    $sheet->setCellValue('J1', 'Департамент/Отдел');
    $sheet->setCellValue('K1', 'Авторизация');


    if(!empty($arResult))
    {
        $count = 2;
        foreach ($arResult as $key => $arItem)
        {
            $sheet->setCellValue('A'.$count, $arItem['ID']);
            $sheet->setCellValue('B'.$count, $arItem['NAME']);
            $sheet->setCellValue('C'.$count, $arItem['SECOND_NAME']);
            $sheet->setCellValue('D'.$count, $arItem['LAST_NAME']);
            $sheet->setCellValue('E'.$count, $arItem['EMAIL']);
            $sheet->setCellValue('F'.$count, $arItem['PERSONAL_PHONE']);
            $sheet->setCellValue('G'.$count, $arItem['PERSONAL_MOBILE']);
            if($arItem['WORK_POSITION'])
            {
                $sheet->getStyle('H'.$count)->applyFromArray([
                    'alignment' => [
                        'wrapText' => true,
                    ]
                ]);
                $sheet->setCellValue('H'.$count, $arItem['WORK_POSITION']);
            }
            if($arItem['WORK_DEPARTMENT'])
            {
                $sheet->getStyle('I'.$count)->applyFromArray([
                    'alignment' => [
                        'wrapText' => true,
                    ]
                ]);
                $sheet->setCellValue('I'.$count, $arItem['WORK_DEPARTMENT']);
            }

            if($arItem['UF_DEPARTMENT'])
            {
                $sheet->getStyle('J'.$count)->applyFromArray([
                    'alignment' => [
                        'wrapText' => true,
                    ]
                ]);
                $dep = implode(', ', getDepartament($arItem['UF_DEPARTMENT']));
                $sheet->setCellValue('J'.$count, $dep);
            }
            $dateTemp = $arItem['LAST_LOGIN']->getTimestamp();
            $lastLoginDate = ConvertTimeStamp($dateTemp, 'FULL');
            $sheet->setCellValue('K'.$count,$lastLoginDate);
            $count++;
        }
    }
    try {

        $res = CSocNetGroup::GetList(array(), array('=ID' => $request), false, false, array('NAME'));
        if($ar_res = $res->fetch())
        {
            $groupName = $ar_res['NAME'];
        }
        $writer = new Xlsx($spreadsheet);
        if(\Bitrix\Main\IO\Directory::isDirectoryExists($_SERVER["DOCUMENT_ROOT"].DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'dev'.DIRECTORY_SEPARATOR.'upload_users_group'))
        {
            \Bitrix\Main\IO\Directory::deleteDirectory($_SERVER["DOCUMENT_ROOT"].DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'dev'.DIRECTORY_SEPARATOR.'upload_users_group');
        }
        \Bitrix\Main\IO\Directory::createDirectory($_SERVER["DOCUMENT_ROOT"].DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'dev'.DIRECTORY_SEPARATOR.'upload_users_group');
        $path = $_SERVER["DOCUMENT_ROOT"].DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'dev'.DIRECTORY_SEPARATOR.'upload_users_group';

        $writer->save($path.DIRECTORY_SEPARATOR.$groupName.'.xlsx');

        echo json_encode($groupName);
    } catch (PhpOffice\PhpSpreadsheet\Writer\Exception $e) {
        echo $e->getMessage();
    }
}

?>
