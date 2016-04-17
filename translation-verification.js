$(document).ready(function() {
    if ($('table.edit').length > 0) {
        $.each($('table.edit input[type="text"]'), function () {
            checkField($(this));
        });
    } else if ($('table.public').length > 0) {
        $.each($('table.public tr td:nth-child(2)'), function () {
            checkCell($(this));
        });
    }
});


function checkField(elem) {
    elem = $(elem);
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
}

function checkCell(elem) {
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
}

function getTagsOrFalse(elem) {
    var tagText = elem.data('tags');
    if (typeof tagText === 'undefined' || tagText === '') {
        return false;
    }
    return tagText.split(',');
}

function hideValidFields() {
    $.each($('table.edit input[type="text"]'), function () {
       if (!$(this).hasClass('empty') && !$(this).hasClass('missing')) {
           $(this).parents('tr').prev('tr').hide();
           $(this).parents('tr').hide();
       }
    });
    $('#hideValidFieldsLink').hide();
    $('#showAllFieldsLink').show();
}

function showAllFields() {
    $('table.edit tr').show();
    $('#showAllFieldsLink').hide();
    $('#hideValidFieldsLink').show();
}
