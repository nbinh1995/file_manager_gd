const SMALL_WIDTH = 992;

$(document).ready(function () {

    Chart.Legend.prototype.afterFit = function () {
        this.height = this.height + 30;
    };

    let ctx = $('#report-chart');
    let chart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: [],
            datasets: [],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                display: true,
                position: 'top',
                align: 'center',
                labels: {
                    fontSize: 10,
                    boxWidth: 20,
                },
            },
            layout: {
                padding: {
                    left: 50,
                    right: 50,
                    top: 50,
                    bottom: 50,
                }
            },
            tooltips: {
                mode: 'label',
                callbacks: {
                    label: function (item, data) {
                        return data['labels'][item['index']] + ': ' + data['datasets'][0]['data'][item['index']] + '%';
                    },
                }
            },
            plugins: {
                datalabels: {
                    formatter: function (value, context) {
                        return context.chart.data.labels[context.dataIndex];
                    },
                    display: 'auto',
                    anchor: 'end',
                    align: 'end',
                    clip: false,
                }
            }
        }

    });

    //load chart config
    $(window).on('resize', function () {
        configChart();
    });

    function getChart(data) {
        let labels = [];
        let percents = [];
        let colors = getColors(data.length);
        data.forEach(function (item) {
            labels.push(item.CustomerName);
            percents.push(item.Percent);
        });
        chart.data.labels = labels;
        chart.data.datasets = [{
            data: percents,
            label: 'Report Chart',
            backgroundColor: colors,
        }];
        chart.update();
    }

    function getChartTable(data) {
        let chartTable = $('#chart-table').DataTable({
            processing: true,
            responsive: true,
            destroy: true,
            searching: false,
            order: [[3, 'desc']],
            bAutoWidth: false,
            data: data,
            columns: [
                {data: 'CustomerID'},
                {data: 'CustomerName'},
                {
                    data: 'TotalPrice', render: $.fn.dataTable.render.number(',', '.', 0, '&#165;')
                },
                {
                    data: 'Percent', render: function (data, type, row) {
                        return data + '%';
                    }
                },
            ],
        });
    }

    function dynamicColor() {
        let r = Math.floor(Math.random() * 255);
        let g = Math.floor(Math.random() * 255);
        let b = Math.floor(Math.random() * 255);
        return `rgb(${r},${g},${b})`;
    }

    function getColors(l) {
        let colors = [];
        for (let i = 0; i < l; i++) {
            colors.push(dynamicColor());
        }
        return colors;
    }

    $('#modal-report').on('shown.bs.modal', function () {
        $.ajax({
            url: 'ajax/ajaxGetChartData',
            method: 'post',
            data: {
                'fromDate': $('#from-date').val(),
                'toDate': $('#to-date').val(),
                'reportType': $('#report-type').val(),
                'CustomerID': $('#customer').val(),
                'MethodID': $('#method').val(),
                'TypeID': $('#type').val(),
                'Paid': $('#paid').val(),
                'Search': $('.dataTables_filter').find('input[type=search]').val(),
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
            },
            success: function (data) {
                getChart(data);
                configChart();
                getChartTable(data);
            },
            error: function () {
                $(this).empty();
            }
        });
    });

    function configChart() {
        let wrapper = $('.chart-wrapper');
        if (chart) {
            if (window.innerWidth <= SMALL_WIDTH) {
                chart.options.legend.display = false;
                let wrapperHeight = wrapper.width();
                wrapper.height(wrapperHeight);
                chart.update();
            } else {
                chart.options.legend.display = true;
                wrapper.height('75vh');
                chart.update();
            }
        }
    }
});