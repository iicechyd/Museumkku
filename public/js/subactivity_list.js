$(document).on('click', '.toggle-status', function(e) {
    e.preventDefault();

    var button = $(this);
    var subActivityId = button.data('id'); 
    var currentStatus = button.data('status');
    var subActivityName = button.data('name'); 

    if (!confirm('คุณต้องการเปลี่ยนสถานะของกิจกรรมย่อย "' + subActivityName + '" หรือไม่?')) {
        return false;
    }

    $.ajax({
        url: '/admin/toggle-subactivity-status/' + subActivityId, 
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            status: currentStatus === 1 ? 0 : 1
        },
        success: function(response) {
            if (response.success) {
                if (currentStatus === 1) {
                    button.find('i').removeClass('fa-toggle-on text-success')
                        .addClass('fa-toggle-off text-secondary')
                        .attr('title', 'Inactive');
                    button.data('status', 0);
                } else {
                    button.find('i').removeClass('fa-toggle-off text-secondary')
                        .addClass('fa-toggle-on text-success')
                        .attr('title', 'Active');
                    button.data('status', 1);
                }
                alert('สถานะถูกเปลี่ยนเรียบร้อยแล้ว');
            } else {
                alert('เกิดข้อผิดพลาดในการเปลี่ยนสถานะ');
            }
        },
        error: function(xhr, status, error) {
            alert('เกิดข้อผิดพลาดในการเปลี่ยนสถานะ');
        }
    });
});

$(document).ready(function() {
    $('.fas.fa-edit').on('click', function() {
        const activityId = $(this).data('id');
        const maxSubactivities = $(this).data('max');

        $('#editActivityId').val(activityId);
        $('#maxSubactivitiesInput').val(maxSubactivities);

        $('#EditMaxSubactivitiesModal').modal('show');
    });

    $('#editMaxSubactivitiesForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: form.serialize(),
            success: function(response) {
                $('#EditMaxSubactivitiesModal').modal('hide');
                location.reload();
            },
            error: function(error) {
                console.log(error);
            }
        });
    });
    $('#EditMaxSubactivitiesModal .close').on('click', function() {
        $('#EditMaxSubactivitiesModal').modal('hide');
    });
});