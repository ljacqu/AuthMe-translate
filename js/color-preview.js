$(document).ready(function () {
    'use strict';

    /** Color code to hex code map. */
    var colorToHex = {
        '&0': '#000',
        '&1': '#00A',
        '&2': '#0A0',
        '&3': '#00A',
        '&4': '#A00',
        '&5': '#A0A',
        '&6': '#FA0',
        '&7': '#AAA',
        '&8': '#555',
        '&9': '#55F',
        '&a': '#5F5',
        '&b': '#5FF',
        '&c': '#F55',
        '&d': '#F5F',
        '&e': '#FF5',
        '&f': "#FFF"
    };

    /**
     * Replaces the color codes in the given text to HTML with the colors applied.
     *
     * @param text the text to replace
     * @returns resulting html
     */
    var replaceColors = function (text) {
        // Can't do & -> &amp; because we're about to replace things like &3
        text = text.replace(/</g, '&lt;');
        var origLength = text.length;
        for (var code in colorToHex) {
            if (colorToHex.hasOwnProperty(code)) {
                var color = colorToHex[code];
                text = text.replace(new RegExp(code, 'g'), '<span style="color: ' + color + '">');
            }
        }

        // Ugly hack to close all <span>'s: compare the new length with the old one and add </span> to the end accordingly
        var newLength = text.length;
        var totalSpanElements = (newLength - origLength) / ('<span style="color: #123">'.length - 2);
        for (var i = 0; i < totalSpanElements; ++i) {
            text += '</span>';
        }
        return text;
    };

    /**
     * Toggle all .colorable elements, converting from color codes to HTML or back.
     */
    var toggleColorables = function () {
        $.each($('.colorable'), function () {
            var rawText = $(this).data('raw-text');
            if (typeof rawText !== 'undefined') {
                $(this).removeData('raw-text');
                $(this).text(rawText);
            } else {
                var text = $(this).text();
                $(this).data('raw-text', text);
                $(this).html(replaceColors(text));
            }
        });
    };

    // Listener for #toggle_color_btn button
    var toggleColorButton = $('#toggle_color_btn');
    if (toggleColorButton.length > 0) {
        toggleColorButton.click(toggleColorables);
    }
});



