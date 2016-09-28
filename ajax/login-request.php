<?php
  
  //
  // Attempt to log the user in
  // $_POST[email, password]
  //
  
  // Include file locations
  require('../include-path.php');
    
  include_once ($includePath . 'database.php');
  include_once ($includePath . 'functions.php');

  // Start session
  sec_session_start();
  
  $result = array();
  $result['error'] = '';
  
  // Check for post variables
  if (isset($_POST['email'], $_POST['password'])) {
    
    // Obtain and filter post variables
    $email = $_POST['email'];
    $password = $_POST['password']; // The hashed password.

    // Login user
    if (login($email, $password, $database) == true) {
    
      // Succes
      $result['description'] = 'Succesfull logged in';

    } else {
    
      // Error -> invalid login
      $result['error'] = 'invalid';
      $result['error-description'] = 'Incorrect username or password';
    
    }
    
  } else {
    
    // Error -> invalid parameters
    $result['error'] = 'parameters';
    $result['error-description'] = 'Required fields are missing';
    
  }
  
  // return result in json
  echo (json_encode($result));

?>