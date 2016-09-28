<!DOCTYPE html >

<html >

  <!-- include file location -->
  <?php require('include-path.php'); ?>
  <!-- head (title, meta, scripts) -->
  <?php $title = 'Questions'; require($includePath . 'head.php'); ?>
  <!-- login status -->
  <?php require($includePath . 'status.php'); ?>
  <!-- database -->
  <?php require_once($includePath . 'database.php')?>
  
  <script src='js/submit-question.js'></script>
  
  <body >

    <!-- header -->
    <?php $page = 'questioncategories'; require($includePath . 'header.php') ?>
  
    <div id="content" class="center-content">
    
      <ul id='questions' class='centered-content'>
      
        <div id='error-message' class='error hidden' ></div ><br />
      
        <!-- fetch categories from database -->
        <?php if($login) :
  
          // Prepare query
          if ($stmt = $database->prepare("SELECT questionID, question, questionType, questionValue FROM questions WHERE categorieID = ?")) {
            
            // Insert parameters
            $stmt->bind_param('i', $_GET['categorie']);

            // Execute query
            $stmt->execute();

            // Bind result to array
            $questions = $stmt->get_result();

            // Clear unused resources
            $stmt->free_result();
            $stmt->close();

          }
          
          if($questions):

            // print all users as rows
            foreach ($questions as $question) {

              switch($question['questionType']) {
                
                case 'open': ?>
        
                  <!-- textbox input -->
                  <li >
                    <div class="question" ><?=$question['question'] ?></div >
                  </li >
                  
                  <li >
                    <span class='questionID hidden' ><?=$question['questionID']?></span >
                    <input type='text' class='text-input' />
                  </li >
                  
                  <br />
        
                <?php break;
                
                case 'radio': ?>
        
                  <!-- radio button input -->
                  <li >
                    <div class="question" ><?=$question['question'] ?></div >
                  </li >
                  
                  <li >
                    <span class='questionID hidden' ><?=$question['questionID']?></span >
                  
                    <?php 

                      $values = explode(", ", $question['questionValue']);
                      foreach($values as $value): ?>
                        
                        <input type='radio' name='test' value="<?=$value?>"/> <?=$value?>
                        
                      <?php endforeach;
                    
                    ?>
                  </li >
                  
                  <br />
        
                <?php break;
                
                case 'dropdown': ?>
                
                  <!-- drop down box input -->
                  <li >
                    <div class="question" ><?=$question['question'] ?></div >
                  </li >
                  
                  <li >
                    <span class='questionID hidden' ><?=$question['questionID']?></span >
                    
                    <select >
                    <?php 

                      $values = explode(", ", $question['questionValue']);
                      foreach($values as $value): ?>
                        
                        <option value='<?=$value?>' ><?=$value?></option >
                        
                      <?php endforeach;
                    
                    ?>
                    </select>
                  </li >
                  
                  <br />
                
                <?php break;
              
              }
             
            }
            
        ?> 
      
            <!-- submit button -->
            <li>
            
              <a id='submit-button' class='button' >Submit</a >
              
            </li>
            
          <?php else: ?>
        
            <!-- invalid question respone -->
            <li>The categorie you selected is invalid or doesnt excist.</li>
            <li><a href="questioncategories">Return</a></li>
        
          <?php endif; ?>
        
        <?php else: ?>
        
        <div ><a href='login.php'>Please login first</a ></div >
        <?php endif; ?>
      
      </ul >
    
    </div >
    
  </body>

</html>