$(function(){
    $('form[name="reset_password_request_form"]').validate({
        rules: {
            'reset_password_request_form[email]': {
                required: true
            },
        },
        messages: {
            'reset_password_request_form[email]': {
                required: 'Enter your email address',
                minlength: 'Enter a valid email address'
            },
        },
        errorElement: 'em',
        errorPlacement: function (error, element) {
            error.addClass('registration-invalid-feedback');

            if (element.prop('type') === 'checkbox') {
                error.insertAfter( element.next('label'));
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid').removeClass('is-valid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).addClass('is-valid').removeClass('is-invalid');
        }
    });
});