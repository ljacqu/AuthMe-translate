$(document).ready(function () {
    'use strict';

    /**
     * Hides the "create new language" link and shows the required form elements.
     */
    var showNewLangInputs = function () {
        $('#new_lang_link').hide();
        $('#new_lang').find('input, span').fadeIn();
    };

    /**
     * Displays an error by replacing an element's inner HTML with red text.
     *
     * @param elem the element to modify
     * @param text the text to display
     */
    var addError = function (elem, text) {
        elem.html('<span style="color: #f00">' + text + '</span>');
    };

    /**
     * Click listener for the create language form, validating that the language code is valid
     * and that it does not exist yet.
     *
     * @returns true if the request should go through, false otherwise
     */
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

    // Listener
    var newLangLink = $('#new_lang_link');
    if (newLangLink.length > 0) {
        newLangLink.click(showNewLangInputs);
        $('#new_lang').find('input[type="submit"]').click(validateLangCode);
    }

});