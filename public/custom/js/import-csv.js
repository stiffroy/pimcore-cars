document.addEventListener(pimcore.events.postOpenAsset, (e) => {
    if (e.detail.type === 'text' && e.detail.asset.data.filename.endsWith('.csv')) {
        e.detail.asset.toolbar.add({
            text: t('CSV Import'),
            iconCls: 'pimcore_icon_import',
            scale: 'small',
            handler: function (obj) {
                //do some stuff here, e.g. open a new window with an PDF download
                console.log(e.detail.asset.data.id);
            }.bind(this, e.detail.object)
        });
        pimcore.layout.refresh();
    }
});