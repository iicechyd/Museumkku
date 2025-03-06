document.addEventListener("DOMContentLoaded", function () {
    const activityDropdown = document.getElementById("activity_id");
    const tmssDropdown = document.getElementById("tmss_id");

    if (activityDropdown) {
        activityDropdown.addEventListener("change", function () {
            const activityId = this.value;

            if (activityId) {
                fetch(getTmssUrl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    body: JSON.stringify({ activity_id: activityId }),
                })
                    .then((response) => response.json())
                    .then((data) => {
                        tmssDropdown.innerHTML = '<option value="">กรุณาเลือกรอบการเข้าชม</option>';
                        tmssDropdown.innerHTML += '<option value="all">ปิดทุกรอบ</option>';
                        data.forEach((tmss) => {
                            tmssDropdown.innerHTML += `<option value="${tmss.tmss_id}">${tmss.start_time} - ${tmss.end_time}</option>`;
                        });
                        tmssDropdown.disabled = false;
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                        alert("ไม่สามารถโหลดข้อมูลรอบการเข้าชมได้");
                    });
            } else {
                tmssDropdown.innerHTML = '<option value="">-- เลือกรอบการเข้าชม --</option>';
                tmssDropdown.disabled = true;
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