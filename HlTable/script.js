document.addEventListener("DOMContentLoaded", () => {
    if($('.main-block').length > 0)
    {
        updateTableList();
        let tableName = document.querySelector('#table_name'),
            tableTitle = document.querySelector('#table_title'),
            result = document.querySelector('#result'),
            btn = document.querySelector('#btn'),
            tableList = document.querySelector('.table-list');
        btn.addEventListener('click', function ()
        {
            let name = translit(tableName.value);
            let altName = tableName.value;
            let title = tableTitle.value;
            result.textContent = '';

            $.ajax({
                url: '/table_hl2/ajax.php',
                method: 'POST',
                dataType: 'json',
                data:
                    {
                        mode: 'createTable',
                        tableName: name,
                        tableAltName: altName,
                        tableTitle: title
                    },
                success: function(data){
                    tableName.value = '';
                    tableTitle.value = '';
                    result.textContent = data;
                    updateTableList();

                },
                error: function (data)
                {
                    tableName.value = '';
                    tableTitle.value = '';
                    result.textContent = data;
                    console.log(data);

                }
            });

        });

        function updateTableList()
        {
            $.ajax({
                url: '/table_hl2/ajax.php',
                method: 'POST',
                dataType: 'json',
                data:
                    {
                        mode: 'updateTableList'
                    },
                success: function(data){
                    tableList.innerHTML = '';
                    if(data !== 'null')
                    {
                        for (let i in data)
                        {
                            $(`<a href="/table_hl2/view_table.php?id=${i}">${data[i]}</a><br>`).appendTo(tableList);
                        }
                    }
                    else
                    {
                        $(`<p>Нет созданных таблиц</p><br>`).appendTo(tableList);
                    }
                },
                error: function (data)
                {

                    console.error(data)
                }
            });
        }

        function translit(word){
            var answer = '';
            var converter = {
                'а': 'a',    'б': 'b',    'в': 'v',    'г': 'g',    'д': 'd',
                'е': 'e',    'ё': 'e',    'ж': 'zh',   'з': 'z',    'и': 'i',
                'й': 'y',    'к': 'k',    'л': 'l',    'м': 'm',    'н': 'n',
                'о': 'o',    'п': 'p',    'р': 'r',    'с': 's',    'т': 't',
                'у': 'u',    'ф': 'f',    'х': 'h',    'ц': 'c',    'ч': 'ch',
                'ш': 'sh',   'щ': 'sch',  'ь': '',     'ы': 'y',    'ъ': '',
                'э': 'e',    'ю': 'yu',   'я': 'ya',

                'А': 'A',    'Б': 'B',    'В': 'V',    'Г': 'G',    'Д': 'D',
                'Е': 'E',    'Ё': 'E',    'Ж': 'Zh',   'З': 'Z',    'И': 'I',
                'Й': 'Y',    'К': 'K',    'Л': 'L',    'М': 'M',    'Н': 'N',
                'О': 'O',    'П': 'P',    'Р': 'R',    'С': 'S',    'Т': 'T',
                'У': 'U',    'Ф': 'F',    'Х': 'H',    'Ц': 'C',    'Ч': 'Ch',
                'Ш': 'Sh',   'Щ': 'Sch',  'Ь': '',     'Ы': 'Y',    'Ъ': '',
                'Э': 'E',    'Ю': 'Yu',   'Я': 'Ya'
            };

            for (var i = 0; i < word.length; ++i ) {
                if (converter[word[i]] == undefined){
                    answer += word[i];
                } else {
                    answer += converter[word[i]];
                }
            }

            return answer;
        }
    }
    if($('.view-table').length > 0)
    {
        let params = (new URL(document.location)).searchParams,
            saveTable = document.querySelector('#save-table'),
            hlId = params.get("id"),
            dataColumnName,
            dataIDStr;

        $.ajax({
            url: '/table_hl2/ajax.php',
            method: 'POST',
            dataType: 'json',
            data:
                {
                    mode: 'viewTable',
                    hlId: hlId
                },
            success: function(data){
                let dataTD = data['FOR_CLIENT'];
                let dataTR = data['COLUMN_TITLE'];
                dataColumnName = data['COLUMN_TEXT_NAME'];
                dataIDStr = data['ID'];
                createTable(dataTD, dataTR, dataColumnName, dataIDStr);
                console.log(data);

            },
            error: function (data)
            {
                console.error(data)
            }
        });

        saveTable.addEventListener('click', function (){
            let spreadsheet = document.querySelector('#spreadsheet'),
                tr =     spreadsheet.querySelectorAll('tr'),
                arrToServerColumns = [],
                arrToServerSTR = [],
                valueText;
            tr.forEach(function (itemTR, keyTR){
               if(keyTR !==0)
               {
                   let child = itemTR.childNodes;
                   child.forEach(function (itemTD, keyTD){
                       if(keyTD == 1)
                       {
                           let sortTR = itemTR.getAttribute('data-sort');
                           arrToServerColumns.push({id: itemTR.getAttribute('data-column'),  sort: sortTR, text: itemTD.textContent});
                       }
                       if(keyTD !== 0)
                       {   if(itemTD.textContent === "")
                           {
                               itemTD.textContent = "--";
                           }
                           valueText = itemTD.textContent;
                           arrToServerSTR.push({column: itemTR.getAttribute('data-column'), id: itemTD.getAttribute('data-strid'), text: valueText, sort: itemTD.getAttribute('data-sort')})
                       }
                   })
               }
            });

            $.ajax({
                url: '/table_hl2/ajax.php',
                method: 'POST',
                dataType: 'json',
                data:
                    {
                        mode: 'saveTable',
                        hlId: hlId,
                        arrToServerSTR: arrToServerSTR,
                        arrToServerColumns: arrToServerColumns,
                    },
                success: function(data){
                    console.log(data);
                    window.location.reload();

                },
                error: function (data)
                {
                    console.error(data)
                }
            });


        });

        function createTable(dataTD, dataTR, dataColumnName)
        {
            let columns = [];
            columns.push({type: 'text', title:'Показатель', width:200})
            dataTR.forEach(function (item, key){
                columns.push({type: 'text', title:item['UF_TABLE_TITLE_COLUMN'], width:200})
            });
            jspreadsheet(document.getElementById('spreadsheet'), {
                data:dataTD,
                columns: columns,
                oninsertrow: insertedRow,
                ondeleterow: deletedRow,
                onchange: changed,
                oninsertcolumn: insertedColumn,
                ondeletecolumn: deletedColumn,

            });
            console.log(columns);
            console.log(dataTR);

            let spreadsheet = document.querySelector('#spreadsheet'),
                tr =     spreadsheet.querySelectorAll('tr');

                tr.forEach(function (itemTR, keyTR){
                   if(keyTR !== 0)
                   {
                       let count = keyTR - 1;
                       itemTR.setAttribute('data-column', dataColumnName[count]);

                       let child = itemTR.childNodes;
                       child.forEach(function (itemTD, keyTD){
                          let countTD = keyTD - 1;
                          if(keyTD !== 0 && keyTD !==1)
                          {
                              let count = keyTD - 2;
                              itemTD.setAttribute('data-strid', dataIDStr[count]);
                          }

                          itemTD.setAttribute('data-sort', countTD);
                       });
                       itemTR.setAttribute('data-sort', keyTR);
                   }
                });

        }
        function tempReload(hlId, arrToServerSTR, arrToServerColumns)
        {
            $.ajax({
                url: '/table_hl2/ajax.php',
                method: 'POST',
                dataType: 'json',
                data:
                    {
                        mode: 'saveTable',
                        hlId: hlId,
                        arrToServerSTR: arrToServerSTR,
                        arrToServerColumns: arrToServerColumns,
                    },
                success: function(data){
                    console.log(data);

                },
                error: function (data)
                {
                    console.error(data)
                }
            });
        }


        let insertedRow = function(instance, cellNum, order, cell, x1, y1, x2, y2, origin) {
            let spreadsheet = document.querySelector('#spreadsheet');
            let tr = spreadsheet.querySelectorAll('tr');
            let tempName = [];

            dataColumnName.forEach(function (item)
            {
                tempName.push(item.replace('UF_TABLE_COLUMN_', ''));
            })
            let b = tempName.map(function (item) {
                return parseFloat(item);
            });
            let maxId =  Math.max.apply(null, b) + 1;
            let columnName = 'UF_TABLE_COLUMN_'+maxId;
            cell[0][0].parentNode.setAttribute('data-column', columnName);


            let c = dataIDStr.map(function (item) {
                return parseFloat(item);
            });
            let maxIdStr =  Math.max.apply(null, c) + 1;


            cell[0][0].parentNode.childNodes.forEach(function (item, key){
                if(key !== 0 && key !== 1)
                {
                    let count = key - 2
                    item.setAttribute('data-strid', dataIDStr[count]);
                }
                if(key > 1)
                {
                    let count = key - 1
                    item.setAttribute('data-sort', count);
                }

            });
            tr.forEach(function (itemTR, keyTR){
                if(keyTR !== 0)
                {
                    itemTR.setAttribute('data-sort', keyTR);
                }
            });
            dataColumnName.push('UF_TABLE_COLUMN_'+maxId);

           $.ajax({
                url: '/table_hl2/ajax.php',
                method: 'POST',
                dataType: 'json',
                data:
                    {
                        mode: 'insertedRow',
                        hlId: hlId,
                        columnNumber: maxId

                    },
                success: function(data){
                    console.log(data);

                },
                error: function (data)
                {
                    console.error(data)
                }
            });


        }
        let deletedRow = function(instance, cellNum, order, cell, x1, y1, x2, y2, origin) {
            let deleteRowName = [];
            let spreadsheet = document.querySelector('#spreadsheet'),
                tr =     spreadsheet.querySelectorAll('tr');

            tr.forEach(function (itemTR, keyTR){
                if(keyTR !== 0)
                {
                    itemTR.setAttribute('data-sort', keyTR);
                }

            });


            tr.forEach(function (itemTR, keyTR){
                if(keyTR !== 0)
                {
                    deleteRowName.push({column: itemTR.getAttribute('data-column')});
                }


            });



           $.ajax({
                url: '/table_hl2/ajax.php',
                method: 'POST',
                dataType: 'json',
                data:
                    {
                        mode: 'deletedRow',
                        hlId: hlId,
                        deleteRowName: deleteRowName,

                    },
                success: function(data){
                    console.log(data);

                },
                error: function (data)
                {
                    console.error(data)
                }
            });



        }
        let changed = function(instance, cellNum, order, cell, x1, y1, x2, y2, origin) {


        };
        let insertedColumn = function(instance, cellNum, order, cell, x1, y1, x2, y2, origin) {
            let spreadsheet = document.querySelector('#spreadsheet');
            let tr = spreadsheet.querySelectorAll('tr');

            let b = dataIDStr.map(function (item) {
                return parseFloat(item);
            });
            let maxIdStr =  Math.max.apply(null, b) + 1;

            tr.forEach(function (itemTR, keyTR){
                if(keyTR !==0)
                {
                    let child = itemTR.childNodes;
                    child.forEach(function (itemTD, keyTD){
                        if(!itemTD.getAttribute('data-strid') && keyTD !== 1)
                        {
                            itemTD.setAttribute('data-strid', maxIdStr);

                        }
                        if(keyTD !== 1)
                        {
                            let count = keyTD - 1;
                            itemTD.setAttribute('data-sort', count);
                        }

                    })
                }
            });

            $.ajax({
                url: '/table_hl2/ajax.php',
                method: 'POST',
                dataType: 'json',
                data:
                    {
                        mode: 'insertedColumn',
                        hlId: hlId,

                    },
                success: function(data){
                    console.log(data);

                },
                error: function (data)
                {
                    console.error(data)
                }
            });

            dataIDStr.push(String(maxIdStr));


        };
        let deletedColumn = function(instance, cellNum, order, cell, x1, y1, x2, y2, origin) {
            let spreadsheet = document.querySelector('#spreadsheet');
            let tr = spreadsheet.querySelectorAll('tr');
            let deleteStr;
            let serverSTR = [];
            let k = cell[0];
            for (let i in k)
            {
               deleteStr = k[i].getAttribute('data-strid')
            }

            tr.forEach(function (itemTR, keyTR){
                if(keyTR !==0)
                {
                    let child = itemTR.childNodes;
                    child.forEach(function (itemTD, keyTD){
                        if(keyTD !== 1)
                        {
                            let count = keyTD - 1;
                            itemTD.setAttribute('data-sort', count);
                            serverSTR.push({id: itemTD.getAttribute('data-strid')})
                        }


                    })
                }
            });
            $.ajax({
                url: '/table_hl2/ajax.php',
                method: 'POST',
                dataType: 'json',
                data:
                    {
                        mode: 'deletedColumn',
                        hlId: hlId,
                        serverSTR: serverSTR
                    },
                success: function(data){
                    console.log(data);
                },
                error: function (data)
                {
                    console.error(data)
                }
            });

        }



    }

});