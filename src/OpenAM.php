<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 8/23/18
 * Time: 11:07 AM
 */

namespace SoC\OpenAM;

use Curl\Curl;

class OpenAM
{

    private $details;
    private $token;
    
    private $OPENAM_SESSION_COOKIE;
    private $OPENAM_SESSION_COOKIE;
    private $OPENAM_BASE_URL;
    private $OPENAM_URL_VALIDATE;
    private $OPENAM_URL_GETDEETS;
    private $OPENAM_URL_LOGIN;
    private $OPENAM_URL_REUTRN;
    private $OPENAM_ALLOWED_NETIDS;


    public function __construct(
        $OPENAM_SESSION_COOKIE,
        $OPENAM_BASE_URL,
        $OPENAM_URL_VALIDATE,
        $OPENAM_URL_GETDEETS,
        $OPENAM_URL_LOGIN,
        $OPENAM_URL_REUTRN,
        $OPENAM_ALLOWED_NETIDS
       
    )
    {

        // Load values
        $this->OPENAM_SESSION_COOKIE = $OPENAM_SESSION_COOKIE;
        $this->OPENAM_BASE_URL = $OPENAM_BASE_URL;
        $this->OPENAM_URL_VALIDATE = $OPENAM_URL_VALIDATE;
        $this->OPENAM_URL_GETDEETS = $OPENAM_URL_GETDEETS;
        $this->OPENAM_URL_LOGIN = $OPENAM_URL_LOGIN;
        $this->OPENAM_URL_REUTRN = $OPENAM_URL_REUTRN;
        $this->OPENAM_ALLOWED_NETIDS = $OPENAM_ALLOWED_NETIDS;
        
        
        // get token from cookie
        $this->token = $this->getTokenFromCookie();

        // just do it
        $this->redirectIfInvalid();

        // get details
        try {
            $this->details = $this->saveDetails();
        } catch (\Exception $e) {
            die($e->getMessage());
        }




        // throw error if not in list
        if (!$this->isAbleToGainAccess()) {
            die("You are not in the approved list of NetIDs. ");
        }


    }

    public function getTokenFromCookie()
    {

        // return false if not set
        $cookie_token_key = $this->OPENAM_SESSION_COOKIE;
        if (!isset($_COOKIE[$cookie_token_key])) {
            return false;
        } else {
            return $_COOKIE[$cookie_token_key];
        }


    }

    public function isTokenValid()
    {

        // if false, just return false
        if ($this->token == false) return false;

        // Check if valid
        try {
            $curl = new Curl();
        } catch (\ErrorException $e) {
            die($e->getMessage() . $e->getFile());
        }

        $curl->get($this->OPENAM_URL_VALIDATE . $this->token);

        if ($curl->error) {
            die($curl->errorMessage);
        }

        $response = $curl->response;



        $curl->close();

        // return true if valid, fail for anything else
        if (stristr($response, 'boolean=true')) {
            return TRUE;
        }

        // If invalid, let's go ahead and throw the cookie away forcing it to go back
        // to OpenAM
        else {
            setcookie($this->OPENAM_SESSION_COOKIE, "", time() - 1);
            return FALSE;
        }
    }

    public function redirectIfInvalid()
    {
        if ($this->isTokenValid() != true) {
            header('Location: ' . $this->OPENAM_URL_LOGIN . $this->OPENAM_URL_REUTRN;

        }
    }


    // check if token valid

    /**
     * @return bool|object
     *
     * If token exist, return what known details there are.
     */
    public function saveDetails()
    {


        if ($this->token == false) return false;

        // curl for details, get response, and close connection
        try {
            $curl = new Curl();
        } catch (\ErrorException $e) {
            die($e->getMessage());
        }
        $curl->get($this->OPENAM_URL_GETDEETS . $this->token);
        $response = $curl->response;


        if ($curl->error) {
            $curl->close();
            die("Could not reach server to validate token");
        }


        $curl->close();

        // Create new array structure to better map key/vales than the
        // string blob returned.
        $structured_array = [];

        // break up blob of text into an array list
        $response_array = explode("\n", $response);

        // Loop over each line. Act if key has "token" or "name". Skip "value" lines
        // as these will be handled in a jump ahead ([$index+1]).
        foreach ($response_array as $index => $line) {

            // break current line into a key/value using array_pad and explode
            list($key, $value) = array_pad(explode("=", $line), 2, ' ');

            // if current line has token, grab it.
            if ($key == 'userdetails.token.id') {
                $structured_array['token'] = $value;
            }

            // if current line contains a tmbining these two lines.
            if (stristr($key, 'name')) {
                list($next_key, $next_value) = array_pad(explode('=', $response_array[$index + 1]), 2, ' ');
                unset($next_key);
                $structured_array[$value] = $next_value;
            }

            // otherwise, intentionally skip. We've already processed the value lines.



        }

        // return cast as object
        return (object)$structured_array;
    }

    public function getDetails()
    {
        return $this->details;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function isAbleToGainAccess()
    {

        // get current
        $whoami = trim(strtolower($this->getNetID()));

        // is netid in the list.
        return in_array($whoami, explode(',', $this->OPENAM_ALLOWED_NETIDS')));
    }

    public function getNetID()
    {
        if (gettype($this->details) == "object") {
            return $this->details->uid;
        }
        else {
            die("Error in __FILE__ at __LINE__. Please refer this error to SoCIT");
        }

    }


}

