<?php
  
  //
  // Attempt to register the user
  // $_Post[username, email, password]
  //
  
  // Include file locations
  require('../include-path.php');
    
  include_once ($includePath . 'database.php');
  include_once ($includePath . 'config.php');
  
  $result = array();
  $result['error'] = '';
  $result['error-description'] = '';
  $result['description'] = 'Registration succesfull';
   
  if (isset($_POST['username'], $_POST['email'], $_POST['password'])) {
    
    // Sanitize and validate the data passed in
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      
      // Not a valid email
      $result['error'] .= 'invalid email, ';
      $result['error-description'] .= '<div >The email address you entered is not valid</div >';
      
    }
   
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    
    if (strlen($password) != 128) {
      
        // The hashed pwd should be 128 characters long.
        // If it's not, something really odd has happened
        $result['error'] .= 'invalid password, ';
        $result['error-description'] .= '<div ">Invalid password configuration.</div >';
        
    }
   
    // Username validity and password validity have been checked client side.
    // This should should be adequate as nobody gains any advantage from
    // breaking these rules.
    //

    $prep_stmt = "SELECT userID FROM users WHERE email = ? LIMIT 1";
    $stmt = $database->prepare($prep_stmt);

    // check existing email  
    if ($stmt) {
      
      $stmt->bind_param('s', $email);
      $stmt->execute();
      $stmt->store_result();

      if ($stmt->num_rows == 1) {
        
        // A user with this email address already exists
        $result['error'] .= 'duplicate email, ';
        $result['error-description'] .= '<div >A user with this email address already exists.</div >';
        $stmt->close();
        
      }
    
    } else {
      
      $result['error'] .= 'database, ';
      $result['error-description'] .= '<div >Database error</div >';
      $stmt->close();
      
    }

    // check existing username
    $prep_stmt = "SELECT userID FROM users WHERE username = ? LIMIT 1";
    $stmt = $database->prepare($prep_stmt);
 
    if ($stmt) {
      
      $stmt->bind_param('s', $username);
      $stmt->execute();
      $stmt->store_result();
 
      if ($stmt->num_rows == 1) {
  
        // A user with this username already exists
        $result['error'] .= 'duplicate username, ';
        $result['error-description'] .= '<div >A user with this username already exists<div >';
        $stmt->close();
        
      }
      
    } else {

      $result['error'] .= 'database, ';
      $result['error-description'] .= '<div >Database error</div >';
      $stmt->close();
      
    }
 
    // If no errors occurred
    if (empty($result['error'])) {
 
      // Create hashed password using the password_hash function.
      // This function salts it with a random salt and can be verified with
      // the password_verify function.
      $password = password_hash($password, PASSWORD_BCRYPT);
      $use_facebook = 0;

      // Insert the new user into the database 
      if ($insert_stmt = $database->prepare("INSERT INTO users (username, email, password, use_facebook) VALUES (?, ?, ?, ?)")) {
        
        $insert_stmt->bind_param('sssi', $username, $email, $password, $use_facebook);
        
        // Execute the prepared query.
        if (! $insert_stmt->execute()) {
        
          //header('Location: ../error.php?err=Registration failure: INSERT');
          
        }
        
      }
      
    }
    
  }
  
  // Write results to page in json
  echo (json_encode($result));

?>