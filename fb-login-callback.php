<?php   

  // Debug
  error_reporting( E_ALL );
  ini_set( "display_errors", 1 );
  
  require('include-path.php'); 
  include_once($includePath . 'database.php');
  include_once($includePath . 'functions.php');
  
  // use session variables
  sec_session_start();

  // Include the required dependencies.
  require_once($includePath . 'vendor/autoload.php' );
  
  // load facebook core
  $fb = new Facebook\Facebook([
    'app_id' => '162075257505836',
    'app_secret' => 'ae67fa98add759f50cda531a44dcd50a',
    'default_graph_version' => 'v2.5',
  ]);
  
  // retrieve javascript values from login
  $jsHelper = $fb->getJavaScriptHelper();

  // fetch acces token
  try {
    
    $accessToken = $jsHelper->getAccessToken();
    
  } catch(Facebook\Exceptions\FacebookResponseException $e) {
    
    // Graph error
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
    
  } catch(Facebook\Exceptions\FacebookSDKException $e) {
    
    // Validation failed or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
    
  }

  if (! isset($accessToken)) {
    
    // Check if values are retrieved
    echo 'No cookie set or no OAuth data could be obtained from cookie.';
    exit;
    
  }

  $client = $fb->getOAuth2Client();

  try {
    
    // Extend token lifetime
    $longAccessToken = $client->getLongLivedAccessToken($accessToken);
    
  } catch(Facebook\Exceptions\FacebookSDKException $e) {
    
    // Graph error
    echo $e->getMessage();
    exit;
    
  }

  // If succesfull logged in
  if (isset($longAccessToken)) {

    // Store accestoke in session
    $facebook_access_token = (string) $longAccessToken;

    // set default acces token
    $fb->setDefaultAccessToken($facebook_access_token);

    // get user data
    $response = $fb->get('/me?fields=id,name,age_range,gender');
    $userInfo = $response->getDecodedBody();

    // Check database if user exists
    $user_exists = user_exists($database, $userInfo['id']);

    if (!$user_exists) {
   
      // Add the user to the database if he isn't found
      add_user($database, $userInfo['name'], $userInfo['id']);
      
    }
 
    // Retrieve some data from the user's account
    $userData = get_user_data($database, $userInfo['id']);
    
    if (!$user_exists) {

      // Write facebook data to the database if the user is new
      set_facebook_data($database, $userData[0], $userInfo);
  
    }          

    // Set session variables to keep the user logged in
    $_SESSION['user_id'] = $userData[0];
    $_SESSION['username'] = $userData[1];
    $_SESSION['user_rank'] = $userData[2];
    $_SESSION['facebook_access_token'] = $facebook_access_token;

    // close database
    $database->close();
    
  }
  
  // Check if user exists in the database
  function user_exists($database, $facebookID) {
    
    // Query
    $test_for_user_query = "SELECT userID FROM users WHERE facebook_id = ? AND use_facebook = '1'";
    
    // Check if query was succesfull parsed
    if ($stmt = $database->prepare($test_for_user_query)) {
      
        // Add paramters to query
        $stmt->bind_param('s', $facebookID);
        
        // Execute
        $stmt->execute();
        
        // Fetch result
        $result = $stmt->get_result();
        
        // Clear unused data
        $stmt->free_result();
        $stmt->close();
        
        return (count($result) != 0);
        
    }
    
    throw new Exception('Database connection failed');
    
  }
  
  // Add user to database
  function add_user($database, $username, $facebookID) {
      
      $add_user_query = "INSERT INTO users (username, facebook_id, use_facebook) VALUES (?, ?, '1')";
      if ($stmt = $database->prepare($add_user_query)) {
          $stmt->bind_param('ss', $username, $facebookID);
          $stmt->execute();
          $stmt->close();
          
          return;
          
      }
      
      throw new Exception('Database connection failed');
    
  }

  // Get user data from table and store for future use
  function get_user_data($database, $facebookID) {

      $get_user_data_query = ("SELECT userID, username, rank FROM users WHERE facebook_id = ?");
      if ($stmt = $database->prepare($get_user_data_query)) {
          $stmt->bind_param('s', $facebookID);
          $stmt->execute();
          $userData = $stmt->get_result()->fetch_all()[0]; // Store result
          $stmt->free_result();
          $stmt->close();
          
          return $userData;
          
      }
    
      throw new Exception('Database connection failed');
    
  }
  
  // Add some facebook data to the answerlist
  function set_facebook_data($database, $username, $facebookData) {
    
      // Add gender query
      $add_facebook_data_query = "INSERT INTO answers (userID, questionID, categorieID, answer) VALUES (?, 1, 0, ?)";
      
      // Test query
      if ($stmt = $database->prepare($add_facebook_data_query)) {
        
          // Bind parameters
          $stmt->bind_param('is', $username, $facebookData['gender']);
          
          // Execute query
          $stmt->execute();
          
          // Close stream
          $stmt->close();
          
      }

      // Add age query
      $add_facebook_data_query = "INSERT INTO answers (userID, questionID, categorieID, answer) VALUES (?, 2, 0, ?)";
      // Test query
      if ($stmt = $database->prepare($add_facebook_data_query)) {
        
          $age_range = $facebookData['age_range']['min'] .' - '. $facebookData['age_range']['max'];
        
          // Bind parameters
          $stmt->bind_param('is', $username, $age_range);
          
          // Execute query
          $stmt->execute();
          
          // Close stream
          $stmt->close();
          
          return;
          
      }
      
      throw new Exception('Database connection failed');
    
  }
  
?>