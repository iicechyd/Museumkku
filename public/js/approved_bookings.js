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

function toggleCommentsAndButton() {
    var status = document.getElementById("statusSelect").value;
    var commentsField = document.getElementById("commentsField");
    var updateButton = document.getElementById("updateButton");

    // แสดงฟิลด์ comments และปุ่มยืนยันเมื่อสถานะเป็น 'cancel' (ค่าเท่ากับ 2)
    if (status == "cancel") {
        commentsField.style.display = "block";
        updateButton.style.display = "block";
    } else {
        commentsField.style.display = "none";
        updateButton.style.display = "none";
    }
}

// เรียกใช้ฟังก์ชันนี้เมื่อหน้าเพจโหลด เพื่อให้แน่ใจว่าฟิลด์ comments และปุ่มถูกตั้งค่าอย่างถูกต้อง
document.addEventListener("DOMContentLoaded", function () {
    toggleCommentsAndButton();
});
