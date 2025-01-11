$(document).ready(function () {
    $(".edit-activity-btn").on("click", function () {
        // ดึงค่าที่เก็บใน data-* attribute ของปุ่ม
        var activityId = $(this).data("id");
        var activityTypeId = $(this).data("activity_type_id");
        var activityName = $(this).data("name");
        var activityDescription = $(this).data("description");
        var activityChildrenPrice = $(this).data("children_price");
        var activityStudentPrice = $(this).data("student_price");
        var activityAdultPrice = $(this).data("adult_price");
        var activityDisabledPrice = $(this).data("disabled_price");
        var activityElderlyPrice = $(this).data("elderly_price");
        var activityMonkPrice = $(this).data("monk_price");
        var activityMaxCapacity = $(this).data("max_capacity");
        var activityImage = $(this).data("image");

        // ใส่ข้อมูลลงใน modal
        $("#edit_activity_id").val(activityId);
        $("#edit_activity_type_id").val(activityTypeId);
        $("#edit_activity_name").val(activityName);
        $("#edit_description").val(activityDescription);
        $("#edit_childrenprice").val(activityChildrenPrice);
        $("#edit_studentprice").val(activityStudentPrice);
        $("#edit_adultprice").val(activityAdultPrice);
        $("#edit_disabledprice").val(activityDisabledPrice);
        $("#edit_elderlyprice").val(activityElderlyPrice);
        $("#edit_monkprice").val(activityMonkPrice);
        $("#edit_max_capacity").val(activityMaxCapacity);

        if (activityImage) {
            $("#current_image").attr("src", activityImage).show();
            $("#current_image_name")
                .text(activityImage.split("/").pop())
                .show();
        } else {
            $("#current_image").hide();
            $("#current_image_name").hide();
        }
    });
});

$(document).on('click', '.toggle-status', function(e) {
    e.preventDefault();

    var button = $(this);
    var activityId = button.data('id');
    var activityName = button.closest('td').data('name');
    var currentStatus = button.data('status');

    // ยืนยันก่อนเปลี่ยนสถานะ
    if (!confirm('คุณต้องการเปลี่ยนสถานะของ ' + activityName + ' หรือไม่?')) {
        return false;
    }

    // ส่งคำขอ Ajax ไปยังเซิร์ฟเวอร์เพื่อเปลี่ยนสถานะ
    $.ajax({
        url: '/activity/toggle-status/' + activityId, // ใช้ URL ที่มีการเปลี่ยนสถานะ
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

