document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'th',
        // events: '/calendar/events',
        eventLimit: true,
        dayMaxEventRows: 3,
        contentHeight: 'auto',
        aspectRatio: 2,
        height: 'auto',
        eventSources: [
            {
                url: '/calendar/events',
            },
            {
                events: function (fetchInfo, successCallback, failureCallback) {
                    let closedDays = [];
                    let start = new Date(fetchInfo.start);
                    let end = new Date(fetchInfo.end);
    
                    while (start <= end) {
                        if (start.getDay() === 2) {
                            closedDays.push({
                                title: 'ปิดให้บริการ',
                                start: start.toISOString().split('T')[0],
                                allDay: true,
                                color: '#dc3545'
                            });
                        }
                        start.setDate(start.getDate() + 1);
                    }
                    successCallback(closedDays);
                }
            }
        ],

        // ปรับแต่งข้อความในปฏิทิน
        eventContent: function (eventInfo) {
            var startTime = eventInfo.event.extendedProps.start_time || '';
            var endTime = eventInfo.event.extendedProps.end_time || '';
            var title = eventInfo.event.title || '';

            // แสดงเฉพาะกรณีที่มี timeslots
            var contentHtml = title;
            if (startTime && endTime) {
                contentHtml = `
                    <div style="
                        white-space: nowrap; 
                        overflow: hidden; 
                        text-overflow: ellipsis; 
                        max-width: 200px;"
                    >
                        ${startTime} น. - ${endTime} น. ${title}
                    </div>
                `;
            }

            return { html: contentHtml };
        },

        eventClick: function (info) {
            info.jsEvent.preventDefault();

            if (info.event.title === 'ปิดให้บริการ') {
                return;
            }

            document.getElementById('eventTitle').innerText = info.event.title;

            var timeslotText = '';
            var timeslotLabel = 'รอบการเข้าชม:';
            if (info.event.extendedProps.start_time && info.event.extendedProps.end_time) {
                timeslotText = `${info.event.extendedProps.start_time} น. - ${info.event.extendedProps.end_time} น.`;
            } else {
                // ถ้าไม่มี timeslot, แสดงระยะเวลากิจกรรม
                var durationDays = info.event.extendedProps.duration_days || 'ไม่ระบุ';
                timeslotLabel = 'ระยะเวลากิจกรรม:';
                timeslotText = `${durationDays} วัน`;

            }
        
            document.getElementById('eventTimeslotLabel').innerText = timeslotLabel;
            document.getElementById('eventTimeslot').innerText = timeslotText;

            var remainingCapacity = info.event.extendedProps.remaining_capacity;
            var remainingText = 'ไม่จำกัดจำนวนคน';

            if (remainingCapacity === 0) {
                remainingText = 'เต็ม';
            } else if (remainingCapacity > 0) {
                remainingText = `${remainingCapacity} คน`;
            }        
            
        document.getElementById('eventRemainingCapacity').innerText = remainingText;

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
