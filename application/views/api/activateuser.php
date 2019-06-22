<div class="content">
    <?php if($result=="activatedsuccessfully"){?>
    <div class="box" style="padding:50px;background:#d4edda;position:relative;top:30%">
        <div class="alert alert-success" role="alert">
          <h4 class="alert-heading">Success!</h4>
          <p>You have been activated successfully, now you can login in the App.</p>
        </div>
    </div>
    <?php } ?>
    
    <?php if($result=="alreadyactivated" || $result=="notexist"){?>
    <div class="box" style="padding:50px;background:#f2dede;position:relative;top:30%">
        <div class="alert alert-danger" role="alert">
          <h4 class="alert-heading">Failure!</h4>
          
          <?php if($result=="alreadyactivated"){?>
          <p>You have already been activated earlier</p>
          <?php } ?>
          
          <?php if($result=="notexist"){?>
          <p>User does not exist</p>
          <?php } ?>
          
        </div>
    </div>
    <?php } ?>
    
    
</div>
