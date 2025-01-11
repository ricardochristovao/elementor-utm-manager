(function($) {
    'use strict';

    $(window).on('elementor/frontend/init', function() {
        var UTMManager = {
            init: function() {
                this.bindEvents();
            },

            bindEvents: function() {
                elementor.hooks.addAction('panel/open_editor/widget/button', function(panel, model, view) {
                    // Futura implementação de preview em tempo real se necessário
                });
            }
        };

        UTMManager.init();
    });
})(jQuery);