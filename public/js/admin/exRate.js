const EX_RATE_URL = '/ajax/get-rate-jpy-usd';

function exRate(callback) {
    $.ajax({
        url: EX_RATE_URL,
        method: 'get',
        success: callback,
        error: callback,
    });
}
