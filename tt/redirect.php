<?php

/* Start session and load library. */
define('CONSUMER_KEY', 'Your Consumer Key');    //Replace your key and secret
define('CONSUMER_SECRET', 'Your Consumer Secret');

@session_start();
require_once('twitteroauth/twitteroauth.php');
define('OAUTH_CALLBACK', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);


class Twitter_ins extends TwitterOAuth {

  //protected $connection;
  protected $request_token;
  protected $token;
  protected $access_token;

  public function __construct() {
    if(@$_SESSION['twitter']['access_token'])
      $this->access_token = $_SESSION['twitter']['access_token'];
  }

  function make_auth() {
    parent::__construct(CONSUMER_KEY, CONSUMER_SECRET);
    $this->request_token = $this->getRequestToken(OAUTH_CALLBACK);
    if ($this->check_error()) {
      $_SESSION['twitter']['oauth_token'] = $this->token = $this->request_token['oauth_token'];
      $_SESSION['twitter']['oauth_token_secret'] = $this->request_token['oauth_token_secret'];
      $url = $this->getAuthorizeURL($this->token);
      if ($this->check_error() && $url) {
        header('Location: ' . $url);
      } else {
        var_dump($this);
        die('Get authorize url failed!');
      }
    }
  }

  function make_access($debug = false) {
    parent::__construct(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['twitter']['oauth_token'], $_SESSION['twitter']['oauth_token_secret']);
    $this->access_token = $this->getAccessToken($_REQUEST['oauth_verifier']);
    if ($this->check_error()) {
      $_SESSION['twitter']['access_token'] = $this->access_token;
      $_SESSION['twitter']['status'] = 'verified';
      parent::__construct(CONSUMER_KEY, CONSUMER_SECRET, $this->access_token['oauth_token'], $this->access_token['oauth_token_secret']);
      if ($debug)
        return true;
    }
    else {
      if ($debug)
        return false;
      else
        die('Get access token failed!');
    }
  }

  function user_detail() {
    return $this->get('account/verify_credentials');
  }

  function check_error($debug = false) {
    switch ($this->http_code) {
      case 200:
        return true;
        break;
      default:
        if ($debug)
          return false;
        else
          var_dump($this);die;
    }
  }

  function check_access() {
    if (array_key_exists('twitter', $_SESSION) && array_key_exists('status', $_SESSION['twitter']) && $_SESSION['twitter']['status'] == 'verified') {
      parent::__construct(CONSUMER_KEY, CONSUMER_SECRET, $this->access_token['oauth_token'], $this->access_token['oauth_token_secret']);
      $this->get('account/verify_credentials');
      if ($this->check_error(1)) {
        return true;
      } else {
        unset($_SESSION['twitter']);
        return false;
      }
    } else {
      return false;
    }
  }

  function user_login() {
    if ($this->check_access()) {
      return true;
    } else {
      //invalid access_token
      if (!array_key_exists('oauth_token', $_REQUEST)) {
        $this->make_auth();
      } else {
        if($this->make_access(1)) return true;
      }
    }    
  }
}

//$tt = new Twitter_ins();
//var_dump($tt->user_detail());
?>
