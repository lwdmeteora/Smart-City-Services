//
// functionallity for login/user button in header
//

// Overflow function 
$.fn.overflown=function(){
  
  var e = this[0];
  return e.scrollHeight > e.clientHeight || e.scrollWidth > e.clientWidth;

};

scaleHeader = function() {
  
  $header = $('header');
  overflown = $header.overflown();
  prevState = $header.data('minimized');
  
  // Check if state changed
  if (overflown != prevState) {
    
    // Change state accordingly
    if (overflown) {
      
      minimizeHeader();

    } else {
      
      if ($header.width() > 370) {
        
        maximizeHeader();
      
      }
      
    }

  }

}

// Set header to minimized
minimizeHeader = function() {
  
  var $headerMinimizedButton = $('#header-minimized', $('header'));
  var $minimizedLinks = $('#header-minimized-links', $headerMinimizedButton);
  var $headerElements = $('.header-button', $header).not('.header-right');
  
  $headerMinimizedButton.removeClass('hidden');
  $headerElements.detach();
  $headerElements.css({'float': 'none', 'margin': '10px 0'});
  $minimizedLinks.append($headerElements);
  
  $header.data('minimized', overflown);

}

// Set header to maximized
maximizeHeader = function() {
  
  var $headerMinimizedButton = $('#header-minimized', $('header'));
  var $minimizedLinks = $('#header-minimized-links', $headerMinimizedButton);
  var $headerElements = $('.header-button', $minimizedLinks);
  
  $headerMinimizedButton.addClass('hidden');
  $headerElements.detach();
  $headerElements.css({'float': 'left', 'margin': '0 0 0 15px'});
  $header.append($headerElements);
  
  $header.data('minimized', overflown);
  
}

$( document ).ready(function() {

  // hover
  $hoverTrigger = $('#user');
  $hoverElement = $('#user-info');
  
  // show element on hover
  $hoverTrigger.hover(function() {
    
    $hoverTrigger.addClass('hover');
    
  }, function() {
    
    $hoverTrigger.removeClass('hover');
    
  });
  
  // hover fix for mobile
  $hoverTrigger.click(function() {
    
    $hoverTrigger.toggleClass('hover');
    
  });
  
  // logout
  $logout = $('#user-logout', $hoverElement);
  
  $logout.click(function() {
    
    $.post( 'ajax/logout-request.php')
    .done(function() {
      
      $hoverTrigger.replaceWith(
    
        "<div class='header-button header-right' >" +
          "<a id='header-login' href='login.php' >" +
            "<text >Login</text >" +
          "</a >" +
        "</div >"
        
      )
      
    });
    
  });
  
  // check if header needs to minimize when rescaling screen
  $(window).resize(function() {
    
    scaleHeader();
    
  });
  
  // check for rescale when page first loaded
  scaleHeader();
  
  // Open / close minimized menu
  $headerMinimizedButton = $('#header-minimized-button');
  $headerMinimizedButton.click(function() {
    
    $('#header-minimized-links').toggleClass('hover');
    
  });
  
});