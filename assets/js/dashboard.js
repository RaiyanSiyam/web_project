// assets/js/dashboard.js
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    // Check if we have data passed from PHP
    const labels = chartData.labels.length > 0 ? chartData.labels : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    const data = chartData.values.length > 0 ? chartData.values : [0, 0, 0, 0, 0, 0, 0];

    // Minimalist SaaS Chart Design
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Sales Revenue (৳)',
                data: data,
                backgroundColor: '#111827', // Black bars
                borderRadius: 4,
                barThickness: 30,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#111827',
                    padding: 12,
                    titleFont: { size: 13, family: 'Inter' },
                    bodyFont: { size: 14, family: 'Inter', weight: 'bold' },
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
                    grid: { color: '#e5e7eb', drawBorder: false, borderDash: [5, 5] },
                    ticks: {
                        font: { family: 'Inter', size: 12 },
                        color: '#6b7280',
                        callback: function(value) { return '৳' + value; }
                    }
                },
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: { font: { family: 'Inter', size: 12 }, color: '#6b7280' }
                }
            }
        }
    });
});