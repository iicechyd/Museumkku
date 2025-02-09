function updateEndTime(startTimeInput) {
    const startTime = startTimeInput.value;
    const endTimeInput = startTimeInput
        .closest(".modal-body")
        .querySelector('input[name="end_time"]');

    if (startTime) {
        const startDate = new Date();
        const [hours, minutes] = startTime.split(":");
        startDate.setHours(hours);
        startDate.setMinutes(minutes);
        startDate.setMinutes(startDate.getMinutes() + 30);

        const formattedHours = String(startDate.getHours()).padStart(2, "0");
        const formattedMinutes = String(startDate.getMinutes()).padStart(
            2,
            "0"
        );
        endTimeInput.value = `${formattedHours}:${formattedMinutes}`;
    }
}

function updateInsertEndTime(startTimeInput) {
    const startTime = startTimeInput.value;
    const endTimeInput = document.getElementById("insert_end_time");

    if (startTime) {
        const startDate = new Date();
        const [hours, minutes] = startTime.split(":");
        startDate.setHours(hours);
        startDate.setMinutes(minutes);
        startDate.setMinutes(startDate.getMinutes() + 30);

        const formattedHours = String(startDate.getHours()).padStart(2, "0");
        const formattedMinutes = String(startDate.getMinutes()).padStart(
            2,
            "0"
        );
        endTimeInput.value = `${formattedHours}:${formattedMinutes}`;
    }
}

$(document).on('click', '.toggle-status', function(e) {
    e.preventDefault();

    var button = $(this);
    var timeslotId = button.data('id'); 
    var currentStatus = button.data('status');

    if (!confirm('คุณต้องการเปลี่ยนสถานะของ หรือไม่?')) {
        return false;
    }

    $.ajax({
        url: '/toggle-status/' + timeslotId, 
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
        },
        success: function(response) {
            if (response.status === 1) {
                button.find('i').removeClass('fa-toggle-off text-secondary')
                    .addClass('fa-toggle-on text-success')
                    .attr('title', 'Active');
                button.data('status', 1);
            } else {
                button.find('i').removeClass('fa-toggle-on text-success')
                    .addClass('fa-toggle-off text-secondary')
                    .attr('title', 'Inactive');
                button.data('status', 'inactive');
            }
            alert(response.message);
        },
        error: function(xhr, status, error) {
            alert('เกิดข้อผิดพลาดในการเปลี่ยนสถานะ');
        }
    });
});


