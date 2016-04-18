$(document).ready(function () {
    'use strict';

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
            var switchText = function (elem, text) {
                elem.fadeOut(200, function () {
                    elem.text(text);
                    elem.fadeIn();
                });
            };
            for (var key in data) {
                if (data.hasOwnProperty(key) && data[key] !== '') {
                    var cellText = $('td.language_template[data-key="' + key + '"]').find('span');
                    switchText(cellText, data[key]);
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
     * For public page: removes all "changed" colors and removes the additional comparison column, if present.
     */
    var unmarkChangesAndHideComparison = function () {
        $('.comparison').remove();
        $('.changed').removeClass('changed');
    };

    /**
     * For public page: adds an additional comparison column with the server's messages and marks the different
     * messages as changed.
     *
     * @param lang the language of the translation
     */
    var showChangesAndAddComparison = function (lang) {
        loadMessages(lang, function (data) {
            unmarkChangesAndHideComparison();
            $.each($('table.public tr').find('td:first'), function () {
                var key = $(this).data('key');
                var message = data.hasOwnProperty(key) ? data[key] : '';
                var nextCell = $(this).siblings('td');
                var cellClass = (message !== nextCell.text()) ? ' changed' : '';
                nextCell.after($('<td class="comparison colorable' + cellClass + '">').text(message));
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
    // Show changes button on edit page
    var showChangesButton = $('#show_changes_btn');
    if (showChangesButton.length > 0) {
        var langCode = showChangesButton.data('lang');
        showChangesButton.click(function () {
            languageSelect.find('select').val(langCode);
            markChangedMessages(langCode);
        });
    }
    // Show changes checkbox on public page
    var showChangesCheckbox = $('#show_changes_chk');
    showChangesCheckbox.attr('checked', false);
    if (showChangesCheckbox.length > 0) {
        var lang = showChangesCheckbox.data('lang');
        showChangesCheckbox.change(function () {
            if ($(this).is(':checked')) {
                showChangesAndAddComparison(lang);
            } else {
                unmarkChangesAndHideComparison();
            }
        });
    }
});