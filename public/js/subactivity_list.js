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
            status: currentStatus === 'active' ? 'inactive' : 'active'
        },
        success: function(response) {
            if (response.success) {
                if (currentStatus === 'active') {
                    button.find('i').removeClass('fa-toggle-on text-success')
                        .addClass('fa-toggle-off text-secondary')
                        .attr('title', 'Inactive');
                    button.data('status', 'inactive');
                } else {
                    button.find('i').removeClass('fa-toggle-off text-secondary')
                        .addClass('fa-toggle-on text-success')
                        .attr('title', 'Active');
                    button.data('status', 'active');
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
