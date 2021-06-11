/* global require */

/** @type {Object} */
var unifaunAddonsDynamicFields = {};

/** @var {Object} */
var unifaunAddonsOptions = {};

/** @var {Object} */
var unifaunAddonsValues = {};

/** @var {Boolean} */
var unifaunDebugFlag = false;

require(['jquery'], function () {

    /* global jQuery */

    /**
     * @param {Mixed} message
     */
    window.unifaunDebug = function (message) {
    if (typeof(console) !== 'undefined' &&
        typeof(console.log) !== 'undefined' &&
        unifaunDebugFlag
       ) {
        console.log(message);
    }
    };

    jQuery(document).ready(function ($) {

        // Load JSON data from server-side
        if ($('#unifaun-addons-dynamic-fields').length) {
        unifaunAddonsDynamicFields = JSON.parse($('#unifaun-addons-dynamic-fields').html());
        window.unifaunDebug(unifaunAddonsDynamicFields);
        $('#unifaun-addons-dynamic-fields').remove();
        }
        if ($('#unifaun-addons-options').length) {
        unifaunAddonsOptions = JSON.parse($('#unifaun-addons-options').html());
        window.unifaunDebug(unifaunAddonsOptions);
        $('#unifaun-addons-options').remove();
        }
        if ($('#unifaun-addons-values').length) {
        unifaunAddonsValues = JSON.parse($('#unifaun-addons-values').html());
        window.unifaunDebug(unifaunAddonsValues);
        $('#unifaun-addons-values').remove();
        }

        window.msunifaun_refresh_shippingmethod_options = function () {
            $('.shippingmethod_options tbody tr').each(function (i, obj) {
                $('input, select', obj).each(function (j, subobj) {
                    var name = $(subobj).attr('name');
                    var newName = name.replace(/\d+/, i);
                    $(subobj).attr('name', newName);
                    window.unifaunDebug(name + ' to ' + newName);
                });
            });
        };

        $('.shippingmethod_options table').on('click', 'button', function (event) {
            event.preventDefault();
            var rows = $('.shippingmethod_options tbody tr').length;
            if (rows > 1) {
                $(this).parent().parent().remove();
                window.msunifaun_refresh_shippingmethod_options();
            }
        });

        $('.shippingmethod_options .buttons button').click(function (event) {
            event.preventDefault();
            var newRow = $('.shippingmethod_options tbody tr:last-child').clone();
            $('input', newRow).val('');
            $('.shippingmethod_options tbody').append(newRow);
            window.msunifaun_refresh_shippingmethod_options();
        });

        $('#msunifaun_shippingmethod_form #method').change(function (event) {
            event.preventDefault();
            var service = $(this).val();
        window.unifaunDebug(service);
        if (typeof(unifaunAddonsOptions[service]) !== 'undefined') {
            $('#addons-fieldset .shippingmethod_addons').remove();
            var html = '', code, specification;
            for (code in unifaunAddonsOptions[service]) {
            if (unifaunAddonsOptions[service].hasOwnProperty(code)) {
                specification = unifaunAddonsOptions[service][code];

                // Add-on field options
                        html += '<div class="shippingmethod_addons admin__field field field-options service-' + service + '"><label title="' + code + '" class="label admin__field-label" for="' + service + '-' + code + '">' + specification.label + '</label><div class="admin__field-control control"><input type="checkbox" id="' + service + '-' + code + '" name="addons[' + service + '][' + code + ']" value="1"' + (typeof(unifaunAddonsValues[code]) !== 'undefined' ? ' checked="checked"' : '') + ' />';

                if (typeof(specification.fields) !== 'undefined') {
                            html += '<fieldset style="display: ' + (typeof(unifaunAddonsValues[code]) !== 'undefined' ? 'block' : 'none') + ';"><legend>Specification</legend>';
                var element, elementSpecification;
                            for (element in specification.fields) {
                            if (specification.fields.hasOwnProperty(element)) {
                    elementSpecification = specification.fields[element];
                    html += '<div' + (typeof(elementSpecification.required) !== 'undefined' ? ' class="required"' : '') + '>';

                    // Dynamic values for field
                    if (unifaunAddonsDynamicFields) {
                                        html += '<select class="dynamic-value admin__control-select" name="addons_specifications[' + service + '][' + code + '][' + element + '_dynamic]"><option value="">Make dynamic..</option>';
                        var name, label;
                                        for (name in unifaunAddonsDynamicFields) {
                                        if (unifaunAddonsDynamicFields.hasOwnProperty(name)) {
                            label = unifaunAddonsDynamicFields[name];
                            html += '<option value="' + name + '"' + (typeof(unifaunAddonsValues[code]) !== 'undefined' && typeof(unifaunAddonsValues[code][element + '_dynamic']) && unifaunAddonsValues[code][element + '_dynamic'] == name ? ' selected="selected"' : '') + '>' + label + '</option>';
                        }
                                        }
                                        html += '</select>';
                    }

                    html += '<div class="wrapper element-' + elementSpecification.type + '">';

                    if (elementSpecification.type === 'text') {
                                        html += '<label title="' + element + '" class="label admin__field-label"><span>' + elementSpecification.label + '</span><br /><input type="text" class="input-text admin__control-text" name="addons_specifications[' + service + '][' + code + '][' + element + ']" id="' + service + '-' + code + '-' + element + '" value="' + (typeof(unifaunAddonsValues[code]) !== 'undefined' && typeof(unifaunAddonsValues[code][element]) !== 'undefined' ? unifaunAddonsValues[code][element] : '') + '" /></label>';
                    } else if (elementSpecification.type === 'textarea') {
                                        html += '<label title="' + element + '" class="label admin__field-label"><span>' + elementSpecification.label + '</span><br /><textarea name="addons_specifications[' + service + '][' + code + '][' + element + ']" class="input-textarea admin__control-textarea" id="' + service + '-' + code + '-' + element + '" rows="3">' + (typeof(unifaunAddonsValues[code]) !== 'undefined' && typeof(unifaunAddonsValues[code][element]) !== 'undefined' ? unifaunAddonsValues[code][element] : '') + '</textarea></label>';
                    } else if (elementSpecification.type === 'select') {
                                        var optionsHtml = '', subKey, option, selected;
                        if (typeof(elementSpecification.options) !== 'undefined') {
                        for (subKey in elementSpecification.options) {
                        if (elementSpecification.options.hasOwnProperty(subKey)) {
                            option = elementSpecification.options[subKey];
                            selected = (typeof(unifaunAddonsValues[code]) !== 'undefined' &&
                                    typeof(unifaunAddonsValues[code][element]) !== 'undefined' &&
                                    unifaunAddonsValues[code][element] === option);
                            optionsHtml += '<option' + (selected ? ' selected="selected"' : '') + '>' + option + '</option>';
                            }
                        }

                        html += '<label title="' + element + '" class="label admin__field-label"><span>' + elementSpecification.label + '</span><br /><select name="addons_specifications[' + service + '][' + code + '][' + element + ']" class="input-select admin__control-select" id="' + service + '-' + code + '-' + element + '">' + optionsHtml + '</select></label>';
                        }
                    } else if (elementSpecification.type == 'checkbox') {
                                        html += '<label title="' + element + '" class="label admin__field-label"><input type="checkbox" name="addons_specifications[' + service + '][' + code + '][' + element + ']" id="' + service + '-' + code + '-' + element + '" value="' + elementSpecification.value + '"' + (typeof(unifaunAddonsValues[code]) !== 'undefined' && typeof(unifaunAddonsValues[code][element]) !== 'undefined' ? ' checked="checked"' : '') + ' /><span>' + elementSpecification.label + '</span></label>';
                    }

                    if (typeof(elementSpecification.description) !== 'undefined') {
                                        html += '<small>' + elementSpecification.description + '</small>';
                    }

                    html += '</div></div>';
                                }
                }
                            html += '</fieldset>';
                }

                html += '</div></div>';
            }
            }
            $('#addons-fieldset').append(html);
            $('#addons-fieldset').css('display', 'block');
        } else {
            $('#addons-fieldset').css('display', 'none');
        }
        });
        if ($('#msunifaun_shippingmethod_form #method').val()) {
            $('#msunifaun_shippingmethod_form #method').trigger('change');
        }

        $('#msunifaun_shippingmethod_form #addons-fieldset').on('change', 'input[type="checkbox"]', function (event) {
            if ($(this).prop('checked')) {
                $('fieldset', $(this).parent()).show();
            } else {
                $('fieldset', $(this).parent()).hide();
            }
        });

    });

});
