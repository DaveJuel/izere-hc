<!--EDIT MODAL-->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">EDIT</h4><span id="update-notification"></span>
            </div>
            <div class="modal-body">                
                <input type="hidden" id="update-instance-id" name="table_data" value="">
                <div id="modal-form-holder">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" id="btn_trigger" onclick="updateOccurence()" class="btn btn-success">Save changes</button>
            </div>
        </div>
    </div>
</div>
<!--END EDIT MODAL-->
<!--REMOVE MODAL-->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">DELETE</h4><span id="delete-notification"></span>
            </div>
            <div class="modal-body" id="deleteModal_body">
                <input type="hidden" id="delete-instance-id" value="">
                Are you sure you want to delete? 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="deleteOccurence()" data-dismiss="modal">Delete</button>
            </div>
        </div>
    </div>
</div>
<!--END REMOVE MODAL-->