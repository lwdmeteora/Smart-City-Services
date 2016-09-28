<!DOCTYPE html >

<html >

  <!-- include file location -->
  <?php require('../include-path.php'); ?>
  <!-- head (title, meta) -->
  <?php $title = 'API Documentation'; require($includePath . 'head.php'); ?>
  <!-- login status -->
  <?php require($includePath . 'status.php'); ?>
  
  <body >
  
    <!-- header -->
    <?php $page = 'index'; require($includePath . 'header.php') ?>
    
    <div id="content" class="center-content" >
      
        <h2 ><text >API usage</text ></h2 >
        <div class='centered-content'> 
            1) Authenticate (id / secret documentation file, or ask the projectmanager). <br />
            2) Using the authentication code, send a request to any endpoint (see image below). <br />
               Query means any valid sql query for which the authenticated account has the rights to execute
        </div >

    </div >
    
    <div class='center-content' >
        <img src='/SmartCityService/images/api-documentation.png' />
    </div >
    
  </body >

</html >