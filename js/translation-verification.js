$(document).ready(function() {
    'use strict';

    /**
     * Retrieves the tags of a message from the element's data-tags attribute, or returns
     * false if empty or absent.
     *
     * @param elem the element to retrieve tag information from
     * @returns {*} string[] of tags, or false
     */
    var getTagsOrFalse = function (elem) {
        var tagText = elem.data('tags');
        if (typeof tagText === 'undefined' || tagText === '') {
            return false;
        }
        return tagText.split(',');
    };

    /**
     * Checks an input field and colors it accordingly. For missing tags, adds a message with
     * the missing tags to the span.error following {@code elem}.
     *
     * @param elem the input element to verify
     */
    var checkField = function (elem) {
        elem = elem instanceof jQuery ? elem : $(elem);
        elem.removeClass('empty');
        elem.removeClass('missing');
        var errorMessage = elem.next('span.error');
        errorMessage.text('');

        var elemText = elem.val().trim();
        var tags;
        if (elemText === '') {
            elem.addClass('empty');
        } else if ((tags = getTagsOrFalse(elem)) !== false) {
            var missingTags = [];
            for (var i = 0; i < tags.length; ++i) {
                if (elemText.indexOf(tags[i]) === -1) {
                    missingTags.push(tags[i]);
                }
            }
            if (missingTags.length > 0) {
                elem.addClass('missing');
                elem.next('span.error').html('<br />Missing tags ' + missingTags.join(', '));
            }
        }
    };

    /**
     * Checks the message in a table cell (&lt;td>) and colors it accordingly.
     *
     * @param elem the table cell with a message to verify
     */
    var checkCell = function (elem) {
        var elemText = elem.text().trim();
        var tags;
        if (elemText === '') {
            elem.addClass('empty');
        } else if ((tags = getTagsOrFalse(elem)) !== false) {
            for (var i = 0; i < tags.length; ++i) {
                if (elemText.indexOf(tags[i]) === -1) {
                    elem.addClass('missing');
                    return;
                }
            }
        }
    };

    /**
     * For edit page: hides all rows that do not have a validation error (row with the message key + row with
     * the associated input and text comparison).
     */
    var hideValidFields = function () {
        $.each($('table.edit input[type="text"]'), function () {
            if (!$(this).hasClass('empty') && !$(this).hasClass('missing')) {
                $(this).parents('tr').prev('tr').hide();
                $(this).parents('tr').hide();
            }
        });
        $('#hideValidFieldsLink').hide();
        $('#showAllFieldsLink').show();
    };

    /**
     * For edit page: shows all rows.
     */
    var showAllFields = function () {
        $('table.edit tr').show();
        $('#showAllFieldsLink').hide();
        $('#hideValidFieldsLink').show();
    };

    // Edit page: Listeners & initial validation
    if ($('table.edit').length > 0) {
        $.each($('table.edit input.verifiable'), function () {
            checkField($(this));
            $(this).keyup(function () {
                checkField($(this));
            });
        });
        $('#hideValidFieldsLink').click(hideValidFields);
        $('#showAllFieldsLink').click(showAllFields);
    }

    // Public page: validation on load
    else if ($('table.public').length > 0) {
        $.each($('table.public td.verifiable'), function () {
            checkCell($(this));
        });
    }
});
