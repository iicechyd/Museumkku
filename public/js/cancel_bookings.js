function openCancelModal(bookingId) {
    $('#cancelModal_' + bookingId).modal('show');
}

$(document).ready(function() {
    $('[data-dismiss="modal"]').on('click', function() {
        var modalId = $(this).closest('.modal').attr('id');
        $('#' + modalId).modal('hide');
    });
});

function submitCancelForm(bookingId) {
    var reason = document.getElementById('reason_' + bookingId).value;

    if (!reason) {
        alert("กรุณากรอกหมายเหตุการยกเลิก");
        return;
    }
    document.getElementById('comments_' + bookingId).value = reason;
    document.getElementById('status_' + bookingId).value = 'cancel';

    var fields = ['actual_children_qty', 'actual_students_qty', 'actual_adults_qty', 'actual_kid_qty', 'actual_disabled_qty', 'actual_elderly_qty', 'actual_monk_qty'];
    fields.forEach(function (field) {
        var input = document.getElementById(field + '_' + bookingId);
        if (!input.value) {
            input.value = 0;
        }
    });

    document.getElementById('statusForm_' + bookingId).submit();
    $('#cancelModal_' + bookingId).modal('hide');
}