(function ($) {
    $(function () {

        var getUrlParameter = function getUrlParameter(sParam) {
            var sPageURL = decodeURIComponent(window.location.search.substring(1)),
                sURLVariables = sPageURL.split('&'),
                sParameterName,
                i;

            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === sParam) {
                    return sParameterName[1] === undefined ? true : sParameterName[1];
                }
            }
        };

        /*  $('#emol_forminstance_fields').sortable({
         containment: 'parent',
         handle: '.emol_move_handler'
         });
         */

        $('#emol_forminstance_itemadd').on('change', function () {
            var fieldId = $('#emol_forminstance_itemadd option:selected').val();

            if (fieldId == 'empty')
                return;

            $('#emol_forminstance_itemadd option:first-child').attr('selected', true);

            var fieldConfig = $('<div />', {
                className: 'emol_fieldconfig',
                html: 'loading...'
            });

            fieldConfig.appendTo('#emol_forminstance_fields');

            var adminURL = window.location.protocol + "//" + window.location.host + "" + window.location.pathname.replace('admin.php', '');


            jQuery.post(
                adminURL + 'admin-ajax.php',
                {
                    action: 'emol-formstance-dummy',
                    fieldId: fieldId,
                    forminstanceId: getUrlParameter('listId')
                },
                function (response) {
                    fieldConfig.html(response);
                    // $('#emol_forminstance_fields').sortable('refresh');
                }
            );
        });

        $('.emol_remove_handler').on('click', function () {
            var fieldContainer = $(this).parent();

            fieldContainer.slideUp(400, function () {
                fieldContainer.remove();
            });

            return false;
        });

        $('#forminstance_persist_form').submit(function () {
            var rowNr = -1;
            $(this).find('.emol_fieldconfig').each(function () {
                rowNr++;

                $(this).find('input, select, textarea').each(function () {
                    var field = $(this),
                        fieldName = field.attr('name');

                    fieldName = fieldName.replace('rownr', rowNr);
                    field.attr('name', fieldName);
                });
            });
        });
    });
})(jQuery);

//enable tab support on textareas
jQuery(document).delegate('.tab_text', 'keydown', function (e) {
    var keyCode = e.keyCode || e.which;

    if (keyCode == 9) {
        e.preventDefault();
        var start = jQuery(this).get(0).selectionStart;
        var end = jQuery(this).get(0).selectionEnd;

        // set textarea value to: text before caret + tab + text after caret
        jQuery(this).val(jQuery(this).val().substring(0, start)
            + "\t"
            + jQuery(this).val().substring(end));

        // put caret at right position again
        jQuery(this).get(0).selectionStart =
            jQuery(this).get(0).selectionEnd = start + 1;
    }
});

jQuery(function ($) {
    if ($.fn.wpColorPicker) {
        $('.emol-color-field').wpColorPicker();
    }

    var $logoId = $('#emol_theme_logo_id');
    var $preview = $('#emol-theme-logo-preview');
    var $clear = $('#emol-theme-logo-clear');
    var frame;

    $('#emol-theme-logo-upload').on('click', function (event) {
        event.preventDefault();

        if (frame) {
            frame.open();
            return;
        }

        frame = wp.media({
            title: 'Logo kiezen',
            button: { text: 'Dit logo gebruiken' },
            multiple: false
        });

        frame.on('select', function () {
            var attachment = frame.state().get('selection').first().toJSON();
            var url = (attachment.sizes && attachment.sizes.medium)
                ? attachment.sizes.medium.url
                : attachment.url;

            $logoId.val(attachment.id);
            $preview.html('<img src="' + url + '" alt="" />');
            $clear.show();
        });

        frame.open();
    });

    $clear.on('click', function (event) {
        event.preventDefault();
        $logoId.val('');
        $preview.empty();
        $clear.hide();
    });

    var $themeInputs = $('input[name="emol_form_theme"]');
    var $jquerySkinRow = $('#emol-jquery-ui-skin-row');

    if ($themeInputs.length && $jquerySkinRow.length) {
        var syncJquerySkinRow = function () {
            var selected = $themeInputs.filter(':checked').val();
            $jquerySkinRow.toggle(selected === 'jquery');
        };

        $themeInputs.on('change', syncJquerySkinRow);
        syncJquerySkinRow();
    }
});
