document.addEventListener("DOMContentLoaded", function () {
    let maxSubactivities = window.maxSubactivities;
    let checkboxes = document.querySelectorAll(
        'input[name="sub_activity_id[]"]'
    );

    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener("change", function () {
            let selectedCount = document.querySelectorAll(
                'input[name="sub_activity_id[]"]:checked'
            ).length;
            if (selectedCount > maxSubactivities) {
                alert(`คุณสามารถเลือกได้สูงสุด ${maxSubactivities} หลักสูตร`);
                checkbox.checked = false;
            }
        });
    });
    calculateTotal();
});

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
                document.getElementById("kid_price").value =
                    data.kid_price;
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
    const kidQty = document.getElementById("kidInput").value || 0;
    const disabledQty = document.getElementById("disabledInput").value || 0;
    const elderlyQty = document.getElementById("elderlyInput").value || 0;
    const monkQty = document.getElementById("monkInput").value || 0;

    const childrenPrice =
        parseFloat(document.getElementById("children_price").value) || 0;
    const studentPrice =
        parseFloat(document.getElementById("student_price").value) || 0;
    const adultPrice =
        parseFloat(document.getElementById("adult_price").value) || 0;
    const kidPrice =
        parseFloat(document.getElementById("kid_price").value) || 0;
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
        parseInt(kidQty) +
        parseInt(disabledQty) +
        parseInt(elderlyQty) +
        parseInt(monkQty);
    const totalPrice =
        childrenQty * childrenPrice +
        studentsQty * studentPrice +
        adultsQty * adultPrice +
        kidQty * kidPrice +
        disabledQty * disabledPrice +
        elderlyQty * elderlyPrice +
        monkQty * monkPrice;

    document.getElementById("totalVisitors").innerText = totalVisitors.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById("totalPrice").innerText = totalPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

$.Thailand({
    $district: $("#subdistrict"),
    $amphoe: $("#district"),
    $province: $("#province"),
    $zipcode: $("#zipcode"),
    onLoad: function () {
        $(".tt-menu").addClass("dropdown-scrollable");
    },
});

flatpickr("#booking_date", {
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "d/m/Y",
    allowInput: true,
    minDate: new Date().fp_incr(3),
    disable: [
        function (date) {
            return date.getDay() === 1;
        },
    ],
    onDayCreate: function (dObj, dStr, fp, dayElem) {
        if (dayElem.dateObj.getDay() === 1) {
            dayElem.classList.add("disabled-day");
        }
    },
    onChange: function (selectedDates, dateStr, instance) {
        if (!dateStr) {
            console.error("Invalid date string:", dateStr);
            return;
        }    
        let formattedDate = dateStr;
        let activityId = document.getElementById("fk_activity_id").value;

        if (formattedDate) {
            fetch(`/available-tms/${activityId}/${formattedDate}`)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error(
                            "Network response was not ok " + response.statusText
                        );
                    }
                    return response.json();
                })
                .then((tmss) => {
                    let tmssSelect =
                        document.getElementById("fk_tmss_id");
                    tmssSelect.innerHTML = "";

                    if (tmss.length === 0) {
                        let option = document.createElement("option");
                        option.value = "";
                        option.text = "ไม่เปิดให้จองในวันนี้";
                        tmssSelect.appendChild(option);
                        tmssSelect.disabled = true;
                    } else {
                        let option = document.createElement("option");
                        option.value = "";
                        option.text = "เลือกรอบการเข้าชม";
                        tmssSelect.appendChild(option);

                        tmss.forEach((tmss, index) => {
                            let option = document.createElement("option");
                            let startTime = new Date(
                                `1970-01-01T${tmss.start_time}Z`
                            );
                            let endTime = new Date(
                                `1970-01-01T${tmss.end_time}Z`
                            );
                            let startFormatted = `${startTime
                                .getUTCHours()
                                .toString()
                                .padStart(2, "0")}:${startTime
                                .getUTCMinutes()
                                .toString()
                                .padStart(2, "0")}`;
                            let endFormatted = `${endTime
                                .getUTCHours()
                                .toString()
                                .padStart(2, "0")}:${endTime
                                .getUTCMinutes()
                                .toString()
                                .padStart(2, "0")}`;

                            option.value = tmss.tmss_id;
                            option.text = `รอบที่ ${
                                index + 1
                            } ${startFormatted} น. - ${endFormatted} น.`;

                            if (tmss.remaining_capacity === 0) {
                                option.disabled = true;
                                option.text += " (เต็ม)";
                            } else {
                                option.text += ` (เหลือ ${tmss.remaining_capacity} ที่นั่ง)`;
                            }
                            tmssSelect.appendChild(option);
                        });

                        tmssSelect.disabled = false;
                    }
                })
                .catch((error) => {
                    console.error(
                        "There was a problem with the fetch operation:",
                        error
                    );
                });
        }
    },
    onReady: function () {
        let existingDate = document.getElementById('booking_date').value;
        let selectedTmssId = document.getElementById("selected_tmss_id").value;

        if (existingDate) {
            let [year, month, day] = existingDate.split("-");
            let formattedDate = `${year}-${month}-${day}`;
            let activityId = document.getElementById("fk_activity_id").value;

            fetch(`/available-tms/${activityId}/${formattedDate}`)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error(
                            "Network response was not ok " + response.statusText
                        );
                    }
                    return response.json();
                })
                .then((tmss) => {
                    let tmssSelect = document.getElementById("fk_tmss_id");
                    tmssSelect.innerHTML = "";

                    if (tmss.length === 0) {
                        let option = document.createElement("option");
                        option.value = "";
                        option.text = "ไม่เปิดให้จองในวันนี้";
                        tmssSelect.appendChild(option);
                        tmssSelect.disabled = true;
                    } else {
                        let option = document.createElement("option");
                        option.value = "";
                        option.text = "เลือกรอบการเข้าชม";
                        tmssSelect.appendChild(option);

                        tmss.forEach((tmss, index) => {
                            let startTime = new Date(`1970-01-01T${tmss.start_time}Z`);
                            let endTime = new Date(`1970-01-01T${tmss.end_time}Z`);
                            let startFormatted = `${startTime.getUTCHours()
                                .toString()
                                .padStart(2, "0")}:${startTime
                                .getUTCMinutes()
                                .toString()
                                .padStart(2, "0")}`;
                            let endFormatted = `${endTime
                                .getUTCHours()
                                .toString()
                                .padStart(2, "0")}:${endTime
                                .getUTCMinutes()
                                .toString()
                                .padStart(2, "0")}`;

                            let option = document.createElement("option");
                            option.value = tmss.tmss_id;
                            option.text = `รอบที่ ${index + 1} ${startFormatted} น. - ${endFormatted} น.`;

                            if (tmss.remaining_capacity === 0) {
                                option.disabled = true;
                                option.text += " (เต็ม)";
                            } else {
                                option.text += ` (เหลือ ${tmss.remaining_capacity} ที่นั่ง)`;
                            }
                            if (option.value == selectedTmssId) {
                                option.selected = true;
                            }
                            tmssSelect.appendChild(option);
                        });

                        tmssSelect.disabled = false;
                    }
                })
                .catch((error) => {
                    console.error(
                        "There was a problem with the fetch operation:",
                        error
                    );
                });
        }

        document
            .querySelector(".input-group-text")
            .addEventListener("click", () => {
                document.querySelector("#booking_date")._flatpickr.open();
            });
    },
});

