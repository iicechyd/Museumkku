document.addEventListener("DOMContentLoaded", function () {
    var ctxBars = document.getElementById("chart-bars").getContext("2d");
    const totalVisitorsPerMonthThisYear =
        window.totalVisitorsPerMonthThisYear || [];

    new Chart(ctxBars, {
        type: "bar",
        data: {
            labels: Object.keys(totalVisitorsPerMonthThisYear),
            datasets: [
                {
                    label: "ผู้เข้าชม",
                    tension: 0.4,
                    borderWidth: 0,
                    borderRadius: 4,
                    borderSkipped: false,
                    backgroundColor: "rgba(255, 255, 255, .8)",
                    data: Object.values(totalVisitorsPerMonthThisYear),
                    maxBarThickness: 6,
                    hoverBorderColor: "#9F2B68",
                    hoverBorderWidth: 0.5,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        color: "#fff",
                        font: {
                            family: "Noto Sans Thai",
                        },
                    },
                },
                tooltip: {
                    callbacks: {
                        label: function (tooltipItem) {
                            const formattedNumber =
                                new Intl.NumberFormat().format(tooltipItem.raw);
                            return formattedNumber + " คน";
                        },
                    },
                    titleColor: "#fff",
                    bodyColor: "#fff",
                    titleFont: {
                        family: "Noto Sans Thai",
                    },
                    bodyFont: {
                        family: "Noto Sans Thai",
                    },
                },
            },
            interaction: {
                intersect: false,
                mode: "index",
            },
            scales: {
                y: {
                    grid: {
                        drawBorder: false,
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: false,
                        borderDash: [5, 5],
                        color: "rgba(255, 255, 255, .2)",
                    },
                    ticks: {
                        suggestedMin: 0,
                        suggestedMax: 500,
                        beginAtZero: true,
                        padding: 10,
                        font: {
                            size: 14,
                            weight: 300,
                            family: "Noto Sans Thai",
                            lineHeight: 2,
                        },
                        color: "#fff",
                    },
                },
                x: {
                    grid: {
                        drawBorder: false,
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: false,
                        borderDash: [5, 5],
                        color: "rgba(255, 255, 255, .2)",
                    },
                    ticks: {
                        display: true,
                        color: "#f8f9fa",
                        padding: 10,
                        font: {
                            size: 14,
                            weight: 300,
                            family: "Noto Sans Thai",
                            lineHeight: 2,
                        },
                    },
                },
            },
        },
    });

    var ctxLine = document.getElementById("chart-line").getContext("2d");
    const yearlyRevenueGeneral = window.yearlyRevenueGeneral || {};
    new Chart(ctxLine, {
        type: "line",
        data: {
            labels: Object.keys(yearlyRevenueGeneral),
            datasets: [
                {
                    label: "รายได้ทั้งหมด",
                    tension: 0,
                    borderWidth: 4,
                    pointRadius: 5,
                    pointBackgroundColor: "rgba(255, 255, 255, .8)",
                    pointBorderColor: "transparent",
                    borderColor: "rgba(255, 255, 255, .8)",
                    backgroundColor: "transparent",
                    fill: true,
                    data: Object.values(yearlyRevenueGeneral),
                    maxBarThickness: 6,
                    pointStyle: "circle",
                    pointHoverRadius: 6,
                    pointHoverBorderColor: "#228B22",
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    callbacks: {
                        label: function (tooltipItem) {
                            const formattedNumber =
                                new Intl.NumberFormat().format(tooltipItem.raw);
                            return formattedNumber + " บาท";
                        },
                    },
                    titleFont: {
                        family: "Noto Sans Thai",
                    },
                    bodyFont: {
                        family: "Noto Sans Thai",
                    },
                },
            },
            interaction: {
                intersect: false,
                mode: "index",
            },
            scales: {
                y: {
                    grid: {
                        drawBorder: false,
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: false,
                        borderDash: [5, 5],
                        color: "rgba(255, 255, 255, .2)",
                    },
                    ticks: {
                        display: true,
                        color: "#f8f9fa",
                        padding: 10,
                        font: {
                            size: 14,
                            weight: 300,
                            family: "Noto Sans Thai",
                            lineHeight: 2,
                        },
                    },
                },
                x: {
                    grid: {
                        drawBorder: false,
                        display: false,
                        drawOnChartArea: false,
                        drawTicks: false,
                        borderDash: [5, 5],
                    },
                    ticks: {
                        display: true,
                        color: "#f8f9fa",
                        padding: 10,
                        font: {
                            size: 14,
                            weight: 300,
                            family: "Noto Sans Thai",
                            lineHeight: 2,
                        },
                    },
                },
            },
        },
    });
});

