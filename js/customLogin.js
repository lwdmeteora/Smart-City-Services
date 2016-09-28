//
// Login without facebook (custom account)
//


// Attempt to log the user in
function login($email, $password) {

  $responseBox = $('#login-result');

  // Hash password
  var pass = hex_sha512($password.val());

  // Send login request to server
  $.post( 'ajax/login-request.php', { email: $email.val(), password: pass } )
  .done(function(JsonResponse) {
    
    
    response = jQuery.parseJSON(JsonResponse);
    
    if (response['error'] == '') {
      
      // Succesfull login
      $responseBox.text(response['description']).removeClass('error').addClass('succes');
      
    } else {
      
      // Error logging in
      $responseBox.text(response['error-description']).removeClass('succes').addClass('error');
      
    }
    
  }).fail(function(error) {
    
    $responseBox.text('Error connecting to server').removeClass('succes').addClass('error');
    console.log( error );
    
  });

}

// Attempt to register a new user
function register($username, $email, $password, $password2) {
  console.log();
  
  $responseBox = $('#register-result');
  
  // Check if password and confirm password match
  if ($password.val() != $password2.val()) {
    
    $responseBox.text('Passwords not matching').removeClass('succes').addClass('error');
    return false;
    
  }
  
  // Hash password
  var pass = hex_sha512($password.val());

  // Send register request to server
  $.post( 'ajax/register-request.php', { username: $username.val(), email: $email.val(), password: pass } )
  .done(function(JsonResponse) {
    
    response = jQuery.parseJSON(JsonResponse);
    
    if (response['error'] == '') {
      
      // Succesfull register
      $responseBox.text(response['description']).removeClass('error').addClass('succes');
      
    } else {
      
      // Error registering
      $responseBox.html(response['error-description']).removeClass('succes').addClass('error');
      
    }
    
  }).fail(function(error) {
    
    $responseBox.text('Error connecting to server').removeClass('succes').addClass('error');
    console.log( error );
    
  });

  
}

// Onload
$( document ).ready(function() {

  $loginButton = $('#locale-login');
  $registerButton = $('#locale-register');
  $backButton = $('.back');

  $chooseLogin = $('#choose-login');
  $loginBox = $('#login-box');
  $registerBox = $('#register-box');

  // login button toggle
  $loginButton.click(function() {
    
    changeFocus($loginBox);
    
  });

  // login button toggle
  $registerButton.click(function() {
    
    changeFocus($registerBox);
    
  });

  // return to choose login
  $backButton.click(function() { 

    changeFocus($chooseLogin); 
    clearForms();
    
  });

  // Switch between login and register view
  function changeFocus(element) {
    
    element.removeClass('hidden').siblings().addClass('hidden');
    
  }

  $login = $('#login-form');
  $register = $('#register-form');
  
  // Login button clicked
  $login.submit(function() {
   
    login($('#login-email'), $('#login-password'));
    return false;
    
  });

  // Register button Clicked
  $register.submit(function() {

    register($('#register-username'), $('#register-email'), $('#register-password'), $('#register-password2'));
    return false;
    
  });
  
  // Clear login and register form (after back button)
  function clearForms() {
    
    // Empty result box
    $('#login-result, #register-result').html('').removeClass('error').removeClass('succes');
    
    // Empty textbox
    $forms = $('#login-form, #register-form');
    $(':text, :password', $forms).val('');
    
  }

});
