document.addEventListener("DOMContentLoaded", function () {
    var ctxBars = document.getElementById("chart-bars").getContext("2d");
    const totalVisitorsPerMonthThisYear =
        window.totalVisitorsPerMonthThisYear || [];

    new Chart(ctxBars, {
        type: "bar",
        data: {
            labels: [
                "Jan",
                "Feb",
                "Mar",
                "Apr",
                "May",
                "Jun",
                "Jul",
                "Aug",
                "Sep",
                "Oct",
                "Nov",
                "Dec",
            ],
            datasets: [
                {
                    label: "ผู้เข้าชม",
                    tension: 0.4,
                    borderWidth: 0,
                    borderRadius: 4,
                    borderSkipped: false,
                    backgroundColor: "rgba(255, 255, 255, .8)",
                    data: totalVisitorsPerMonthThisYear,
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
                            family: "Roboto",
                            style: "normal",
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
                            family: "Roboto",
                            style: "normal",
                            lineHeight: 2,
                        },
                    },
                },
            },
        },
    });

    var ctxLine = document.getElementById("chart-line").getContext("2d");
    const yearlyRevenueType1 = window.yearlyRevenueType1 || {};
    new Chart(ctxLine, {
        type: "line",
        data: {
            labels: Object.keys(yearlyRevenueType1),
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
                    data: Object.values(yearlyRevenueType1),
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
                            family: "Roboto",
                            style: "normal",
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
                            family: "Roboto",
                            style: "normal",
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
        labels: Object.keys(yearlyRevenueType2),
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
                data: Object.values(yearlyRevenueType2),
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
                        family: "Roboto",
                        style: "normal",
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
                        family: "Roboto",
                        style: "normal",
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
        monk_qty;
    var visitorPieChart = new Chart(ctx, {
        type: "pie",
        data: {
            labels: [
                "เด็ก",
                "นักเรียน",
                "ผู้ใหญ่",
                "ผู้พิการ",
                "ผู้สูงอายุ",
                "พระสงฆ์",
            ],
            datasets: [
                {
                    label: "จำนวนผู้เข้าชม",
                    data: [
                        children_qty,
                        students_qty,
                        adults_qty,
                        disabled_qty,
                        elderly_qty,
                        monk_qty,
                    ],
                    backgroundColor: [
                        "#FF6F61",
                        "#6B5B95",
                        "#88B04B",
                        "#F1C40F",
                        "#2980B9",
                        "#8E44AD",
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
                },
                tooltip: {
                    callbacks: {
                        label: function (tooltipItem) {
                            let value = tooltipItem.raw;
                            let percentage = ((value / total) * 100).toFixed(2);
                            return `${tooltipItem.label}: ${value} คน (${percentage}%)`;
                        },
                    },
                },
            },
        },
    });
});
