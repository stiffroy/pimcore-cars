document.addEventListener(pimcore.events.postOpenAsset, (e) => {
    if (e.detail.type === 'text' && e.detail.asset.data.filename.endsWith('.csv')) {
        e.detail.asset.toolbar.add({
            text: t('CSV Import'),
            iconCls: 'pimcore_icon_import',
            scale: 'small',
            handler: function (obj) {
                const path = "/api/import/cars/" + e.detail.asset.id;
                let xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState === 4 && this.status === 200) {
                        let response = JSON.parse(this.responseText);
                        alert(response.message);
                    }
                };
                xhttp.open("GET", path, true);
                xhttp.send();
            }.bind(this, e.detail.asset)
        });
        pimcore.layout.refresh();
    }
});
