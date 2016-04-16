var on_select_change = function () {
    var lang = $('#language_selector').find('select').val();
    $.getJSON('lang_retrieval.php', {lang: lang}, function (data) {
        console.log(data);
        for (var key in data) {
            if (data.hasOwnProperty(key) && data[key] !== '') {
                const cellText = $('td.language_template[data-key="' + key + '"]').find('span');
                const _key = key;
                cellText.fadeOut(200, function () {
                    cellText.text(data[_key]);
                    cellText.fadeIn();
                });
            }
        }
    });
};

$(document).ready(function () {
    if ($('#language_selector').length === 0) {
        return;
    }

    $.getJSON('lang_retrieval.php', {list: 1}, function (data) {
        populate_language_selector(data.codes);
        $('#language_selector').find('select').change(on_select_change);
    });

});

function populate_language_selector(codes) {
    var text = 'Messages on left from language: ' +
            '<select name="lang">';
    for (var i = 0; i < codes.length; ++i) {
        var tagEnd = codes[i] === 'en' ? ' selected="selected">' : '>';
        text += '<option' + tagEnd + codes[i] + '</option>';
    }
    $('#language_selector').html(text + '</select>');
}