(function($){

    var spinnerSelector = '[class^="app-ajax-"]';

    function response(options, data) {
        
        $(spinnerSelector).hide();
        
        if (data.html && !options.appNoInit) {
            for (var id in data.html) {
                $('#' + id).replaceWith(data.html[id]);
                $.appCommonInit($('#' + id));
            }
        }
        
        if (data.debug) {
            console.log(data.debug);
        }
        
        if (options.callback) {
            options.callback(data);
        }
        
        if (data.scripts) {
            setTimeout(function(){
                $.each(data.scripts, function(i, script){
                    $.globalEval(script);
                });
            }, 1);
        }
        
        if (data.alerts) {
            for (var type in data.alerts) {
                var text = '<div id="w8-success" class="alert-' + type + ' alert fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' + data.alerts[type] + '</div>';
                $('.alerts-container').append(text);
            }
        }
    };

    var methods = {
        request: function(url, options) {
            options = options || {};
            options.success = function(data, textStatus, jqxhr) {
                response(options, data);
            };
            options.error = function(jqxhr, textStatus, errorThrown) {
                console.error(errorThrown);
                response(options, {});
            };
            
            $(spinnerSelector).show();

            return $.ajax(url, options);
        },
    };

    $.appAjax = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else {
            $.error('appAjax: method ' +  method + ' does not exist');
        }
    };

})(jQuery);
