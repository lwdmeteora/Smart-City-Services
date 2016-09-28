<!DOCTYPE html >

<html >

  <!-- include file location -->
  <?php require('include-path.php'); ?>
  <!-- head (title, meta) -->
  <?php $title = 'Home'; require($includePath . 'head.php'); ?>
  <!-- login status -->
  <?php require($includePath . 'status.php'); ?>
  
  <script src='js/translate.js'></script>
  
  <body >

    <!-- header -->
    <?php $page = 'index'; require($includePath . 'header.php') ?>
    
    <div id="content" class="center-content" style="margin: 0">
    
      <div id="welcome-wrapper" class="center-wrapper" >
      
        <div id="welcome" class="centered-content" >
        
          <text >Welcome to Leeuwarden.</text >
          
        </div >
        
      </div >
      
      <!-- arrow -->
      <div id="arrow-wrapper" >
      
        <div id="arrow-left" class="arrow centered-content" ></div >
        <div id="arrow-right" class="arrow centered-content" ></div >
      
      </div>
      
      <div class="block-wrapper" >
      
        <div id="question-left" class="" >
          <text >So what is so interesting here?</text >
        </div >
        
        <div id="question-right" class="" >
          <text >Thats what we ask you!</text >
        </div >
      
      </div>
      
      <div id="election-wrapper">
          
          <a href="questioncategories.php" ><text >Questionlist</text ></a ><br />
          <a id='translate'><text >Translate</text ></a >
          
      </div >
    
    </div >
    
  </body >

</html >