function confirmSubmission() {
    const privacyCheckbox = document.getElementById("privacyPolicy");
    const privacyAlert = document.getElementById("privacyAlert");

    if (!privacyCheckbox.checked) {
        privacyAlert.innerText = "โปรดยอมรับนโยบายความเป็นส่วนตัวก่อนยืนยันข้อมูล";
        privacyAlert.style.display = "block";
        return;
    } else {
        privacyAlert.style.display = "none";
    }

    const childrenQty = document.getElementById("childrenInput").value || 0;
    const studentsQty = document.getElementById("studentInput").value || 0;
    const adultsQty = document.getElementById("adultsInput").value || 0;
    const kidQty = document.getElementById("kidInput").value || 0;
    const disabledQty = document.getElementById("disabledInput").value || 0;
    const elderlyQty = document.getElementById("elderlyInput").value || 0;
    const monkQty = document.getElementById("monkInput").value || 0;
    const totalVisitors = childrenQty + studentsQty + adultsQty + kidQty + disabledQty + elderlyQty + monkQty;

    if (totalVisitors === 0) {
        document.getElementById("errorMessage").innerText =
            "*กรุณาระบุจำนวนผู้เข้าชมอย่างน้อย 1 ประเภท";
        document.getElementById("errorMessage").style.display = "block";
        return;
    } else {
        document.getElementById("errorMessage").style.display = "none";
    }

    if (totalVisitors <= 0) {
        alert("จำนวนผู้เข้าชมต้องมากกว่า 0 คน");
        return;
    }

    if (window.subactivities.length > 0) {
        const selectedSubactivities = document.querySelectorAll(
            'input[name="sub_activity_id[]"]:checked'
        );
        const maxSubactivities = window.maxSubactivities;

        if (selectedSubactivities.length === 0) {
            alert("กรุณาเลือกหลักสูตร");
            return;
        }

        if (selectedSubactivities.length < maxSubactivities) {
            alert(`กรุณาเลือกให้ครบ ${maxSubactivities} หลักสูตร`);
            return;
        }
    }

    const formInput = {
        zipcode: document.getElementById("zipcode").value.trim(),
        subdistrict: document
            .getElementById("subdistrict")
            .value.trim()
            .toLowerCase(),
        district: document
            .getElementById("district")
            .value.trim()
            .toLowerCase(),
        province: document
            .getElementById("province")
            .value.trim()
            .toLowerCase(),
    };

    $.getJSON("/raw_database.json", function (data) {
        const filteredByZipcode = data.filter(
            (item) => String(item.zipcode) === formInput.zipcode
        );

        console.log("ค่าจากฟอร์ม:", formInput);
        console.log("ข้อมูลที่กรองด้วยรหัสไปรษณีย์:", filteredByZipcode);

        const isValid = filteredByZipcode.some((item) => {
            console.log("ตรวจสอบรายการ:", item);
            return (
                item.district.trim().toLowerCase() ===
                    formInput.subdistrict.toLowerCase() &&
                item.amphoe.trim().toLowerCase() ===
                    formInput.district.toLowerCase() &&
                item.province.trim().toLowerCase() ===
                    formInput.province.toLowerCase()
            );
        });

        if (!isValid) {
            alert(
                "ข้อมูลที่อยู่ของคุณไม่ถูกต้อง กรุณาตรวจสอบให้แน่ใจว่ารหัสไปรษณีย์, ตำบล, อำเภอ และจังหวัดให้ถูกต้อง"
            );
            return;
        }

        const isConfirmed = confirm("คุณต้องการยืนยันการส่งข้อมูลใช่หรือไม่?");
        if (isConfirmed) {
            document.querySelector("form").submit();
        }
    }).fail(function () {
        alert("ไม่สามารถโหลดข้อมูลจากระบบได้ กรุณาลองใหม่");
    });
}

