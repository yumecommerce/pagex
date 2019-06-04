var pagexLocationHash = window.location.hash.substr(1);

if (pagexLocationHash.length) {
    jQuery('h2.nav-tab-wrapper .nav-tab').removeClass('nav-tab-active');
    jQuery('.pagex-tab-section').removeClass('active');
    jQuery('h2.nav-tab-wrapper a[href="#' + pagexLocationHash + '"]').addClass('nav-tab-active');
    jQuery('#' + pagexLocationHash).addClass('active');
}

jQuery('h2.nav-tab-wrapper .nav-tab, a[href^="#tab_"]').on('click', function (e) {
    e.preventDefault();
    let tab = jQuery(this).attr('href');
    jQuery('h2.nav-tab-wrapper .nav-tab').removeClass('nav-tab-active');
    jQuery('.pagex-tab-section').removeClass('active');
    jQuery('h2.nav-tab-wrapper a[href="' + tab + '"]').addClass('nav-tab-active');
    jQuery(tab).addClass('active');
});

jQuery('#pagex-export-settings').click(function () {
    jQuery.post(ajaxurl, {
        action: 'pagex_export_settings',
    }, function (data) {
        jQuery('#pagex-export-settings-area').html(JSON.stringify(data));
    }).fail(function () {
        console.error('Fail to export the settings');
    });
});

jQuery('.sync-adobe-fonts').click(function () {
    jQuery.post(ajaxurl, {
        action: 'pagex_sync_adobe_fonts',
        id: jQuery('#adobe_fonts_id').val()
    }, function (data) {
        jQuery('.adobe-fonts').html(data);
    }).fail(function () {
        console.error('Fail to export the settings');
    });
});

(function ($, window, document) {
    $('.add-new-font').click(function () {
        var id = Math.random().toString(36).substr(2, 5),
            font = pagexCustomFont;

        $('.pagex-custom-fonts').append(font.replace(new RegExp('uniqid', 'g'), id));
    });

    $(document).on('click', '.add-font-variation', function () {
        var variation = pagexCustomFontVariation,
            parent = $(this).parents('.custom-font-variations'),
            id = $(this).parents('.custom-font-row').find('[data-font-id]').attr('data-font-id');

        parent.prepend(variation.replace(new RegExp('uniqid', 'g'), id));
    });

    $(document).on('click', '.select-font-file', function () {
        var input = $(this).parent('p').find('input'),
            frame = window.wp.media.frames.downloadable_file = window.wp.media({
                multiple: false,
                library: {
                    type: [ 'font' ]
                },
            });

        frame.open();

        frame.on('select', function () {
            var attachment = frame.state().get('selection').first().toJSON();

            input.val(attachment.url);
        });
    });

    $(document).on('click', '.delete-font', function () {
        $(this).parents('.custom-font-row').remove();
    });

    $(document).on('click', '.delete-font-variation', function () {
        $(this).parents('.custom-font-variation').remove();
    });

}(window.jQuery, window, document));