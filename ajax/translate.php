<?php 

  //
  // Translate the given text to specified language
  // (Translations not completed)
  //
  
  // Language to translate to
  $languageTo = preg_replace("/[^A-Za-z0-9]/", '', $_POST["language"]);
  // Text to translate
  $text = json_decode($_POST["text"]);
  // Resulting text
  $result = array();
  
  // Lookup table for translations
  $dutch = array(
    'Welcome to Leeuwarden.' => 'Welkom in Leeuwarden.',
    'So what is so interesting here?' => 'Wat is er zo interessant hier?',
    'Thats what we ask you!' => 'Dat is wat we jouw vragen!',
    'Questionlist' => 'Vragenlijst',
    'Translate' => 'Vertaal'
  );

  // Decide language to translate to
  switch($languageTo) {
    
    case 'dutch':
    
      foreach($text as $sentence) {
      
        array_push($result, getTranslation($sentence, $dutch));
        
      }
      
    break;
    
  }
  
  // Perform translation
  function getTranslation($sentence, $translation) {
    
    if (array_key_exists($sentence, $translation)) {
      
      return $translation[$sentence];
      
    } else {
      
      return $sentence;
      
    }
    
  }
  
  // Write the result as json to the page
  echo(json_encode($result));
  
?>