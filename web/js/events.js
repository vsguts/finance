(function($){

var modal_options = {
    backdrop: true,
};

// Document ready
$(document).on('ready', function() {
    $.appCommonInit();
});

// Events
$(document).on('click', function(e) {
    var jelm = $(e.target);

    var elm = jelm.closest('.app-toggle');
    if (elm.length) {
        elm.appToggle();
    }

    var elm = jelm.closest('.app-toggle-comb');
    if (elm.length) {
        elm.appToggleComb();
    }

    var elm = jelm.parents('.app-tabs-save');
    if (elm.length) {
        var selected = elm.find('.active a');
        $.cookie('app-tabs-' + elm.attr('id'), selected.attr('href'));
    }

    var elm = jelm.closest('.app-checkboxes-group-allow');
    if (elm.length) {
        elm.appCheckboxesGroupAllow();
    }

    var elm = jelm.closest('.app-serialize-form');
    if (elm.length) {
        elm.appSerializeForm();
    }

    if (jelm.hasClass('app-ajax')) {
        $.appAjax('request', jelm.attr('href'), {
            method: jelm.data('appMethod') || 'get',
            data: {
                target_id: jelm.data('appTargetId'),
            },
        });
        return false;
    }

    var modal_elm = jelm.closest('.app-modal');
    if (modal_elm.length) {
        var target_id = modal_elm.data('targetId'),
            target = $('#' + target_id);
        
        if (target.length && !modal_elm.hasClass('app-modal-force')) {
            target.modal(modal_options);
        } else {
            if (target.length) {
                target.remove();
            }
            var href = modal_elm.attr('href');
            if (href.length) {
                $.appAjax('request', href, {
                    data: {
                        target_id: target_id,
                    },
                    callback: function(data){
                        if (data.html && data.html[target_id]) {
                            $(data.html[target_id]).modal(modal_options);
                            $.appCommonInit($('#' + target_id));
                        }
                    },
                });
            }
        }
        return false;
    }

    // Items
    var elm = jelm.closest('.app-item-new');
    if (elm.length) {
        var item = elm.closest('.app-item'),
            container = item.parent();
        container.find('.app-item.app-item-template:last').appClone().removeClass('app-item-template');
    }

    var elm = jelm.closest('.app-item-remove');
    if (elm.length) {
        var item = elm.closest('.app-item');
        if (item.parent().find('.app-item:not(.app-item-template)').length > 1) {
            item.remove();
        }
    }

    // Grid
    var elm = jelm.closest('.app-grid-toggle');
    if (elm.length) {
        elm.closest('table').find('tr[data-key="' + elm.closest('tr').data('key') + '-extra"]').toggle();
        var icon = elm.find('.glyphicon');
        if (icon.length) {
            icon.toggleClass('glyphicon-menu-down').toggleClass('glyphicon-menu-up');
        }
        return false;
    }

    // Bootstrap fixes

    if (jelm.closest('[data-dismiss="alert"]').length) {
        setTimeout(function() {
            $.appReflowFloatThead();
        }, 150);
    }

});

$(document).on('change', function(e) {
    var jelm = $(e.target);

    if (jelm.hasClass('app-dtoggle')) {
        jelm.appDToggle();
    }

    if (jelm.hasClass('app-selector')) {
        jelm.appSelector();
    }

    if (jelm.hasClass('app-account')) {
        jelm.appAccountSelect();
    }

    if (jelm.hasClass('app-classification')) {
        jelm.appClassificationSelect();
    }
});

$(document).on('submit', function(e) {
    var form = $(e.target);
    if (form.hasClass('app-ajax')) {
        $.appAjax('request', form.attr('action'), {
            type: form.attr('method') || "post",
            data: form.serialize(),
            appNoInit: true,
            callback: function(data) {
                // Close modal if need
                if (form.data('appModal')) {
                    $('#' + form.data('appModal')).modal('hide');
                }

                // Open another modal if need
                if (form.hasClass('app-modal')) {
                    var target_id = form.find('input[name="target_id"]').val(),
                        target = $('#' + target_id);
                    
                    if (target.length) {
                        target.remove();
                    }
                    if (data.html && data.html[target_id]) {
                        $(data.html[target_id]).modal(modal_options);
                        $.appCommonInit($('#' + target_id));
                    }
                }
            },
        });
        return false;
    }
});


/**
 * Rewrite Yii events
 */
$(document).on('click.app', yii.clickableSelector, function(e) {
    var jelm = $(e.target);

    if (jelm.data('appProcessItems')) {
        var url = jelm.data('url') || jelm.attr('href');
        jelm.data('url', url);
        var obj_name = jelm.data('appProcessItems'),
            url_params = {},
            keys = $('.grid-view').yiiGridView('getSelectedRows');
        
        if (!keys.length) {
            alert(yii.app.langs['No items selected']);
            e.stopImmediatePropagation();
            return false;
        }
        
        url_params[obj_name] = keys;
        var delimiter = url.indexOf('?') == -1 ? '?' : '&';
        jelm.attr('href', url + delimiter + decodeURIComponent($.param(url_params)));
        return true;
    }
});

// Yii events
$(document).on('beforeValidateAttribute', function(event, obj, msg, deferreds){
    var jeml = $(obj.container);
    if (jeml.find('input,select').attr('disabled')) { // skip validation
        delete obj['validate'];
        return true;
    }
});

})(jQuery);
