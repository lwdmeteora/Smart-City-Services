$(document).ready(function() {
  
  // change page to show results
  $('#submit-button').click(function() {
    
    // Get all question fields
    $fields = $('li:has(input, select):has(span)', $('#questions'));
    
    result = [];
    error = false;
    
    // remove old error style
    removeErrorMessage();
    
    // Loop through each question
    $.each($fields, function(index, field) {

      answer = getAnswer($(field));
      error = (checkForErrors(answer, $(field)) || error);

      result.push(answer);
      
    });

    if(error) {
      
      return true;
      
    }

    sendAnswers(result)
    
  });
  
});

function getAnswer($field) {
  
  // Get id and input fields
  var $idField = $('.questionID', $field);
  var $inputField = $('input', $field);
  var answer = {};
  
  // Set the question id
  answer['id'] = $idField.text();
  // Set the question categorie
  answer['categorie'] = "<?php echo(urlencode($_GET['categorie']))?>";
  
  
  // == retrieve answer depending on question type ==
  // Standard input field (text)
  if($inputField.length == 1) {
    
    answer['value'] = $inputField.val();
  
  // Multiple inputs (radio, checkbox)
  } else if ($inputField.length > 1) {
    
    $checked = $inputField.filter(':checked');

    // One selected checkbox or radio
    if ($checked.length == 1){
      
      answer['value'] = $checked.val();
      
    // Multiple selected checkbox
    } else if ($checked.length > 1){
      
      answer['value'] = [];
      $.each($checked, function(index, input) {
        
        answer['value'].push($checked.val());
        
      });
     
    // No selected elements
    } else {
      
      answer['value'] = '';
      
    }
    
  // No input field present
  } else {
    
    // Check for dropdown menu (doesn't use the default input field)
    $select = $('select', $field);
    if ($select.length == 1) {
      
      answer['value'] = $select.val();
     
    // Nothing found, return empty answer
    } else {
      
      answer['value'] = '';
      
    }
    
  }
  
  return answer;
  
}

// check for errors in the submitted values
function checkForErrors(answer, $field) {
  
  var error = false;
  
  // check for empty id fields
  if(answer['id'] == '') {
    
    $('#error-message').text('Missing question ID, please try reloading the page').removeClass('hidden');
    error = true;
    
  }
  
  // check for empty forms
  if(answer['value'] == '' || answer['value'] == 'undefined') {
    
    $('#error-message').text('Please make sure all fields are filled').removeClass('hidden');
    $('input', $field).addClass('incorrect');
    error = true;
    
  }
  
  return error;
  
}

// remove old error message css
function removeErrorMessage() {
  
  $('#error-message').text('').addClass('hidden');
  $('#questions .incorrect').removeClass('incorrect');
  
}

function sendAnswers(answers) {
  console.log(answers);
  var jsonAnswers = JSON.stringify(answers);
  console.log(jsonAnswers);
  // Send results to server
  $.post( 'ajax/question-result.php', { answers: jsonAnswers} )
  .done(function(JsonResponse) {
    
    //response = $.parseJSON(JsonResponse);
    
    //if (response['error'] == '') {
      
      // Succesfull login
      //$responseBox.text(response['description']).removeClass('error').addClass('succes');
      //window.location.replace('questionresults?categorie=<?php echo(urlencode($_GET['categorie']))?>');
      
    //} else {
      
      // Error logging in
      //$responseBox.text(response['error-description']).removeClass('succes').addClass('error');
      
    //}
    
  }).fail(function(error) {
    
    $('#error-message').text('Error connecting to the server').removeClass('hidden');
    console.log( error );
    
  });
  
}

$('#questions input').click(function(event){
  
  $input = $(event.target);
  $input.removeClass('incorrect');
  
});