<?php
  
  //
  // Safe answers given by the user on the question page
  // $_POST[answers]
  //

  // Include file locations
  require('../include-path.php');
    
  include_once ($includePath . 'database.php');
  include_once ($includePath . 'functions.php');
  include_once ($includePath . 'status.php');

  $response['error'] = '';
  
  if($login) {

    if (isset($_POST['answers'])) {
      
      $answers = json_decode($_POST['answers'], true);
      
      foreach ($answers as $answer) {
    
        // =required= make check if user already posted
      
        // Prepare query
        if ($stmt = $database->prepare("INSERT INTO answers (userID, questionID, categorieID, answer) VALUES (?,?, ?, ?)")) {

          // Insert parameters
          $stmt->bind_param('iiis', $_SESSION['user_id'], $answer['id'], $answer['categorie'], $answer['value']);

          // Execute query
          $stmt->execute();

          // Bind result to array
          $questions = $stmt->get_result();

          // Clear unused resources
          $stmt->free_result();
          $stmt->close();

        }
      
      }
    
    } else {
      
      $response['error'] = 'Missing values in server request';
      
    }
    
  } else {
    
    $response['error'] = 'Not logged in or session ended';
    
  }

?>