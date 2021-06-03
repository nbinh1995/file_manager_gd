const TABLE = "JMethod";
const SIZE_MODE = "modal-md";
const ID_FORM_CREATE = "form-create";
const CLASS_FORM_EDIT = "form-edit";

const URL_SHOW = "jmethods/list";
const ID_TABLE = "#common-table";
const ID_MODAL = "#common-modal";
let jmethodTable;
var jmethod = jmethod || {};
jmethod.setModal = function (titleModal, btnName, sizeModal, idForm, contentModal) {
    $(ID_MODAL).find(".modal-title").text(titleModal);
    $(ID_MODAL).find(".modal-dialog").addClass(sizeModal);
    $(ID_MODAL).find(".modal-body").text(contentModal);
    $(ID_MODAL).find("button[type='submit']").text(btnName);
    $(ID_MODAL).find("button[type='submit']").attr("form", idForm);
}

jmethod.showList = function () {
    return $(ID_TABLE).DataTable({
        responsive: true,
        autoWidth: false,
        processing: true,
        serverSide: true,
        order: [[0, 'desc']],
        destroy: true,
        ajax: {
            url: URL_SHOW,
            type: "GET",
            complete: function() {
                $('.loading-process').hide();
            },
        },
        columns: [
            { data: 'ID', name: 'ID'},
            { data: 'Name', name: 'Name'},
            { data: 'Action', name: 'Action', orderable: false, searchable: false },
        ],
        lengthMenu: [10, 20],
    });
}

jmethod.createItem = function (url_create) {
    return $.ajax({
        url: url_create,
        method: "GET",
        dataType: "json",
        beforeSend:function (){
            $('.loading-process').show();
        },
        complete: function() {
            $('.loading-process').hide();
        },
        success: function (data) {
            let tbody = `${ID_MODAL} .modal-body`;
            $(tbody).html(data.html);
        },
        error: function (xhr, status, error) { },
    });
}

jmethod.storeItem = function (element, message = "Created Success!") {
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
        beforeSend:function (){
            $('.loading-process').show();
        },
        success: function (data) {
            jmethodTable.clear();
            jmethodTable.ajax.reload();
            toastr.options = { positionClass: "toast-bottom-right" };
            toastr["success"](`${message}`);
        },
        error: function (xhr, status, error) {
            $('.loading-process').hide();
        },
    });
}

jmethod.editItem = function (url_edit) {
    return $.ajax({
        url: url_edit,
        method: "GET",
        dataType: "json",
        success: function (data) {
        },
        error: function (xhr, status, error) { },
    });
}

jmethod.updateItem = function (element, message = "Updated Success!") {
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
        success: function (data) {
            toastr.options = { positionClass: "toast-bottom-right" };
            toastr["success"](`${message}`);
        },
        error: function (xhr, status, error) { },
    });
}

jmethod.destroyItem = function (url_delete, message = "Removed Success!") {
    let token = $("meta[name='csrf-token']").attr("content");
    return $.ajax({
        type: "DELETE",
        url: url_delete,
        data: {
            _token: token,
        },
        beforeSend:function (){
            $('.loading-process').show();
        },
        success: function (data) {
            jmethodTable.clear();
            jmethodTable.ajax.reload();
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
jmethod.init = function () {
    jmethodTable = jmethod.showList();
};

$(document).ready(function () {
    jmethod.init();

    $(document).on("click", ".create", function (e) {
        jmethod.setModal(
            `Create ${TABLE}`,
            `Add ${TABLE}`,
            SIZE_MODE,
            ID_FORM_CREATE,
            ""
        );

        jmethod.createItem($(e.target).data("url"));
        $(ID_MODAL).modal("show");
    });

    $(document).on("click", ".edit", function (e) {
        let row = $(`#${TABLE}-${$(e.target).data('id')}`);
        jmethod.editItem($(e.target).data("url")).done(function (data) {
            row.find(`.Name-${TABLE}`).empty();
            row.find(`.Name-${TABLE}`).html(data.html);
            $('.edit').hide();
            row.find('input:not([type=hidden])').first().focus();
        });

    });

    $(document).on("click", ".back", function (e) {
        e.preventDefault();

        let row = $(`#${TABLE}-${$(e.target).data('id')}`);
        row.find(`.Name-${TABLE}`).empty();
        row.find(`.Name-${TABLE}`).html(`${$(e.target).data('name')}`);
        $('.edit').show();
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
                    jmethod.destroyItem($(e.target).data("url"));
                }
            },
        });

    });

    $(document).on("submit", `#${ID_FORM_CREATE}`, function (e) {
        e.preventDefault();
        jmethod.storeItem(e.target).done(function (data) {
            $(ID_MODAL).modal("hide");
        }).fail(function (jqXHR, textStatus, errorThrown) {
            let data = JSON.parse(jqXHR.responseText);
            let tbody = `${ID_MODAL} .modal-body`;
            $(tbody).html(data.html);
        });
    });

    $(document).on("submit", `.${CLASS_FORM_EDIT}`, function (e) {
        e.preventDefault();

        let row = $(`#${TABLE}-${$(e.target).find('[name=id]').val()}`);
        jmethod.updateItem(e.target).done(function (data) {
            row.find(`.Name-${TABLE}`).empty();
            row.find(`.Name-${TABLE}`).html(data.Name);
            $('.edit').show();
        }).fail(function (jqXHR, textStatus, errorThrown) {
            let data = JSON.parse(jqXHR.responseText);
            row.find(`.Name-${TABLE}`).empty();
            row.find(`.Name-${TABLE}`).html(data.html);
        });
    });
});
