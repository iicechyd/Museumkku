document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'th',
        events: '/calendar/events',
        eventLimit: true,
        dayMaxEventRows: 3, 
        contentHeight: 'auto',
        aspectRatio: 2,
        height: 'auto',

        // ปรับแต่งข้อความในปฏิทิน
        eventContent: function (eventInfo) {
            // ดึงเวลาเริ่มต้นและชื่อกิจกรรม
            var startTime = eventInfo.event.extendedProps.start_time || '';
            var endTime = eventInfo.event.extendedProps.end_time || '';
            var title = eventInfo.event.title || '';
            
            // กำหนดข้อความที่จะแสดง
            return {
                html: `
                    <div style="
                        white-space: nowrap; 
                        overflow: hidden; 
                        text-overflow: ellipsis; 
                        max-width: 200px;"
                    >
                        ${startTime} น. - ${endTime} น. ${title}
                    </div>
                `
            };            
        },

        eventClick: function (info) {
            info.jsEvent.preventDefault();

            document.getElementById('eventTitle').innerText = info.event.title;
          
            var timeslot = info.event.extendedProps.start_time + " - " + info.event.extendedProps.end_time;
            document.getElementById('eventTimeslot').innerText = timeslot;

            var myModal = new bootstrap.Modal(document.getElementById('eventModal'));
            myModal.show();
        },
        eventDidMount: function (info) {
            var status = info.event.extendedProps.status;
            if (status === 0) {
                info.el.style.backgroundColor = '#ffc107';
                info.el.style.color = '#ffffff';
            } else if (status === 1) {
                info.el.style.backgroundColor = '#28a745';
                info.el.style.color = '#ffffff';
            } else if (status === 2) {
                info.el.style.backgroundColor = '#dc3545';
                info.el.style.color = '#ffffff';
            }
        },
    });

    calendar.render();
});