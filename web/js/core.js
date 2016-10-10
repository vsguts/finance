(function($){

window.yii.app = {}; // Common namespace

var form_group_class = 'form-group';

var select2 = {
    employees: {
        allowClear: true,
        minimumInputLength: 2,
        ajax: {
            url: '', // data-m-url attr
            dataType: 'json',
            cache: true,
            data: function(params){
                var data = {
                    q: params
                };
                if (this.data('mOrganizationsOnly')) {
                    data.organizations = true;
                }

                return data;
            },
            results: function(data){
                return {
                    results: data.employees
                };
            },
            width: 'resolve',
        },
        initSelection: function(element, callback) {
            callback({
                text: element.data('initValueText'),
            });
        },
    },
};

function matchClass(elem, str) {
    var jelm = $(elem),
        cls = jelm.attr('class');
    if (typeof(cls) !== 'object' && cls) {
        var result = cls.match(str);
        if (result) {
            return result[0];
        }
    }
};

$.extend({

    appCommonInit: function(context) {
        context = $(context || document);

        $("[data-toggle='tooltip']", context).tooltip();

        $('.app-toggle-save', context).each(function(){
            var elm = $(this),
                target_class = elm.data('targetClass'),
                status = $.cookie('app-toggle-' + target_class);
            
            elm.appToggle(!!status);
        });

        $('.app-tabs-save', context).each(function(){
            var elm = $(this),
                selected_href = $.cookie('app-tabs-' + elm.attr('id'));
            
            var href = elm.find('[href="' + selected_href + '"]');
            if (href.is(':visible')) {
                href.click();
            }
        });

        $('.app-dtoggle', context).each(function(){
            $(this).appDToggle();
        });

        $('.app-select2', context).each(function(){
            $(this).appSelect2();
        });

        $('.app-account', context).each(function(){
            $(this).appAccountSelect();
        });

        $('.app-classification', context).each(function(){
            $(this).appClassificationSelect();
        });

        $('.app-checkboxes-group-allow', context).each(function(){
            $(this).appCheckboxesGroupAllow();
        });

        var elms = $('.app-float-thead', context);
        if (elms.length) {
            elms.floatThead({
                position: 'fixed',
                top: 51,
                zIndex: 500,
            });
        }
    },

    uniqid: function uniqid (prefix, moreEntropy) {
        if (typeof prefix === 'undefined') {
            prefix = '';
        }

        var _formatSeed = function(seed, reqWidth) {
            seed = parseInt(seed, 10).toString(16);
            if (reqWidth < seed.length) {
                return seed.slice(seed.length - reqWidth);
            }
            if (reqWidth > seed.length) {
                return Array(1 + (reqWidth - seed.length)).join('0') + seed;
            }
            return seed;
        }

        var $global = (typeof window !== 'undefined' ? window : GLOBAL);
        $global.$locutus = $global.$locutus || {};
        var $locutus = $global.$locutus;
        $locutus.php = $locutus.php || {};

        if (!$locutus.php.uniqidSeed) {
            $locutus.php.uniqidSeed = Math.floor(Math.random() * 0x75bcd15);
        }
        $locutus.php.uniqidSeed ++;

        var retId = prefix;
        retId += _formatSeed(parseInt(new Date().getTime() / 1000, 10), 8);
        retId += _formatSeed($locutus.php.uniqidSeed, 5);
        if (moreEntropy) {
            retId += (Math.random() * 10).toFixed(8).toString();
        }

        return retId;
    },

    appReflowFloatThead: function() {
        var elms = $('.app-float-thead:not(".floatThead-table")');
        if (elms.length) {
            elms.floatThead('reflow');
        }
    },

    appCalc: function(string) {
        var result;
        try {
            result = eval(string.replace(/[^-()\d/*+.]/g, ''));
        } catch(e) {
            result = string;
        }
        return result || '';
    },

});

$.fn.extend({

    appHide: function() {
        this.hide();
        this.find('input, textarea, select').attr('disabled', 'disabled');
    },

    appShow: function() {
        this.show();
        this.find('input, textarea, select').removeAttr('disabled');
    },

    serializeObject: function()
    {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (typeof(o[this.name]) !== 'undefined' && this.name.indexOf('[]') > 0) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    },

    appToggle: function(display) {
        var target_class = this.data('targetClass'),
            toggle_class = this.data('toggleClass'),
            target = $('.' + target_class);

        target.toggle(display);
        var status = target.is(':visible');
        $('.' + target_class + '-on').toggle(status);
        $('.' + target_class + '-off').toggle(!status);

        if (toggle_class) {
            this.toggleClass(toggle_class, display);
        }
        if (this.hasClass('app-toggle-save') && typeof(display) == 'undefined') {
            if (target.is(':visible')) {
                $.cookie('app-toggle-' + target_class, 1);
            } else {
                $.removeCookie('app-toggle-' + target_class);
            }
        }

        $.appReflowFloatThead();
    },

    appToggleComb: function() {
        var prefix = this.data('targetPrefix'),
            status = !this.data('displayStatus'),
            items = $('.app-toggle[data-target-class^="' + prefix + '"]');
        
        items.each(function(){
            $(this).appToggle(status);
        });

        $('.' + prefix + '-on').toggle(status);
        $('.' + prefix + '-off').toggle(!status);

        this.data('displayStatus', status);
    },

    appDToggle: function() {
        var name = matchClass(this, /app-dtoggle-([-\w]+)?/gi).replace('app-dtoggle-', ''),
            value = this.attr('type') == 'checkbox' ? (this.is(':checked') ? 'on' : 'off') : this.val(),
            sel_dep_all = '[class^="app-dtoggle-' + name + '-"',
            sel_dep = '.app-dtoggle-' + name + '-' + value;

        this.find('option').each(function(i, elm){
            var val = $(elm).val();
            if (val != value) {
                sel_dep = sel_dep + ', .app-dtoggle-' + name + '-n' + val;
            }
        });
        
        if (!value && !sel_dep.length) {
            $(sel_dep_all).appShow();
        } else {
            $(sel_dep_all).appHide();
            $(sel_dep).appShow();
        }
    },

    appSelect2: function() {
        var params = {
            width: 'resolve',
        };
        if (this.hasClass('app-select2-employee')) {
            params = select2.employees;
            params.ajax.url = this.data('mUrl');
        }
        this.select2(params);
        this.on('change', function(e){
            $.appReflowFloatThead();
        });
    },

    appClone: function() {
        var item = this.clone().insertBefore(this);
        item.find('[id]').each(function(){
            var elm = $(this),
                id = elm.attr('id');
            elm.attr('id', id + 'z');
        });

        // Hash
        var html = item.html().replace(/clonehash[a-z0-9]*/g, 'clonehash' + $.uniqid());
        item.html(html);

        // Select 2 fixes
        item.find('.select2-container').remove();
        item.find('.app-select2').show();
        
        $.appCommonInit(item);
        return item;
    },

    appSelector: function(){
        var url = this.data('appUrl'),
            delimiter = url.indexOf('?') == -1 ? '?' : '&',
            year = this.val();
        window.location = url + delimiter + 'year=' + year;
    },

    appAccountSelect: function() {
        var form = this.parents('form');
        var cur_dependencies = form.find('.app-accounts-currency');
        if (cur_dependencies.length) {
            var from_val = form.find('#' + cur_dependencies.data('accountFrom')).val(),
                to_val = form.find('#' + cur_dependencies.data('accountTo')).val(),
                from_cur = yii.app.account_currencies[from_val],
                to_cur = yii.app.account_currencies[to_val];
            cur_dependencies.appHide();
            if (from_cur != to_cur) {
                cur_dependencies.appShow();
            }
        }
    },

    appClassificationSelect: function() {
        var form = this.parents('form'),
            classification_id = this.val(),
            classification = classification_id ? yii.app.classifications[classification_id] : {};

        var inflow = form.find('.app-classification-inflow');
        var outflow = form.find('.app-classification-outflow');

        inflow.appHide();
        outflow.appHide();

        if (classification.id) {
            if (classification.type == 'inflow') {
                inflow.appShow();
            } else if (classification.type == 'outflow') {
                outflow.appShow();
            } else if (classification.type == 'transfer' || classification.type == 'conversion') {
                inflow.appShow();
                outflow.appShow();
            }
        }

    },

    appCheckboxesGroupAllow: function()
    {
        var all_checkbox = this.find('input[type="checkbox"][value="0"]'),
            selected_checkboxes = this.find('input[type="checkbox"][value!="0"]:checked');
        if (selected_checkboxes.length) {
            all_checkbox.prop('checked', false).prop('disabled', false);
        } else {
            all_checkbox.prop('checked', true).prop('disabled', true);
        }
    },

    appSerializeForm: function()
    {
        var form = $('#' + this.data('formId')),
            target = $('#' + this.data('targetId'));

        if (form.length && target.length) {
            target.val(form.serialize());
        }
    },

});

})(jQuery);
