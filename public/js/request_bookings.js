document.addEventListener("DOMContentLoaded", function() {
    var requestBookings = JSON.parse(document.getElementById('requestBookingsData').textContent);
    requestBookings.forEach(function(booking_id) {
        toggleCommentsField(booking_id);
    });
});

function toggleCommentsField(booking_id) {
    var status = document.getElementById("statusSelect_" + booking_id).value;
    var commentsField = document.getElementById("commentsField_" + booking_id);

    if (status === "cancel") {
        commentsField.style.display = "block";
    } else {
        commentsField.style.display = "none";
    }
}

function confirmUpdateStatus(event) {
    var confirmAction = confirm("คุณต้องการอัปเดตสถานะการจองนี้ใช่หรือไม่?");
    if (!confirmAction) {
        event.preventDefault();
    }
    return confirmAction;
}