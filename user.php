<!DOCTYPE html >

<html >

  <!-- include file location -->
  <?php require('include-path.php'); ?>
  <!-- head (title, meta) -->
  <?php $title = 'User'; require($includePath . 'head.php'); ?>
  <!-- login status -->
  <?php require($includePath . 'status.php'); ?>
  
  <body >
    
    <?php require($includePath . 'header.php') ?>

    <div id="content" class="center-content" >
    
      <div class='centered-content' >
      
        <?php 
        
          if ($login) {
            
            if (using_facebook()) {
              
              try {
                
                # GET request in v5a
                $response = $fb->get('/me?fields=id,name,first_name,last_name,age_range,link,gender,picture,timezone,verified&type=large&redirect=false');
                $userInfo = $response->getDecodedBody();
              
                $response = $fb->get('/me/picture?type=large&redirect=false');
                $userPicture = $response->getDecodedBody();
                
              } catch (Exception $e) {
                
                $login = false;
                
              }

        ?>
        
          <div > name: <?=$userInfo['name']?></div >
          <div > id: <?=$userInfo['id']?></div >
          <div > first name: <?=$userInfo['first_name']?></div >
          <div > last name: <?=$userInfo['last_name']?></div >
          <div > age range: min <?=$userInfo['age_range']['min']?>, max <?=$userInfo['age_range']['max']?></div >
          <div > link: <?=$userInfo['link']?></div >
          <div > gender: <?=$userInfo['gender']?></div >
          <div > timezone: <?=$userInfo['timezone']?></div >
          <div > verified: <?=$userInfo['verified']?></div >
          <div > picture is silhouette: <?=$userPicture['data']['is_silhouette']?></div >
    
          <img src='<?=$userPicture['data']['url']?>'></img >
          
        <?php
        
            } else {
              
              $user_id = $_SESSION['user_id'];
              
              if ($stmt = $database->prepare("SELECT username, email, rank FROM users WHERE userID = ? AND use_facebook = 0 LIMIT 1")) {
          
                  // Bind "$user_id" to parameter. 
                  $stmt->bind_param('i', $user_id);
                  $stmt->execute();   // Execute the prepared query.
                  $stmt->store_result();

                  if ($stmt->num_rows == 1) {

                    // If the user exists get variables from result.
                    $stmt->bind_result($userInfo['username'], $userInfo['email'], $userInfo['rank']);
                    $stmt->fetch();
                    
                  } else { echo('login failed'); /* not logged in, or user not found */ }
                  
              } else { echo('request failed'); /* failed database request */ }
          
        ?>
          
          <div > Username: <?=$userInfo['username']?></div >
          <div > Email: <?=$userInfo['email']?></div >
          <div > Rank: <?=$userInfo['rank']?></div >
    
        <?php

            }
           
          }

        ?>
      
      </div >
    
    </div >
    
  </body >
  
</html >