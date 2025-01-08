function fetchActivityPrice() {
    const activityId = document.getElementById("fk_activity_id").value;
    if (activityId) {
        fetch(`/getActivityPrice/${activityId}`)
            .then((response) => response.json())
            .then((data) => {
                document.getElementById("children_price").value =
                    data.children_price;
                document.getElementById("student_price").value =
                    data.student_price;
                document.getElementById("adult_price").value = 
                    data.adult_price;
                document.getElementById("disabled_price").value = 
                    data.disabled_price;
                document.getElementById("elderly_price").value = 
                    data.elderly_price;
                document.getElementById("monk_price").value = 
                    data.monk_price;
                calculateTotal();
            })
            .catch((error) =>
                console.error("Error fetching activity price:", error)
            );
    }
}

function toggleInput(inputId) {
    const inputField = document.getElementById(inputId);
    inputField.disabled = !inputField.disabled;
    if (inputField.disabled) {
        inputField.value = "";
    }
    calculateTotal();
}
function calculateTotal() {
    const childrenQty = document.getElementById("childrenInput").value || 0;
    const studentsQty = document.getElementById("studentInput").value || 0;
    const adultsQty = document.getElementById("adultsInput").value || 0;
    const disabledQty = document.getElementById("disabledInput").value || 0;
    const elderlyQty = document.getElementById("elderlyInput").value || 0;
    const monkQty = document.getElementById("monkInput").value || 0;

    const childrenPrice =
        parseFloat(document.getElementById("children_price").value) || 0;
    const studentPrice =
        parseFloat(document.getElementById("student_price").value) || 0;
    const adultPrice =
        parseFloat(document.getElementById("adult_price").value) || 0;
    const disabledPrice =
        parseFloat(document.getElementById("disabled_price").value) || 0;
    const elderlyPrice =
        parseFloat(document.getElementById("elderly_price").value) || 0;
    const monkPrice =
        parseFloat(document.getElementById("monk_price").value) || 0;

    const totalVisitors =
        parseInt(childrenQty) + 
        parseInt(studentsQty) + 
        parseInt(adultsQty) + 
        parseInt(disabledQty) + 
        parseInt(elderlyQty) + 
        parseInt(monkQty);
    const totalPrice =
        childrenQty * childrenPrice +
        studentsQty * studentPrice +
        adultsQty * adultPrice +
        disabledQty * disabledPrice +
        elderlyQty * elderlyPrice +
        monkQty * monkPrice;

    document.getElementById("totalVisitors").innerText = totalVisitors;
    document.getElementById("totalPrice").innerText = totalPrice.toFixed(2);
}

$.Thailand({
    $district: $('#subdistrict'),
    $amphoe: $('#district'),
    $province: $('#province'),
    $zipcode: $('#zipcode'),
    onLoad: function () {
        $('.tt-menu').addClass('dropdown-scrollable');
    }
});

document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("instituteName").value = "โรงเรียนเด่นดี";
    document.getElementById("instituteAddress").value =
        "123/45 หมู่ 2 บ้านหมีน้อย";
    document.getElementById("visitorName").value = "นางสาวชญาดา วิชัยโย";
    document.getElementById("visitorEmail").value = "iicechyd.gaming@gmail.com";
    document.getElementById("tel").value = "0967121279";
    calculateTotal();
});

flatpickr("#booking_date", {
    dateFormat: "d/m/Y",
    minDate: new Date().fp_incr(3),
    disable: [
        function(date) {
            return date.getDay() === 1;
        }
    ],
    onDayCreate: function(dObj, dStr, fp, dayElem) {
        if (dayElem.dateObj.getDay() === 1) {
            dayElem.classList.add("disabled-day");
        }
    },
    onChange: function(selectedDates, dateStr, instance) {
        let [day, month, year] = dateStr.split('/');
        let formattedDate = `${year}-${month}-${day}`;

        let activityId = document.getElementById('fk_activity_id').value;

        if (formattedDate) {
            fetch(`/available-timeslots/${activityId}/${formattedDate}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();
                })
                .then(timeslots => {
                    let timeslotsSelect = document.getElementById('fk_timeslots_id');
                    timeslotsSelect.innerHTML = '';

                    if (timeslots.length === 0) {
                        let option = document.createElement('option');
                        option.value = "";
                        option.text = "ไม่เปิดให้จองในวันนี้";
                        timeslotsSelect.appendChild(option);
                        timeslotsSelect.disabled = true;
                    } else {
                        let option = document.createElement('option');
                        option.value = "";
                        option.text = "เลือกรอบการเข้าชม";
                        timeslotsSelect.appendChild(option);

                        timeslots.forEach((timeslot, index) => {
                            let option = document.createElement('option');
                            option.value = timeslot.timeslots_id;
                            option.text = `รอบที่ ${index + 1} ${timeslot.start_time} น. - ${timeslot.end_time} น.`;
                            timeslotsSelect.appendChild(option);
                        });

                        timeslotsSelect.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });
        }
    },
    onReady: function() {
        document.querySelector('.input-group-text').addEventListener('click', () => {
            document.querySelector("#booking_date")._flatpickr.open();
        });
    }
});

function confirmSubmission() {
    const childrenQty = document.getElementById("childrenInput").value || 0;
    const studentsQty = document.getElementById("studentInput").value || 0;
    const adultsQty = document.getElementById("adultsInput").value || 0;
    const disabledQty = document.getElementById("disabledInput").value || 0;
    const elderlyQty = document.getElementById("elderlyInput").value || 0;
    const monkQty = document.getElementById("monkInput").value || 0;

    if (
        childrenQty == 0 &&
        studentsQty == 0 &&
        adultsQty == 0 &&
        disabledQty == 0 &&
        elderlyQty == 0 &&
        monkQty == 0
    ) {
        document.getElementById("errorMessage").innerText = "*กรุณาระบุจำนวนผู้เข้าชมอย่างน้อย 1 ประเภท";
        document.getElementById("errorMessage").style.display = "block";
        return;
    } else {
        document.getElementById("errorMessage").style.display = "none";
    }
    const isConfirmed = confirm("คุณต้องการยืนยันการส่งข้อมูลใช่หรือไม่?");
    if (isConfirmed) {
        document.querySelector('form').submit();
    }
}

let calendar;

document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'th',
        eventLimit: true,
        dayMaxEventRows: 3,
        aspectRatio: 2,
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
                    <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;">
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
            }
        },
    });

    calendar.render();
});

function toggleCalendar() {
    const calendarContainer = document.getElementById('calendar');
    const toggleText = document.getElementById('toggleText');
    const arrowIcon = document.getElementById('arrowIcon');

    if (calendarContainer.classList.contains('hidden')) {
        calendarContainer.classList.remove('hidden');
        toggleText.innerText = "ซ่อนปฏิทินการจอง";
        arrowIcon.innerHTML = "&#9650;";

        calendar.updateSize();
    } else {
        calendarContainer.classList.add('hidden');
        toggleText.innerText = "แสดงปฏิทินการจอง";
        arrowIcon.innerHTML = "&#9660;";
    }
}

