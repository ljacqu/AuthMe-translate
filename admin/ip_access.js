$(document).ready(function () {
    $('#add_ip_field').keydown(function (e) {
        // 13 = Enter
        if (e.keyCode === 13) {
            add_ip();
        }
    });
    $('#add_ip_btn').click(function () {
        add_ip();
    });
    $('#ip_list').find('a').click(function () {
        remove_ip($(this));
    });
});

var remove_ip = function (linkElem) {
    $('#add_ip_response').text('');
    var elem = (linkElem instanceof jQuery) ? linkElem.parent('li') : $(linkElem).parent('li');
    var ip = elem.data('ip');

    $.post('grant_access.php', {remove: ip}, function (response) {
        if (response.success && response.error === '') {
            var existingEntry = $('#ip_list li').filter(function() {
                // Cannot use the data selector as it will only recognize data- attributes in the initial HTML
                return $(this).data('ip') === ip;
            });
            existingEntry.remove();
            $('#add_ip_response').text('Deleted IP address "' + ip + '"');
        } else {
            var message = 'Error' + (response.error ? ' - ' + response.error : '');
            $('#add_ip_response').text(message);
        }
    });
};

function add_ip() {
    $('#add_ip_response').text('');
    var ip = $('#add_ip_field').val().trim();
    if (ip === '' || !ip.match(/^(\d{1,3}\.){3}\d{1,3}$/)) {
        $('#add_ip_response').text('Please enter a valid IP address');
        return;
    }

    $.post('grant_access.php', {add: ip}, function (response) {
        if (response.success && response.error === '') {
            var existingEntry = $('#ip_list li').filter(function() {
                // Cannot use the data selector as it will only recognize data- attributes in the initial HTML
                return $(this).data('ip') === ip;
            });
            if (existingEntry.length > 0) {
                existingEntry.html(new_elem_text(ip));
                $('#add_ip_response').text('Extended access for "' + ip + '"');
            } else {
                $('#ip_list').append('<li data-ip="' + ip + '">' + new_elem_text(ip) + '</li>');
                $('#add_ip_response').text('Granted access to "' + ip + '"');
                // Ugly way of ensuring that we have a click handler on the 'remove' link of the new entry
                $('#ip_list a:last').click(function () { remove_ip($(this)); });
            }
            $('#add_ip_field').val('');
        } else {
            var message = 'Error' + (response.error ? ' - ' + response.error : '');
            $('#add_ip_response').text(message);
        }
    });
}

function new_elem_text(ip) {
    return ip + ' - expires in 30 days [<a onclick="remove_ip(this)">remove</a>]';
}