(function ($) {
    'use strict';

    /**
     * Admin JS stuff.
     */
    $(function () {

        $('#save-kivi-settings').on('click', function () {
            if ($('#kivi-show-statusbar').val() == '') {
                $('#kivi-show-statusbar').val(0);
            }
            var opts = {
                'action': 'kivi_save_settings',
                'kivi-brand-color': $('#kivi-brand-color').val(),
                'kivi-slug': $('#kivi-slug').val(),
                'kivi-show-statusbar': $('#kivi-show-statusbar').val(),
                'kivi-prefilter-name': $('#kivi-prefilter-name').val(),
                'kivi-prefilter-value': $('#kivi-prefilter-value').val(),
                'kivi-gmap-id': $('#kivi-gmap-id').val(),
                'kivi-rest-user': $('#kivi-rest-user').val(),
                'kivi-rest-pass': $('#kivi-rest-pass').val()
            };

            doAjax(opts, function (res, type) {
                showMsg(res, type);
            });
        });

        $('#rest-update-all').on('click', function () {
            $('body').css('opacity', '0.8');
            $('body').css('cursor', 'wait');
            doAjax({'action': 'kivi_sync'}, function (res, type) {
                showMsg(res, type);
                $('body').css('opacity', '1');
                $('body').css('cursor', 'initial');
            });

        });

        $('#import-reset').on('click', function () {
            if (confirm('Oletko varma että haluat keskeyttää ja poistaa jo tuodut kohteet?')) {
                doAjax({'action': 'kivi_reset'}, function (res, type) {
                    showMsg(res, type);
                });
            } else {
                return false;
            }
        });

        // Add Color Picker
        $('#kivi-brand-color').wpColorPicker();
    });

    function doAjax(data, callback) {
        var jqxhr = $.ajax({type: 'post', url: ajaxurl, data: data, dataType: 'json'})
            .done(function (res) {
                callback(res, 'success');
            })
            .fail(function (err) {
                callback(err, 'error');
            })
            .always(function (res) {
                console.log(JSON.stringify(res));
            });
    }

    function showMsg(res, type) {
        $('#admin-info').addClass('admin-info--' + type).text(res.message);
        location.reload();
    }

})(jQuery);
