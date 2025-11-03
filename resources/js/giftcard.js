import './app';

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function () {
    const statusToggle = $('#statusToggle');
    const statusInput = $('#status');
    const statusLabel = $('#statusLabel');

    statusToggle.on('change', function () {
        if ($(this).is(':checked')) {
            statusInput.val('1');
            statusLabel.text('Enabled')
                .removeClass('text-danger')
                .addClass('text-success');
        } else {
            statusInput.val('0');
            statusLabel.text('Disabled')
                .removeClass('text-success')
                .addClass('text-danger');
        }
    });
});
