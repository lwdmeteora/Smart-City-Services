<!DOCTYPE html >

<html >

  <!-- include file location -->
  <?php require('include-path.php'); ?>
  <!-- head (title, meta) -->
  <?php $title = 'Question categories'; require($includePath . 'head.php'); ?>
  <!-- login status -->
  <?php require($includePath . 'status.php'); ?>
  <!-- database -->
  <?php require_once($includePath . 'database.php')?>
  
  <body >
    
    <?php $page = 'questioncategories'; require($includePath . 'header.php') ?>
  
    <div id="content" class="center-content">
    
      <ul id="question-list" class="centered-content">

        <!-- fetch categories from database -->
        <?php if ($login):
   
          // Prepare query
          if ($stmt = $database->prepare("SELECT DISTINCT categorieID, categorie FROM questions")) {
            
            // Execute query
            $stmt->execute();

            // Bind result to array
            $categories = $stmt->get_result();

            // Clear unused resources
            $stmt->free_result();
            $stmt->close();
          
          }
          
          if ($categories):

            // print all users as rows
            foreach ($categories as $categorie): ?>

              <li >
                <div class="list-question" >
                <a href="questions?categorie=<?php echo(urlencode($categorie['categorieID']))?>"><?=$categorie['categorie'] ?></a ></div >
                
              </li >

            <?php endforeach; ?>
            
          <?php endif; ?>
          
        <?php else: ?>
          <!-- Not logged in -->
          <a href='login.php' >
            <text >Please login first.</text >
          </a >

        <?php endif; ?>
      
      </ul >
    
    </div >
    
  </body>

</html>