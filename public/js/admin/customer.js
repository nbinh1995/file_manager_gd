const TABLE = "Customer";
const SIZE_MODE = "modal-md";
const ID_FORM_CREATE = "form-create";
const CLASS_FORM_EDIT = "form-edit";

const URL_SHOW = "customers/list";
const ID_TABLE = "#common-table";
const ID_MODAL = "#common-modal";
let status_input = true;
let unpaid = 0;
let advanced_sort = 0;
let customerTable;

const URL_UPDATE_SETTINGS = 'ajax/ajaxUpdateUnpaid';
const URL_UNPAID_COUNT = 'ajax/ajaxGetUnPaidCount';

function formatNumber(num) {
    return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
}

function formatTruncateTextarea(note, ID) {
    let arrayNote = note.split('<br>');
    let html = `<div><label class="label-value" title="${arrayNote[0]}">${arrayNote[0]}</label>`
    html += arrayNote.length !== 1 ? `<p class='collapse' id='viewmore-${ID}'>${note}</p>
  <p><a href='javascript:void(0)' class='view-more' data-toggle="collapse" data-target="#viewmore-${ID}">More...</a></p>` : '';
    html += '</div>';
    return html;
}

function formatSaveTextarea(note) {

    return note.search(/[a-zA-Z0-9]/g) === -1 ? '' : note.replace(/\n/g, '<br>');
}

function formatShowTextarea(note) {
    return note.replace(/<br\s*[\/]?>/g, '\n');
}

function detectOneEditCellAndSave() {
    if ($(`${ID_TABLE} tbody`).find('input:visible, textarea:visible')[0]) {
        saveCell($(`${ID_TABLE} tbody`).find('input:visible , textarea:visible'));
    }
}

function combineKeyValue(value, key) {
    return value.reduce(function (data, field, index) {
        data[key[index]] = field;
        return data;
    }, {});
}

function setValueElement(element, value) {
    if (element.is('input')) {
        return element.attr('value', value);
    }
    if (element.is('textarea')) {
        return element.text(value);
    }
}

