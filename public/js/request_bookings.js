function confirmUpdateStatus(event, bookingId) {
    var confirmAction = confirm("คุณต้องการอัปเดตสถานะการจองนี้ใช่หรือไม่?");
    if (!confirmAction) {
        event.preventDefault();
        return false;
    }

    document.getElementById('status_' + bookingId).value = 'approve';
    document.getElementById('statusForm_' + bookingId).submit();
}

function openCancelModal(bookingId) {
    $('#cancelModal_' + bookingId).modal('show');
}

function submitCancelForm(bookingId) {
    var reason = document.getElementById('reason_' + bookingId).value;

    if (!reason) {
        alert("กรุณากรอกเหตุผลการยกเลิก");
        return;
    }
    document.getElementById('status_' + bookingId).value = 'cancel';
    document.getElementById('comments_' + bookingId).value = reason;

    document.getElementById('statusForm_' + bookingId).submit();
    $('#cancelModal_' + bookingId).modal('hide');
}
