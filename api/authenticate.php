<?php

  error_reporting( E_ALL );
  ini_set( "display_errors", 1 );
  
  /**
   * @author      Alex Bilbie <hello@alexbilbie.com>
   * @copyright   Copyright (c) Alex Bilbie
   * @license     http://mit-license.org/
   *
   * @link        https://github.com/thephpleague/oauth2-server
   */
  use League\OAuth2\Server\AuthorizationServer;
  use League\OAuth2\Server\CryptKey;
  use League\OAuth2\Server\Exception\OAuthServerException;
  use OAuth2ServerExamples\Repositories\AccessTokenRepository;
  use OAuth2ServerExamples\Repositories\ClientRepository;
  use OAuth2ServerExamples\Repositories\ScopeRepository;
  use Psr\Http\Message\ResponseInterface;
  use Psr\Http\Message\ServerRequestInterface;
  use Slim\App;
  use Zend\Diactoros\Stream;

  // Include file location -->
  include_once('../include-path.php' );
  include_once($includePath . 'config.php' );
  include_once($includePath . 'vendor/autoload.php' );
  //include_once($includePath . 'database.php' );

  $app = new App([
      'settings'                => [
          'displayErrorDetails' => true,
      ],
      AuthorizationServer::class => function () {
          
          global $includePath;
          
          // Init our repositories
          $clientRepository = new ClientRepository(); // instance of ClientRepositoryInterface
          $scopeRepository = new ScopeRepository(); // instance of ScopeRepositoryInterface
          $accessTokenRepository = new AccessTokenRepository(); // instance of AccessTokenRepositoryInterface
          
          // Path to public and private keys
          $privateKey = new CryptKey('file://' . $includePath . 'private.key', OAUTH2_KEY); // private key
          $publicKey = new CryptKey('file://' . $includePath . 'public.key', OAUTH2_KEY);   // public key 
          
          // Setup the authorization server
          $server = new AuthorizationServer(
              $clientRepository,
              $accessTokenRepository,
              $scopeRepository,
              $privateKey,
              $publicKey
          );
          
          // Enable the client credentials grant on the server
          $server->enableGrantType(
              new \League\OAuth2\Server\Grant\ClientCredentialsGrant(),
              new \DateInterval('PT1H') // access tokens will expire after 1 hour
          );
          return $server;
      },
  ]);
  
  $app->post('/access_token', function (ServerRequestInterface $request, ResponseInterface $response) use ($app) {
      
      // @var \League\OAuth2\Server\AuthorizationServer $server
      $server = $app->getContainer()->get(AuthorizationServer::class);
      try {
          // Try to respond to the request
          return $server->respondToAccessTokenRequest($request, $response);
      } catch (OAuthServerException $exception) {
          // All instances of OAuthServerException can be formatted into a HTTP response
          return $exception->generateHttpResponse($response);
      } catch (\Exception $exception) {
          // Unknown exception
          $body = new Stream('php://temp', 'r+');
          $body->write($exception->getMessage());
          return $response->withStatus(500)->withBody($body);
      }
  });
  
  $app->run();
  
  /*
  
    Exapmples
  
    $.ajax({
        url: 'https://xlab.nhl.nl/SmartCityService/api/authenticate.php/access_token',
        type: 'post',
      
        data: {
            grant_type: 'client_credentials',
            client_id: 'app',
            client_secret: '-?!u+9A&N8(Bon$X',
            scope: 'app'
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
    
    $.ajax({
      url: 'https://xlab.nhl.nl/SmartCityService/api/authenticate.php/access_token',
      type: 'post',
    
      data: {
          grant_type: 'client_credentials',
          client_id: 'calculation',
          client_secret: '!vl1Fxy4",YJ@QUz',
          scope: 'app calculation'
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