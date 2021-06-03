const TABLE = 'JOB TABLE';
const ID_YEAR_CHART = 'year-chart';
const ID_MONTH_CHART = 'month-chart';
const COLOR_SALE = 'rgb(129, 189, 162)';
const COLOR_PAYMENT = 'rgb(220, 53, 69)';
const URL_MONTH_ON_YEAR = url_month;
const FORMATTER = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 0
})



var YearCanvas;

var DATA = {
    labels: [],
    datasets: [{
        label: "Sale",
        backgroundColor: COLOR_SALE,
        borderColor: COLOR_SALE,
        data: [],
        fill: false,
        datalabels: {
            align: '240',
        }
    },
    {
        label: "Payment",
        backgroundColor: COLOR_PAYMENT,
        borderColor: COLOR_PAYMENT,
        data: [],
        fill: false,
        datalabels: {
            align: '-45',
        }
    }]
}

var OPTION = {
    maintainAspectRatio: false,
    tooltips: {
                mode: 'index',
                intersect: false,
                yAlign:'bottom',
            },
    hover: {
                mode: 'nearest',
                intersect: true
            },
    plugins: {
        datalabels: {
            backgroundColor: function(context) {
                return context.active ? context.dataset.backgroundColor : 'transparent';
            },
            borderColor: function(context) {
                return context.dataset.backgroundColor;
            },
            borderRadius: 20,
            borderWidth: 1,
            color: function(context) {
                return context.active ? 'white' : context.dataset.backgroundColor;
            },
            font: {
                weight: 'bold',
                size: 10
            },
            formatter: function(value, context) {
                return FORMATTER.format(value);
            },
            offset: 10,
            textAlign: 'center',
            display: 'auto',
        }
    },
    responsive: true,
    title: {
        display: true,
        text: ''
    },
    scales: {
        xAxes: [{
            display: true,
            ticks: {
                fontSize: 10,
            }
            
        }],
        yAxes: [{
            display: true,
            scaleLabel: {
                display: true,
                labelString: 'USD'
            },
            ticks: {
                fontSize: 10,
                beginAtZero: true,
                callback: function (value, index, values) {
                    return FORMATTER.format(value);
                }
            }
        }]
    }
}

function ajaxPriceMonthOnYear() {
    let chartFrom = $('[name=chartFrom]').val();
    let chartTo = $('[name=chartTo]').val();
    return $.ajax({
        url: URL_MONTH_ON_YEAR,
        method: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            chartFrom: chartFrom,
            chartTo: chartTo
        },
        success: function (data) {
        },
        error: function (xhr, status, error) { },
    });
}

function init() {
    $('input[data-date]').datepicker( {
        dateFormat: 'yy-mm',
        changeMonth: true,
        changeYear: true,
        yearRange: '2009:'+(new Date().getFullYear()),
        showMonthAfterYear: true,
        showButtonPanel: true,
        onClose: function(dateText, inst) {
            var month=$("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).datepicker('setDate', new Date(year, month, 1));
            $(".ui-datepicker-calendar").hide();
        },
        beforeShow: function(input, inst) {
            if ((selDate = $(this).val()).length > 0) 
            {
                var tmp = $(this).val().split('-');
                $(this).datepicker('option','defaultDate',new Date(tmp[0],tmp[1]-1,1));
                $(this).datepicker('setDate', new Date(tmp[0], tmp[1]-1, 1));
            }
        }
        });
        $("input[data-date]").on('focus click focusout',function () {
            $('#ui-datepicker-div').find('button[data-handler=today]').text('Current');
            $(".ui-datepicker-calendar").hide();
            $("#ui-datepicker-div").position({
                my: "center top",
                at: "center bottom",
                of: $(this)
            });
        });
    YearCanvas = $(`#${ID_YEAR_CHART}`).get(0).getContext('2d')
    YearChart = new Chart(YearCanvas, {
        type: 'line',
        data: DATA,
        plugins: [ChartDataLabels],
        options: OPTION
    });

    updateChartMonth();
}

function updateChartMonth() {
    ajaxPriceMonthOnYear().done(function (data) {
        YearChart.options.title.text = 'Annual Revenue';
        YearChart.data.labels = data.chartData.labels;
        YearChart.data.datasets[0].data = data.chartData.LineSale;
        YearChart.data.datasets[1].data = data.chartData.LinePayment;
        YearChart.update();
    });
}


$(document).ready(function () {

    Chart.Legend.prototype.afterFit = function() {
        this.height = this.height + 50;
    };
    init();

    // $(document).on('submit', '#form-report', function (e) {
    //     e.preventDefault();
    //     ajaxChartDaysOnMonth(e.target).done(function (data) {
    //         let labels = [];
    //         let values = [];
    //         data.priceDate.forEach(function (v) {
    //             labels.push(v.date);
    //             values.push(v.sum_price);
    //         });
    //         MonthChart.options.title.text = `From ${$(e.target).find('#from-date').val()} To ${$(e.target).find('#to-date').val()}`;
    //         MonthChart.data.labels = labels;
    //         MonthChart.data.datasets[0].data = values;
    //         MonthChart.update();
    //     });
    // });

    $(document).on('click', '#refresh-chart', function (e) {
        updateChartMonth();
    });

});