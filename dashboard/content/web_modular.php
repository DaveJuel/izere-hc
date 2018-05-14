<!--DONOR APPLICATION MODAL-->
<div class="modal fade" id="donorApplicationModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">New application</h4>
                <p>Fill this form for you to be a part of blood donors. If you're already registered <a href="../admin/views/login.php">click here</a></p>
            </div>
            <div class="modal-body" id="deleteModal_body">
                <form role="form" onsubmit="return validateDonorForm()" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">                    
                    <div class="form-group">
                        <div class="input-group" >
                            <input name="donate_name" type="text" id="donate_name" class="form-control" placeholder="Full names"> 
                        </div>                       
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <input name="donate_phone" type="text" id="donate_phone" class="form-control" placeholder="Phone">
                        </div>                       
                    </div>
                    <div class="form-group">
                        <div class="input-group" id="locationField">
                            <input name="donate_address" type="text" onFocus="geolocate()" class="form-control" id="autocomplete" placeholder="Enter your address">
                        </div>                        
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <input name="donate_age" type="text" id="donate_age" class="form-control" placeholder="Type your age">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <select name="donate_gender" class="form-control">
                                <option value="0">Select gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>                           
                        </div>
                    </div>
                    <div class="col-md-4">
                        <span id="notification-par"><?php echo $user->status; ?></span>
                    </div>
                    <div class="modal-footer">
                        <input type="submit" class="button  btn_blue btn_full" name="action" value="APPLY"><br/>                       
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>