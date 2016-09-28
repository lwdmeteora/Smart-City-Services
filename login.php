

<!DOCTYPE html >
<html >

  <!-- include file location -->
  <?php require('include-path.php'); ?>
  <!-- head (title, meta) -->
  <?php $title = 'Login'; require($includePath . 'head.php'); ?>
  <!-- login status -->
  <?php require($includePath . 'status.php'); ?>
  
  <body>
    
    <!-- facebook login script -->
    <script type="text/JavaScript" src="js/sha512.js"></script> 
    <script type="text/javascript" src="js/facebookLogin.js" ></script >
    <script type="text/javascript" src="js/customLogin.js" ></script >
    
    <?php $page = 'login'; require($includePath . 'header.php') ?>

    <div id="content" class="center-content" >
    
      <div class="centered-content" >
      
        <div id='choose-login' >
        
          <div class='login-text' >
            Login with facebook
          </div > 
          
          <!-- facebok login button -->
          <div id='fb-login-wrapper' >
          
            <fb:login-button scope="public_profile,email" onlogin="checkLoginState();" >
            </fb:login-button >
          
            <div id='fb-login-overlay' ></div >
            <span id='fb-logo'>f</span >
            <span id='fb-text'>Login</span >
          
          </div >
          
          <div class='login-text' >
            simple, fast, secure  
          </div > 
          
          <div class='login-text' >
            Or take the alternate login
          </div>
          
          <!-- alternate login button -->
          <a id='locale-login' >
            <span >Login</span >
          </a >
          
          <a id='locale-register' class='login-text' >
            register account
          </a >
          
        </div >
        
        <!-- login box -->
        <div id='login-box' class='hidden' >
       
          <span class='login-box-title' > Login </span >
          <div id='login-result' ></div >
           
          <a class='back button'><</a >

          <form id='login-form' >
          
            <table >
          
              <tr >
              <td > Email </td >
              <td ><input type='text' id='login-email' /></td >
              </tr >
              
              <tr >
              <td >Password</td >
              <td ><input type='password' id='login-password' /></td >
              </tr >
              
              <tr >
              <td ></td >
              <td ><input type='submit' id='login-button' class='button' value='Login' /></td >
              </tr >
          
            </table >

          </form >

        </div >
        
        <!-- register box -->
        <div id='register-box' class='hidden' >
          
          <span class='login-box-title' > Register </span >
          <div id='register-result' ></div>
          
          <a class='back button'><</a >
           
          <form id='register-form' >
          
            <table >
              
              <tr >
              <td > Username </td >
              <td ><input id='register-username' type='text' /></td >
              </tr >

              <tr >
              <td > Email </td >
              <td ><input id='register-email' type='text' /></td >
              </tr >
              
              <tr >
              <td >Password</td >
              <td ><input id='register-password' type='password' /></td >
              </tr >

              <tr >
              <td >Confirm password</td >
              <td ><input id='register-password2' type='password' /></td >
              </tr >
              
              
              <tr >
              <td ></td >
              <td ><input type='submit' id='register-button' class='button' value='Register' /></td >
              </tr >
            
            </table>

          </form >
          
        </div >

      </div >
    
    </div >
    
  </body >
  
</html >