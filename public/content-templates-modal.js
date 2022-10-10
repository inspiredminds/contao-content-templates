(function(){
    'use strict';

    const initialized = new WeakMap();

    const init = (element) => {
        if (initialized.has(element)) {
            return;
        }

        initialized.set(element, true);

        element.addEventListener('click', function(e) {
            e.preventDefault();
    
            Backend.openModalSelector({
                "id": "tl_listing",
                "title": element.dataset.title,
                "url": element.href,
                "callback": function(table, value) {
                    window.location = element.dataset.apply+'/'+value[0];
                }
            });

            return false;
        });
    };

    document.querySelectorAll('.content-templates-modal').forEach(init);

    new MutationObserver(function (mutationsList) {
        for (const mutation of mutationsList) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(function (element) {
                    if (element.matches && element.matches('.content-templates-modal')) {
                        init(element);
                    }
                })
            }
        }
    }).observe(document, {
        attributes: false,
        childList: true,
        subtree: true
    });
})();
