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
        startDate.setMinutes(startDate.getMinutes() + 30); // Add 30 minutes

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
        startDate.setMinutes(startDate.getMinutes() + 30); // Add 30 minutes

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

    // ยืนยันก่อนเปลี่ยนสถานะ
    if (!confirm('คุณต้องการเปลี่ยนสถานะของ หรือไม่?')) {
        return false; // หยุดการทำงานหากผู้ใช้กด "Cancel"
    }

    // ส่งคำขอ Ajax ไปยังเซิร์ฟเวอร์เพื่อเปลี่ยนสถานะ
    $.ajax({
        url: '/toggle-status/' + timeslotId, // ใช้ URL ที่มีการเปลี่ยนสถานะ
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'), // ส่ง CSRF token ไปด้วย
        },
        success: function(response) {
            // อัปเดตไอคอนและสถานะในหน้าเว็บ
            if (response.status === 'active') {
                button.find('i').removeClass('fa-toggle-off text-secondary')
                    .addClass('fa-toggle-on text-success')
                    .attr('title', 'Active');
                button.data('status', 'active'); // อัปเดตข้อมูลสถานะใน data-status
            } else {
                button.find('i').removeClass('fa-toggle-on text-success')
                    .addClass('fa-toggle-off text-secondary')
                    .attr('title', 'Inactive');
                button.data('status', 'inactive'); // อัปเดตข้อมูลสถานะใน data-status
            }
            alert(response.message); // แสดงข้อความแจ้งเตือนเมื่อเปลี่ยนสถานะสำเร็จ
        },
        error: function(xhr, status, error) {
            alert('เกิดข้อผิดพลาดในการเปลี่ยนสถานะ');
        }
    });
});


