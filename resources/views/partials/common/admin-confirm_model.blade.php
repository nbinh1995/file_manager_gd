<div class="modal fade" id="confirm-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{$model_title??__('Confirm modal')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>{{$model_message??__('Confirm message')}}</p>
            </div>
            <div class="modal-footer justify-content-between">
                <button id="cancel-button" type="button" class="btn btn-default" data-dismiss="modal">{{$model_cancel??__('Cancel')}}</button>
                <button id="confirm-button" type="button" class="btn btn-primary">{{$model_confirm??__('Confirm')}}</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
