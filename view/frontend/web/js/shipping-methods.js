define([
    'jquery',
    'mage/template',
    'mage/translate',
], function ($, mageTemplate) {
    'use strict';

    var globalOptions = {
        productBundleSelector: 'input.bundle.option, select.bundle.option, textarea.bundle.option',
        bundleOptionName: 'bundle_option',
        configurableOptionName: 'super_attribute',
        configurableSwatchSelector: '.swatch-attribute',
        configurableSwatchOption: '.swatch-option',
        configurableSelector: '.super-attribute-select',
        qtyFieldSelector: 'input.qty',
        bundleOptionQty: '#bundle-option-id-qty-input',
        defaultQtySelector: '#qty',
    };

    $.widget('clawrock.shippingMethods', {
        _create: function() {
            $.extend(this.options, globalOptions);
            var self = this;
            $(this.options.productBundleSelector).on('change', function(e) {
                if (!$(e.currentTarget).data('bundle-initialized')) {
                    return;
                }
                self.abortCurrentRequest();
                self.fetch(self.prepareBundleRequest());
            });
            $(document).ready(function(){
                self.getShippingMethods();
            });
            $(document).on('click', this.options.configurableSwatchOption, function(e) {
                self.abortCurrentRequest();
                self.fetch(self.prepareConfigurableRequest());
            });

            $(this.options.configurableSelector).on('change', function(e) {
                self.abortCurrentRequest();
                self.fetch(self.prepareConfigurableRequest());
            });

            $(this.options.qtyFieldSelector+', '+this.options.defaultQtySelector).on('change', function(e) {
                self.abortCurrentRequest();
                self.getShippingMethods();
            });
        },

        abortCurrentRequest: function() {
            if (this.currentRequest) {
                this.currentRequest.abort();
            }
        },

        getShippingMethods: function() {
            if (this.options.type == 'bundle') {
                this.fetch(this.prepareBundleRequest());
                return;
            }
            if (this.options.type == 'configurable') {
                this.fetch(this.prepareConfigurableRequest());
                return;
            }
            this.fetch(this.defaultRequestData({}));
        },

        prepareConfigurableRequest: function() {
            var request = {};
            var superAttribute = {};
            $(this.options.configurableSwatchSelector).each(function(i, elm) {
                var el = $(elm);
                superAttribute[el.attr('attribute-id')] = el.attr('option-selected');
            });
            var optionId = $(this.options.configurableSelector).attr('name').match(new RegExp(this.options.configurableOptionName+'\\[(.*?)\\]'))[1];
            if (optionId) {
                superAttribute[optionId] = $(this.options.configurableSelector).val();
            }
            request['super_attribute'] = superAttribute;
            return this.defaultRequestData(request);
        },

        prepareBundleRequest: function() {
            var self = this;
            var request = {};
            var selectedOptions = {};
            var selectedOptionsQty = {};
            $(this.options.productBundleSelector).each(function(i, elm) {
                var selected = false;
                var optionId = elm.name.match(new RegExp(self.options.bundleOptionName+'\\[(.*?)\\]'))[1];
                if ($(elm).is("select")) {
                    selected = true;
                    selectedOptions[optionId] = $(elm).val();
                }
                if (elm.checked) {
                    selected = true;
                    if (selectedOptions[optionId]) {
                        selectedOptions[optionId].push(elm.value);
                    } else {
                        selectedOptions[optionId] = [elm.value];
                    }
                }
                if (selected == true) {
                    var optionQty = $(self.options.bundleOptionQty.replace("id", optionId)).val();
                    if (!optionQty || optionQty == 0) {
                        optionQty = 1;
                    }
                    selectedOptionsQty[optionId] = optionQty;
                }
            });
            request['bundle_option'] = selectedOptions;
            request['bundle_option_qty'] = selectedOptionsQty;
            return this.defaultRequestData(request);
        },

        defaultRequestData: function(request) {
            request['sku'] = this.options.sku;
            request['qty'] = $(this.options.defaultQtySelector).val();
            return {options: request};
        },

        fetch: function(data) {
            this.currentRequest = $.ajax({
                url: this.options.url,
                method: 'POST',
                data: JSON.stringify(data),
                dataType: 'json',
                contentType: 'application/json; charset=utf-8',
                showLoader: true,
                loaderContext: '.clawrock-productshipping',
                success: this.renderShippingMethods.bind(this)
            });
        },

        renderShippingMethods: function(response) {
            this.currentRequest = null;
            var template = mageTemplate('#shipping-methods-template'),
                $shippingMethods = $('#clawrock-shipping-methods ul');

            $shippingMethods.remove();
            $shippingMethods = $('<ul></ul>');
            $('#clawrock-shipping-methods').append($shippingMethods);
            if (!response.length) {
                $shippingMethods.append($('<li><p>' + this.options.message + '</p></li>'));
                return;
            }
            response.forEach(function(el) {
                $shippingMethods.append(template({
                    data: {
                        title: el.title,
                        price: el.price
                    }
                }));
            });
        }
    });

    return $.clawrock.shippingMethods;
});
