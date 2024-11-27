$(document).ready(function () {
    $(".edit-activity-btn").on("click", function () {
        // ดึงค่าที่เก็บใน data-* attribute ของปุ่ม
        var activityId = $(this).data("id");
        var activityTypeId = $(this).data("activity_type_id");
        var activityName = $(this).data("name");
        var activityDescription = $(this).data("description");
        var activityChildrenPrice = $(this).data("children_price");
        var activityStudentPrice = $(this).data("student_price");
        var activityAdultPrice = $(this).data("adult_price");
        var activityMaxCapacity = $(this).data("max_capacity");
        var activityImage = $(this).data("image");

        // ใส่ข้อมูลลงใน modal
        $("#edit_activity_id").val(activityId);
        $("#edit_activity_type_id").val(activityTypeId);
        $("#edit_activity_name").val(activityName);
        $("#edit_description").val(activityDescription);
        $("#edit_childrenprice").val(activityChildrenPrice);
        $("#edit_studentprice").val(activityStudentPrice);
        $("#edit_adultprice").val(activityAdultPrice);
        $("#edit_max_capacity").val(activityMaxCapacity);

        if (activityImage) {
            $("#current_image").attr("src", activityImage).show();
            $("#current_image_name")
                .text(activityImage.split("/").pop())
                .show();
        } else {
            $("#current_image").hide();
            $("#current_image_name").hide();
        }
    });
});

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

        // Calculate the desired width
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

// Get ready, they're about to resize
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

$(document).on('click', '.toggle-status', function() {
    var button = $(this);
    var activityId = button.data('id'); // ดึง id ของกิจกรรม
    var currentStatus = button.data('status'); // ดึงสถานะปัจจุบันของกิจกรรม

    // ส่งคำขอ Ajax ไปยังเซิร์ฟเวอร์เพื่อเปลี่ยนสถานะ
    $.ajax({
        url: '/toggle-status/' + activityId, // ใช้ URL ที่มีการเปลี่ยนสถานะ
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'), // ส่ง CSRF token ไปด้วย
        },
        success: function(response) {
            // อัปเดตไอคอนและสถานะในหน้าเว็บ
            if(response.status === 'active') {
                button.find('i').removeClass('fa-toggle-off text-secondary')
                              .addClass('fa-toggle-on text-success')
                              .attr('title', 'Active');
                button.data('status', 'active'); // อัปเดตข้อมูลสถานะใน data-status
            } else {
                button.find('i').removeClass('fa-toggle-on text-success')
                              .addClass('fa-toggle-off text-secondary')
                              .attr('title', 'Inactive');
                button.data('status', 'inactive'); // อัปเดตข้อมูลสถานะใน data-status
            }
            alert(response.message); // แสดงข้อความแจ้งเตือนเมื่อเปลี่ยนสถานะสำเร็จ
        },
        error: function(xhr, status, error) {
            alert('เกิดข้อผิดพลาดในการเปลี่ยนสถานะ');
        }
    });
});
