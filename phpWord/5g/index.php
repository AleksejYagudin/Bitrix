<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");?>
<?
//РАБОТАЮТ сложные таблицы, простые работаею с ошибкой
global $USER;
if (!$USER->IsAuthorized())
{
    LocalRedirect('/');
}
require_once 'bootstrap.php';
use \Bitrix\Main\Loader;
Loader::includeModule('iblock');
use \PhpOffice\PhpWord\Style\Language;

$iblock = 155;
$elemId = $_GET['id'];


$res = \Bitrix\Iblock\ElementTable::getList(array(
    'filter' => array(
        'IBLOCK_ID' => $iblock,
        'IBLOCK_SECTION_ID' => $elemId,
        'ACTIVE' => 'Y'
    ),
    'order' => array('SORT' => 'ASC')
    /* 'select' => array(
            'ID', 'NAME', 'DETAIL_TEXT',
        )*/
));
$arResult = array();
while ($ar_res = $res->fetch())
{
    $arResult[$ar_res['ID']] = $ar_res;
}

function m2t($millimeters){
    return floor($millimeters*56.7); //1 твип равен 1/567 сантиметра
}

$title = \Bitrix\Iblock\SectionTable::getRowById($elemId)['NAME'];

$phpWord = new \PhpOffice\PhpWord\PhpWord();
$phpWord->setDefaultFontName('Times New Roman');
$phpWord->setDefaultFontSize(12);


