const CUR_CURRENCY = 'JPY';
const TARGET_CURRENCY = 'USD';
const ID_MODAL = '#modal-report';
const PER_PAGE = 20;
var status_input = true;
let fullRowEdit = false;
const DATA_AJAX_URL = 'ajax/ajaxGetData';

$(document).ready(function () {

    //update money ex rate price
    getTotalPrice();

    function getTotalPrice(search) {
        $.ajax({
            url: 'ajax/ajaxGetTotalPriceView',
            method: 'post',
            data: {
                'fromDate': $('#from-date').val(),
                'toDate': $('#to-date').val(),
                'reportType': $('#report-type').val(),
                'CustomerID': $('#customer').val(),
                'MethodID': $('#method').val(),
                'TypeID': $('#type').val(),
                //'RealJob': $('#real').val(),
                'Paid': $('#paid').val(),
                'Search': search,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
            },
            success: function (data) {
                if (data.html) {
                    $('.total').html(data.html);
                } else {
                    $('.total').html('<p class="text-danger text-right text-md">Could not get data from server. Please try again.</p>');
                }
            },
        });
    }

    $('#customer, #method, #type').select2();

    $('#from-date, #to-date').inputmask({
        alias: 'date',
        mask: '9999-99-99',
        placeholder: 'yyyy-mm-dd',
        insertMode: false,
    }).datepicker({
        dateFormat: 'yy-mm-dd',
    });


    let types = '';
    let customers = '';
    let methods = '';

    let reportTable;

    function getData() {
        $.ajax({
            url: DATA_AJAX_URL,
            method: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
            },
            success: function (data, textStatus, jqXHR) {
                if (data && jqXHR.status === 200) {
                    data.types.forEach((type) => {
                        types += `<option value="${type.ID}">${type.Name}</option>`;
                    });
                    data.methods.forEach((method) => {
                        methods += `<option value="${method.ID}">${method.Name}</option>`;
                    });
                    data.customers.forEach((customer) => {
                        customers += `<option value="${customer.ID}">${customer.Name}</option>`;
                    });
                }
                datatables();
            },
        });
    }

    //get init data
    getData();

    function datatables() {
        reportTable = $('#reportTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            searching: true,
            destroy: true,
            order: [[0, 'desc']],
            bAutoWidth: false,
            pageLength: 20,
            lengthMenu: [10, 20, 30, 50, 100],
            ajax: {
                url: 'ajax/ajaxGetReports',
                method: 'post',
                data: {
                    'fromDate': $('#from-date').val(),
                    'toDate': $('#to-date').val(),
                    'reportType': $('#report-type').val(),
                    'CustomerID': $('#customer').val(),
                    'MethodID': $('#method').val(),
                    'TypeID': $('#type').val(),
                    //'RealJob': $('#real').val(),
                    'Paid': $('#paid').val(),
                    'Search': $('#search').val(),
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
                },
            },
            columns: [
                {data: 'ID', name: 'ID', searchable: false},
                {
                    data: 'Name', name: 'Name', class: 'mw-120 box-relative', render: function (data, type, row, meta) {
                        let html = `<label title="${data}" class="label-value" >${data}</label><input style="display:none;" name="Name" class="form-control form-control-sm" type="text" value="${data}">`;
                        if (!md.mobile() && !md.phone() && !md.tablet()) {
                            html = `<button class="btn text-dark btn-xs box-br edit-cell"><i class="far fa-edit" style="pointer-events: none"></i></button>` + html;
                        }
                        return html;
                    }
                },
                {
                    data: 'Price',
                    name: 'Price',
                    class: 'mw-50 box-relative',
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return `<label title="${formatNumber(data)}" class="label-value">${formatNumber(data)}</label><input disabled style="display:none" class="form-control form-control-sm" name="Price" type="number" value="${data}">`;
                    }
                },
                {
                    data: 'PriceYen',
                    name: 'PriceYen',
                    class: 'mw-80 box-relative',
                    searchable: false,
                    render: function (data, type, row, meta) {
                        let html = `<label title="${formatNumber(data)}" class="label-value" >${formatNumber(data)}</label><input style="display:none;" class="form-control form-control-sm" name="PriceYen" type="number" value="${data}">`;
                        if (!md.mobile() && !md.phone() && !md.tablet()) {
                            html = `<button class="btn text-dark btn-xs box-br edit-cell"><i class="far fa-edit" style="pointer-events: none"></i></button>` + html;
                        }
                        return html;
                    }
                },
                {
                    data: 'StartDate',
                    name: 'StartDate',
                    class: 'mw-60 box-relative',
                    searchable: false,
                    render: function (data, type, row, meta) {
                        let html = `<label title="${data}" class="label-value" >${data}</label><input style="display:none;" data-date name="StartDate" class="form-control form-control-sm" type="text" value="${data}">`;
                        if (!md.mobile() && !md.phone() && !md.tablet()) {
                            html = `<button class="btn text-dark btn-xs box-br edit-cell"><i class="far fa-edit" style="pointer-events: none"></i></button>` + html;
                        }
                        return html;
                    }
                },
                {
                    data: 'Paydate',
                    name: 'Paydate',
                    class: 'mw-60 box-relative',
                    searchable: false,
                    render: function (data, type, row, meta) {
                        let html = `<label title="${data}" class="label-value" >${data}</label><input style="display:none;" data-date name="Paydate" class="form-control form-control-sm" type="text" value="${data}">`;
                        if (!md.mobile() && !md.phone() && !md.tablet()) {
                            html = `<button class="btn text-dark btn-xs box-br edit-cell"><i class="far fa-edit" style="pointer-events: none"></i></button>` + html;
                        }
                        return html;
                    }
                },
                {
                    data: 'Paid',
                    name: 'Paid',
                    class: 'mw-30 box-relative',
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return `<input type="checkbox"${data == 1 ? ' checked' : ''} name="Paid">`;
                    }
                },
                {
                    data: 'CustomerName',
                    name: 'CustomerName',
                    class: 'mw-120 box-relative',
                    searchable: false,
                    render: function (data, type, row, meta) {
                        let select = `<label title="${data}" class="label-value">${data}</label>`;
                        select += '<select style="display:none;" name="CustomerID" data-name="customer" class="form-control form-control-sm select2bs4">';
                        select += customers;
                        select += '</select>';
                        if (!md.mobile() && !md.phone() && !md.tablet()) {
                            select = '<button class="btn text-dark btn-xs box-br edit-cell"><i class="far fa-edit" style="pointer-events: none"></i></button>' + select;
                        }
                        let regex = new RegExp('value="' + row.CustomerID + '"', 'g');
                        return select.replace(regex, 'value="' + row.CustomerID + '" selected');
                    }
                },
                {
                    data: 'MethodName',
                    name: 'MethodName',
                    class: 'mw-120 box-relative',
                    searchable: false,
                    render: function (data, type, row, meta) {
                        let select = `<label title="${data}" class="label-value">${data}</label>`;
                        select += '<select style="display:none;" name="MethodID" data-name="method" class="form-control form-control-sm select2bs4">';
                        select += methods;
                        select += '</select>';
                        if (!md.mobile() && !md.phone() && !md.tablet()) {
                            select = '<button class="btn text-dark btn-xs box-br edit-cell"><i class="far fa-edit" style="pointer-events: none"></i></button>' + select;
                        }
                        let regex = new RegExp('value="' + row.MethodID + '"', 'g');
                        return select.replace(regex, 'value="' + row.MethodID + '" selected');
                    }
                },
                {
                    data: 'TypeName',
                    name: 'TypeName',
                    class: 'mw-120 box-relative',
                    searchable: false,
                    render: function (data, type, row, meta) {
                        let select = `<label title="${data}" class="label-value">${data}</label>`;
                        select += '<select style="display:none;" name="TypeID" data-name="type" class="form-control form-control-sm select2bs4">';
                        select += types;
                        select += '</select>';
                        if (!md.mobile() && !md.phone() && !md.tablet()) {
                            select = '<button class="btn text-dark btn-xs box-br edit-cell"><i class="far fa-edit" style="pointer-events: none"></i></button>' + select;
                        }
                        let regex = new RegExp('value="' + row.TypeID + '"', 'g');
                        return select.replace(regex, 'value="' + row.TypeID + '" selected');
                    }
                },
                {
                    data: 'Deadline',
                    name: 'Deadline',
                    class: 'mw-60 box-relative',
                    searchable: false,
                    render: function (data, type, row, meta) {
                        let html = `<label title="${data}" class="label-value" >${data}</label><input style="display:none;" data-date name="Deadline" class="form-control form-control-sm" type="text" value="${data}'">`;
                        if (!md.mobile() && !md.phone() && !md.tablet()) {
                            html = `<button class="btn text-dark btn-xs box-br edit-cell"><i class="far fa-edit" style="pointer-events: none"></i></button>` + html;
                        }
                        return html;
                    }
                },
                {
                    data: 'FinishDate',
                    name: 'FinishDate',
                    class: 'mw-60 box-relative',
                    searchable: false,
                    render: function (data, type, row, meta) {
                        let html = `<label title="${data}" class="label-value" >${data}</label><input style="display:none;" data-date name="FinishDate" class="form-control form-control-sm" type="text" value="${data}">`;
                        if (!md.mobile() && !md.phone() && !md.tablet()) {
                            html = `<button class="btn text-dark btn-xs box-br edit-cell"><i class="far fa-edit" style="pointer-events: none"></i></button>` + html;
                        }
                        return html;
                    }
                },
                {
                    data: 'Note',
                    name: 'Note',
                    class: 'mw-250 box-relative',
                    searchable: false,
                    render: function (data, type, row, meta) {
                        let html = `<label title="${data}" class="label-value">${data}</label><textarea style="display:none;" name="Note" class="form-control form-control-sm">${data}</textarea>`;
                        if (!md.mobile() && !md.phone() && !md.tablet()) {
                            html = `<button class="btn text-dark btn-xs box-br edit-cell"><i class="far fa-edit" style="pointer-events: none"></i></button>` + html;
                        }
                        return html;
                    }
                },
                {
                    data: 'Action',
                    name: 'Action',
                    class: 'mw-120',
                    searchable: false,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return `<a href="#" class="ml-2 btn btn-sm btn-danger delete" data-id="${row.ID}" data-toggle="modal" data-target="#confirm-modal" data-id=""><i class="fas fa-trash"></i></a>`;
                    }
                },
            ],
        })

        reportTable.on('draw.dt', function () {
            getTotalPrice(reportTable.search());
            destroyEventKeyUp();
            status_input = true;
        });

        reportTable.on('responsive-resize', function (e, datatable, columns) {
            let parent = $('.child');
            if (parent && fullRowEdit) {
                if (!md.mobile() && !md.phone() && !md.tablet()) {
                    hiddenEditBtn();
                }
            }
        });

        //disable default search
        $('.dataTables_filter input[type=search]').unbind();
        $('.dataTables_filter input[type=search]').on('keyup', function (event) {
            if (event.keyCode === 13) {
                reportTable.search(this.value).draw();
            }
        });
    }

    $(document).on('click', 'input[name=Paid]', function (e) {
        let currentRow = $(e.target).closest('tr');
        let rowMain = currentRow;
        if (currentRow.hasClass('child')) {
            rowMain = currentRow.prev();
        }

        let ID = rowMain.children()[0].innerText;
        let data;
        if ($(e.target).attr('checked') === 'checked') {
            $(e.target).removeAttr('checked');
            data = 0;
        } else {
            $(e.target).attr('checked', true);
            data = 1;
        }
        $.ajax({
            url: `ajax/ajaxUpdatePaidCell/${ID}`,
            method: 'put',
            data: {
                'Paid': data
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
            },
            success: function (data) {

            },
            error: function (res) {
            },
        });
    });

    //begin delete

    $('#confirm-modal').on('show.bs.modal', function (event) {
        let id = $(event.relatedTarget).attr('data-id');
        $(this).find('#confirm-button').attr('data-id', id);
    });

    $('#confirm-button').on('click', function (event) {
        let id = $(this).attr('data-id');
        addOverlay();
        doDelete(id);
    });

    function doDelete(id) {
        status_input = true;
        $.ajax({
            url: 'ajax/ajaxSoftJobDelete',
            method: 'post',
            data: {
                id: id,
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (data) {
                if (data['message'] === 'success') {
                    reportTable.ajax.reload(completeDelete, false);
                } else {
                    closeModal();
                    toastr.error('Could not delete the record. Please try again later.');
                }

            },
            error: function () {
                closeModal();
                toastr.error('Server error.');
            },
        });
    }

    function completeDelete() {
        getTotalPrice();
        closeModal();
        toastr.success('The record has been deleted.');
    }

    function closeModal() {
        removeOverlay();
        $('#confirm-modal').modal('toggle');
    }

    function addOverlay() {
        $('#confirm-modal .modal-content').append('<div class="overlay d-flex justify-content-center align-items-center"><i class="fas fa-2x fa-sync fa-spin"></i></div>');
    }

    function removeOverlay() {
        $('#confirm-modal .overlay').remove();
    }

    //end delete

    if (!md.mobile() && !md.phone() && !md.tablet()) {
        $('html').on('click', '.edit-cell', function (e) {
            if (status_input) {
                let element_hidden = $(e.target).find('~ input, ~ select,~ textarea');
                if (element_hidden.attr('name') !== 'Price') {
                    status_input = false;
                    $(e.target).hide();
                    $(e.target).find('+ .label-value').hide();
                    if (element_hidden.is('[data-date]')) {

                        attachDatePicker(element_hidden);
                        element_hidden.show().focus().select();
                        element_hidden.on('keyup', (e) => {
                            if (e.key === 'Enter' || e.keyCode === 13) {
                                return saveCell(e.target);
                            }
                        });
                    } else {
                        if (element_hidden.is('select')) {
                            $(element_hidden).select2({
                                width: '100%',
                            });
                            $(element_hidden).select2('open');
                            element_hidden.show().focus().select();
                            element_hidden.on('select2:close', (e) => {
                                return saveCell(e.target);
                            });
                        } else {
                            element_hidden.show().focus().select();
                            element_hidden.on('keyup', (e) => {
                                if (e.key === 'Enter' || e.keyCode === 13) {
                                    return saveCell(e.target);
                                }
                            });
                        }
                    }
                }
            } else {
                detectOneEditCellAndSave();
            }
        });
    }

    $('#reportTable').on('click', 'td.dtr-control', function () {
        let parent = $(this).parent();
        if (!parent.hasClass('parent')) {
            hideEditRow(parent);
        }
    });

    function hideEditRow(currentParent) {
        if (!md.mobile() && !md.phone() && !md.tablet()) {
            showEditBtn();
        }
        fullRowEdit = false;
        $(currentParent).find('label:not(".checkbox-label")').show();
        $(currentParent).find('input:not([type=checkbox]), select, textarea').each(function () {
            if ($(this).is('[data-date]')) {
                detachDatePicker(this);
            }
            $(this).removeClass('is-invalid');
        }).hide();
        $(currentParent).find('select').each(function () {
            detachSelect2(this);
        });

        if (currentParent.hasClass('child')) {
            let row = currentParent.prev();
            $.each(row, function (index, parent) {
                $(parent).find('label:not(".checkbox-label")').show();
                $(parent).find('input:not([type=checkbox]), select, textarea').each(function () {
                    if ($(this).is('[data-date]')) {
                        detachDatePicker(this);
                    }
                    $(this).removeClass('is-invalid');
                }).hide();
                $(parent).find('select').each(function () {
                    detachSelect2(this);
                });
            });
        }

    }

    function attachSelect2(ele) {
        $(ele).select2();
    }

    function detachSelect2(ele) {
        if ($(ele).data('select2')) {
            $(ele).select2('destroy');
        }
    }

    function attachDatePicker(ele) {
        $(ele).inputmask({
            mask: "9999-99-99",
            alias: "date",
            placeholder: 'yyyy-mm-dd',
            insertMode: false,
        }).datepicker({
            dateFormat: 'yy-mm-dd'
        });
    }

    function detachDatePicker(ele) {
        $(ele).datepicker('destroy');
    }

    function formatNumber(num) {
        return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    }

    function setValueElement(element, value) {
        if (element.is('[data-date]')) {
            detachDatePicker($(element));
            return element.val(value);
        }
        if (element.is('input:not([data-date])')) {
            return element.attr('value', value);
        }
        if (element.is('select')) {
            element.find(`[selected]`).removeAttr('selected');
            return element.find(`option[value=${value}]`).attr('selected', true);
        }
        if (element.is('textarea')) {
            return element.val(value);
        }
    }

    function saveCell(e) {
        let element = $(e);
        if (element.hasClass('is-invalid')) {
            element.removeClass('is-invalid');
        }
        if (element.data('select2')) {
            element.select2('destroy');
        }

        let currentRow = element.closest('tr');
        let rowMain = currentRow;
        if (currentRow.hasClass('child')) {
            rowMain = currentRow.prev();
        }

        let ID = rowMain.children()[0].innerText;

        let col_name = ['ID', element.attr('name')];
        let col_value = [ID, element.val()];
        var data = combineKeyValue(col_value, col_name);
        element.hide();
        $.ajax({
            url: `ajax/ajaxUpdateCell/${ID}/${element.attr('name')}`,
            method: 'put',
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
            },
            success: function (response) {
                switch (response.code) {
                    case 'normal':
                        setValueElement(element, response.attr);
                        element.prev().text(response.attr);
                        element.prev().attr('title', response.attr);
                        element.prev().prev().show();
                        element.prev().show();
                        updateCellDataTable(rowMain, element.attr('name'), response.attr);
                        if (element.attr('name') === 'StartDate' || element.attr('name') === 'Paydate') {
                            getTotalPrice();
                        }
                        break;
                    case 'price':
                        setValueElement(element, response.priceYen);
                        element.prev().text(formatNumber(response.priceYen));
                        element.prev().attr('title', response.priceYen);
                        $(rowMain).find('input[name=Price]').prev().text(formatNumber(response.price));
                        $(rowMain).find('input[name=Price]').prev().attr('title', formatNumber(response.price));
                        $(rowMain).find('input[name=Price]').attr('value', response.price);
                        element.prev().prev().show();
                        element.prev().show();
                        updatePriceCell(rowMain, response.price, response.priceYen);
                        getTotalPrice();
                        break;
                    case 'select':
                        setValueElement(element, response.ID);
                        element.prev().text(response.Name);
                        element.prev().attr('title', response.Name);
                        element.prev().prev().show();
                        element.prev().show();
                        updateCellDataTable(rowMain, element.attr('name'), response.Name, response.ID);
                        $(element).unbind("select2:close");
                        break;
                    default:
                        setValueElement(element, response.attr);
                        element.prev().prev().show();
                        element.prev().show();
                        updateCellDataTable(rowMain, element.attr('name'), response.attr);
                }
                if (response.code !== 'select') {
                    element.unbind('keyup');
                }
                status_input = true;

            },
            error: function (res) {
                if (res) {
                    element.show();
                    element.addClass('is-invalid');
                    element.focus().select();
                }
            },
        });

    }

    function updateCellDataTable(row, name_col, value_col, id = null) {
        let rowData = reportTable.row($(row)).data();
        switch (name_col) {
            case 'TypeID':
                rowData["TypeID"] = id;
                rowData["TypeName"] = value_col;
                break;
            case 'CustomerID':
                rowData["CustomerID"] = id;
                rowData["CustomerName"] = value_col;
                break;
            case 'MethodID':
                rowData["MethodID"] = id;
                rowData["MethodName"] = value_col;
                break;
            case 'Paydate':
                $(row).find('[name=Paydate]').attr('id', '');
                rowData["Paydate"] = value_col;
                break;
            case 'FinishDate':
                $(row).find('[name=FinishDate]').attr('id', '');
                rowData["FinishDate"] = value_col;
                break;
            case 'StartDate':
                $(row).find('[name=StartDate]').attr('id', '');
                rowData["StartDate"] = value_col;
                break;
            case 'Deadline':
                $(row).find('[name=Deadline]').attr('id', '');
                rowData["Deadline"] = value_col;
                break;
            default:
                rowData[`${name_col}`] = value_col;
        }
        reportTable.row($(row)).data(rowData);
    }

    function updatePriceCell(row, price, priceYen) {
        let rowData = reportTable.row($(row)).data();
        rowData['PriceYen'] = priceYen;
        rowData['Price'] = price;
        reportTable.row($(row)).data(rowData);
    }

    function combineKeyValue(value, key) {
        return value.reduce(function (data, field, index) {
            data[key[index]] = field;
            return data;
        }, {});
    }

    function detectOneEditCellAndSave() {
        if ($('#reportTable tbody').find('input:not([name=Paid]):visible , select:visible , textarea:visible')[0]) {
            saveCell($('#reportTable tbody').find('input:not([name=Paid]):visible , select:visible , textarea:visible'));
        }
    }

    function hiddenEditBtn() {
        $('.box-br').hide();
        status_input = false;
        destroyEventKeyUp();
    }

    function showEditBtn() {
        $('.box-br').show();
        status_input = true;
        destroyEventKeyUp();
    }

    function destroyEventKeyUp() {
        if ($('#reportTable tbody').find('input:not([name=Paid]):visible , select:visible , textarea:visible')) {
            $('#reportTable tbody').find('input:not([name=Paid]):visible , select:visible , textarea:visible').each(
                function (index, element) {
                    $(element).unbind("keyup");
                    // if(element.is(['data-date'])){
                    //     detachDatePicker(element);
                    // }
                }
            );
        }
    }
});
