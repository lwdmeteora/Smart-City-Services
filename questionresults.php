<!DOCTYPE html >

<html >

    <!-- include file location -->
  <?php require('include-path.php'); ?>
  <!-- head (title, meta) -->
  <?php $title = 'Question results'; require($includePath . 'head.php'); ?>
  <!-- login status -->
  <?php require($includePath . 'status.php'); ?>
  
  <?php

    if ($login) {

      try {
        
        # GET request in v5a
        $response = $fb->get('/me');
        $userInfo = $response->getDecodedBody();

      } catch (Exception $e) {
        
        $login = false;
        
      }

    }
    
  ?>
  
  <body >
    
    <?php $page = 'questioncategories'; require($includePath . 'header.php') ?>
  
    <div id="content" class="center-content">
    
      <div id="questionresult" class="centered-content">

        Scores <?php echo htmlspecialchars($_GET['question']) ?><br />
        <a href="questioncategories.php">Back to categories</a >
    
      </div >
    
    </div >
    
  </body>

</html>