$numberNestedListStyle = ['listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER_NESTED];
$section = $phpWord->addSection(array('marginLeft' => 1000, 'marginRight' => 1000, 'marginTop' => 500, 'marginBottom' => 500));
$phpWord->setDefaultParagraphStyle(array('align' => 'both','spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(0)));
$footer = $section->addFooter();
$footer->addPreserveText('{PAGE}', array('name'=>'Times New Roman', 'size' => 10),array('alignment' => 'right'));
$header = $section->addHeader();
$header->firstPage();
$curDataTime = date('d.m.Y H:i:s');
$header->addPreserveText($curDataTime, array('alignment' => 'left', 'italic' => true));


$section->addText("Доклад о развитии высокотехнологического направления", array(
    'name' => 'Times New Roman',
    'size' => 14,
    'color' => '000000',
    'bold' => true
),
    array(
        'align' => 'center',
        'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(0),
    )
);

$section->addText('«'.trim($title).'»', array(
    'name' => 'Times New Roman',
    'size' => 14,
    'color' => '000000',
    'bold' => true
),
    array(
        'align' => 'center',
        'spaceAfter' => '400'
    )
);
$section->addText('Оглавление', array(
    'name' => 'Times New Roman',
    'size' => 14,
    'color' => '000000',
    "bold" => true,
),
    array(
        'align' => 'center',
        'spaceAfter' => '400',

    )
);
$phpWord->getSettings()->setUpdateFields(true);


$fontStyle = array(
    'spaceAfter' => 60,
    'size' => 12,

);
$fontStyle2 = array('size' => 10);

$phpWord->addTitleStyle(1, array('size' => 16, 'color' => '000000', 'bold' => true), array('align' => 'center', 'spaceAfter' => '400'));
$phpWord->addTitleStyle(2, array('size' => 14, 'color' => '000000', 'bold' => true));
$phpWord->addTitleStyle(3, array('size' => 12, 'color' => '000000', 'bold' => true));
$phpWord->addTitleStyle(4, array('size' => 12));

$toc = $section->addTOC($fontStyle);
$section->setStyle( array(
    'marginLeft' => 1133,
    'marginRight' => 852,
    'marginTop' => 1133,
    'marginBottom' => 1133,

));

$arrTitle = array(
    'I. О ТЕКУЩЕМ СОСТОЯНИИ РАЗВИТИЯ ВТН В МИРЕ И ПРОГНОЗЫ ЕГО РАЗВИТИЯ',
    'II. О ТЕКУЩЕМ СОСТОЯНИИ РАЗВИТИЯ ВТН В РОССИИ ПО СРАВНЕНИЮ С ЛИДИРУЮЩИМИ СТРАНАМИ, ПЛАНИРУЕМЫЕ ЦЕЛЕВЫЕ РЕЗУЛЬТАТЫ ЕГО РАЗВИТИЯ ДО 2024 ГОДА И В ПЕРСПЕКТИВЕ ДО 2030 ГОДА',
    'III. КЛЮЧЕВЫЕ НАПРАВЛЕНИЯ ДЕЙСТВИЙ ПО ДОСТИЖЕНИЮ ЦЕЛЕВЫХ РЕЗУЛЬТАТОВ РАЗВИТИЯ ВТН, В ТОМ ЧИСЛЕ ПО СОЗДАНИЮ НА ОСНОВЕ РЫНОЧНЫХ ОТНОШЕНИЙ СИСТЕМЫ РАЗВИТИЯ ВТН, РЕСУРСНАЯ ОБЕСПЕЧЕННОСТЬ',
    'ПРИЛОЖЕНИЕ',
    '2.2 Анализ динамики развития выделенных базовых технологий'
);

function printName($name, $arrTitle)
{
    if (strpos($name, 'ВВЕДЕНИЕ') !== false) {
        return array($name, 1);
    } elseif (strpos($name, 'ОБЩАЯ ИНОФРМАЦИЯ И ГЛОССАРИЙ') !== false) {
        return array($name, 1);
    } elseif (substr($name, 0, 3) == '1.1') {
        return array(subtitle($arrTitle[0]), 1);
    } elseif (substr($name, 0, 3) == '2.1') {
        return array(subtitle($arrTitle[1]), 1);
    } elseif ($name == '1.2.2. Анализ динамики развития выделенных базовых технологий') {
        return array('2.1 Анализ динамики развития выделенных базовых технологий', 3);
    } elseif (substr($name, 0, 3) == '3.1') {
        return array(subtitle($arrTitle[2]), 1);
    } elseif ($name == '4. ОБЩИЕ ВЫВОДЫ ПО РАЗВИТИЮ ВТН И СВЯЗЬ С ПРОЕКТАМИ-МАЯКАМИ') {
        return array('IV. ОБЩИЕ ВЫВОДЫ ПО РАЗВИТИЮ ВТН И СВЯЗЬ С ПРОЕКТАМИ-МАЯКАМИ', 1);
    } elseif (substr($name, 0, 3) == '5.1') {
        return array(subtitle($arrTitle[3]), 1);
    } elseif (strpos($name, 'Выводы') !== false) {
        return array('Выводы', 2);
    } elseif (substr($name, 0, 3) == '5.1' || substr($name, 0, 3) == '5.2') {
        $newTitle = mb_substr($name, 5);
        return array($newTitle, 2);

    } else {

        return false;


    }
}
function clearStr($str)
{
    if($str !== '')
    {
        $html = str_replace(array('h1', 'h2', 'h3', 'h4', 'h5'), "p", $str);
        $html= str_replace('&nbsp;', " ", $html);
        $html = str_replace("<br/>", "", $html);
        $html = str_replace("<br />", "", $html);$html = str_replace("&deg;", "°", $html);
        $html = str_replace("&pound;", "£", $html);
        $html = str_replace("&euro;", "€", $html);
        $html = str_replace("&para;", "¶", $html);
        $html = str_replace("&sect;", "§", $html);
        $html = str_replace("&copy;", "©", $html);
        $html = str_replace("&reg;", "®", $html);
        $html = str_replace("&trade;", "™", $html);
        $html = str_replace("&deg;", "°", $html);
        $html = str_replace("&plusmn;", "±", $html);
        $html = str_replace("&frac14;", "¼", $html);
        $html = str_replace("&frac12;", "½", $html);
        $html = str_replace("&frac34;", "¾", $html);
        $html = str_replace("&times;", "×", $html);
        $html = str_replace("&divide;", "÷", $html);
        $html = str_replace("&fnof;", "ƒ", $html);
        $html = str_replace("&larr;", "←", $html);
        $html = str_replace("&uarr;", "↑", $html);
        $html = str_replace("&rarr;", "→", $html);
        $html = str_replace("&darr;", "↓", $html);
        $html = str_replace("&harr;", "↔", $html);
        $html = str_replace("&spades;", "♠", $html);
        $html = str_replace("&clubs;", "♣", $html);
        $html = str_replace("&hearts;", "♥", $html);
        $html = str_replace("&diams;", "♦", $html);
        $html = str_replace("&quot;", "\"", $html);
        $html = str_replace("&amp;", "and", $html);
        $html = str_replace("&lt;", "<", $html);
        $html = str_replace("&gt;", ">", $html);
        $html = str_replace("&hellip;", "…", $html);
        $html = str_replace("&prime;", "′", $html);
        $html = str_replace("&Prime;", "″", $html);
        $html = str_replace("&ndash;", "–", $html);
        $html = str_replace("&mdash;", "—", $html);
        $html = str_replace("&lsquo;", "‘", $html);
        $html = str_replace("&rsquo;", "’", $html);
        $html = str_replace("&sbquo;", "‚", $html);
        $html = str_replace("&ldquo;", "“", $html);
        $html = str_replace("&rdquo;", "”", $html);
        $html = str_replace("&bdquo;", "„", $html);
        $html = str_replace("&laquo;", "«", $html);
        $html = str_replace("&raquo;", "»", $html);
        $html = str_replace("&alpha;", "α", $html);
        $html = str_replace("&auml;", "Ä", $html);
        $html = str_replace("&ouml;", "Ö", $html);
        $html = str_replace("&iacute;", "í", $html);

        $html = str_replace("<p><em> </em></p>", "", $html);
        $html = str_replace("<p> </p>", "", $html);


        $html = str_replace('<span style="font-family: \'times new roman\', times, serif;">', "<span>", $html);
        $html = str_replace('<tr></tr>', "", $html);
        $html = str_replace('<p></p>', "", $html);
        $html = str_replace('style="font-family: \'times new roman\', times, serif;">', ">", $html);
        $html = preg_replace('/\s?width=["][^"]*"\s?/i', '', $html);
        $html = str_replace('<p>', "<p><span>\t</span>", $html);
        $html = preg_replace('/\s?<table[^>]*?>\s?/', '<table align="center" style = "border: 1px #000000 solid;">', $html);
        $html = preg_replace('/\s?<a name[^>]*?>.*?<\/a>\s?/si', '', $html);

        $html = str_replace('<span>	</span>', '', $html);
        $html = preg_replace('/\s?style=["][^"]*"\s?/i', ' ', $html);
        $html = str_replace('<td >', '<td style="text-align: left;">', $html);
        $html = str_replace('<td>', '<td style="text-align: left;">', $html);

        $html = preg_replace('/\s?<a>.*?<\/a>\s?/si', '', $html);

    }
    return $html;
}
function clearStr2($str)
{
    if($str !== "")
    {
        $value=str_replace('<', "\n<", $str);
        $value=str_replace('>', ">\n", $value);
        $value=str_replace('>', ">\n", $value);

        $value=str_replace('<tr >', "<tr>", $value);
        $value=str_replace('<td >', "<td>", $value);
        $value=str_replace('&nbsp;', " ", $value);
        $value = str_replace("&deg;", "°", $value);
        $value = str_replace("&alpha;", "α", $value);
        $value = str_replace("&le;", "≤", $value);
        $value = str_replace("&gt;", ">", $value);
        $value = str_replace("&times;", "×", $value);
        $value = str_replace("&plusmn;", "±", $value);
        $value = str_replace("&middot;", "·", $value);
        $value = str_replace("<br/>", "", $value);
        $value = str_replace("<br />", "", $value);
        $value = str_replace('&ndash;', " - ", $value);
        $value = str_replace('&laquo;', "«", $value);
        $value = str_replace('&raquo;', "»", $value);
        $value = str_replace('&hellip;', "...", $value);
        $value = str_replace("&amp;", "and", $value);
        $value = str_replace("&mdash;", "—", $value);
        $value = str_replace("&ldquo;", "“", $value);
        $value = str_replace("&rdquo;", "”", $value);

        $value = preg_replace('/\s?style=["][^"]*"\s?/i', ' ', $value);


    }
    return $value;
}
function subtitle($str)
{
    $firstElem = substr($str,0,1);
    if(is_numeric($firstElem))
    {
        $subTitle =  mb_substr($str, 2);
        return $subTitle;
    }
    else
    {
        return $str;
    }
}
function searchCaption($str)
{
    if(strpos($str, '<caption>') !==false)
    {
        return $str;
    }
    else
    {
        return false;
    }

}
function createTable($html, $phpWord, $textBefore = '', $elemId)
{
    if(\Bitrix\Main\IO\Directory::isDirectoryExists($_SERVER["DOCUMENT_ROOT"].DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'dev'.DIRECTORY_SEPARATOR.'5g'))
    {
        \Bitrix\Main\IO\Directory::deleteDirectory( $_SERVER["DOCUMENT_ROOT"].DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'dev'.DIRECTORY_SEPARATOR.'5g');
    }
    \Bitrix\Main\IO\Directory::createDirectory( $_SERVER["DOCUMENT_ROOT"].DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'dev'.DIRECTORY_SEPARATOR.'5g');
    file_put_contents($_SERVER["DOCUMENT_ROOT"].DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'dev'.DIRECTORY_SEPARATOR.'5g'.DIRECTORY_SEPARATOR.'table_1_2.html', $html);
    if(file_exists($_SERVER["DOCUMENT_ROOT"].DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'dev'.DIRECTORY_SEPARATOR.'5g'.DIRECTORY_SEPARATOR.'table_1_2.html'))
    {

        $str = file_get_contents($_SERVER["DOCUMENT_ROOT"].DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'dev'.DIRECTORY_SEPARATOR.'5g'.DIRECTORY_SEPARATOR.'table_1_2.html');

        $value = clearStr2($str);
        $f = fopen($_SERVER["DOCUMENT_ROOT"].DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'dev'.DIRECTORY_SEPARATOR.'5g'.DIRECTORY_SEPARATOR.'table_1_2.html', 'w');
        fwrite($f, $value);
        fclose($f);

        $fancyTableStyle = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80, 'align' => 'left');
        $fancyTableFirstRowStyle = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '66BBFF');
        $fancyTableCellStyle = array('valign' => 'center');
        $fancyTableCellBtlrStyle = array('valign' => 'center', 'textDirection' => \PhpOffice\PhpWord\Style\Cell::TEXT_DIR_BTLR);
        $fancyTableFontStyle = array('bold' => true);
        $phpWord->addTableStyle('', $fancyTableStyle, $fancyTableFirstRowStyle);



        $arrTable = file($_SERVER["DOCUMENT_ROOT"].DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'dev'.DIRECTORY_SEPARATOR.'5g'.DIRECTORY_SEPARATOR.'table_1_2.html');

        $rowspanCount = 0;
        /*$section = $phpWord->addSection();*/
        $phpWord->setDefaultParagraphStyle(array('align' => 'left','spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(0)));
        $tableStart = false;
        $strUl = false;
        $strUlLi = array();

        foreach ($arrTable as $arTempItem1)
        {

            //Исбавляемся от мусора
            if($html == "\r\n" || $html == "\t\n" || $html == "\t\t\t\n" || $html == "\t\t\n" || $html == "\n" || $html == "") continue;
            $arTempItem2 = str_replace("\n", "", $arTempItem1);
            if($arTempItem2 == "" || trim($arTempItem2) == "") continue;
            $arItemStr = $arTempItem2;

            //Стили текста
            if(strpos($arItemStr, '<strong>') !== false)
            {
                $fStyle = array('bold' => true);
            }
            if(strpos($arItemStr, '</strong>') !== false)
            {
                $fStyle = array('bold' => false);
            }
            if(strpos($arItemStr, '<em>') !== false)
            {
                $fStyle = array('italic' => true);
            }
            if(strpos($arItemStr, '</em>') !== false)
            {
                $fStyle = array('italic' => false);
            }
            if(strpos($arItemStr, '<caption>') !== false)
            {
                $strCaption = true;
            }
            if(strpos($arItemStr, '</caption>') !== false)
            {
                $strCaption = false;
            }
            if(strpos($arItemStr, '<ul>') !== false)
            {
                $strUl = true;
            }
            if(strpos($arItemStr, '</ul>') !== false)
            {
                $strUl = false;
            }
            if(strpos($arItemStr, '<li>') !== false)
            {
                $strUlLi = true;
            }
            if(strpos($arItemStr, '</li>') !== false)
            {
                $strUlLi = false;
            }

            //Стили ячейки и строки
            if(strpos($arItemStr, '<td colspan') !==false)
            {
                if(is_numeric(substr($arItemStr, 14, 1)))
                {
                    $colspanTmp1 = substr($arItemStr, 13, 1);
                    $colspanTmp2 = substr($arItemStr, 14, 1);
                    $colspanTmp3 = (string)$colspanTmp1.(string)$colspanTmp2;
                    $colspan = (string)$colspanTmp3;
                    $cellColSpan = array('gridSpan' => $colspan, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::END);
                }
                else
                {

                    $colspan = substr($arItemStr, 13, 1);
                    $cellColSpan = array('gridSpan' => $colspan, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::END);
                }
            }


            if(strpos($arItemStr, "<td") !==false)
            {




                if(strpos($arItemStr, '<td rowspan') !== false)
                {
                    $rowspan = substr($arItemStr, 13, 1);
                    if(is_numeric(substr($arItemStr, 14, 1)))
                    {
                        $rowspanTmp1 = substr($arItemStr, 13, 1);
                        $rowspanTmp2 = substr($arItemStr, 14, 1);
                        $rowspanTmp3 = (string)$rowspanTmp1.(string)$rowspanTmp2;
                        $rowspan = (int)$rowspanTmp3;

                    }

                }
                if(strpos($arItemStr, 'colspan') !== false && strpos($arItemStr, 'rowspan') !==false)
                {
                    $rowspan =  substr($arItemStr, 25, 1);

                }





                if($rowspan > 0)
                {
                    for($i = 1; $rowspan > $i; $i++)
                    {
                        $tableMapCont[$TRCount + $i][$TDCount][] = 'continue';
                    }
                }


            }



            //Поиск таблицы
            if(strpos($arItemStr, '<table ') !==false || strpos($arItemStr, '<table>') !==false)
            {
                $tableStart = true;
                $rowspan = 0;
                $colspan = 0;
                $str = '';
                $cellColSpan = array();
                $cellStyle = array();
                $section = $phpWord->addSection();
                if($textBefore != '')
                {
                    $header = array('size' => 12);
                    $textBefore = strip_tags(clearStr($textBefore));
                    $section->addText($textBefore, $header);
                }
                $sectionStyle = $section->getStyle();
                $sectionStyle->setOrientation($sectionStyle::ORIENTATION_LANDSCAPE);

                $table = $section->addTable(array('unit' => \PhpOffice\PhpWord\Style\Table::WIDTH_PERCENT, 'width' => 100 * 50, 'borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START));

            }
            if(strpos($arItemStr, '</table>') !==false)
            {
                $tableStart = false;
            }


            //Новая строка
            if(strpos($arItemStr, '<tr>') !==false)
            {
                $tableMap[$TRCount] = [];
                $TDCount = 0;

            }
            if(strpos($arItemStr, '</tr>') !==false)
            {
                $TRCount++;
            }

            //Накапливаем текст до тех пор пока не встретится td
            if(strpos($arItemStr, '<') ==  false && strpos($arItemStr, '>') == false && $tableStart == true && $strCaption == false)
            {
                if(!$strUl)
                {
                    if($str == '')
                    {
                        $str = strip_tags($arItemStr);
                        $str = trim($str);
                    }
                    else
                    {

                        $str .= strip_tags($arItemStr) ."<w:br />";
                    }
                }
                if($strUl)
                {
                    $strUlLiStr[] = strip_tags($arItemStr);

                }

            }


            //Печатем ячейку
            if(strpos($arItemStr, "</td>") !==false && $tableStart == true && $strCaption == false && $strUl == false)
            {
                $TDCount++;

                if($rowspan > 0)
                {
                    $cellColSpanR = array('vMerge' => 'restart', 'valign' => 'center');
                    if(!empty($cellColSpan))
                    {
                        $cellColSpanR = array_merge($cellColSpan, $cellColSpanR);
                    }
                    if(!$strUl && !empty($strUlLiStr))
                    {
                        $str = $strUlLiStr;
                        unset($strUlLiStr);
                    }

                    $tableMap[$TRCount][$TDCount] = [$str, $cellColSpanR, $cellStyle, array('indentation' => array ('firstLine' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(0)), 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START)];
                    $rowspan = 0;
                }
                else
                {
                    if(!$strUl && !empty($strUlLiStr))
                    {
                        $str = $strUlLiStr;
                        unset($strUlLiStr);
                    }

                    $tableMap[$TRCount][$TDCount] = [$str, $cellColSpan, $cellStyle, array('indentation' => array ('firstLine' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(0)), 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START)];
                }

                $str = '';
                $cellColSpan = array();
            }

        }
        $continueReset = false;
        //Собираем таблицу

        $l1=1;
        foreach ($tableMap as $key1 => $arItem1)
        {
            if(is_array($arItem1))
            {
                $table->addRow();
                $continueReset = false;
                foreach ($arItem1  as $key2 => $arItem2)
                {
                    if(array_key_exists($key1, $tableMapCont) && $continueReset== false)
                    {

                        $continueCount = 0;
                        $tempArr = $tableMapCont[$key1];
                        foreach ($tempArr as $arCount)
                        {
                            foreach ($arCount as $arCountTemp)
                            {
                                if($arCountTemp == 'continue')
                                {
                                    $continueCount++;
                                }
                            }
                        }

                        if($continueCount == 5)
                        {
                            while(($continueCount + 5) > 0)
                            {
                                $cellColSpanCont = array('vMerge' => 'continue');
                                $table->addCell(null, $cellColSpanCont);
                                $continueCount--;
                            }
                        }
                        else
                        {

                            if($elemId == 243 && $continueCount == 3)
                            {
                                $continueCount = 2;
                                $addCountinue = true;
                            }
                            if($elemId == 248 && $continueCount == 2)
                            {
                                $continueCount = 1;
                                $addCountinue = true;
                            }
                            if($elemId == 246 && $continueCount == 4)
                            {
                                $continueCount = 3;
                                $addCountinue = true;

                            }


                            while($continueCount > 0)
                            {
                                $cellColSpanCont = array('vMerge' => 'continue');
                                $table->addCell(null, $cellColSpanCont);
                                $continueCount--;
                            }

                        }


                        $continueReset = true;

                    }

                    if(is_array($arItem2[0]))
                    {
                        $table_cell = $table->addCell();
                        foreach ($arItem2[0] as $arItem)
                        {
                            $table_cell->addListItem($arItem,0);//0 is the list level
                        }

                    }
                    else
                    {
                        $arItem2[2] =  array("align" => "left");
                        $table->addCell(null, $arItem2[1])->addText($arItem2[0], $arItem2[2], $arItem2[2]);

                        if($elemId == 243 && $addCountinue)
                        {
                            $countCont++;
                        }
                        if($elemId == 243 && $addCountinue && $countCont==2)
                        {
                            $cellColSpanCont = array('vMerge' => 'continue');
                            $table->addCell(null, $cellColSpanCont);
                            $addCountinue = false;
                            $countCont = 0;
                        }

                        if($elemId == 248 && $addCountinue)
                        {
                            $countCont++;
                        }
                        if($elemId == 248 && $addCountinue && $countCont==2)
                        {
                            $cellColSpanCont = array('vMerge' => 'continue');
                            $table->addCell(null, $cellColSpanCont);
                            $addCountinue = false;
                            $countCont = 0;
                        }

                        if($elemId == 246 && $addCountinue)
                        {
                            $countCont++;
                        }
                        if($elemId == 246 && $addCountinue && $countCont==2)
                        {
                            $cellColSpanCont = array('vMerge' => 'continue');
                            $table->addCell(null, $cellColSpanCont);
                            $addCountinue = false;
                            $countCont = 0;
                        }



                    }

                }
            }
        }




    }

}
function exitFromHtml($tables, $textDetail, $name)
{
    foreach ($tables as $arItem)
    {
        if(searchCaption($arItem))
        {
            $tempArr2['POSITION'][] = mb_stripos($textDetail, $arItem);
            $tempArr2['LENGTH'][] = mb_strlen($arItem, 'utf-8');
        }
    }
    $tempStr = '';
    $allCount = 0;

    $countElem = 0;
    foreach ($tempArr2['POSITION'] as $key => $arItemDetail)
    {
        if($countElem == 0)
        {
            $firstText = mb_substr($textDetail, 0, $arItemDetail);
            $tempArr1['STR'][] = $firstText;
            $lenText1 = mb_strlen($firstText, 'utf-8');

            $secondText = mb_substr($textDetail, $arItemDetail, $tempArr2['LENGTH'][$key]);
            $tempArr1['STR'][] = $secondText;
            $allCount = $lenText1 + $tempArr2['LENGTH'][$key];

        }
        if($countElem !==0)
        {
            $diff = $arItemDetail - $allCount;
            $firstText = mb_substr($textDetail, $allCount, $diff);
            $tempArr1['STR'][] = $firstText;
            $lenText1 = mb_strlen($firstText, 'utf-8');
            $secondText = mb_substr($textDetail, $arItemDetail, $tempArr2['LENGTH'][$key]);
            $tempArr1['STR'][] = $secondText;
            $allCount += $lenText1 + $tempArr2['LENGTH'][$key];

        }

        $countElem++;
    }
    $tempArr1['STR'][] = mb_substr($textDetail, $allCount);
    return $tempArr1;
}

$tableMap = array();
$tableMapCont = array();
$TRCount = 0;
$TDCount = 0;

foreach ($arResult as $key=> $arItem)
{


    $section = $phpWord->addSection(
        array(
            'marginLeft' => 1133,
            'marginRight' => 852,
            'marginTop' => 1133,
            'marginBottom' => 1133,

        ));


    if($key == 'ID') continue;


    //Вклиниваемся в разбор DETAIL_TEXT
    //Если DETAIL_TEXT содержит таблицу - разибраем. Если нет едим дальше
    $caption = 0;
    $tempArr1 = array();
    $searchTable = preg_match_all("%\<table((?s).*?)</table>%",$arItem['DETAIL_TEXT'], $tables);
    $tablesCount = count($tables[0]);
    if($tablesCount)
    {
        $hands = true;

    }



    if($hands)
    {
        foreach ($tables[0] as $arItemTable)
        {
            if(searchCaption($arItemTable))
            {
                $caption ++;
            }
        }

        if($caption > 0)
        {
            $sectionName = printName($arItem['NAME'], $arrTitle);
            if($sectionName)
            {
                $section->addTitle($sectionName[0], $sectionName[1]);
                if(in_array($sectionName[0], $arrTitle))
                {
                    if($arItem['NAME'] == '5.1. Анализ патентной и публикационной информации о развитии ВТН')
                    {
                        $section->addTitle('Анализ патентной и публикационной информации о развитии ВТН', 2);
                    }
                    else
                    {
                        $section->addTitle(subtitle($arItem['NAME']), 2);
                    }

                }
            }
            else
            {
                $section->addTitle(subtitle($arItem['NAME']), 2);
            }


            $arrText = exitFromHtml($tables[0], $arItem['DETAIL_TEXT'], $arItem['NAME']);
            $count = 0;
            foreach ($arrText['STR'] as $arTable)
            {
                if(substr($arTable, 0,6) == '<table')
                {

                    $captionStr = preg_match('/<caption[^>]*?>(.*?)<\/caption>/si', $arTable, $matches);
                    $tableCaption = $matches[1];

                    $arTable = clearStr($arTable);

                    createTable($arTable, $phpWord, $tableCaption, $elemId);

                }
                if(substr($arTable, 0,6) !== '<table')
                {
                    $html = clearStr($arTable);
                    if($count > 0 && !empty($html) && $html !== " ")
                    {
                        $section = $phpWord->addSection(
                            array(
                                'marginLeft' => 1133,
                                'marginRight' => 852,
                                'marginTop' => 1133,
                                'marginBottom' => 1133,

                            ));

                    }
                    if(substr($arItem['NAME'],0,3) == '5.2')
                    {
                        $sectionStyle = $section->getStyle();
                        $sectionStyle->setOrientation($sectionStyle::ORIENTATION_LANDSCAPE);

                    }
                    if(!empty($html) && $html !== " ")
                    {
                        \PhpOffice\PhpWord\Shared\Html::addHtml($section, $html, false, false);
                        $count++;
                    }






                }
            }
            $caption = 0;
            continue;
        }


    }

    $sectionName = printName($arItem['NAME'], $arrTitle);
    if($sectionName)
    {
        $section->addTitle($sectionName[0], $sectionName[1]);
        if(in_array($sectionName[0], $arrTitle))
        {
            if($arItem['NAME'] == '5.1. Анализ патентной и публикационной информации о развитии ВТН')
            {
                $section->addTitle('Анализ патентной и публикационной информации о развитии ВТН', 2);
            }
            else
            {
                $section->addTitle(subtitle($arItem['NAME']), 2);
            }

        }
    }
    else
    {
        $section->addTitle(subtitle($arItem['NAME']), 2);
    }




    if(substr($arItem['NAME'],0,3) == '5.2')
    {
        $sectionStyle = $section->getStyle();
        $sectionStyle->setOrientation($sectionStyle::ORIENTATION_LANDSCAPE);

    }

    $html = clearStr($arItem['DETAIL_TEXT']);


    \PhpOffice\PhpWord\Shared\Html::addHtml($section, $html, false, false);
    $html = '';


}

$title = str_replace(",", " ", $title);
$title = 'Content-Disposition: attachment;filename='.$title.'.docx';

$phpWord->getSettings()->setThemeFontLang(new Language(Language::RU_RU));
$phpWord->getCompatibility()->setOoxmlVersion(15);
header('Content-Type: text/html; charset=utf-8');
header($title);
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save('php://output');
?>
