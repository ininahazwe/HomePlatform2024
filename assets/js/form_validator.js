$(function(){
    //console.log('Validation - Ready !');
    $('form[name="registration_form"]').validate({
        rules: {
            'registration_form[nom]': {
                required: true
            },
            'registration_form[prenom]': {
                required: true
            },
            'registration_form[email]': {
                required: true,
                email: true
            },
            userName: {
                required: true,
                minlength: 6
            },
            'registration_form[password][first]': {
                required: true,
                minlength: 8
            },
            'registration_form[password][second]': {
                required: true,
                minlength: 8,
                equalTo: '#registration_form_password_first'
            },
            'registration_form[agreeTerms]': {
                required: true,
            }
        },
        messages: {
            'registration_form[nom]': 'Insert your firstname',
            'registration_form[prenom]': 'Insert your lastname',
            'registration_form[email]': 'Insert a valid email',
            userName: {
                required: 'Please enter a User Name',
                minlength: 'Your user Name must consist of at least 6 characters'
            },
            'registration_form[password][first]': {
                required: 'Insert a password',
                minlength: 'Password must contains at least 8 letters or numbers'
            },
            'registration_form[password][second]': {
                required: 'Insert a password',
                minlength: 'Password must contains at least 8 letters or numbers',
                equalTo: 'Passwords must be the same'
            },
            'registration_form[agreeTerms]': 'You must check this box to validate'
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