<?php require '../includes/interface.php'; ?>
<?php $title = 'Dashboard' ?>
 <?php ob_start() ?>
  <div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="panel info-box panel-white">
            <div class="panel-body">
                <div class="info-box-stats">
                    <p class="counter"><?php echo $dashboard->number[0]; ?></p>
                    <span class="info-box-title"><?php echo $dashboard->title[0]; ?></span>
                </div>
                <div class="info-box-icon">
                    <i class="icon-users"></i>
                </div>
                <div class="info-box-progress">
                    <div class="progress progress-xs progress-squared bs-n">
                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="panel info-box panel-white">
            <div class="panel-body">
                <div class="info-box-stats">
                    <p class="counter"><?php echo $dashboard->number[1]; ?></p>
                    <span class="info-box-title"><?php echo $dashboard->title[1]; ?></span>
                </div>
                <div class="info-box-icon">
                    <i class="icon-eye"></i>
                </div>
                <div class="info-box-progress">
                    <div class="progress progress-xs progress-squared bs-n">
                        <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="panel info-box panel-white">
            <div class="panel-body">
                <div class="info-box-stats">
                    <p><span class="counter"><?php echo $dashboard->number[2]; ?></span></p>
                    <span class="info-box-title"><?php echo $dashboard->title[2]; ?></span>
                </div>
                <div class="info-box-icon">
                    <i class="icon-basket"></i>
                </div>
                <div class="info-box-progress">
                    <div class="progress progress-xs progress-squared bs-n">
                        <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="panel info-box panel-white">
            <div class="panel-body">
                <div class="info-box-stats">
                    <p class="counter"><?php echo $dashboard->number[3]; ?></p>
                    <span class="info-box-title"><?php echo $dashboard->title[3]; ?></span>
                </div>
                <div class="info-box-icon">
                    <i class="icon-envelope"></i>
                </div>
                <div class="info-box-progress">
                    <div class="progress progress-xs progress-squared bs-n">
                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><!-- Row -->
 <?php $content = ob_get_clean() ?>
 <?php include '../layout/layout_main.php' ?>