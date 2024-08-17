<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sign in</title>

    <!-- Font Icon -->
    <link rel="stylesheet" href="fonts/material-icon/css/material-design-iconic-font.min.css">

    <!-- Main css -->
    <!-- <link rel="stylesheet" href="css/style.css"> -->
<style>
/*# sourceMappingURL=style.css.map */

.password-toggle {
    position: relative;
  }
  .password-toggle .toggle-password {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
  }
  
.oserp-login,
.image {
  min-height: 100vh;
}
.bg-image {
  background-image: url('../uploads/images/login-bg.webp');
  background-size: cover;
  background-position: center center;
  width:100%;
  height:100vh;
}
.remember-sec{
  display:flex;
  align-items:center;
  justify-content:space-between;
}
.oserp-login{
  display:flex;
  align-items:center;
  justify-content:center; 
}
.login-cnt{
  width:56%;
}
.login-cnt .form-control{
  border: 1px solid #AFAFAF; 
  padding:12px 10px;
  height:inherit;
}
.login-cnt .form-group{
  margin-bottom:24px;
}
.login-cnt .form-group.margin-bottom-0{
margin-bottom:0px;
}
.login-cnt .label-txt, .remember-sec .forgot-pass, .remember-sec .caption{
  color:#000000;
  font-size:13px;  
  font-weight:500;
}
.login-cnt .btn-bg{
border-radius: 3px;
border: 1px solid #016BD6;
background: #016BD6;
color:#ffffff; 
}
.login-cnt .btn-bg:hover{
background: #0052a4;
}
.login-btn{
  margin-top:32px;
}
.login-cnt .title-sec .h3{
  font-size:26px;
  color:#000;
  font-weight:600;
}
.login-cnt .title-sec .tagline-txt{
  font-size:18px;
  color:#444;
}
.login-cnt .logo-panel{
margin-bottom:20px;
}
.title-sec{
  margin-bottom:24px;
}
</style>    

</head>
<body>

  <div class="main">
    <div class="container-fluid">
      <div class="row no-gutter">
      <div class="col-md-6">
        <div class="bg-image"></div>
      </div>      
      <div class="col-md-6 bg-light">
        <div class="oserp-login">           
            <div class="login-cnt mx-auto">
                <div class="logo-panel">
                  <?php
                      if(customCompute($siteinfos->photo)) {
                          echo "<img width='50' height='50' src=".base_url('uploads/images/'.$siteinfos->photo)." />";
                      }
                  ?>
                <h4><?php echo namesorting($siteinfos->sname, 25); ?></h4>
              </div>
              <div class="title-sec">
                <h3 class="h3">Login and Manage Activities</h3>
                <p class="tagline-txt text-muted mb-4">From your school at your convenience</p>
              </div>
                
                <form class="login_form" method="post">
                <?php
                if($form_validation == "No"){
                } else {
                    if(customCompute($form_validation)) {
                        echo "<div class=\"alert alert-danger alert-dismissable\">
                            <i class=\"fa fa-ban\"></i>
                            <button aria-hidden=\"true\" data-dismiss=\"alert\" class=\"close\" type=\"button\">×</button>
                            $form_validation
                        </div>";
                    }
                }
                if($this->session->flashdata('reset_success')) {
                    $message = $this->session->flashdata('reset_success');
                    echo "<div class=\"alert alert-success alert-dismissable\">
                        <i class=\"fa fa-ban\"></i>
                        <button aria-hidden=\"true\" data-dismiss=\"alert\" class=\"close\" type=\"button\">×</button>
                        $message
                    </div>";
                }
            ?>
                  <div class="form-group">
                      <label class="label-txt" for="username">Username</label>
                      <input class="form-control shadow-sm px-4" placeholder="mail@abc.com" name="username" type="text"  autofocus value="<?=set_value('username')?>">
                  </div>
                  <div class="form-group margin-bottom-0">
                      <label class="label-txt" for="username">Password</label>
                      <input class="form-control shadow-sm px-4" placeholder="******" id="password" name="password" type="password">
                      <i class="toggle-password fa fa-eye"></i>
                    </div>

                  <div class="remember-sec">
                      <label class="control control--checkbox mb-0">
                        <input type="checkbox" checked="checked"/>
                        <span class="caption">Remember me</span>
                      <div class="control__indicator"></div>
                    </label>
                    <span class="ml-auto"><a href="#" class="forgot-pass">Forgot Password</a></span> 
                  </div> 
                  
                  <?php if(isset($siteinfos->captcha_status) && $siteinfos->captcha_status == 0) { ?>
                      <div class="form-group">
                          <?php echo $recaptcha['widget']; echo $recaptcha['script']; ?>
                      </div>
                  <?php } ?>

                   <div class="login-btn">
                    <input type="submit" class="btn btn-lg btn-bg col-md-12 col-sm-12 col-xs-12" value="Login" />
                   </div>                   

                  <?php if(config_item('demo')) { ?>                           
                  <?php } ?>  
                  <div class="pull-right hidden-xs">
                    <b>version</b> <?php echo "6.6.7";//config_item('ini_version');?>
                  </div>                                 
                </form>            
            </div><!-- End -->
          </div>
        </div>
        </div>
      </div>
    </div>

    <!-- JS -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="js/main.js"></script>

    
    <script>
       $(document).ready(function(){
  $('.toggle-password').click(function(){ 
    $(this).toggleClass('fa-eye fa-eye-slash');
    var input = $($(this).prev('input'));
    if (input.attr('type') == 'password') {
      input.attr('type', 'text');
    } else {
      input.attr('type', 'password');
    }
  });
});
    </script>

</body>
</html>