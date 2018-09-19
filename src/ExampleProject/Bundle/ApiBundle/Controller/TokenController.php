<?php

namespace ExampleProject\Bundle\ApiBundle\Controller;

use Nelmio\ApiDocBundle\Annotation as Nelmio;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TokenController
 *
 * Allows user to authenticate using OAuth 2.0
 *
 */
class TokenController
{
    /**
     * Get access token
     *
     * @Nelmio\ApiDoc(
     *  section="OAuth 2.0",
     *  parameters={
     *      {"name"="grant_type",       "dataType"="string", "required"=true,  "format"="[password, refresh_token]", "description"="Type of authentication."},
     *      {"name"="username",         "dataType"="string", "required"=false, "description"="Username or email (required when grant_type is password)."},
     *      {"name"="password",         "dataType"="string", "required"=false, "description"="Password (required when grant_type is password)."},
     *      {"name"="refresh_token",    "dataType"="string", "required"=false, "description"="Refresh token (required when grant_type is refresh_token)."},
     *
     *      {"name"="client_id",        "dataType"="string", "required"=true,  "description"="Is the following format: id_randomId."},
     *      {"name"="client_secret",    "dataType"="string", "required"=true,  "description"="Is the following format: secret."}
     *
     *  },
     *  statusCodes={
     *      200="Returned when successful",
     *      400={
     *        "Returned when authentication fails",
     *        "Returned when invalid parameters detected"
     *      }
     *  }
     * )
     *
     * {@inheritdoc}
     *
     * @return Response
     */
    public function tokenAction(Request $request)
    {
        try {

            // We try to get a response from OAuth 2.0 server

            /**
             * @var Response $response
             */
            $response = $this->server->grantAccessToken($request);

            $data = array(); // Set data from response

        } catch(OAuth2ServerException $e) {

            // In case of an exception, we re-throw our own Exception

            throw new AuthException($e->getDescription());
        }

        $auth = new Auth(
            $data['access_token'],
            $data['expires_in'],
            $data['token_type'],
            $data['scope'],
            $data['refresh_token']
        );

        return $auth;
    }
}
