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


