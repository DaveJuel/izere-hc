<!--EDIT MODAL-->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">EDIT</h4>
            </div>
            <div class="modal-body">                
                <input type="hidden" id="instance_value" name="table_data" value="">
                <input type="hidden" id="field_value" value="<?php if(isset($_REQUEST['article'])) echo $_REQUEST['article']; ?>">               
                <?php
                if (isset($_REQUEST['article'])) {
                    //form building                         
                    $main->formBuilder($_REQUEST['article'], "update", null);
                    echo $main->status;
                }
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" id="btn_trigger" class="btn btn-success">Save changes</button>
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
                <h4 class="modal-title" id="myModalLabel">DELETE</h4>
            </div>
            <div class="modal-body" id="deleteModal_body">
                <input type="hidden" id="instance-value" value="">
                <input type="hidden" id="instance-id" value="">
                Are you sure you want to delete ...? 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>
<!--END REMOVE MODAL-->