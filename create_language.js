$(document).ready(function () {

    var showNewLangInputs = function () {
        $('#new_lang_link').hide();
        $('#new_lang').find('input, span').fadeIn();
    };

    var addError = function (elem, text) {
        elem.html('<span style="color: #f00">' + text + '</span>');
    };

    var validateLangCode = function () {
        var newLangError = $('#new_lang_error');
        newLangError.text('');
        var code = $('#new_lang').find('input[type="text"]').val();
        if (code.match(/^[a-z]{2,4}$/)) {
            var doesCodeExist = $('#create').find('option[value="' + code + '"]').length > 0;
            if (doesCodeExist) {
                addError(newLangError, 'Language code already exists!');
            } else {
                return true;
            }
        } else {
            addError(newLangError, 'Invalid language code! Use 2-4 lowercase a-z characters');
        }
        return false;
    };

    var newLangLink = $('#new_lang_link');
    if (newLangLink.length > 0) {
        newLangLink.click(showNewLangInputs);
        $('#new_lang').find('input[type="submit"]').click(validateLangCode);
    }

});