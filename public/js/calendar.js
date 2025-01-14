document.addEventListener("DOMContentLoaded", function () {
    var calendarEl = document.getElementById("calendar");

    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: "th",
        dayMaxEventRows: 3,
        contentHeight: "auto",
        aspectRatio: 2,
        height: "auto",
        eventClassNames: function(arg) {
            return ['custom-event'];
        },
        eventSources: [
            {
                url: "/calendar/events",
            },
            {
                events: function (fetchInfo, successCallback, failureCallback) {
                    let closedDays = [];
                    let start = new Date(fetchInfo.start);
                    let end = new Date(fetchInfo.end);

                    while (start <= end) {
                        if (start.getDay() === 2) {
                            closedDays.push({
                                title: "ปิดให้บริการ",
                                start: start.toISOString().split("T")[0],
                                allDay: true,
                                color: "#dc3545",
                            });
                        }
                        start.setDate(start.getDate() + 1);
                    }
                    successCallback(closedDays);
                },
            },
        ],
        eventContent: function (eventInfo) {
            var startTime = eventInfo.event.extendedProps.start_time || "";
            var endTime = eventInfo.event.extendedProps.end_time || "";
            var title = eventInfo.event.title || "";
            var contentHtml = title;
            if (startTime && endTime) {
                contentHtml = `
                    <div style="
                        white-space: nowrap; 
                        overflow: hidden; 
                        text-overflow: ellipsis; 
                        max-width: 200px;"
                    >
                         ${title}
                    </div>
                `;
            }

            return { html: contentHtml };
        },

        eventClick: function (info) {
            info.jsEvent.preventDefault();
        
            var eventTitle = info.event.title || "";
            var eventProps = info.event.extendedProps || {};

            if (eventTitle.includes("จำนวนผู้เข้าชม")) {
                document.getElementById("eventTitle").innerText = eventTitle;
        
                var timeslotDetails = Object.values(eventProps.booking_details || {});
    
                var groupedByActivity = timeslotDetails.reduce(function (acc, detail) {
                    var activityName = detail.activity_name || "ไม่ระบุชื่อกิจกรรม";
                    var startTime = detail.start_time.slice(0, 5);
        
                    if (!acc[activityName]) {
                        acc[activityName] = {};
                    }
                    if (!acc[activityName][startTime]) {
                        acc[activityName][startTime] = 0;
                    }
                    acc[activityName][startTime] += detail.total_approved;
        
                    return acc;
                }, {});
        
                var timeslotText = Object.keys(groupedByActivity).map(function (activityName) {
                    var slots = Object.keys(groupedByActivity[activityName])
                        .map(function (startTime) {
                            var totalApproved = groupedByActivity[activityName][startTime];
                            return `รอบ ${startTime} น. จำนวน ${totalApproved} คน`;
                        })
                        .join("<br>");
                    return `<strong>${activityName}</strong><br>${slots}`;
                }).join("<br><br>");
        
                document.getElementById("eventTimeslot").innerHTML =
                    timeslotText || "ไม่มีรายละเอียดการจอง";
        
                var myModal = new bootstrap.Modal(
                    document.getElementById("eventModal")
                );
                myModal.show();
            } else {
                document.getElementById("eventTitle").innerText = eventTitle;
        
                var timeslotText = "";
                var timeslotLabel = "";
                if (eventProps.start_time && eventProps.end_time) {
                    timeslotText = `${eventProps.start_time} น. - ${eventProps.end_time} น.`;
                }
        
                document.getElementById("eventTimeslotLabel").innerText =
                    timeslotLabel;
                document.getElementById("eventTimeslot").innerText =
                    timeslotText;
        
                var myModal = new bootstrap.Modal(
                    document.getElementById("eventModal")
                );
                myModal.show();
                
            }
        },
        eventDidMount: function (info) {
            var status = info.event.extendedProps.status;
            if (status === 1) {
                info.el.style.backgroundColor = "#28a745";
                info.el.style.color = "#ffffff";
            }
        },
    });

    calendar.render();
});
