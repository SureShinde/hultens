/* global require */
require(['jquery'], function($) {
    $(document).ready(function() {
        // When creating packages use kilograms instead of pounds
        if ($('#package_template select.options-units-weight').length) {
            if (!$('#package_template select.options-units-weight option[value="KILOGRAM"]').length) {
                $('#package_template select.options-units-weight').append('<option value="KILOGRAM">kg</option>');
            }
            $('#package_template select.options-units-weight option[value="KILOGRAM"]').attr('selected', 'selected');
        }

        // When creating packages use centimeters instead of inches
        if ($('#package_template select.options-units-dimensions').length) {
            if (!$('#package_template select.options-units-dimensions option[value="METER"]').length) {
                $('#package_template select.options-units-dimensions').append('<option value="METER">m</option>');
            }
            $('#package_template select.options-units-dimensions option[value="METER"]').attr('selected', 'selected');
        }
    });
});
