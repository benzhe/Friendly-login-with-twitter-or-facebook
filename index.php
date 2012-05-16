<?php

$login_method = isset($_REQUEST['login']) ? $_REQUEST['login'] : '';
if ($login_method == 'facebook') {
  require 'fb/redirect.php';
  $ins = new Facebook_ins();
  if ($ins->user_login()) {
    $user_info = $_SESSION['user_info'] = $ins -> api('/me');
  }
}
elseif ($login_method == 'twitter') {
  require 'tt/redirect.php';
  $ins = new Twitter_ins();
  if ($ins->user_login()) {
    $user_info = $_SESSION['user_info'] = object_to_array($ins->get('account/verify_credentials');
  }

}

if($user_info){

?>
<p style="padding-top:30px;">sign in: <a href="?login=facebook">Facebook</a>&nbps;<a href="?login=twitter">Twitter</a></p>
<?php
    
}else {

?>
<p style="padding-top:30px;">your name is <?php=$user_info['name'] ?></p>
<?php
}
?>