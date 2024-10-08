const min = 150;
const columnTypeToRatioMap = {
    numeric: 1,
    "text-short": 1.67,
    "text-long": 3.33,
};

const table = document.querySelector("table");
const columns = [];
let headerBeingResized;

const onMouseMove = (e) =>
    requestAnimationFrame(() => {
        console.log("onMouseMove");

        horizontalScrollOffset = document.documentElement.scrollLeft;
        const width =
            horizontalScrollOffset + e.clientX - headerBeingResized.offsetLeft;

        const column = columns.find(
            ({ header }) => header === headerBeingResized
        );
        column.size = Math.max(min, width) + "px";

        columns.forEach((column) => {
            if (column.size.startsWith("minmax")) {
                column.size = parseInt(column.header.clientWidth, 10) + "px";
            }
        });

        table.style.gridTemplateColumns = columns
            .map(({ header, size }) => size)
            .join(" ");
    });

const onMouseUp = () => {
    console.log("onMouseUp");

    window.removeEventListener("mousemove", onMouseMove);
    window.removeEventListener("mouseup", onMouseUp);
    headerBeingResized.classList.remove("header--being-resized");
    headerBeingResized = null;
};

const initResize = ({ target }) => {
    console.log("initResize");

    headerBeingResized = target.parentNode;
    window.addEventListener("mousemove", onMouseMove);
    window.addEventListener("mouseup", onMouseUp);
    headerBeingResized.classList.add("header--being-resized");
};

document.querySelectorAll("th").forEach((header) => {
    const max = columnTypeToRatioMap[header.dataset.type] + "fr";
    columns.push({
        header,
        size: `minmax(${min}px, ${max})`,
    });
    header
        .querySelector(".resize-handle")
        .addEventListener("mousedown", initResize);
});

$(document).ready(function () {
    $(".status-dropdown").change(function () {
        var bookingId = $(this).data("id");
        var status = $(this).val();

        $.ajax({
            url: "/admin/request_bookings/update-status/" + bookingId,
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                status: status,
            },
            success: function (response) {
                if (response.success) {
                    alert("สถานะการจองได้รับการอัปเดต");

                    // โหลดข้อมูลใหม่หลังจากเปลี่ยนสถานะ
                    loadPendingBookings();
                } else {
                    alert("เกิดข้อผิดพลาด: " + response.message);
                }
            },
            error: function (xhr) {
                alert("เกิดข้อผิดพลาดในการอัปเดตสถานะ");
            },
        });
    });

    // ฟังก์ชันในการโหลดข้อมูลใหม่ของการจองรออนุมัติ
    function loadPendingBookings() {
        $.ajax({
            url: "{{ route('admin.request_bookings.getPendingBookings') }}",
            type: "GET",
            success: function (data) {
                $("#pendingBookingsTable").html(data);
            },
            error: function (xhr) {
                alert("เกิดข้อผิดพลาดในการดึงข้อมูลรายการจอง");
            },
        });
    }
});

// function changeBookingStatus(bookingId) {
//     $.ajax({
//         url: "/change_status/" + bookingId,
//         method: "POST",
//         data: {
//             _token: $('meta[name="csrf-token"]').attr("content"),
//         },
//         success: function (response) {
//             if (response.success) {
//                 const statusElement = $("#status-btn-" + bookingId);
//                 if (response.newStatus) {
//                     statusElement
//                         .text("อนุมัติ")
//                         .removeClass("status-btn-request")
//                         .addClass("status-btn-approved");
//                 } else {
//                     statusElement
//                         .text("รออนุมัติ")
//                         .removeClass("status-btn-approved")
//                         .addClass("status-btn-request");
//                 }
//             }
//         },
//         error: function (xhr, status, error) {
//             console.error("เกิดข้อผิดพลาด:", error);
//         },
//     });
// }
