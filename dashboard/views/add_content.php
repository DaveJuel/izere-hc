<?php
require '../includes/interface.php';
?>
<?php $title = 'Add content' ?>
<?php ob_start(); ?>
<div class="col-md-8">
    <form class="form-horizontal" id="content-add" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>" role="form">
        <div class="form-group">
            <label class="control-label">Title</label>
            <input type="text" required class="form-control" name="subject_title" id="content-title" placeholder="Set the subject title">
        </div>
        <div class="form-group">
            <label class="control-label">Number of attributes</label>
            <input type="number" required class="form-control" onblur="addAttribute(this);" name="subject_count_attr"  id="content-count-attr" placeholder="Number of attributes">
        </div>
        <div class="form-inline"  >
            <label id="attr-form-label">Define attributes</label>
            <div id="attributes">

            </div>
        </div>
        <div class="form-group">
            <label>Commenting</label>
            <div>
                <label class="checkbox-inline">
                    <input type="radio" name="subject_commenting" value="true">
                    Enabled </label>
                <label class="checkbox-inline">
                    <input type="radio" name="subject_commenting" value="false">
                    Disabled </label>
            </div>
        </div>
        <div class="form-group">
            <label>Likes</label>
            <div>
                <label class="checkbox-inline">
                    <input type="radio" name="subject_likes" value="true">
                    Enabled </label>
                <label class="checkbox-inline">
                    <input type="radio" name="subject_likes" value="false">
                    Disabled </label>
            </div>
        </div>		
        <div class="form-group">
            <label>Display views</label>
            <div>
                <label class="checkbox-inline">
                    <input type="radio" name="subject_display_views" value="true">
                    Enabled </label>
                <label class="checkbox-inline">
                    <input type="radio" name="subject_display_views" value="false">
                    Disabled </label>
            </div>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-dark" name="action" value="Add subject">			
        </div>
        <div class="form-group"><?php echo $subject->status; ?></div> 
    </form>
</div>
<?php $content = ob_get_clean(); ?>
<?php
include '../layout/layout_main.php';
?>