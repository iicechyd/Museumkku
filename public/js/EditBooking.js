document.addEventListener("DOMContentLoaded", function () {
    calculateTotal();
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

function toggleInput(inputId) {
    const inputField = document.getElementById(inputId);
    inputField.disabled = !inputField.disabled;
    if (inputField.disabled) {
        inputField.value = "";
    }
    calculateTotal();
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

document.addEventListener("DOMContentLoaded", function () {
    let bookingInput = document.getElementById("booking_date");
    let tmssSelect = document.getElementById("fk_tmss_id");
    let activityId = document.getElementById("fk_activity_id").value;
    let existingDate = bookingInput.value;
    let selectedTmssId = tmssSelect?.getAttribute("data-selected") || null;

    function fetchTmss(dateStr) {
        if (!dateStr || !tmssSelect) return;

        fetch(`/available-tms/${activityId}/${dateStr}`)
            .then((response) => response.json())
            .then((tmss) => {
                if (!tmssSelect) return;
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
                        option.text = `รอบที่ ${index + 1} ${startFormatted} น. - ${endFormatted} น.`;

                        if (tmss.remaining_capacity === 0) {
                            option.text += " (เต็ม)";
                        } else {
                            option.text += ` (เหลือ ${tmss.remaining_capacity} ที่นั่ง)`;
                        }

                        if (tmss.tmss_id == selectedTmssId) {
                            option.selected = true;
                        }

                        tmssSelect.appendChild(option);
                    });

                    tmssSelect.disabled = false;
                }
            })
            .catch((error) => {
                console.error("There was a problem with the fetch operation:", error);
            });
    }

    flatpickr("#booking_date", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d/m/Y",
        minDate: new Date().fp_incr(3),
        defaultDate: existingDate || null,
        disable: [
            function (date) {
                return date.getDay() === 1;
            },
        ],
        onChange: function (selectedDates, dateStr, instance) {
            tmssSelect.innerHTML = "<option value=''>เลือกรอบการเข้าชม</option>";
            selectedTmssId = '';
            fetchTmss(dateStr);
        },
        onReady: function (selectedDates, dateStr, instance) {
            instance.altInput.setAttribute("id", "booking_date_alt");

            const label = document.querySelector("label[for='booking_date']");
            if (label) {
                label.setAttribute("for", "booking_date_alt");
            }

            document.querySelector(".input-group-text").addEventListener("click", () => {
                instance.open();
            });

            if (existingDate) {
                fetchTmss(existingDate);
            }
        },
    });
});

function confirmSubmission() {
    const childrenQty = document.getElementById("childrenInput").value || 0;
    const studentsQty = document.getElementById("studentInput").value || 0;
    const adultsQty = document.getElementById("adultsInput").value || 0;
    const kidQty = document.getElementById("kidInput").value || 0;
    const disabledQty = document.getElementById("disabledInput").value || 0;
    const elderlyQty = document.getElementById("elderlyInput").value || 0;
    const monkQty = document.getElementById("monkInput").value || 0;

    if (
        childrenQty == 0 &&
        studentsQty == 0 &&
        adultsQty == 0 &&
        kidQty == 0 &&
        disabledQty == 0 &&
        elderlyQty == 0 &&
        monkQty == 0
    ) {
        document.getElementById("errorMessage").innerText =
            "*กรุณาระบุจำนวนผู้เข้าชมอย่างน้อย 1 ประเภท";
        document.getElementById("errorMessage").style.display = "block";
        return;
    } else {
        document.getElementById("errorMessage").style.display = "none";
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
