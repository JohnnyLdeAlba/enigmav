<?php

/**************************************************
 * Application: EnigmaV                           *
 * Author: Johnny L. de Alba                      *
 * Date: 09/14/2013                               *
 **************************************************/

define('DBCONNECTFAIL', 'Database Connection Failed.');
define('LOGGEDIN', 'You must be logged out to use this feature.');
define('QUERYFAIL', 'We are unable to take your request at this time.');
define('RECPERIOD', 'You must wait 24 hours before submitting a new password request.');

define('EMPTYEMAIL', 'The email field must not be empty.');
define('INVALIDEMAIL', 'The email address you entered is invalid.');
define('EMAILNOEXIST', 'The email address you entered does not exist in our records.');

define('SENDFAIL', 'A new password could not be sent at this time.');
define('SENDOK', 'A new password has been sent to the specified email address.');

class MRecoverPassword {

public $result;

/**************************************************
 * State: Initialize                              *
 **************************************************/

public function Initialize() {

  global $sql, $in;

  $this->result = '';

  if ($in->GetInput('o') == FALSE) $in->input['o'] = 'DEFAULT';
    $in->input['o'] = strtoupper($in->input['o']);

  if ($sql->Connect() == FALSE) return FALSE;
  if ($sql->UseDatabase() == FALSE) return FALSE;

return TRUE; }

/**************************************************
 * Component: get_input_email                     *
 **************************************************/

public function get_input_email() {

  global $in;

  if ($in->GetInput('email') == FALSE) {
    $this->result = 'EMPTYEMAIL';
  return FALSE; }

  if ($in->ValidateEmail('email') == FALSE) {
    $this->result = $in->result;
  return FALSE; }
  
return TRUE; }

/**************************************************
 * State: mail_password                           *
 **************************************************/

public function mail_password($fullname, $email, $password) {
 
  $header = "From: The Arkonviox Network <administrator@arkonviox.com>";
  $header.= "\r\nReply-To: johnny.dealba@gmail.com";
  $header.= "\r\nX-Mailer: PHP/".phpversion();
 
  $subject = "Password Recovery";

  $message = "Dear %s\r\n\r\n";
  $message.= "Your password has been reset for %s at email address %s.";
  $message.= "The new password is as follows:\r\n\r\n";
  
  $message.= "Password: %s\r\n\r\n";
  $message.= "Sincerly,\r\n\r\n";
  $message.= "The Arkonviox Network Team";
  
  $message = sprintf($message, $fullname, $fullname, $email, $password);

  if (mail($email, $subject, $message, $header) == FALSE)
    return FALSE;

return TRUE; }

/**************************************************
 * State: RecoverPassword                         *
 **************************************************/

 public function RecoverPassword() {
 
  global $in, $out, $usr, $ses;

  if ($ses->ValidateGuestSeed($usr->client_ip) == FALSE)
    return TRUE;

  if ($ses->RecoveryPeriod($usr->client['ip']) == TRUE) {
    $this->result = 'RECPERIOD';
  return TRUE; }
	
  if ($this->get_input_email() == FALSE)
    return TRUE;
  
  $user = $usr->GetUser('email', $in->input['email']);
  if ($user == FALSE) {
  
  if ($usr->result == 'QUERYFAIL') {
    $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }
  
    $this->result = 'EMAILNOEXIST';
  return TRUE; }
 
  if ($user['type'] == 'ADN') {
    $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }
 
  $password = $out->get_encrpyted_data();
  $user['password'] = md5($password);
 
  if ($this->mail_password($user['fullname'],
    $user['email'], $password) == FALSE) {
      $this->result = 'SENDFAIL';
  return TRUE; }
 
  if ($usr->ModifyUser($user) == FALSE) {
    $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }
 
  $ses->SetRecoveryPeriod($usr->client['ip']);
  $this->result = 'SENDOK';
  
  $out->get_dialog('NC', SENDOK);
 
 return FALSE; }
 
/**************************************************
 * Component: set_dialog_box                      *
 **************************************************/

public function set_dialog_box() {

  global $out, $usr;

  if ($this->result == '') {
    $out->SetVariable('dialog_box', '');
  return FALSE; }

  if ($this->result == 'LOGGEDOUT') {
     $out->set_dialog_box('NC', LOGGEDOUT);
  return FALSE; }

  $result = '';
  switch ($this->result) {
  
  case 'RECPERIOD': $result = RECPERIOD;
  break; case 'EMPTYEMAIL': $result = EMPTYEMAIL;
  break; case 'INVALIDEMAIL': $result = INVALIDEMAIL;
  break; case 'EMAILNOEXIST': $result = EMAILNOEXIST;
  break; case 'SENDFAIL': $result = SENDFAIL; }

  $out->set_dialog_box('ER', $result);

return FALSE; }

/**************************************************
 * State: Display                                 *
 **************************************************/

public function Display() {

  global $usr, $ses, $in, $out;

  $out->title = 'Recover Password';
  $out->GetFile('recover-password.htm');

  $this->set_dialog_box();

  $in->input['s'] = $ses->GenerateGuestSeed($usr->client_ip);
  $out->SetVariable('s', $in->input['s']);

  $out->Display('DEFAULT');

return; }

/**************************************************
 * Method: Create                                 *
 **************************************************/

public function Create() {

  global $in, $out, $usr;

  if ($this->Initialize() == FALSE) {
    $out->get_dialog('ER', DBCONNECTFAIL);
  return FALSE; }

  if ($usr->UpdateSession() == FALSE) {
    if ($usr->result == 'QUERYFAIL') {
      $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }}

  if (! ($usr->client == NULL)) {
    $out->get_dialog('NC', LOGGEDIN);
  return FALSE; }
  
  if ($in->input['o'] == 'RECOVER-PASSWORD')
    if ($this->RecoverPassword() == FALSE)
      return FALSE;

  $this->Display();

return TRUE; }

/**************************************************
 * End Class                                      *
 **************************************************/

};

$z = new MRecoverPassword();
$z->Create();

?>
