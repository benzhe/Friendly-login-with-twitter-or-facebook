<?php
/* redirect and callback */
@session_start();
require_once 'src/facebook.php';
class Facebook_ins extends Facebook 
{
  // Get User ID
  public $user ;
  
  public function __construct() {
    $config = array(
      'appId'  => 'Your AppID',       //Replace your id and secret
      'secret' => 'Your Secret',
    );
    parent::__construct($config);
    $this -> user = $this->getUser();
  } 
  
  public function check_login(){
    //It is awful to check the login status in facebook!
    //    $params = array(
    //      'ok_session' => 'https://www.myapp.com/',
    //      'no_user' => 'https://www.myapp.com/no_user',
    //      'no_session' => 'https://www.myapp.com/no_session',
    //    );
    //    if($this->getLoginStatusUrl()) {
    //      
    //    }
    try {
      $this->api('/me');
      return true;
    }
    catch(FacebookApiException $e){
      //Error, auth again
      $this->user = null;
      $this->user_login();
      return false;
    }
    
  }

  public function user_detail() {
    return object_to_array ($ins -> api ('/me'));    
  }
  
  public function user_login() {
    if ($this->user) {
      if($this->check_login()){
        $_SESSION['facebook']['status'] = 'verified';
        return true;
      }
    } 
    else {
      if (isMobile()) {
        $loginUrl = $this->getLoginUrl(array('scope' => 'read_stream, friends_likes, publish_stream','display' => 'touch'));
      }
      else {
        $loginUrl = $this->getLoginUrl(array('scope' => 'read_stream, friends_likes, publish_stream'));
        header('Location:' . $loginUrl);
      }
    }
  }

}
//$fb = new Facebook_ins();
//if($fb->user_login()) var_dump($fb->user_detail());
?>
