document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("timeslotsForm");
    const startTimeInput = document.getElementById("start_time");
    const endTimeInput = document.getElementById("end_time");

    function validateTimes(event) {
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;

        if (startTime && endTime && startTime >= endTime) {
            event.preventDefault(); // ป้องกันการส่งฟอร์ม
            alert("❌ เวลาเริ่มต้นต้องเร็วกกว่าเวลาสิ้นสุด กรุณาเลือกเวลาใหม่!");
            return false;
        }
        return true;
    }

    // ตรวจสอบทุกครั้งที่ผู้ใช้เปลี่ยนค่า
    startTimeInput.addEventListener("change", validateTimes);
    endTimeInput.addEventListener("change", validateTimes);

    // ตรวจสอบก่อนส่งฟอร์ม
    form.addEventListener("submit", function (event) {
        if (!validateTimes(event)) {
            return;
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".delete-form").forEach(form => {
        form.addEventListener("submit", function (event) {
            event.preventDefault(); 
            if (confirm("คุณแน่ใจหรือไม่ว่าต้องการลบรอบการเข้าชมนี้?")) {
                this.submit();
            }
        });
    });
});

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


