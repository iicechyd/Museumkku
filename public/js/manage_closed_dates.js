document.addEventListener("DOMContentLoaded", function () {
    const activityDropdown = document.getElementById("activity_id");
    const timeslotsDropdown = document.getElementById("timeslots_id");

    if (activityDropdown) {
        activityDropdown.addEventListener("change", function () {
            const activityId = this.value;

            if (activityId) {
                fetch(getTimeslotsUrl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    body: JSON.stringify({ activity_id: activityId }),
                })
                    .then((response) => response.json())
                    .then((data) => {
                        timeslotsDropdown.innerHTML = '<option value="">-- เลือกรอบการเข้าชม --</option>';
                        timeslotsDropdown.innerHTML += '<option value="all">ปิดทุกรอบ</option>';
                        data.forEach((timeslot) => {
                            timeslotsDropdown.innerHTML += `<option value="${timeslot.timeslots_id}">${timeslot.start_time} - ${timeslot.end_time}</option>`;
                        });
                        timeslotsDropdown.disabled = false;
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                        alert("ไม่สามารถโหลดข้อมูลรอบการเข้าชมได้");
                    });
            } else {
                timeslotsDropdown.innerHTML = '<option value="">-- เลือกรอบการเข้าชม --</option>';
                timeslotsDropdown.disabled = true;
            }
        });
    }
});

flatpickr("#closed_on", {
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
    onReady: function() {
        document.querySelector('.input-group-text').addEventListener('click', () => {
            document.querySelector("#closed_on")._flatpickr.open();
        });
    }
});