$(document).ready(function () {
    $(".edit-activity-btn").on("click", function () {
        var activityId = $(this).data("id");
        var activityTypeId = $(this).data("activity_type_id");
        var activityName = $(this).data("name");
        var activityDescription = $(this).data("description");
        var activityChildrenPrice = $(this).data("children_price");
        var activityStudentPrice = $(this).data("student_price");
        var activityAdultPrice = $(this).data("adult_price");
        var activityKidPrice = $(this).data("kid_price");
        var activityDisabledPrice = $(this).data("disabled_price");
        var activityElderlyPrice = $(this).data("elderly_price");
        var activityMonkPrice = $(this).data("monk_price");
        var activityMaxCapacity = $(this).data("max_capacity");
        var activityImage = $(this).data("image");

        $("#edit_activity_id").val(activityId);
        $("#edit_activity_type_id").val(activityTypeId);
        $("#edit_activity_name").val(activityName);
        $("#edit_description").val(activityDescription);
        $("#edit_childrenprice").val(activityChildrenPrice);
        $("#edit_studentprice").val(activityStudentPrice);
        $("#edit_adultprice").val(activityAdultPrice);
        $("#edit_kidprice").val(activityKidPrice);
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

    if (!confirm('คุณต้องการเปลี่ยนสถานะของ ' + activityName + ' หรือไม่?')) {
        return false;
    }

    $.ajax({
        url: '/activity/toggle-status/' + activityId,
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
        },
        success: function(response) {
            if (response.status === 'active') {
                button.find('i').removeClass('fa-toggle-off text-secondary')
                    .addClass('fa-toggle-on text-success')
                    .attr('title', 'Active');
                button.data('status', 'active');
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

$(document).ready(function() {
    $('.delete-image-form').on('submit', function(e) {
        e.preventDefault();

        if (!confirm('ยืนยันการลบรูปภาพนี้?')) {
            return false;
        }

        var form = $(this);
        var button = form.find('button[type="submit"]');
        button.prop('disabled', true);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                form.closest('.col-md-4').fadeOut('slow', function() {
                    $(this).remove();
                    if ($('.row').children('.col-md-4').length === 0) {
                        $('.row').html('<div class="col-12 text-center"><h2>ไม่พบรูปภาพสำหรับกิจกรรมนี้</h2></div>');
                    }
                });
                alert('ลบรูปภาพสำเร็จ');
            },
            error: function(xhr, status, error) {
                alert('เกิดข้อผิดพลาดในการลบรูปภาพ');
                button.prop('disabled', false);
            }
        });
    });
});
