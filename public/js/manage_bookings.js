function confirmUpdateStatus(event, bookingId) {
    var confirmAction = confirm("คุณต้องการอัปเดตสถานะการจองนี้ใช่หรือไม่?");
    if (!confirmAction) {
        event.preventDefault();
        return false;
    }

    document.getElementById('status_' + bookingId).value = 'approve';
    document.getElementById('statusForm_' + bookingId).submit();
}

function openVisitorModal(bookingId) {
    $('#visitorModal_' + bookingId).modal('show');
}

function submitVisitorCount(bookingId) {
    var visitorCount = document.getElementById('visitor_count_' + bookingId).value;
    
    if(visitorCount > 0) {
        document.getElementById('number_of_visitors_' + bookingId).value = visitorCount;
        document.getElementById('status_' + bookingId).value = 'checkin'; // Set status to checkin
        document.getElementById('statusForm_' + bookingId).submit();
    } else {
        alert('กรุณากรอกจำนวนผู้เข้าชมที่ถูกต้อง');
    }
}


function openCancelModal(bookingId) {
    $('#cancelModal_' + bookingId).modal('show');
}

function submitCancelForm(bookingId) {
    var reason = document.getElementById('reason_' + bookingId).value;

    if (!reason) {
        alert("กรุณากรอกหมายเหตุการยกเลิก");
        return;
    }
    document.getElementById('status_' + bookingId).value = 'cancel';
    document.getElementById('comments_' + bookingId).value = reason;

    document.getElementById('statusForm_' + bookingId).submit();
    $('#cancelModal_' + bookingId).modal('hide');
}

flatpickr("#closed_on", {
    dateFormat: "d/m/Y",
    minDate: "today",

});