/*
 EazyMatch functions
 */

var EazyWP = {
    init: function ($) {

        if (!jQuery('#emol-free-search-cv-input').hasClass('noautosubmit')) {
            jQuery('#emol-free-search-cv-input').keyup(function (event) {
                if (event.keyCode == '13') {
                    emolSearchCv();
                }
            });

        }

        if (!navigator.share) {
            jQuery('.emol-share-btn-native').hide();
        }

        jQuery(document).on('click', '.emol-share-btn', function (e) {
            var mode = this.getAttribute('data-share');
            var section = jQuery(this).closest('.emol-sharing-section');
            var shareTitle = section.attr('data-share-title') || document.title;
            var shareUrl = section.attr('data-share-url') || window.location.href;

            if (mode === 'native') {
                e.preventDefault();
                if (navigator.share) {
                    navigator.share({ title: shareTitle, url: shareUrl }).catch(function () {});
                } else {
                    var first = section.find('.emol-share-btn[data-share="popup"]').get(0);
                    if (first) {
                        window.open(first.getAttribute('href'), 'emol-share', 'noopener,noreferrer,width=640,height=560');
                    }
                }
                return;
            }

            if (mode === 'popup') {
                e.preventDefault();
                window.open(this.getAttribute('href'), 'emol-share', 'noopener,noreferrer,width=640,height=560');
            }
        });

        if (!jQuery('#emol-free-search-input').hasClass('noautosubmit')) {
            jQuery('#emol-free-search-input').keyup(function (event) {
                if (event.keyCode == '13') {
                    emolSearchJob();
                }
            });
        }

        jQuery(document).on('click', '#emol-read-ps', function () {

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
        });

        // grid initialize
        var newrowIndex = 10000;

        jQuery('.emol_grid').each(function () {
            var grid = $(this);

            //grid.delegate( '.button-grid-add', 'click', function( event ){
            jQuery('.button-grid-add').on('click', function (event) {
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
            jQuery('.button-grid-remove').on('click', function (event) {
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
        jQuery('.emol_checktree').each(function () {
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
            yearRange: '1910:2045',
            altField: $hidden,
            altFormat: 'yy-mm-dd'
        });
    },

    form: {
        bound: false,

        messages: function () {
            var i18n = (typeof window !== 'undefined' && window.EmolForm) ? window.EmolForm : {};
            return {
                required: i18n.required || 'Dit veld is niet of incorrect ingevuld',
                email: i18n.email || 'Dit is een ongeldig e-mailadres',
                wait: i18n.wait || 'Een moment geduld'
            };
        },

        isEmailField: function (field) {
            if (!field) {
                return false;
            }
            var className = ' ' + (field.className || '') + ' ';
            return field.id === 'emol-email' || field.type === 'email' || className.indexOf(' email ') !== -1;
        },

        isValidEmail: function (email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(email || '').toLowerCase());
        },

        isFilled: function (field) {
            if (!field) {
                return false;
            }
            if (field.type === 'checkbox') {
                return !!field.checked;
            }
            if (field.type === 'radio') {
                var form = field.form;
                if (!form || !field.name) {
                    return !!field.checked;
                }
                var selected = form.querySelector('input[type="radio"][name="' + field.name.replace(/"/g, '\\"') + '"]:checked');
                return !!(selected && String(selected.value || '').trim() !== '');
            }
            if (field.type === 'file') {
                return !!(field.files && field.files.length > 0);
            }
            return String(field.value || '').trim() !== '';
        },

        fields: function (form) {
            var nodes = form.querySelectorAll('.required, [required]');
            var list = [];
            for (var i = 0; i < nodes.length; i++) {
                var field = nodes[i];
                if (!field || field.disabled || field.type === 'hidden') {
                    continue;
                }
                if (list.indexOf(field) === -1) {
                    list.push(field);
                }
            }
            return list;
        },

        errorId: function (field) {
            return 'eazymatch-error-' + (field.id || field.name || 'field');
        },

        clearField: function (field) {
            if (!field) {
                return;
            }
            field.removeAttribute('aria-invalid');
            field.classList.remove('error');
            var id = this.errorId(field);
            var existing = document.getElementById(id);
            if (existing && existing.parentNode) {
                existing.parentNode.removeChild(existing);
            }
            var described = field.getAttribute('aria-describedby');
            if (described) {
                var parts = described.split(/\s+/).filter(function (part) {
                    return part && part !== id;
                });
                if (parts.length) {
                    field.setAttribute('aria-describedby', parts.join(' '));
                } else {
                    field.removeAttribute('aria-describedby');
                }
            }
        },

        showError: function (field, message) {
            this.clearField(field);
            field.setAttribute('aria-invalid', 'true');
            field.classList.add('error');
            var id = this.errorId(field);
            var el = document.createElement('div');
            el.className = 'emol-error-label';
            el.id = id;
            el.setAttribute('role', 'alert');
            el.textContent = message;
            if (field.parentNode) {
                field.parentNode.appendChild(el);
            }
            var described = field.getAttribute('aria-describedby');
            field.setAttribute('aria-describedby', described ? described + ' ' + id : id);
        },

        validateField: function (field) {
            var messages = this.messages();
            this.clearField(field);
            if (!this.isFilled(field)) {
                this.showError(field, messages.required);
                return false;
            }
            if (this.isEmailField(field) && !this.isValidEmail(field.value)) {
                this.showError(field, messages.email);
                return false;
            }
            return true;
        },

        validate: function (form) {
            var fields = this.fields(form);
            var firstInvalid = null;
            var valid = true;
            for (var i = 0; i < fields.length; i++) {
                if (!this.validateField(fields[i])) {
                    valid = false;
                    if (!firstInvalid) {
                        firstInvalid = fields[i];
                    }
                }
            }
            if (firstInvalid) {
                if (typeof firstInvalid.focus === 'function') {
                    firstInvalid.focus();
                }
                if (typeof firstInvalid.scrollIntoView === 'function') {
                    firstInvalid.scrollIntoView({ block: 'center' });
                }
            }
            return valid;
        },

        lockSubmit: function (form) {
            var buttons = form.querySelectorAll('.emol-form-submit, .emol-button-submit, [type="submit"]');
            var wait = this.messages().wait;
            for (var i = 0; i < buttons.length; i++) {
                var button = buttons[i];
                if (button.id === 'emol-apply-back-button') {
                    continue;
                }
                button.setAttribute('disabled', 'disabled');
                if (button.tagName === 'INPUT' && button.type !== 'checkbox' && button.type !== 'radio') {
                    if (!button.getAttribute('data-emol-label')) {
                        button.setAttribute('data-emol-label', button.value);
                    }
                    button.value = wait;
                }
            }
        },

        showWait: function () {
            var modal = document.getElementById('eazymatch-wait-modal');
            if (!modal || typeof jQuery === 'undefined' || !jQuery.fn || typeof jQuery.fn.dialog !== 'function') {
                return;
            }
            try {
                jQuery(modal).dialog({
                    show: { effect: 'blind', duration: 500 },
                    hide: { effect: 'blind', duration: 100 },
                    buttons: [],
                    closeOnEscape: false,
                    draggable: false,
                    modal: true,
                    width: 500
                });
            } catch (error) {
                console.error(error);
            }
        },

        bind: function (form) {
            var self = this;
            if (form.getAttribute('data-emol-form-bound') === '1') {
                return;
            }
            form.setAttribute('data-emol-form-bound', '1');
            form.setAttribute('novalidate', 'novalidate');

            form.addEventListener('submit', function (event) {
                event.preventDefault();
                if (!self.validate(form)) {
                    return;
                }
                self.lockSubmit(form);
                self.showWait();
                HTMLFormElement.prototype.submit.call(form);
            });

            form.addEventListener('input', function (event) {
                if (event.target && event.target.matches && event.target.matches('.required, [required]')) {
                    self.clearField(event.target);
                }
            });

            form.addEventListener('change', function (event) {
                if (event.target && event.target.matches && event.target.matches('.required, [required]')) {
                    self.clearField(event.target);
                }
            });
        },

        init: function () {
            if (typeof document === 'undefined') {
                return;
            }
            var forms = document.querySelectorAll('#emol-apply-form, #emol-react-form');
            for (var i = 0; i < forms.length; i++) {
                this.bind(forms[i]);
            }
            if (!this.bound) {
                document.addEventListener('click', function (event) {
                    var target = event.target;
                    var button = target && target.closest ? target.closest('.emol-form-submit') : null;
                    if (!button || button.disabled || button.type !== 'button') {
                        return;
                    }
                    var form = button.form || (button.closest ? button.closest('form') : null);
                    if (!form) {
                        return;
                    }
                    event.preventDefault();
                    if (typeof form.requestSubmit === 'function') {
                        form.requestSubmit();
                    } else {
                        var submitEvent = document.createEvent('Event');
                        submitEvent.initEvent('submit', true, true);
                        form.dispatchEvent(submitEvent);
                    }
                });
            }
            this.bound = true;
        }
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

function emolBindFront() {
    if (typeof jQuery !== 'undefined') {
        EazyWP.init(jQuery);
    }
    EazyWP.form.init();
}

if (typeof jQuery !== 'undefined') {
    jQuery(emolBindFront);
} else if (typeof document !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', emolBindFront);
    } else {
        emolBindFront();
    }
}

/**
 * legacy functionnames support
 */
var emolSearch = EazyWP.search.general,
    emolSearchJob = EazyWP.search.job;
emolSearchCv = EazyWP.search.cv;

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

function emolEnableCaptchaSubmit() {
    var btn = document.getElementById('emol-apply-submit-button');
    if (btn) {
        btn.removeAttribute('disabled');
    }
}

function emolRecaptchaCallback() {
    emolEnableCaptchaSubmit();
}

function emolBindAltcha() {
    var widgets = document.querySelectorAll('altcha-widget');
    for (var i = 0; i < widgets.length; i++) {
        widgets[i].addEventListener('statechange', function (ev) {
            if (ev.detail && ev.detail.state === 'verified') {
                emolEnableCaptchaSubmit();
            }
        });
    }
}

if (typeof document !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', emolBindAltcha);
    } else {
        emolBindAltcha();
    }
}

function emol_connect_linkedin(url, instance) {

    var url = 'https://signup-linkedin.eazymatch.cloud?refer=' + url + '&instance=' + instance;
    //var features = 'width=600;height=350;menubar=no;directories=no;location=no;modal=yes';
    //window.open(url, 'emol_connect_linkedin', features, false);
    window.location = url;
};

