<?php

  // Debug, show errors
  error_reporting( E_ALL );
  ini_set( "display_errors", 1 );

  use League\OAuth2\Server\AuthorizationServer;
  use League\OAuth2\Server\CryptKey;
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
    
    // Add the authorization server to the DI container
    AuthorizationServer::class => function () {
      
      global $includePath;

        // Setup the authorization server
        $server = new AuthorizationServer(
            new ClientRepository(),                 // instance of ClientRepositoryInterface
            new AccessTokenRepository(),            // instance of AccessTokenRepositoryInterface
            new ScopeRepository(),                  // instance of ScopeRepositoryInterface
            new CryptKey('file://' . $includePath . 'private.key', OAUTH2_KEY), // private key
            new CryptKey('file://' . $includePath . 'public.key', OAUTH2_KEY)   // public key 
        );
        $grant = new PasswordGrant(
            new UserRepository(),           // instance of UserRepositoryInterface
            new RefreshTokenRepository()    // instance of RefreshTokenRepositoryInterface
        );
        $grant->setRefreshTokenTTL(new \DateInterval('P1M')); // refresh tokens will expire after 1 month
        // Enable the password grant on the server with a token TTL of 1 hour
        $server->enableGrantType(
            $grant,
            new \DateInterval('PT1H') // access tokens will expire after 1 hour
        );
        return $server;
    },
    
  ]);
  
  // Authenticate user > Oauth2 user name + password
  $app->post('/access_token', function (ServerRequestInterface $request, ResponseInterface $response) use ($app) {

          /* @var \League\OAuth2\Server\AuthorizationServer $server */
          $server = $app->getContainer()->get(AuthorizationServer::class);
          try {
              
              // Try to respond to the access token request
              return $server->respondToAccessTokenRequest($request, $response);
              
          } catch (OAuthServerException $exception) {
              
              // All instances of OAuthServerException can be converted to a PSR-7 response
              return $exception->generateHttpResponse($response);
              
          } catch (\Exception $exception) {

              // Catch unexpected exceptions
              $body = $response->getBody();
              $body->write($exception->getMessage());
              return $response->withStatus(500)->withBody($body);
              
          }
  });

  $app->run();

  /*

    pw = hex_sha512('test1');

    $.ajax({
        url: 'api/password.php/access_token',
        type: 'post',
      
        data: {
            grant_type: 'password',
            client_id: '_client_id_',
            client_secret: '_client_secret_',
            username: 'test@gmail.com',
            password: pw,
            scope: 'basic email'
        },
      
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            Accept: '1.0'
        },
      
        dataType: 'json',
      
        success: function (data) {
            console.info(data);
        }
    });

  */

?>