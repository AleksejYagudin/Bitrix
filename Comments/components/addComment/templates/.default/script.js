BX.ready(function(){
    let form = BX.findChild(BX('comment'),{tag: 'FORM'}, true),
        text = BX.findChild(BX('comment'),{tag: 'TEXTAREA'}, true),
        button = BX.findChild(BX('comment'),{tag: 'INPUT', attribute:{type: 'submit'}}, true),
        ratings = BX.findChildren(BX('comment'),{tag: 'INPUT', attribute:{type: 'radio'}}, true);
        data = {};

    BX.bind(text, 'change', function(){
        BX.adjust(text, {style: {border: '1px dashed #969696'}})
    });

    ratings.forEach(item => {
        BX.bind(item, 'click', function (e){
            clearRating();
            setRating(item);
            data.rating = e.target.value;
        })
    });
    data.id = button.getAttribute('data-id');
    data.iblock = button.getAttribute('data-iblock');

    BX.bind(button, 'click', function (e){
        e.preventDefault();
        if(text.value == '')
        {
            BX.adjust(text, {style: {border: '1px dashed red'}})
            return false;
        }
        data.text = text.value;

        BX.ajax({
            url: '/local/components/addComment/templates/.default/ajax.php',
            data: data,
            method: 'POST',
            dataType: 'json',

            onsuccess: function(data){
                BX.cleanNode(form, true);
                BX.adjust(BX('comment'), {style: {height: '35px'}})
                let responceText = ''
                BX.prepend(BX.create('div', {
                    style:{color: '#515151', fontSize: '16px', textAlign: 'center'},
                    text: data
                }), BX('comment'));

            },
            onfailure: function(){
                console.error(data);
            }
        });

    });

    function clearRating()
    {
        ratings.forEach(item =>{
            BX.adjust(item, {attrs: {checked: ""}})
        });
    }
    function setRating(value){
        BX.adjust(value, {attrs: {checked: "checked"}})
    }
});