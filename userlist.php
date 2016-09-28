<!DOCTYPE html >

<html >

  <!-- include file location -->
  <?php require('include-path.php'); ?>
  <!-- head (title, meta) -->
  <?php $title = 'Userlist'; require($includePath . 'head.php'); ?>
  <!-- login status -->
  <?php require($includePath . 'status.php'); ?>
  
  <body >
    
    <!-- include header -->
    <?php require($includePath . 'header.php') ?>

    <div id="content" class="center-content" >
    
      <div class='centered-content' >
        
        <!-- check if logged in with correct rights -->
        <?php  if($login && $_SESSION['user_rank'] == 'admin'): ?>

          <!-- user table -->
          <table id='user-list'>
            <thead >
            
              <!-- table header -->
              <tr >
                
                <th >ID</th >
                <th >Username</th >
                <th >Facebook user</th >
              
              </tr>
              
            </thead >
            <tbody >
            
            <!-- table rows from database -->
            <?php 

              // request all users
              $userlist = $database->query_array("SELECT * FROM users");
              
              // print all users as rows
              foreach ($userlist as $user): ?>
            
                <tr >

                  <td ><?=$user['userID']?></td >
                  <td ><?=$user['username']?></td >
                  <td ><?=($user['use_facebook']==1?'True':'False')?></td >
                  
                </tr >
            
              <?php endforeach; ?>
              
            </tbody >
          </table >
              
        <?php else: ?>
          
          <!-- Not logged in -->
          <a href="login.php">Please login with admin rights to view this content</a>
          
        <?php endif; ?>
            
      </div >
    
    </div >
    
  </body >
  
</html >