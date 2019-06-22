<div class="content">
    <div class="resetform" style="position:relative;top:30%">
        <div class="">
            <div class="col-md-offset-3 col-md-6 col-md-offset-3">
                <form action="<?php echo base_url().'resetuserpass'?>" method="post">
                  <div class="form-group">
                    <label for="pass">Password:</label>
                    <input type="password" class="form-control pa" name="password" id="pass" required>
                  </div>
                  <div class="form-group">
                    <label for="confpass">Confirm Password:</label>
                    <input type="password" class="form-control pa" name="confirmpassword" id="confpass" required>
                  </div>
                  <p style="color:red" class="error"></p>
                  <input type="hidden" name="email" value="<?php echo $mail;?>">
                  <button type="submit" class="btn btn-default" name="submit">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
   $(document).ready(function(){
       $('form').on('submit',function(e){
           var pass=$('#pass').val();
           var confpass=$('#confpass').val();
           if(pass.length<6){
             $('.error').text('Password must be Min 6 characters long');
             e.preventDefault();return;
           }
           if(pass!=confpass){
             $('.error').text('Password and confirm password are not same');
             e.preventDefault();return;
           }
       });
       
       $('.pa').on('keyup',function(){
             $('.error').text('');   
       });
   });
</script>
