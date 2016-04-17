$(document).ready(function () {
    /**
     * Loads a language's messages from the server.
     *
     * @param lang the language to retrieve
     * @param callback callback to execute with the loaded data
     */
    var loadMessages = function (lang, callback) {
        $.getJSON('lang_retrieval.php', {lang: lang}, callback);
    };

    /**
     * Loads the messages in the given language and updates the left-hand cells with them.
     *
     * @param lang the language to retrieve
     */
    var updateMessagesInCell = function (lang) {
        loadMessages(lang, function (data) {
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

    /**
     * Marks all cells that are different from the server messages and updates the left-hand cells.
     *
     * @param lang the language code of the translation
     */
    var markChangedMessages = function (lang) {
        $('td.changed').removeClass('changed');
        loadMessages(lang, function (data) {
            $.each($('table.edit td.language_template'), function () {
                var key = $(this).data('key');
                var message = data.hasOwnProperty(key) ? data[key] : '';
                var rightCell = $(this).siblings('td');
                if (message !== rightCell.find('input').val()) {
                    rightCell.addClass('changed');
                }
                if (message !== '') {
                    $(this).find('span').text(message);
                }
            });
        });
    };

    /**
     * Creates a select element with the given language codes.
     *
     * @param codes the language codes
     */
    var populateLanguageSelector = function (codes) {
        var text = 'Messages on left from language: ' +
            '<select name="lang">';
        for (var i = 0; i < codes.length; ++i) {
            var tagEnd = codes[i] === 'en' ? ' selected="selected">' : '>';
            text += '<option' + tagEnd + codes[i] + '</option>';
        }
        $('#language_selector').html(text + '</select>');
    };


    // -----------
    // Initializer, listeners
    // -----------
    var languageSelect = $('#language_selector');
    if (languageSelect.length > 0) {
        $.getJSON('lang_retrieval.php', {list: 1}, function (data) {
            populateLanguageSelector(data.codes);
            languageSelect.find('select').change(function () {
                $('td.changed').removeClass('changed');
                updateMessagesInCell($(this).val());
            });
        });
    }
    var showChangesButton = $('#show_changes_btn');
    if (showChangesButton.length > 0) {
        var langCode = showChangesButton.data('lang');
        showChangesButton.click(function () {
            languageSelect.find('select').val(langCode);
            markChangedMessages(langCode);
        })
    }
});