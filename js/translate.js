//
// Translate all text on the page in <text> tags
// Translations can be found in /ajax/translate.php
//

$(document).ready(function() {

  // translate (test version)
  $translateButton = $('#translate');

  $translateButton.click(function() {
      
    // aquire all text on page (wrapped in text tags)
    $text = [];
    $('text').each(function(index, element) {
      
      $text[index] = $(element).text().trim();
      
    });
    
    // parse text to json
    $jsonText = JSON.stringify($text, null, 2);
    
    // request translation from server
    $.ajax({
      
      method: "POST",
      url: "ajax/translate.php",
      data: { language: 'dutch', text: $jsonText }
      
    }).done(function(responseInJSON) {
      
      // parse response to array
      $translatedText = JSON.parse(responseInJSON);
      
      applyTranslation($translatedText);
      
    });
    
    // insert translated text
    function applyTranslation($translatedText) {
      
      $('text').each(function(index, element) {
        
        $(element).text($translatedText[index]);
        
      });
      
    }

  });
 
});