BX.ready(function(){
    let customFilter = BX('custom-filter');
    container = BX('cont');
    function clearStyle(){
        let items = BX.findChildren(customFilter, {tag: 'span', class: 'click'}, true);
        items.forEach(item =>{
            BX.removeClass(item, 'item-select');
        });
    }
    function toggle(elem){

        if(elem.getAttribute('data-sort') == 'ASC')
        {
            clearStyle();
            BX.addClass(elem, 'arrowDown');
            let nextElem = BX.findNextSibling(elem);
            BX.removeClass(nextElem, 'arrowDown');
            BX.addClass(nextElem, 'arrowUP');
            BX.addClass(nextElem, 'item-select');
        }
        else if(elem.getAttribute('data-sort') == 'DESC'){
            clearStyle();
            BX.removeClass(elem, 'arrowUP');
            BX.addClass(elem, 'arrowDown');
            let prevElem = BX.findPreviousSibling(elem);
            BX.removeClass(prevElem, 'arrowDown');
            BX.addClass(prevElem, 'arrowUP');
            BX.addClass(prevElem, 'item-select');
        }
    }
    function setCookie(sortName, sortValue){
        let cook_val = [sortName, sortValue ]
        BX.setCookie('sortFilter', JSON.stringify(cook_val), {expires: 86400, path: '/'})
    }
    function sendAjaxx(dataAjax){
        BX.ajax({
            url: '/local/components/yago/ajaxFilter/templates/ajax.php',
            data: dataAjax,
            method: 'POST',
            dataType: 'html',
            onsuccess: function(data){
                BX.cleanNode(container);
                container.innerHTML = data;
            },
            onfailure: function(data){
                console.error(data)
            }
        });
    }

    if(BX.getCookie('sortFilter') !== undefined){
        let getCookie = JSON.parse(BX.getCookie('sortFilter'));
        let dataCookie = {};
        dataCookie.name = getCookie[0];
        dataCookie.sort = getCookie[1];
        sendAjaxx(dataCookie);
        let item = BX.findChild(customFilter,
            {
                tagName: 'span',
                attribute: {'data-name': dataCookie.name}

            }, true);
        toggle(item);
    }
    BX.bindDelegate(customFilter, 'click',
        {
            tag: 'span',
            class: 'click'
        },
        function (e)
        {
            let data = {};
            data.name = this.getAttribute('data-name');
            data.sort = this.getAttribute('data-sort');
            setCookie(data.name, data.sort);
            toggle(this);
            e.preventDefault();
            sendAjaxx(data);

        }
    );
});