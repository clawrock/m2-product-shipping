define([
    'jquery',
    'jquery/ui'
], function($) {
    'use strict';
    return function(widget) {
        $.widget('mage.priceBundle', widget, {
            _init: function() {
                if (this.options.bundleInitialized) {
                    return;
                }
                this._super();
                var form = this.element,
                options = $(this.options.productBundleSelector, form);
                options.data('bundle-initialized', 'true');
                this.options.bundleInitialized = true;
            },
        });

        return $.mage.priceBundle;
    };
});
