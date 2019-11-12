/*
 EazyMatch functions
 */

var EazyWP = {
    init: function ($) {

        if (!$('#emol-free-search-cv-input').hasClass('noautosubmit')) {
            $('#emol-free-search-cv-input').keyup(function (event) {
                if (event.keyCode == '13') {
                    emolSearchCv();
                }
            });
        }

        if ($("#emol-share-btns").length) {
            $("#emol-share-btns").jsSocials({
                showLabel: false,
                showCount: "inside",
                shareIn: "popup",
                shares: ["whatsapp", "messenger", "email", "linkedin", "twitter", "facebook"]
            });
        }

        if (!$('#emol-free-search-input').hasClass('noautosubmit')) {
            $('#emol-free-search-input').keyup(function (event) {
                if (event.keyCode == '13') {
                    emolSearchJob();
                }
            });
        }

        $(document).on('click', '#emol-read-ps', function () {

            var wWidth = $(window).width();
            var dWidth = wWidth * 0.8;
            var wHeight = $(window).height();
            var dHeight = wHeight * 0.8;

            jQuery('#emolAvgStatement').dialog({
                show: {
                    effect: "blind",
                    duration: 500
                },
                hide: {
                    effect: "blind",
                    duration: 100
                },
                buttons: [
                    {
                        text: "Akkoord",
                        click: function () {
                            jQuery('#emol-avg-check').attr('checked', 'checked');
                            jQuery('#emolAvgStatement').dialog("close");
                        }
                    }, {
                        text: "Sluiten",
                        click: function () {
                            jQuery('#emolAvgStatement').dialog("close");
                        }
                    }
                ],
                draggable: true,
                modal: true,
                closeOnEscape: true,
                draggable: true,
                width: dWidth,
                height: dHeight,
                resizable: false
            });
            jQuery(".ui-dialog-titlebar").hide();
            //$("#emolAvgStatement").css({height:"400px", overflow:"auto"});

        }).on('keyup change', '#emol-form-wrapper .required', function () {

            if (jQuery(this).val()) {
                jQuery('#eazymatch-error-' + jQuery(this).attr('id')).remove();
            }

        }).on('click', '.emol-form-submit', function () {


            var hasError = false;
            jQuery('#emol-form-wrapper .required').each(function (a, b) {


                if (jQuery(b).attr('id') == 'emol-avg-check') {
                    if (!jQuery(b).is(':checked')) {
                        var $errEl = '<div class="emol-error-label" id="eazymatch-error-' + jQuery(b).attr('id') + '">Dit veld is niet of incorrect ingevuld</div>';
                        jQuery(b).parent().append($errEl);
                        hasError = true;
                    }
                } else {
                    if (!jQuery(b).val()) {
                        var $errEl = '<div class="emol-error-label" id="eazymatch-error-' + jQuery(b).attr('id') + '">Dit veld is niet of incorrect ingevuld</div>';
                        jQuery(b).parent().append($errEl);
                        hasError = true;
                    }
                }
            }).promise().done(function () {

                if (hasError) {
                    return false;
                } else {
                    jQuery('.emol-form-submit').attr('disabled', 'disabled');
                    jQuery('.emol-form-submit').html('Een Moment Geduld...');

                    jQuery.featherlight($('#eazymatch-wait-modal'), {
                        closeOnEsc: false,
                        closeIcon: '',
                    });
                    jQuery('#emol-apply-form').submit();
                }
            });
        });

        // grid initialize
        var newrowIndex = 10000;

        $('.emol_grid').each(function () {
            var grid = $(this);

            //grid.delegate( '.button-grid-add', 'click', function( event ){
            $('.button-grid-add').on('click', function (event) {
                event.preventDefault();

                var template = grid.find('.emol_grid_template').html();

                newrowIndex++;
                template = template.replace(/templateid/gi, newrowIndex);

                // initialize datepicker objects
                template = $(template);
                template.find('.datepicker').each(function () {
                    var element = $(this);

                    EazyWP.createDatePicker(element);
                });

                grid.find('.emol_grid_rows').append(template);
            });

            //grid.delegate( '.button-grid-remove', 'click', function( event ){
            $('.button-grid-remove').on('click', function (event) {
                event.preventDefault();

                if (!confirm('Weet u zeker dat u deze rij wilt weghalen?'))
                    return;

                var button = $(this),
                    row = button.parents('.emol_grid_row');

                row.hide();
                row.remove();
            });
        });

        // initialize tree behavior
        $('.emol_checktree').each(function () {
            var tree = $(this);

            //tree.delegate( 'input', 'change', function( event ){
            $('.input').on('change', function (event) {
                var $el = $(this),
                    checked = $el.is(':checked');

                if (checked) {
                    var parents = $el.parentsUntil(tree, '.emol_checktree_leaf');

                    if (parents.length > 0) {
                        var parent = $(parents[1]).children('input');
                        parent.attr('checked', 'checked');
                        parent.trigger('change');
                    }
                } else {
                    $el.parent().find('input').attr('checked', false);
                }
            });
        });

        // create datepickers for date fields
        jQuery('.datepicker:not(:hidden)').each(function () {

            EazyWP.createDatePicker($(this));
        });
    },

    createDatePicker: function ($input) {
        var $ = jQuery,
            currentDate = $input.val(),
            $hidden = $('<input />', {
                type: 'hidden',
                name: $input.attr('name'),
                value: currentDate
            });

        $input.attr('name', $input.attr('name') + '_original');
        var date = new Date(currentDate);

        // make sure initial input is in dutch format if date is valid
        if (isNaN(date.getFullYear())) {
            $input.val('');
        } else {
            $input.val(date.getDate() + '-' + (date.getMonth() + 1) + '-' + date.getFullYear());
        }

        $input.after($hidden);

        $input.datepicker({
            closeText: 'Sluiten',
            prevText: '&lt;',
            nextText: '&gt;',
            currentText: 'Vandaag',
            monthNames: ['januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december'],
            monthNamesShort: ['jan', 'feb', 'maa', 'apr', 'mei', 'jun', 'jul', 'aug', 'sep', 'okt', 'nov', 'dec'],
            dayNames: ['zondag', 'maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'zaterdag'],
            dayNamesShort: ['zon', 'maa', 'din', 'woe', 'don', 'vri', 'zat'],
            dayNamesMin: ['zo', 'ma', 'di', 'wo', 'do', 'vr', 'za'],
            weekHeader: 'Wk',
            dateFormat: 'dd-mm-yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: '',
            showButtonPanel: false,
            changeYear: true,
            changeMonth: true,
            yearRange: '1910:2020',
            altField: $hidden,
            altFormat: 'yy-mm-dd'
        });
    },

    search: {
        general: function (baseUrl) {

            var seperator = '/'
            var addStringVar = ''
            var emolCleanUrl = location.href.substring(0, location.href.indexOf('/', 14));

            var locationOrFreeSearch = false;

            //check free search values
            var placeholdertext = jQuery('#emol-free-search-input').attr('placeholder');
            if (jQuery('#emol-free-search-input').val() != '' && jQuery('#emol-free-search-input').val() != undefined && placeholdertext != jQuery('#emol-free-search-input').val()) {
                baseUrl = baseUrl + '/free,' + jQuery('#emol-free-search-input').val();
                seperator = ',';
                locationOrFreeSearch = true;
            }

            //check zipcode search values
            var placeholdertext = jQuery('#emol-zipcode-search-input').attr('placeholder');
            if (jQuery('#emol-zipcode-search-input').val() != '' && jQuery('#emol-zipcode-search-input').val() != undefined && placeholdertext != jQuery('#emol-zipcode-search-input').val()) {
                var range = 50;
                if (jQuery('#emol-range-search-input option:selected').val() > 0) {
                    range = jQuery('#emol-range-search-input option:selected').val();
                }
                baseUrl = baseUrl + seperator + 'location,' + jQuery('#emol-zipcode-search-input').val();
                baseUrl = baseUrl + ',' + range;
                seperator = ',';
                locationOrFreeSearch = true;
            }

            //check province search values
            var province = jQuery('#emol-province-search-input option:selected').val(),
                hasProvince = typeof province == 'string' && province != '';

            if (hasProvince) {
                baseUrl += seperator + 'province,' + province;
                seperator = ',';
                locationOrFreeSearch = true;
            }

            //loop all selected selectboxes
            jQuery('.search_competences option:selected').each(function () {
                if (jQuery(this).attr("value") != '')
                    addStringVar += ',' + jQuery(this).attr("value");
            });

            //loop all selected selectboxes
            jQuery('.search_competences_checkboxes:checked').each(function () {
                if (jQuery(this).attr("value") != '')
                    addStringVar += ',' + jQuery(this).attr("value");
            });

            //check competences
            if (addStringVar != '') {
                baseUrl = '' + baseUrl + seperator + 'competence' + addStringVar;
            } else if (locationOrFreeSearch == false) {
                baseUrl = '' + baseUrl + '/all/';
            }

            //finalize url
            baseUrl = '/' + baseUrl;
            baseUrl = baseUrl.replace('//', '/');
            baseUrl = emolCleanUrl + baseUrl + '/';

            window.location = baseUrl;
        }
    }
};

// initialize EazyMatch on page ready
jQuery(function () {
    EazyWP.init(jQuery);
})

/**
 * legacy functionnames support
 */
var emolSearch = EazyWP.search.general,
    emolSearchJob = EazyWP.search.job;
emolSearchCv = EazyWP.search.cv;

function emolLoginPopup() {
    jQuery('#emolLoginDialog').dialog({
        show: {
            effect: "blind",
            duration: 500
        },
        hide: {
            effect: "blind",
            duration: 100
        },
        buttons: [
            {
                text: "Sluiten",
                click: function () {
                    jQuery('#emolLoginDialog').dialog("close");
                }
            },
            {
                text: "Inloggen",
                click: function () {
                    jQuery('#emolLoginDialog').submit();
                }
            }
        ],
        closeOnEscape: true,
        draggable: true,
        modal: true,
        width: 500
    });
    jQuery(".ui-dialog-titlebar").hide();
}

/**
 * TODO: implement page navigation prevention when editing forms
 */
var navProtect = {
    enabled: false,

    message: 'De wijzigingen op deze pagina zijn nog niet doorgevoerd, weet u zeker dat u deze pagina wilt verlaten?',

    enable: function () {
        navProtect.enabled = true;
    },

    disable: function () {
        navProtect.enabled = false;
    },

    unloadCheck: function () {
        if (!navProtect.enabled)
            return;

        return navProtect.message;
    }
};

window.onbeforeunload = navProtect.unloadCheck;


function emol_connect_linkedin(url, instance) {
    var url = 'https://linkedin.eazymatch.cloud/?refer=' + url + '&instance=' + instance;
    //var features = 'width=600;height=350;menubar=no;directories=no;location=no;modal=yes';
    //window.open(url, 'emol_connect_linkedin', features, false);
    window.location = url;
}

