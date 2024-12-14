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

function fetchTimeslots() {
    const activityId = document.getElementById("fk_activity_id").value;
    
    fetch(`/timeslots/${activityId}`)
        .then((response) => response.json())
        .then((data) => {
            const timeslotSelect = document.getElementById("fk_timeslots_id");
            timeslotSelect.innerHTML =
                '<option value="">เลือกรอบการเข้าชม</option>';
            data.forEach((timeslot) => {
                const option = document.createElement("option");
                option.value = timeslot.id;
                option.text = `${timeslot.start_time} - ${timeslot.end_time}`;
                timeslotSelect.appendChild(option);
            });
        });
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
    document.getElementById("visitorName").value = "นางสาวแก้วกานต์ แก่นดี";
    document.getElementById("visitorEmail").value = "kaewkarn@gmail.com";
    document.getElementById("tel").value = "0812345678";
    calculateTotal();
});

flatpickr("#booking_date", {
    dateFormat: "d/m/Y",
    minDate: new Date().fp_incr(3),
});

function confirmSubmission() {
    const childrenQty = document.getElementById("childrenInput").value || 0;
    const studentsQty = document.getElementById("studentInput").value || 0;
    const adultsQty = document.getElementById("adultsInput").value || 0;
    const disabledQty = document.getElementById("disabledInput").value || 0;
    const elderlyQty = document.getElementById("elderlyInput").value || 0;
    const monkQty = document.getElementById("monkInput").value || 0;

    // ตรวจสอบว่าผู้ใช้ไม่ได้กรอกข้อมูลในฟิลด์ใด ๆ
    if (
        childrenQty == 0 &&
        studentsQty == 0 &&
        adultsQty == 0 &&
        disabledQty == 0 &&
        elderlyQty == 0 &&
        monkQty == 0
    ) {
        // แสดงข้อความแจ้งเตือนเมื่อไม่ได้กรอกจำนวน
        document.getElementById("errorMessage").innerText = "*กรุณาระบุจำนวนผู้เข้าชมอย่างน้อย 1 ประเภท";
        document.getElementById("errorMessage").style.display = "block";
        return; // หยุดการทำงานถ้ายังไม่ได้กรอกข้อมูล
    } else {
        // ซ่อนข้อความแจ้งเตือนเมื่อกรอกข้อมูลครบ
        document.getElementById("errorMessage").style.display = "none";
    }
    const isConfirmed = confirm("คุณต้องการยืนยันการส่งข้อมูลใช่หรือไม่?");
    if (isConfirmed) {
        document.querySelector('form').submit();
    }
}

let calendar; // ตัวแปรโกลบอล

document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'th',
        events: '/calendar/events',
        eventLimit: true,
        dayMaxEventRows: 3,
        aspectRatio: 2,

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
            } else if (status === 2) {
                info.el.style.backgroundColor = '#dc3545';
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
        arrowIcon.innerHTML = "&#9650;"; // ลูกศรขึ้น

        // บังคับให้ปฏิทินคำนวณขนาดใหม่เมื่อแสดง
        calendar.updateSize();
    } else {
        calendarContainer.classList.add('hidden');
        toggleText.innerText = "แสดงปฏิทินการจอง";
        arrowIcon.innerHTML = "&#9660;"; // ลูกศรลง
    }
}
