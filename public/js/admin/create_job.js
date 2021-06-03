const CUR_CURRENCY = 'JPY';
const TARGET_CURRENCY = 'USD';
/*const URL_METHOD = url_method;
const URL_TYPE = url_type;
const URL_CUSTOMER = url_customer;*/
$(document).ready(function () {
    exRate(result => {
        if (result.rate != -1) {
            let rate = result.rate;
            let text = '';
            if (rate) {
                text = '<b>Rate:</b> <span class="text-danger text-bold">1USD</span> = <span class="text-primary text-bold">' + Math.round(1 / rate) + 'JPY</span>';
            } else {
                text = '<span class="text-danger text-bold">Could not get the exchange rate. Please try again.</span>';
            }
            $('#ex-text').html(text);
        } else {
            text = '<span class="text-danger text-bold">Could not get the exchange rate. Please try again.</span>';
            $('#ex-text').html(text);
        }

    });

    $('.select2bs4').select2();
    $('#StartDate, #FinishDate').inputmask({
        mask: '9999-99-99',
        alias: 'date',
        placeholder: 'yyyy-mm-dd',
        insertMode: false,
    }).datepicker({
        dateFormat: 'yy-mm-dd',
    });

    $('#Type, #Customer, #Method').select2({
        placeholder: "Please select an option",
        allowClear: true
    });

    $('#PriceYen').on('keyup', function () {
        if ($(this).val().length) {
            exRate(result => {
                if (result.rate != -1) {
                    let usd = $(this).parentsUntil('form-group').next().find('[name=Price]');
                    let price = Math.round(result.rate * $(this).val());
                    $(usd).val(price);
                }
            });
        } else {
            $('#Price').val('');
        }
    });

    $('button[type=submit]').on('click', function(event){
        $(this).attr('disabled', true).html('<i class="fas fa-sync fa-spin mr-2"></i><span>Saving...</span>');
        $('form').submit();
    });
});