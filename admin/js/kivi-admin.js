(function( $ ) {
	'use strict';

	/**
	* Admin JS stuff.
	 */

	$(function() {

    $('#save-kivi-settings').on('click', function() {
      if ( $('#kivi-show-statusbar').val() == '' ) {
        $('#kivi-show-statusbar').val(0);
      }
      var opts = {
        'action': 'kivi_save_settings',
        'kivi-brand-color': $('#kivi-brand-color').val(),
        'kivi-slug': $('#kivi-slug').val(),
        'kivi-show-statusbar': $('#kivi-show-statusbar').val(),
        'kivi-show-sidebar': $('#kivi-show-sidebar').is(':checked') ? true : "",
				'kivi-use-www-size': $('#kivi-use-www-size').is(':checked') ? true : "",
        'kivi-gmap-id': $('#kivi-gmap-id').val()
      };

      doAjax(opts, function(res, type) {
        showMsg(res, type);
      });
    });

		$('#xmlimport-sync').on('click', function() {
      $('body').css('opacity', '0.8');
      $('body').css('cursor', 'wait');
      $('#xmlimport-sync').css('cursor', 'wait');
      $('#kivi-show-statusbar').val(1);

      var opts = {
        'action': 'kivi_set_remote_url',
        'kivi-remote-url': $('#kivi-remote-url').val(),
      };
      doAjax(opts, function() {
        doAjax({'action': 'kivi_sync', 'kivi-show-statusbar': $('#kivi-show-statusbar').val()}, function(res, type) {
          showMsg(res, type);
          $('body').css('opacity', '1');
          $('body').css('cursor', 'initial');
          $('#xmlimport-sync').css('cursor', 'initial');
        });
      });
		});

    $('#xmlimport-stop-sync').on('click', function() {
      if (confirm('Oletko varma että haluat keskeyttää?')) {
        doAjax( {'action': 'kivi_stop'}, function(res, type) {
          showMsg(res, type);
        });
      } else {
        return false;
      }
    });

    $('#xmlimport-reset').on('click', function() {
      if (confirm('Oletko varma että haluat keskeyttää ja poistaa jo tuodut kohteet?')) {
        doAjax( {'action': 'kivi_reset'}, function(res, type) {
          showMsg(res, type);
        });
      } else {
        return false;
      }
    });

    $("#kivi-brand-color").spectrum({
      className: "kivi-colorpicker",
      preferredFormat: "hex",
      localStorageKey: "kivi.colorpicker",
      showInput: true,
      move: function (color) {

      },
      show: function () {

      },
      beforeShow: function () {

      },
      hide: function () {

      },
      change: function(color) {
        $('#kivi-brand-color').val(color);
        $('label[for=""]')
      }
    });

	});


  function doAjax(data, callback) {
    var jqxhr = $.ajax( { type: 'post', url: ajaxurl, data: data, dataType: 'json' } )
    .done(function(res) {
      callback(res, 'success');
    })
    .fail(function(err) {
      callback(err, 'error');
    })
    .always(function(res) {
      console.log(JSON.stringify(res));
    });
  }

  function showMsg(res, type) {
    $('#admin-info').addClass('admin-info--'+type).text(res.message);
    location.reload();
  }

})( jQuery );
