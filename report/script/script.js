$(function() {
    let header = $('.header');

    $(window).scroll(function() {
        if($(this).scrollTop() > 1) {
            header.addClass('header_fixed');
        } else {
            header.removeClass('header_fixed');
        }
    });

    //SART
    let mode = 'all',
        dataStart = getUrlVar()['arFilterForm%5BSTART_DATE%5D'],
        dataEnd = getUrlVar()['arFilterForm%5BEND_DATE%5D'],
        firstElemLiProject = document.querySelector('.first-elem_project'),
        firstElemLiPeriod = document.querySelector('.first-elem_period'),
        tableBlock = document.querySelector('.table-block'),
        tableValues = tableBlock.querySelector('.table_values'),
        tbody = tableValues.querySelector('tbody');

    firstElemLiProject.classList.add('li__active');
    firstElemLiPeriod.classList.add('li__active');
    sendRequest(dataStart, dataEnd, mode = 'all', modePeriod = 'cur', projectId = '1');
    //SART

    let liItemProject = document.querySelectorAll('.list-project');
    liItemProject.forEach(function (item){
        item.addEventListener('click', function (e){
            tableBlock.style.display = 'none';
            dellBottomLine(liItemProject)
            item.classList.add('li__active');
            let mode = item.getAttribute('data-project'),
                dataStart = getUrlVar()['arFilterForm%5BSTART_DATE%5D'],
                dataEnd = getUrlVar()['arFilterForm%5BEND_DATE%5D'],
                dataProject = '';

            let selectItemProject = document.querySelectorAll('.select__item');

            selectItemProject.forEach(function (item){
                if(item.classList.contains('li__active')){
                    dataProject = item.getAttribute('data-project');
                    return dataProject;
                }
            });
            if(!dataProject){
                dataProject = 1;
            }

            sendRequest(dataStart, dataEnd, mode, '', dataProject);
        })
    })

    let liItemPeriod = document.querySelectorAll('.list-period');
    liItemPeriod.forEach(function (item) {
        item.addEventListener('click', function (e){
            tableBlock.style.display = 'none';
            dellBottomLine(liItemPeriod)
            item.classList.add('li__active');
            let modePeriod = item.getAttribute('data-period'),
                dataStart = getUrlVar()['arFilterForm%5BSTART_DATE%5D'],
                dataEnd = getUrlVar()['arFilterForm%5BEND_DATE%5D'],
                liItemProject = document.querySelectorAll('.list-project'),
                dataProject,
                projectId = '';


            liItemProject.forEach(function (item){
                if(item.classList.contains('li__active')){
                    dataProject = item.getAttribute('data-project');
                    return dataProject;
                }
            })

            let selectItemProject = document.querySelectorAll('.select__item');
            selectItemProject.forEach(function (item){
                if(item.classList.contains('select-project')){
                    projectId = item.getAttribute('data-idproject');
                }
            });
            if(!projectId){
                projectId = 1;
            }

          sendRequest(dataStart, dataEnd, dataProject, modePeriod, projectId);
        })
    })

    let selectItemProject = document.querySelectorAll('.select__item');
    selectItemProject.forEach(function (item){
        item.addEventListener('click', function (){
            tbody.innerHTML = '';
            selectItemProject.forEach(function (item){
               if(item.classList.contains('select-project')){
                   item.classList.remove('select-project')
               }
            });

            let projectId = item.getAttribute('data-idproject'),
                dataStart = '',
                dataEnd = '',
                dataPeriod = '',
                dataProject = '';
            liItemPeriod.forEach(function (item){
                if(item.classList.contains('li__active')){
                    dataPeriod = item.getAttribute('data-period');
                    return dataPeriod;
                }
            });

            liItemProject.forEach(function (item){
                if(item.classList.contains('li__active')){
                    dataProject = item.getAttribute('data-project');
                    return dataProject;
                }
            });
            item.classList.add('select-project');
            sendRequest(dataStart, dataEnd, dataProject, dataPeriod, projectId);
        })

    });

    function sendRequest(dataStart, dataEnd, modeProject, modePeriod = '', project = '', projectName = ''){
        BX.ajax({
            method: 'POST',
            url: '/timesheets/reports2/php/GetDataFromHL.php',
            data: {dataStart: dataStart, dataEnd: dataEnd, mode: modeProject, modePeriod: modePeriod, project: project, projectName: projectName},
            dataType: 'json',
            onsuccess: function(data)
            {
                let firstReport = data['FIRST'],
                    secondReport = data['SECOND'],
                    thirdReport = data['THIRD'],
                    fourthReport = data['FOURTH'];
                    fifthReport = data['FIFTH'];


                createFirstReport(firstReport, fifthReport);
                createSecondReport(secondReport);
                createThirdReport(thirdReport);
                createFourthReport(fourthReport);

            }
        });
    }

    function createFirstReport(data, data2){
        let newData = [];

        for(let i in data)
        {
            let hours = `${i} (${(data[i])} ч.)`;

            newData.push({name: i, count: data[i]})
        }

        am4core.ready(function() {
            am4core.useTheme(am4themes_animated);
            var chart = am4core.create("chartdiv1", am4charts.PieChart);

            chart.data = newData;

            var pieSeries = chart.series.push(new am4charts.PieSeries());
            pieSeries.dataFields.value = "count";
            pieSeries.dataFields.category = "name";
            pieSeries.innerRadius = am4core.percent(50);
            pieSeries.ticks.template.disabled = false;
            pieSeries.labels.template.disabled = false;

            let smallRound = [];

            pieSeries.slices.template.events.on("hit", function(ev){
                let projectName = ev.target.dataItem.category;
                       if(!tableBlock.classList.contains('hide')){
                           tableBlock.style.display = 'block';
                           tbody.innerHTML = '';

                           for(let i in data2){
                               if(i === projectName){
                                   for(let v in data2[i]){
                                       smallRound.push({name: v, count: data2[i][v]});
                                      tbody.insertAdjacentHTML('afterbegin', `<tr><td>${v}</td><td style="text-align: center;">${data2[i][v]}</td></tr>`)
                                   }
                               }
                           }

                           am4core.ready(function() {
                               am4core.useTheme(am4themes_animated);
                               var chart = am4core.create("smallRound", am4charts.PieChart);

                               chart.data = smallRound;

                               var pieSeries = chart.series.push(new am4charts.PieSeries());
                               pieSeries.dataFields.value = "count";
                               pieSeries.dataFields.category = "name";
                               pieSeries.innerRadius = am4core.percent(50);
                               pieSeries.ticks.template.disabled = true;
                               pieSeries.labels.template.disabled = true;

                               console.log(smallRound)
                           })

                       }
            });

            let legendBtn = document.querySelector('.legend-btn'),
                legendBtnClose = document.querySelector('.legend-btn_close');
            if(legendBtn){
                legendBtn.addEventListener('click', function (){
                    tableBlock.classList.add('hide');
                    tableBlock.style.display = 'none';
                    legendBtnClose.style.display = 'block';

                });
            }
            if(legendBtnClose){
                legendBtnClose.addEventListener('click', function (){
                    tableBlock.classList.remove('hide');
                    tableBlock.style.display = 'block';
                    tbody.innerHTML = '';
                    legendBtnClose.style.display = 'none';
                });
            }

            chart.legend = new am4charts.Legend();

            chart.legend.position = 'left';
            chart.legend.paddingTop = 0;
            chart.legend.paddingLeft = 0;
            chart.legend.maxWidth = 300;

            chart.legend.valueLabels.template.align = "right";
            chart.legend.valueLabels.template.textAlign = "end";
            chart.legend.scrollable = true;

        });

    }
    function createSecondReport(data){

        let newData = [];
        am4core.ready(function() {

            am4core.useTheme(am4themes_animated);

            var chart = am4core.create('chartdiv2', am4charts.XYChart)
            chart.colors.step = 2;

            chart.legend = new am4charts.Legend()
            chart.legend.position = 'bottom'
            chart.legend.paddingTop = 50;
            chart.legend.labels.template.maxWidth = 50;


            var xAxis = chart.xAxes.push(new am4charts.CategoryAxis())
            xAxis.dataFields.category = 'category'
            xAxis.renderer.cellStartLocation = 0.1
            xAxis.renderer.cellEndLocation = 0.9
            xAxis.renderer.grid.template.location = 0;

            var yAxis = chart.yAxes.push(new am4charts.ValueAxis());
            yAxis.min = 0;

            yAxis.title.fontSize = "24";
            yAxis.title.text = "Количество часов";

            function createSeries(value, name) {
                var series = chart.series.push(new am4charts.ColumnSeries())
                series.dataFields.valueY = value
                series.dataFields.categoryX = 'category'
                series.name = name

                series.events.on("hidden", arrangeColumns);
                series.events.on("shown", arrangeColumns);

                var bullet = series.bullets.push(new am4charts.LabelBullet())
                bullet.interactionsEnabled = false
                bullet.dy = 30;
                bullet.label.text = '{valueY}'
                bullet.label.fill = am4core.color('#ffffff')

                return series;
            }
            let countElem = Object.keys(data).length;

            for(let i in data){
                let curItem = data[i];
                for (let v in curItem) {
                    let fact = +curItem[v]['FACT'],
                        plan = +curItem[v]['PLAN'];
                    newData.push({category: `${v} ${(countElem === 2) ? i+'г.': ''}`, first:fact, second: plan})
                }
            }
            chart.data = newData;

            createSeries('first', 'Количество часов');
            createSeries('second', 'План часов');

            function arrangeColumns() {

                var series = chart.series.getIndex(0);

                var w = 1 - xAxis.renderer.cellStartLocation - (1 - xAxis.renderer.cellEndLocation);
                if (series.dataItems.length > 1) {
                    var x0 = xAxis.getX(series.dataItems.getIndex(0), "categoryX");
                    var x1 = xAxis.getX(series.dataItems.getIndex(1), "categoryX");
                    var delta = ((x1 - x0) / chart.series.length) * w;
                    if (am4core.isNumber(delta)) {
                        var middle = chart.series.length / 2;

                        var newIndex = 0;
                        chart.series.each(function(series) {
                            if (!series.isHidden && !series.isHiding) {
                                series.dummyData = newIndex;
                                newIndex++;
                            }
                            else {
                                series.dummyData = chart.series.indexOf(series);
                            }
                        })
                        var visibleCount = newIndex;
                        var newMiddle = visibleCount / 2;

                        chart.series.each(function(series) {
                            var trueIndex = chart.series.indexOf(series);
                            var newIndex = series.dummyData;

                            var dx = (newIndex - trueIndex + middle - newMiddle) * delta

                            series.animate({ property: "dx", to: dx }, series.interpolationDuration, series.interpolationEasing);
                            series.bulletsContainer.animate({ property: "dx", to: dx }, series.interpolationDuration, series.interpolationEasing);
                        })
                    }
                }
            }

            var label = chart.chartContainer.createChild(am4core.Label);
            label.fontSize = 22;
            label.text = "Месяц";
            label.align = "center";

        });

    }
    function createThirdReport(data){
        let newData = [];
        for(let i in data)
        {
            newData.push(data[i])
        }

        am4core.ready(function() {
            am4core.useTheme(am4themes_animated);
            var chart = am4core.create("chartdiv3", am4charts.XYChart);

            chart.data = newData;
            chart.legend = new am4charts.Legend();
            chart.legend.position = "bottom";
            chart.legend.paddingTop = 50;
            chart.legend.labels.template.maxWidth = 50;

            var categoryAxis = chart.yAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "month";
            categoryAxis.title.fontSize = "24";
            categoryAxis.title.text = "Месяц";
            categoryAxis.renderer.grid.template.opacity = 0;

            var valueAxis = chart.xAxes.push(new am4charts.ValueAxis());
            valueAxis.min = 0;
            valueAxis.renderer.grid.template.opacity = 0;
            valueAxis.renderer.ticks.template.strokeOpacity = 0.5;
            valueAxis.renderer.ticks.template.stroke = am4core.color("#495C43");
            valueAxis.renderer.ticks.template.length = 10;
            valueAxis.renderer.line.strokeOpacity = 0.5;
            valueAxis.renderer.baseGrid.disabled = true;
            valueAxis.renderer.minGridDistance = 40;

            valueAxis.title.fontSize = "20";
            valueAxis.title.text = "Количество часов";


            function createSeries(field, name) {
                var series = chart.series.push(new am4charts.ColumnSeries());
                series.dataFields.valueX = field;
                series.dataFields.categoryY = "month";
                series.stacked = true;
                series.name = name;

                var labelBullet = series.bullets.push(new am4charts.LabelBullet());
                labelBullet.locationX = 0.5;
                labelBullet.label.text = "{valueX}";
                labelBullet.label.fill = am4core.color("#fff");
            }

            newData.forEach(function (item){
                for(let i in item)
                {
                    if(i !== 'month'){
                        createSeries(i, i);
                    }

                }
            });

        });
    }
    function createFourthReport(data){
        let newData = [];

        for(let i in data)
        {
            let hours = `${i} (${(data[i])} ч.)`;

            newData.push({name: i, count: data[i]})
        }

        am4core.ready(function() {
            am4core.useTheme(am4themes_animated);
            var chart = am4core.create("chartdiv11", am4charts.PieChart);

            chart.data = newData;

            var pieSeries = chart.series.push(new am4charts.PieSeries());
            pieSeries.dataFields.value = "count";
            pieSeries.dataFields.category = "name";
            pieSeries.innerRadius = am4core.percent(50);
            pieSeries.ticks.template.disabled = false;
            pieSeries.labels.template.disabled = false;

            chart.legend = new am4charts.Legend();
            chart.legend.position = 'bottom';
            chart.legend.paddingTop = 50;

            chart.legend.labels.template.maxWidth = 50;
            chart.legend.valueLabels.template.align = "right";
            chart.legend.valueLabels.template.textAlign = "end";

        });

    }


    function dellBottomLine(node){
        node.forEach(function (item){
            if(item.classList.contains('li__active')){
                item.classList.remove('li__active');
            }
        });
    }
    function getUrlVar(){
        var urlVar = window.location.search;
        var arrayVar = [];
        var valueAndKey = [];
        var resultArray = [];
        arrayVar = (urlVar.substr(1)).split('&');
        if(arrayVar[0]=="") return false;
        for (i = 0; i < arrayVar.length; i ++) {
            valueAndKey = arrayVar[i].split('=');
            resultArray[valueAndKey[0]] = valueAndKey[1];
        }
        return resultArray;
    }



    $('.select').on('click', '.select__head', function () {
        if ($(this).hasClass('open')) {
            $(this).removeClass('open');
            $(this).next().fadeOut();
        } else {
            $('.select__head').removeClass('open');
            $('.select__list').fadeOut();
            $(this).addClass('open');
            $(this).next().fadeIn();
        }
    });

    $('.select').on('click', '.select__item', function () {
        $('.select__head').removeClass('open');
        $(this).parent().fadeOut();
        $(this).parent().prev().text($(this).text());
        $(this).parent().prev().prev().val($(this).text());
    });

    $(document).click(function (e) {
        if (!$(e.target).closest('.select').length) {
            $('.select__head').removeClass('open');
            $('.select__list').fadeOut();
        }
    });

});
