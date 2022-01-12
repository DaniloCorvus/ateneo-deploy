const barChart = {
    series: [{
        data: [222, 1166, 6713, 7877, 8022, 10231, 22541, 52892, 60671, 141451]
    }],

    chartOptions: {
        chart: {
            toolbar: {
                show: false,
            }
        },
        plotOptions: {
            bar: {
                horizontal: true,
            }
        },
        dataLabels: {
            enabled: false
        },
        colors: ['#34c38f'],
        grid: {
            borderColor: '#f1f1f1',
        },
        xaxis: {
            categories: ['Tolima', 'Cundinamarca', 'Huila', 'Boyaca', 'Meta', 'Santander', 'Cauca', 'Antioquia', 'Nte Santander', 'Bogota'],
        }
    }
};

const donutChart = {
    series: [89.134, 222.545, 1.032],
    chartOptions: {
        labels: ["Contributivo", "Subsidiado","Particular"],
        colors: ["#1cbb8c", "#5664d2","#fcb92c"],
        legend: {
            show: true,
            position: 'bottom',
            horizontalAlign: 'center',
            verticalAlign: 'middle',
            floating: false,
            fontSize: '10px',
            offsetX: -10,
            offsetY: -10
        },
        responsive: [{
            breakpoint: 600,
            options: {
                chart: {
                    height: 240
                },
                legend: {
                    show: true
                },
            }
        }]
    }
};

export { donutChart, barChart };