function saveCell(e) {
    let element = $(e);

    if (element.hasClass('is-invalid')) {
        element.removeClass('is-invalid');
    }

    let currentRow = element.closest('tr');
    let rowMain = currentRow;
    if (currentRow.hasClass('child')) {
        rowMain = currentRow.prev();
    }
    let ID = customerTable.row($(rowMain)).data().ID;

    let col_name = ['ID', element.attr('name')];
    let col_value = element.is('textarea') ? [ID, formatSaveTextarea(element.val())] : [ID, element.val()];
    var data = combineKeyValue(col_value, col_name);
    element.hide();
    $('.save').hide();
    $.ajax({
        url: `customers/${ID}/${element.attr('name')}`,
        method: 'put',
        data: data,
        headers: {
            'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
        },
        success: function (response) {
            setValueElement(element, response.attr);
            element.prev().text(response.attr);
            element.prev().attr('title', response.attr);
            element.prev().prev().show();
            element.prev().show();
            updateCellDataTable(rowMain, element.attr('name'), response.attr);
            element.unbind('keyup');
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

function updateCellDataTable(row, name_col, value_col) {
    let rowData = customerTable.row($(row)).data();
    rowData[`${name_col}`] = value_col;
    customerTable.row($(row)).data(rowData);
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
    if ($(`${ID_TABLE} tbody`).find('input:visible')) {
        $(`${ID_TABLE} tbody`).find('input:visible').each(
            function (index, element) {
                $(element).unbind("keyup");
                $(element).hide();
                $(element).prev().show();
            }
        );
    }
}

var customer = customer || {};

customer.setModal = function (titleModal, btnName, sizeModal, idForm, contentModal) {
    $(ID_MODAL).find(".modal-title").text(titleModal);
    $(ID_MODAL).find(".modal-dialog").addClass(sizeModal);
    $(ID_MODAL).find(".modal-body").text(contentModal);
    $(ID_MODAL).find("button[type='submit']").text(btnName);
    $(ID_MODAL).find("button[type='submit']").attr("form", idForm);
}

customer.showList = function () {
    return $(ID_TABLE).DataTable({
        responsive: true,
        autoWidth: false,
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: {
            //url: `${URL_SHOW}/?unpaid=${unpaid}`,
            url: URL_SHOW,
            type: "GET",
            data: {
                unpaid: unpaid,
                advanced_sort: advanced_sort,
            },
            complete: function () {
                $('.loading-process').hide();
            },
        },
        columns: [
            {data: 'ID', name: 'ID', searchable: false},
            {
                data: 'Name',
                name: 'Name',
                class: 'mw-120 box-relative',
                render: function (data, type, row, meta) {
                    let html = `<label title="${data}" class="label-value" >${data}</label><input style="display:none;" name="Name" class="form-control" type="text" value="${data}">`;
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
                    let html = `${data ? formatTruncateTextarea(data, row.ID) : ''}
                    <textarea name="Note" class="form-control text-area" style='display:none;overflow:hidden;padding-right:20px;'>${data ? formatShowTextarea(data) : ''}</textarea>
                    <button class='btn btn-info btn-xs note-btn save'>
                        <i class=' fas fa-paper-plane' style="pointer-events: none"></i>
                    </button>
                    `;
                    if (!md.mobile() && !md.phone() && !md.tablet()) {
                        html = `<button class="btn text-dark btn-xs box-br edit-cell"><i class="far fa-edit" style="pointer-events: none"></i></button>` + html;
                    }
                    return html;
                }
            },
            {
                data: 'UnPaid',
                name: 'UnPaid',
                class: 'mw-120 box-relative',
                searchable: false,
                render: function (data, type, row, meta) {
                    let html = `<label title="${data != null ? formatNumber(data) : 0}" class="label-value" >${data != null ? formatNumber(data) : 0}</label>`;
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
                    return `<button class='btn btn-danger remove btn-xs' data-url='${row.ID}'><i class='far fa-trash-alt'
                style='pointer-events: none'></i> Remove</button>`;
                }
            },
        ],
        lengthMenu: [10, 20],
    });
}

customer.createItem = function (url_create) {
    return $.ajax({
        url: url_create,
        method: "GET",
        dataType: "json",
        success: function (data) {
            let tbody = `${ID_MODAL} .modal-body`;
            $(tbody).html(data.html);
        },
        error: function (xhr, status, error) {
        },
    });
}

customer.storeItem = function (element, message = "Created Success!") {
    let url = $(element).attr("action");
    let data = new FormData(element);

    return $.ajax({
        url: url,
        method: "POST",
        data: data,
        dataType: "json",
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function () {
            $('.loading-process').show();
        },
        success: function (data) {
            if (unpaid == '1') {
                $('#option-list option[value = 0]').attr('selected', true);
                $('#option-list option[value = 1]').removeAttr('selected');
                unpaid = 0;
            }
            customerTable.clear();
            customerTable = customer.showList();
            toastr.options = {positionClass: "toast-bottom-right"};
            toastr["success"](`${message}`);
        },
        error: function (xhr, status, error) {
            $('.loading-process').hide();
        },
    });
}

customer.destroyItem = function (id, message = "Removed Success!") {
    status_input = true;
    let token = $("meta[name='csrf-token']").attr("content");
    return $.ajax({
        type: "DELETE",
        url: `customers/${id}`,
        data: {
            _token: token,
        },
        beforeSend: function () {
            $('.loading-process').show();
        },
        success: function (data) {
            customerTable.clear();
            customerTable = customer.showList();
            toastr.options = {
                positionClass: "toast-bottom-right",
            };
            toastr["success"](`${message}`);
        },
        error: function (xhr, status, error) {
            $('.loading-process').hide();
        },
    });
}
customer.init = function () {
    customerTable = customer.showList();
    customerTable.on('draw.dt', function (event) {
        destroyEventKeyUp();
        status_input = true;
    });
};

$(document).ready(function () {
    customer.init();

    $(document).on('focus input', '.text-area', function () {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
    if (!md.mobile() && !md.phone() && !md.tablet()) {
        $('html').on('click', '.edit-cell', function (e) {
            if (status_input) {
                let element_hidden = $(e.target).find('~ input,~ textarea');
                $(e.target).hide();
                $(e.target).find('+ div, + label').hide();
                element_hidden.show().focus().select();
                status_input = false;
                if (element_hidden.is('textarea')) {
                    $(element_hidden).find('~ .save').show();
                    $(element_hidden).find('~ .save').on('click', function () {
                        return saveCell(element_hidden);
                    })
                } else {
                    element_hidden.on('keyup', (e) => {
                        if (e.key === 'Enter' || e.keyCode === 13) {
                            return saveCell(e.target);
                        }
                    });
                }

            } else {
                detectOneEditCellAndSave();
            }
        });
    }

    $(document).on('click', '.view-more', function (e) {
        if ($(e.target).attr('aria-expanded') == 'true') {
            $(e.target).closest('div').find('.label-value').hide();
        } else {
            $(e.target).closest('div').find('.label-value').show();
        }
    });

    $(document).on('change', '#option-list', function (e) {
        switch ($(e.target).val()) {
            case '1':
                unpaid = 1;
                break;
            default:
                unpaid = 0;
        }
        customerTable.clear();
        customerTable = customer.showList();
    });

    $(ID_TABLE).on('click', 'td.dtr-control', function () {
        let parent = $(this).parent();
        if (!parent.hasClass('parent')) {
            showEditBtn();
        }
    });


    $(document).on("click", ".create", function (e) {
        customer.setModal(
            `Create ${TABLE}`,
            `Add ${TABLE}`,
            SIZE_MODE,
            ID_FORM_CREATE,
            ""
        );
        customer.createItem($(e.target).data("url"));
        $(ID_MODAL).modal("show");
    });

    $(document).on("click", ".remove", function (e) {
        bootbox.confirm({
            message: "Are you sure?",
            buttons: {
                confirm: {
                    label: "Yes",
                    className: "btn-success",
                },
                cancel: {
                    label: "No",
                    className: "btn-danger",
                },
            },
            callback: function (result) {
                if (result) {
                    customer.destroyItem($(e.target).data("url"));
                }
            },
        });

    });

    $(document).on("submit", `#${ID_FORM_CREATE}`, function (e) {
        e.preventDefault();
        customer.storeItem(e.target).done(function (data) {
            $(ID_MODAL).modal("hide");
        }).fail(function (jqXHR, textStatus, errorThrown) {
            let data = JSON.parse(jqXHR.responseText);
            let tbody = `${ID_MODAL} .modal-body`;
            $(tbody).html(data.html);
        });

    });

    $('.update-unpaid').on('click', function (event) {
        event.preventDefault();
        $.ajax({
            url: URL_UPDATE_SETTINGS,
            method: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
            },
            data: {
                'unpaid-amount': $('.unpaid-amount').val(),
                '_method': 'put',
            },
            success: function (data, responseText, jqXHR) {
                if ('success' === data.status && 200 === jqXHR.status) {
                    $('.unpaid-amount').attr('title', formatNumber($('.unpaid-amount').val()));
                    toastr.success(data.message);
                    if (location.search.indexOf('unpaid=on') >= 0) {
                        customer.showList();
                    }
                    updateBadge();
                    if (unpaid == '1') {
                        customerTable.clear();
                        customer.showList();
                    }
                } else {
                    toastr.error(data.message);
                }
            },
            error: function (response) {
                let message = JSON.parse(response.responseText);
                if (message.message) {
                    toastr.error(message.message);
                } else {
                    toastr.error('Server error. Please try again.');
                }
            },
        });
    });

    function updateBadge() {
        $.ajax({
            url: URL_UNPAID_COUNT,
            method: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
            },
            success: function (data, responseText, jqXHR) {
                if (data.unpaid_count && data.unpaid_count > 0) {
                    let badge = `<span class="badge badge-danger badge-pill badge-sidebar position-absolute">${data.unpaid_count}</span>`;
                    $('.fa-users').empty().append(badge);
                } else {
                    $('.fa-users').empty();
                }
            },
        });
    }

    $('#sort-option').on('change', function (event) {
        advanced_sort = this.value;
        customerTable.clear();
        customerTable = customer.showList();
    });
});
