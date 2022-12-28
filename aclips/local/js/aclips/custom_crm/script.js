(function () {
    BX.ready(function () {
        let target = document.querySelector('body');
        let observer = new MutationObserver(function () {
            let entityDetailContainer = BX.findChildByClassName(document, 'ui-entity-editor-content-block', true)

            if (entityDetailContainer) {
                let streamContainer = document.querySelector('.crm-entity-stream-container')
                if(streamContainer){
                    streamContainer.remove()
                }
                observer.disconnect()
            }
        });

        observer.observe(target, {attributes: true, characterData: true, childList: true, subtree: true});
    })
})()