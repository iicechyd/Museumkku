document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'th',
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
        eventContent: function (eventInfo) {
            var startTime = eventInfo.event.extendedProps.start_time || '';
            var endTime = eventInfo.event.extendedProps.end_time || '';
            
            var title = eventInfo.event.title || '';
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
            var startTime = info.event.extendedProps.start_time || '';
            var endTime = info.event.extendedProps.end_time || '';
            var durationDays = info.event.extendedProps.duration_days || '';

            document.getElementById('eventTitle').innerText = info.event.title;
            if (startTime && endTime) {
                document.getElementById('eventTimeslotLabel').innerText = 'รอบการเข้าชม:';
                document.getElementById('eventTimeslot').innerText = `${startTime} น. - ${endTime} น.`;
            } else {
                document.getElementById('eventTimeslotLabel').innerText = 'ระยะเวลากิจกรรม:';
                document.getElementById('eventTimeslot').innerText = `${durationDays} วัน`;
            }
            document.getElementById('eventVisitor').innerText = info.event.extendedProps.visitor_name || 'ไม่ระบุ';
            document.getElementById('eventvisitorEmail').innerText = info.event.extendedProps.visitorEmail || 'ไม่ระบุ';
            document.getElementById('eventTel').innerText = info.event.extendedProps.tel || 'ไม่ระบุ';
            document.getElementById('eventInstitute').innerText = info.event.extendedProps.institute_name || 'ไม่ระบุ';
            document.getElementById('eventProvince').innerText = info.event.extendedProps.province || 'ไม่ระบุ';
            document.getElementById('eventDistrict').innerText = info.event.extendedProps.district || 'ไม่ระบุ';
            document.getElementById('eventSubdistrict').innerText = info.event.extendedProps.subdistrict || 'ไม่ระบุ';
            document.getElementById('eventZipcode').innerText = info.event.extendedProps.zipcode || 'ไม่ระบุ';
            document.getElementById('eventTotalQty').innerText = info.event.extendedProps.total_qty || 'ไม่ระบุ';
        
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

            }
        },
    });

    calendar.render();
});
