jQuery(document).ready(function ($) {
    $('#validate-schema-btn').on('click', function () {
        var url = $('#schema-test-url').val();
        if (!url) {
            alert('Please enter a valid URL.');
            return;
        }

        $('#schema-validation-results').hide();
        $('#schema-response').html('<strong>Validating schema...</strong>');

        $.post(ajaxurl, {
            action: 'validate_schema',
            nonce: schemaValidatorData.nonce,
            url: url
        }, function (response) {
            if (response.success) {
                $('#schema-response').html(JSON.stringify(response.data.response, null, 2));
            } else {
                $('#schema-response').html('<strong>Error:</strong> ' + response.data.message);
            }
            $('#schema-validation-results').show();
        });
    });
});
