var pagexLocationHash = window.location.hash.substr(1);

if (pagexLocationHash.length) {
    jQuery('h2.nav-tab-wrapper .nav-tab').removeClass('nav-tab-active');
    jQuery('.pagex-tab-section').removeClass('active');
    jQuery('h2.nav-tab-wrapper a[href="#' + pagexLocationHash + '"]').addClass('nav-tab-active');
    jQuery('#' + pagexLocationHash).addClass('active');
}

jQuery('h2.nav-tab-wrapper .nav-tab, a[href^="#tab_"]').on('click', function (e) {
    e.preventDefault();
    tab = jQuery(this).attr('href');
    jQuery('h2.nav-tab-wrapper .nav-tab').removeClass('nav-tab-active');
    jQuery('.pagex-tab-section').removeClass('active');
    jQuery('h2.nav-tab-wrapper a[href="' + tab + '"]').addClass('nav-tab-active');
    jQuery(tab).addClass('active');
});

jQuery('#pagex-export-settings').click(function () {
    jQuery.post(ajaxurl, {
        action: 'pagex_export_settings',
    }, function (data) {
        var dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(data));
        var downloadAnchorNode = document.createElement('a');
        downloadAnchorNode.setAttribute("href", dataStr);
        downloadAnchorNode.setAttribute("download", "pagex-settings.json");
        document.body.appendChild(downloadAnchorNode); // required for firefox
        downloadAnchorNode.click();
        downloadAnchorNode.remove();
    }).fail(function () {
        console.error('Fail to export the settings');
    });
});