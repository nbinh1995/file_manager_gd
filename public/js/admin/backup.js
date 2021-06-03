const URL_SHOW = "backup/list";
const URL_BACKUP = "backup/manual";
const URL_DOWNLOAD = "backup/download";
const URL_DESTROY = "backup/destroy";
const ID_TABLE = "#common-table";
const URL_UPDATE_SETTINGS = 'ajax/ajaxUpdateKeepDays';

var backup = backup || {};
backup.showList = function () {
    return $(ID_TABLE).DataTable({
        responsive: true,
        autoWidth: false,
        processing: true,
        serverSide: true,
        destroy: true,
        searching: false,
        order: [[4, 'desc']],
        ajax: {
            url: URL_SHOW,
            type: "GET",
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {
                data: null,
                name: 'Checked',
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    return `<input name="checked" class="check-file" type="checkbox" data-value="${row.filename}">`;
                }
            },
            {data: 'filename', name: 'filename'},
            {
                data: 'type', name: 'type', render: function (data, type, row, meta) {
                    if (data != null && data === 'Manual') {
                        return `<label class="badge badge-pill badge-primary">${data}</label>`;
                    }
                    return `<label class="badge badge-pill badge-success">${data}</label>`;
                }
            },
            {data: 'modified', name: 'modified'},
            {data: 'size', name: 'size'},
            {data: 'Action', name: 'Action', orderable: false, searchable: false},
        ],
        lengthMenu: [10, 20],
    });
}

backup.dowloadItem = function (element) {
    let name = $(element).data("name");
    let token = $("meta[name='csrf-token']").attr("content");
    return $.ajax({
        url: URL_DOWNLOAD,
        method: "POST",
        data: {
            _token: token,
            name: name
        },
        xhr: function () {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 2) {
                    if (xhr.status == 200) {
                        xhr.responseType = "blob";
                    } else {
                        xhr.responseType = "text";
                    }
                }
            };
            return xhr;
        },
        success: function (data, status, xhr) {
            let filename = "";
            let disposition = xhr.getResponseHeader('Content-Disposition');
            if (disposition && disposition.indexOf('attachment') !== -1) {
                let filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                let matches = filenameRegex.exec(disposition);
                if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
            }
            let a = document.createElement('a');
            let url = window.URL.createObjectURL(data);
            a.href = url;
            a.download = filename.replace('UTF-8', '');
            ;
            document.body.append(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
        },
        error: function (xhr, status, error) {
            toastr["success"](`${xhr.responseText}`);
        },
    });
}

backup.backupItem = function (element, message = "Backup Success!") {
    let html = $(element).html();
    return $.ajax({
        type: "GET",
        url: URL_BACKUP,
        beforeSend: function () {
            let loading = `<i class="fas fa-sync fa-spin mr-2"></i>Loading...`;
            $(element).empty();
            $(element).html(loading);
            $(element).attr('disabled', true);
        },
        success: function (data) {
            backup.tableName.ajax.reload();
            toastr["success"](`${message}`);
        },
        error: function (xhr, status, error) {
        },
        complete: function () {
            $(element).empty();
            $(element).html(html);
            $(element).attr('disabled', false);
        },
    });
}

backup.destroyItem = function (element, message = "Removed Success!") {
    let token = $("meta[name='csrf-token']").attr("content");
    let name = $(element).data("name");
    return $.ajax({
        type: "DELETE",
        url: URL_DESTROY,
        data: {
            _token: token,
            name: name
        },
        success: function (data) {
            backup.tableName.ajax.reload();
            toastr["success"](`${message}`);
        },
        error: function (xhr, status, error) {
        },
    });
}

backup.destroySelected = function (message = "Removed Success!") {
    let token = $("meta[name='csrf-token']").attr("content");
    let name = [];
    $.each($('.check-file'), function (index, ele) {
        if ($(ele).is(':checked')) {
            name.push($(ele).attr('data-value'));
        }
    });
    return $.ajax({
        type: "DELETE",
        url: URL_DESTROY,
        data: {
            _token: token,
            name: name
        },
        success: function (data) {
            backup.tableName.ajax.reload();
            toastr["success"](`${message}`);
        },
        error: function (xhr, status, error) {
            toastr["error"](`Server error. Please try again.`);
        },
    });
}

backup.init = function () {
    backup.tableName = backup.showList();
};

$(document).ready(function () {
    backup.init();

    $(document).on("click", ".download", function (e) {
        backup.dowloadItem(e.target);
    });

    $(document).on("click", ".backup", function (e) {
        backup.backupItem(e.target);
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
                    backup.destroyItem(e.target);
                }
            },
        });
    });

    $('.update-keep-days').on('click', function (event) {
        event.preventDefault();
        $.ajax({
            url: URL_UPDATE_SETTINGS,
            method: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
            },
            data: {
                'keep-days': $('.keep-days').val(),
                '_method': 'put',
            },
            success: function (data, responseText, jqXHR) {
                if ('success' === data.status && 200 === jqXHR.status) {
                    toastr.success(data.message);
                    $('.keep-days').attr('title', $('.keep-days').val());
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

    $('html body').on('change', '.check-all', function (event) {
        event.preventDefault();
        if (this.checked === true) {
            $('.check-file').prop('checked', true);
            $('.check-all').prop('checked', true);
        } else {
            $('.check-file').prop('checked', false);
            $('.check-all').prop('checked', false);
        }
        multiRemove();
    });

    $('html body').on('change', '.check-file', function (event) {
        event.preventDefault();
        multiRemove();
    })

    $('html body').on('click', '.remove-selected', function (event) {
        event.preventDefault();
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
                    backup.destroySelected();
                }
            },
        });
    });

    backup.tableName.on('draw', function (event) {
        $('.check-all').prop('checked', false);
        $('.multi-remove').fadeOut();
    });
});

function multiRemove() {
    if (!$('.check-file').is(':checked')) {
        $('.multi-remove').fadeOut();
    } else if (!$('.multi-remove').is(':visible')) {
        $('.multi-remove').fadeIn();
    }
}