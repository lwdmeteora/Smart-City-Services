<?php

  // Debug, show errors
  error_reporting( E_ALL );
  ini_set( "display_errors", 1 );

  use League\OAuth2\Server\AuthorizationServer;
  use League\OAuth2\Server\CryptKey;
  use League\OAuth2\Server\ResourceServer;
  use League\OAuth2\Server\Exception\OAuthServerException;
  use League\OAuth2\Server\Grant\PasswordGrant;
  use OAuth2ServerExamples\Repositories\AccessTokenRepository;
  use OAuth2ServerExamples\Repositories\ClientRepository;
  use OAuth2ServerExamples\Repositories\RefreshTokenRepository;
  use OAuth2ServerExamples\Repositories\ScopeRepository;
  use OAuth2ServerExamples\Repositories\UserRepository;
  use Psr\Http\Message\ResponseInterface;
  use Psr\Http\Message\ServerRequestInterface;
  use Slim\App;

  // Include file location -->
  include_once('../include-path.php' );
  include_once($includePath . 'config.php' );
  include_once($includePath . 'vendor/autoload.php' );
  include_once($includePath . 'database.php' );
  
  $app = new App([
  
      // Enable error notification
    'settings' => [
        'displayErrorDetails' => true,
    ],
    
    // Add the resource server to the DI container
    ResourceServer::class => function () {
      
      global $includePath;
      
      $server = new ResourceServer(
          new AccessTokenRepository(),            // instance of AccessTokenRepositoryInterface
          new CryptKey('file://' . $includePath . 'public.key', OAUTH2_KEY)   // public key 
      );
      
      return $server;
      
    },
    
  ]);

  // Add the resource server middleware which will intercept and validate requests  
  $authenticationCheck = new \League\OAuth2\Server\Middleware\ResourceServerMiddleware(
          $app->getContainer()->get(ResourceServer::class)
  );

  // Home endpoint, rerout to documentation
  $app->get('/', function ($request, $response, $args) {
    
      $router = $this->router;
      return $response->withRedirect($router->pathFor('home') . 'documentation.php');
      
  })->setName('home');

  // Calculation endpoint, allows showing answers and store the results
  $app->get('/calculation', function($request, $response, $args) {

      // Check for correct permissions
      if (in_array('calculation', $request->getAttribute('oauth_scopes')) === false) {
        
        return $response->write('insufficient rights');
        
      }
      
      // Init database with calculation privileges
      $calculationDatabase = new database(DATABASE_CALCULATION_USERNAME, DATABASE_CALCULATION_PASSWORD);
      
      // Request all parameters
      $allGetVars = $request->getQueryParams();
      $query = $allGetVars['query'];

      // Execute sended query
      $result = execute_query($calculationDatabase, $query);
      
      // Set result to an empty array if execution fails
      if (!$result) {
        
        $result = array();
        
      }
      
      // Return database response
      return $response->withJson($result);
      
  })->add($authenticationCheck);
  
  // App endpoint, allows showing the results
  $app->get('/app', function($request, $response, $args) {

      // Check for correct permissions
      if (in_array('app', $request->getAttribute('oauth_scopes')) === false) {
        
        return $response->write('insufficient rights');
        
      }
      
      // Init database with app privileges
      $appDatabase = new database(DATABASE_APP_USERNAME, DATABASE_APP_PASSWORD);
      
      // Request all parameters
      $allGetVars = $request->getQueryParams();
      
      if (!isset($allGetVars['query'])) {
        
        return $response->write("Missing parameter 'query'");
        
      }
      $query = $allGetVars['query'];

      // Execute sended query
      $result = execute_query($appDatabase, $query);

      // Return database response
      return $response->withJson($result);
      
  })->add($authenticationCheck);

   // Show categorie names by id's
  $app->get('/categorieID[/{id}]', function($request, $response, $args) {
      
      // Check for correct permissions
      if (in_array('calculation', $request->getAttribute('oauth_scopes')) === false) {
        
        return $response->write('insufficient rights');
        
      }
      
      // Open database connection with permissions
      $calculationDatabase = new database(DATABASE_CALCULATION_USERNAME, DATABASE_CALCULATION_PASSWORD);
      $id = null;
      
      // Check for id parameter
      if (isset($args['id'])) {
        
        $id = $args['id'];
        
      }
        
      // Query database
      $result = get_categorie_ids($calculationDatabase, $id);
      
      // Return result
      return $response->withJson($result);
    
  })->add($authenticationCheck);

  // show questions by question id's
  $app->get('/questionID[/{categorie}[/{id}]]', function($request, $response, $args) {
    print_r(PHP_EOL);
      // Check for correct permissions
      if (in_array('calculation', $request->getAttribute('oauth_scopes')) === false) {
        
        return $response->write('insufficient rights');
        
      }
      
      // Open database connection with permissions
      $calculationDatabase = new database(DATABASE_CALCULATION_USERNAME, DATABASE_CALCULATION_PASSWORD);
      $categorie = null;
      $id = null;
      
      // Check for categorie parameter
      if (isset($args['categorie'])) {
        
        // Set categorie paramater, ignore if 0 or smaller
        if (!($args['categorie'] < 0)) {
          $categorie = $args['categorie'];
        }
        
      }

      // Check for id Parameter
      if (isset($args['id'])) {
        
        $id = $args['id'];
        
      }

      // Query database
      $result = get_question_ids($calculationDatabase, $categorie, $id);
      
      // Return result
      return $response->withJson($result);
    
  })->add($authenticationCheck);
 
  //return $response->withStatus(200);

  $app->run();

  function execute_query($database, $query) {

      if ($stmt = $database->prepare($query)) {

        // Execute query
        $stmt->execute();

        // Bind result to array
        $result = $stmt->get_result();
        $resultArray = array();
        
        while($row = $result->fetch_array(MYSQL_ASSOC)) {
            
          $resultArray[] = $row;
          
        }

        // Clear unused resources
        $stmt->free_result();
        $stmt->close();

        return $resultArray;

      }
      
  }


  function get_categorie_ids($database, $id = null) {
    
    // Check if a specific id is given
    if ($id != null) {
      
      // Get categorie id + name once for give categorie
      $query = 'SELECT DISTINCT categorieID, categorie FROM questions WHERE categorieID = ?';
    } else {
      
      // Get all categories id + name once
      $query = 'SELECT DISTINCT categorieID, categorie FROM questions';
    }
    
    if ($stmt = $database->prepare($query)) {
      
        // Bind id parameter if specified
        if ($id != null) {
          
          $stmt->bind_param('i', $id);
          
        }

        // Execute query
        $stmt->execute();

        // Bind result to array
        $result = $stmt->get_result();
        $resultArray = array();
        
        while($row = $result->fetch_array(MYSQL_ASSOC)) {
            
          $resultArray[] = $row;
          
        }

        // Clear unused resources
        $stmt->free_result();
        $stmt->close();

        return $resultArray;

      }
    
  }

  function get_question_ids($database, $categorie = null, $id = null) {

    // Build query depending on parameters
    $query = 'SELECT questionID, question FROM questions';
    if ($categorie != null || $id != null) {$query .= ' WHERE ';}
    if ($categorie != null)                {$query .= 'categorieID = ?';}
    if ($categorie != null && $id != null) {$query .= ' AND ';}
    if ($id != null)                       {$query .= 'questionID = ?';}
    
      if ($stmt = $database->prepare($query)) {

        // Bind given parameters
        if ($categorie != null && $id != null) {
          $stmt->bind_param('ii', $categorie, $id);
        } else {
          if ($categorie != null) {$stmt->bind_param('i', $categorie);}
          if ($id != null) {$stmt->bind_param('i', $id);}
        }

      // Execute query
      $stmt->execute();

      // Bind result to array
      $result = $stmt->get_result();
      $resultArray = array();
      
      while($row = $result->fetch_array(MYSQL_ASSOC)) {
          
        $resultArray[] = $row;
        
      }

      // Clear unused resources
      $stmt->free_result();
      $stmt->close();

      return $resultArray;

    }
    
  }
  
  // Request all post variables
  //$allPostPutVars = $request->getParsedBody();

  // Read parameter from url (or GET if no parameter is given)
  /*      

    '/app[/{query}]'

    if (isset ($args['type'])) {

      $type = $args['type'];
    
    } else if (isset($allGetVars['type'])){

      $type = $allGetVars['type'];
      
    } else {
      
      // missing type paramater
      print_r('error');
      return;
      
    }
  */

  /*  == API request example in javascript (new authorization key needed) ==
    $.ajax({
        url: 'https://xlab.nhl.nl/SmartCityService/api/users',
        type: 'get',
      
        data: {
          query: 'SELECT * FROM answers'
        },
      
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            Accept: '1.0',
            Authorization: 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImNlNDI1NWFiNjljOTIwMTJmYmFkNTMxNjJkMWVkMGFjYTlmNDQ2OGUwNjUxODU2MjZkYTJlOTY5MTFhN2JjNTRmYzY0YTM3NGU2NmE1M2JkIn0.eyJhdWQiOiJfY2xpZW50X2lkXyIsImp0aSI6ImNlNDI1NWFiNjljOTIwMTJmYmFkNTMxNjJkMWVkMGFjYTlmNDQ2OGUwNjUxODU2MjZkYTJlOTY5MTFhN2JjNTRmYzY0YTM3NGU2NmE1M2JkIiwiaWF0IjoxNDYzNDA3NjgyLCJuYmYiOjE0NjM0MDc2ODIsImV4cCI6MTQ2MzQxMTI4Miwic3ViIjoiIiwic2NvcGVzIjpbImJhc2ljIiwiZW1haWwiXX0.FKiVeMdJjCsCGeM7XMLngvOj3ccWIgElrE5rCpp7MMjjGo_fqKer68zyJN2xUVhU-I5BVD4VQHAhrvSNhZY-0sFS0Fl8wtv-CmiDtp0Nlz9jhPEfhHREcmLrKeAJ5Vh4P0IhMrnC2QiuET1hBoVxc9VVxcnab8n4ERDaqri9UOM'
        },
      
        dataType: 'json',
      
        success: function (data) {
            console.info(data);
        }
    });
    
  */

?>