var ctx3 = document.getElementById("chart-line-tasks").getContext("2d");
new Chart(ctx3, {
    type: "line",
    data: {
        labels: Object.keys(yearlyRevenueActivity),
        datasets: [
            {
                label: "รายได้จากการจัดกิจกรรม",
                tension: 0,
                borderWidth: 0,
                pointRadius: 5,
                pointBackgroundColor: "rgba(255, 255, 255, .8)",
                pointBorderColor: "transparent",
                borderColor: "rgba(255, 255, 255, .8)",
                borderWidth: 4,
                backgroundColor: "transparent",
                fill: true,
                data: Object.values(yearlyRevenueActivity),
                maxBarThickness: 6,
                pointStyle: "circle",
                pointHoverRadius: 6,
                pointHoverBorderColor: "#000",
            },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false,
            },
            tooltip: {
                callbacks: {
                    label: function (tooltipItem) {
                        const formattedNumber = new Intl.NumberFormat().format(
                            tooltipItem.raw
                        );
                        return formattedNumber + " บาท";
                    },
                },
                titleFont: {
                    family: "Noto Sans Thai",
                },
                bodyFont: {
                    family: "Noto Sans Thai",
                },
            },
        },
        interaction: {
            intersect: false,
            mode: "index",
        },
        scales: {
            y: {
                grid: {
                    drawBorder: false,
                    display: true,
                    drawOnChartArea: true,
                    drawTicks: false,
                    borderDash: [5, 5],
                    color: "rgba(255, 255, 255, .2)",
                },
                ticks: {
                    display: true,
                    padding: 10,
                    color: "#f8f9fa",
                    font: {
                        size: 14,
                        weight: 300,
                        family: "Noto Sans Thai",
                        lineHeight: 2,
                    },
                },
            },
            x: {
                grid: {
                    drawBorder: false,
                    display: false,
                    drawOnChartArea: false,
                    drawTicks: false,
                    borderDash: [5, 5],
                },
                ticks: {
                    display: true,
                    color: "#f8f9fa",
                    padding: 10,
                    font: {
                        size: 14,
                        weight: 300,
                        family: "Noto Sans Thai",
                        lineHeight: 2,
                    },
                },
            },
        },
    },
});

document.addEventListener("DOMContentLoaded", function () {
    var ctx = document.getElementById("visitorPieChart").getContext("2d");
    var total =
        children_qty +
        students_qty +
        adults_qty +
        disabled_qty +
        elderly_qty +
        monk_qty +
        free_teachers_qty;
    var visitorPieChart = new Chart(ctx, {
        type: "pie",
        data: {
            labels: [
                "เด็ก",
                "มัธยม/นักศึกษา",
                "ผู้ใหญ่ / คุณครู",
                "เด็กเล็ก",
                "ผู้พิการ",
                "ผู้สูงอายุ",
                "เณร / พระสงฆ์",
                "คุณครู (ฟรี)",
            ],
            datasets: [
                {
                    label: "จำนวนผู้เข้าชม",
                    data: [
                        children_qty,
                        students_qty,
                        adults_qty,
                        kid_qty,
                        disabled_qty,
                        elderly_qty,
                        monk_qty,
                        free_teachers_qty,
                    ],
                    backgroundColor: [
                        "#6B5B95",
                        "#32CD32",
                        "#EE82EE",
                        "#2980B9",
                        "#0000FF",
                        "#FF6F61",
                        "#F1C40F",
                        "#F1040F",
                    ],
                    borderColor: "#fff",
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: "top",
                    labels: {
                        boxWidth: 15,
                        padding: 10,
                        font: {
                            family: "'Noto Sans Thai', sans-serif",
                        },
                    },
                },
                tooltip: {
                    callbacks: {
                        label: function (tooltipItem) {
                            let value = tooltipItem.raw;
                            let percentage = ((value / total) * 100).toFixed(2);
                            return `${tooltipItem.label}: ${value} คน (${percentage}%)`;
                        },
                    },
                    titleFont: {
                        family: "'Noto Sans Thai', sans-serif",
                    },
                    bodyFont: {
                        family: "'Noto Sans Thai', sans-serif",
                    },
                },
            },
        },
    });
});

document.addEventListener("DOMContentLoaded", function() {
    let carousel = document.getElementById("visitorCarousel");
    let title = document.getElementById("visitorTitle");

    if (!carousel || !title) return;

    let startMonthThai = title.dataset.startMonth;
    let endMonthThai = title.dataset.endMonth;

    carousel.addEventListener("slid.bs.carousel", function(event) {
        if (event.to === 0) {
            title.innerHTML = `จำนวนผู้เข้าชมตามการจองในปีนี้
                <p class="text-sm">ปีงบประมาณ ${startMonthThai} - ${endMonthThai}</p>`;
        } else if (event.to === 1) {
            title.innerHTML = `จำนวนผู้เข้าชมตามการวอคอินในปีนี้
                <p class="text-sm">ปีงบประมาณ ${startMonthThai} - ${endMonthThai}</p>`;
        }
    });
});
