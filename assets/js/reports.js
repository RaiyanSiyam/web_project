// assets/js/reports.js
document.addEventListener('DOMContentLoaded', () => {
    
    // Shared Chart.js styling variables
    const fontFamily = "'Inter', sans-serif";
    const primaryColor = '#111827'; 
    const gridColor = '#f3f4f6';
    const textColor = '#6b7280';

    // 1. Sales Trend Line Chart
    const ctxTrend = document.getElementById('salesTrendChart').getContext('2d');
    
    // Fallback data if completely empty
    const trendLabels = analyticsData.sales_trend.labels.length ? analyticsData.sales_trend.labels : ['No Data'];
    const trendValues = analyticsData.sales_trend.values.length ? analyticsData.sales_trend.values : [0];

    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Daily Revenue (৳)',
                data: trendValues,
                borderColor: primaryColor,
                backgroundColor: 'rgba(17, 24, 39, 0.05)',
                borderWidth: 3,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: primaryColor,
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.3 // Smooth curves
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: primaryColor,
                    padding: 12,
                    titleFont: { size: 13, family: fontFamily },
                    bodyFont: { size: 14, family: fontFamily, weight: 'bold' },
                    callbacks: {
                        label: function(context) {
                            return '৳ ' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor, drawBorder: false },
                    ticks: { font: { family: fontFamily, size: 12 }, color: textColor }
                },
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: { font: { family: fontFamily, size: 12 }, color: textColor }
                }
            }
        }
    });

    // 2. Top Products Doughnut Chart
    const ctxDoughnut = document.getElementById('topProductsChart').getContext('2d');
    
    const prodLabels = analyticsData.top_products.labels.length ? analyticsData.top_products.labels : ['No Sales Yet'];
    const prodValues = analyticsData.top_products.values.length ? analyticsData.top_products.values : [1];
    
    // A premium semantic palette: Black, Slate, Emerald Green, Indigo, Amber
    const palette = ['#111827', '#475569', '#10b981', '#6366f1', '#f59e0b'];

    new Chart(ctxDoughnut, {
        type: 'doughnut',
        data: {
            labels: prodLabels,
            datasets: [{
                data: prodValues,
                backgroundColor: palette,
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%', // Thin, modern ring
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: { family: fontFamily, size: 12 },
                        color: textColor
                    }
                },
                tooltip: {
                    backgroundColor: primaryColor,
                    padding: 12,
                    bodyFont: { size: 14, family: fontFamily },
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.parsed + ' units sold';
                        }
                    }
                }
            }
        }
    });

});