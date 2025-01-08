document.addEventListener("DOMContentLoaded", function() {
    var approvedBookings = JSON.parse(document.getElementById('approvedBookingsData').textContent);
    approvedBookings.forEach(function(booking_id) {
        toggleCommentsField(booking_id);
    });
});

function toggleCommentsField(booking_id) {
    var status = document.getElementById("statusSelect_" + booking_id).value;
    var commentsField = document.getElementById("commentsField_" + booking_id);
    var visitorsField = document.getElementById("visitorsField_" + booking_id);

    if (status === "checkin") {
        commentsField.style.display = "none";
        visitorsField.style.display = "block";
    } else if (status === "cancel") {
        commentsField.style.display = "block";
        visitorsField.style.display = "none";
    } else {
        commentsField.style.display = "none";
        visitorsField.style.display = "none";
    }
}

function confirmUpdateStatus(event) {
    var confirmAction = confirm("คุณต้องการอัปเดตสถานะการจองนี้ใช่หรือไม่?");
    if (!confirmAction) {
        event.preventDefault();
    }
    return confirmAction;
}