let calendar;

document.addEventListener("DOMContentLoaded", function () {
    var calendarEl = document.getElementById("calendar");

    calendar = new FullCalendar.Calendar(calendarEl, {
        locale: "th",
        dayMaxEventRows: 3,
        aspectRatio: 2,
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
                    <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;">
                        ${startTime} น. - ${endTime} น. ${title}
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

                var tmssDetails = Object.values(
                    eventProps.booking_details || {}
                );

                var groupedByActivity = tmssDetails.reduce(function (
                    acc,
                    detail
                ) {
                    var activityName =
                        detail.activity_name || "ไม่ระบุชื่อกิจกรรม";
                    var startTime = detail.start_time.slice(0, 5);

                    if (!acc[activityName]) {
                        acc[activityName] = {};
                    }
                    if (!acc[activityName][startTime]) {
                        acc[activityName][startTime] = 0;
                    }
                    acc[activityName][startTime] += detail.total_approved;

                    return acc;
                },
                {});

                var tmssText = Object.keys(groupedByActivity)
                    .map(function (activityName) {
                        var slots = Object.keys(groupedByActivity[activityName])
                            .map(function (startTime) {
                                var totalApproved =
                                    groupedByActivity[activityName][startTime];
                                return `รอบ ${startTime} น. จำนวน ${totalApproved} คน`;
                            })
                            .join("<br>");
                        return `<strong>${activityName}</strong><br>${slots}`;
                    })
                    .join("<br><br>");

                document.getElementById("eventTmss").innerHTML =
                    tmssText || "ไม่มีรายละเอียดการจอง";

                var myModal = new bootstrap.Modal(
                    document.getElementById("eventModal")
                );
                myModal.show();
            } else {
                document.getElementById("eventTitle").innerText = eventTitle;

                var tmssText = "";
                var tmssLabel = "";
                if (eventProps.start_time && eventProps.end_time) {
                    tmssText = `${eventProps.start_time} น. - ${eventProps.end_time} น.`;
                }

                document.getElementById("eventTmssLabel").innerText =
                    tmssLabel;
                document.getElementById("eventTmss").innerText =
                    tmssText;

                var myModal = new bootstrap.Modal(
                    document.getElementById("eventModal")
                );
                myModal.show();
            }
        },

        eventDidMount: function (info) {
            var status = info.event.extendedProps.status;
            if (status === 0) {
                info.el.style.backgroundColor = "#ffc107";
                info.el.style.color = "#ffffff";
            } else if (status === 1) {
                info.el.style.backgroundColor = "#28a745";
                info.el.style.color = "#ffffff";
            }
        },
    });

    calendar.render();
});

function toggleCalendar() {
    const calendarContainer = document.getElementById("calendar");
    const toggleText = document.getElementById("toggleText");
    const arrowIcon = document.getElementById("arrowIcon");

    if (calendarContainer.classList.contains("hidden")) {
        calendarContainer.classList.remove("hidden");
        toggleText.innerText = "ซ่อนปฏิทินการจอง";
        arrowIcon.innerHTML = "&#9650;";

        calendar.updateSize();
    } else {
        calendarContainer.classList.add("hidden");
        toggleText.innerText = "แสดงปฏิทินการจอง";
        arrowIcon.innerHTML = "&#9660;";
    }
}
