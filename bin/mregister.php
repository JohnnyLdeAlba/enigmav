<?php

/**************************************************
 * Application: EnigmaV                           *
 * Author: Johnny L. de Alba                      *
 * Date: 09/14/2012                               *
 **************************************************/

define('DBCONNECTFAIL', 'Database Connection Failed.');
define('LOGGEDIN', 'You must be logged out to use this feature.');
define('QUERYFAIL', 'We are unable to take your request at this time.');

define('RECPERIOD', 'You must wait 24 hours before registering for a new account.');
define('CAPTCHAFAIL', 'Please enter the random letters field exactly as they appear.');

define('EMPTYUSERNAME', 'The username field must not be empty.');
define('COUNTUSERNAME', 'Username\'s cannot be more than 16 characters in length.');

define('EMPTYEMAIL', 'The email field must not be empty.');
define('INVALIDEMAIL', 'The email address you entered is invalid.');
define('EXISTEMAIL', 'The email address you entered is already associated with another account.');

define('EMPTYPASS', 'The password field must not be empty.');
define('COUNTPASS', 'Passwords must be between 6-16 characters in length.');
define('NOMATCHVPASS', 'Please retype your new password in the verify password field.');

class MRegister {

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
 * Component: get_input_all                       *
 **************************************************/

 public function get_input_all() {
 
  global $in;
 
  if ($in->GetInput('fullname') == FALSE) $in->input['fullname'] = '';
    else $in->input['fullname'] = $in->PrepForStorage($in->input['fullname']);
	
  if ($in->GetInput('email') == FALSE) $in->input['email'] = '';
    else $in->input['email'] = $in->PrepForStorage($in->input['email']);
	
  if ($in->GetInput('password') == FALSE) $in->input['password'] = '';
    else $in->input['password'] = $in->PrepForStorage($in->input['password']);

return TRUE; }

/**************************************************
 * Component: get_input_password                 *
 **************************************************/

public function validate_input_password() {

  global $in;

  if (empty($in->input['password']) == TRUE) { 
    $this->result = 'EMPTYPASS';
  return FALSE; }

  if ($in->CharacterCount('password', 6, 16) == FALSE) {
    $this->result = 'COUNTPASS';
  return FALSE; }

  if ($in->GetInput('verify_password') == FALSE) {
    $this->result = 'NOMATCHVPASS';
  return FALSE; }

  if ($in->input['password'] != $in->input['verify_password']) {
    $this->result = 'NOMATCHVPASS';
  return FALSE; }

return TRUE; }

/**************************************************
 * State: Register                                *
 **************************************************/

public function Register() {

  global $in, $out, $usr, $ses;

  if ($ses->ValidateGuestSeed($usr->client_ip) == FALSE)
    return TRUE;

  if ($ses->RecoveryPeriod($usr->client['ip']) == TRUE) {
    $this->result = 'RECPERIOD';
  return TRUE; }
	
  if ($in->GetInput('r') == FALSE) {
    $this->result = 'CAPTCHAFAIL';
  return TRUE; }

  $random = $ses->GetGuestSession($usr->client_ip, 'RANDOM');
  
  if (! ($in->input['r'] == $random['data'])) {
    $this->result = 'CAPTCHAFAIL';
  return TRUE; }

  if (empty($in->input['fullname']) == TRUE) {
      $this->result = 'EMPTYUSERNAME';
  return TRUE; }

  if ($in->CharacterCount('fullname', 1, 16) == FALSE) {
    $this->result = 'COUNTUSERNAME';
  return TRUE; }
  
  if (empty($in->input['email']) == TRUE) {
      $this->result = 'EMPTYEMAIL';
  return TRUE; }

  if ($in->ValidateEmail('email') == FALSE) {
    $this->result = 'INVALIDEMAIL';
  return TRUE; }

  if ($this->validate_input_password() == FALSE)
    return TRUE;

  $result = $usr->CreateUser($in->input['email'],
    $in->input['password'], $in->input['fullname']);
	
  if ($result == FALSE) {
  
  if ($usr->result == 'QUERYFAIL') {
    $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }
  
    $this->result = 'EXISTEMAIL';
  return TRUE; }

  $ses->SetRecoveryPeriod($usr->client['ip']);
  $this->result = 'REGISTEROK';

  $output = "You have successfully registered for an account as %s (%s).";
  $out->get_dialog('NC', sprintf($output, $in->input['fullname'], $in->input['email']));
  
return FALSE; }

/**************************************************
 * Component: set_dialog_box                      *
 **************************************************/

public function set_dialog_box() {

  global $out, $usr;

  if ($this->result == '') {
    $out->SetVariable('dialog_box', '');
  return; }

  if ($this->result == 'NOTLOGGEDIN') {
     $out->set_dialog_box('NC', NOTLOGGEDIN);
  return; }

  $result = '';
  switch ($this->result) {

  case 'RECPERIOD': $result = RECPERIOD;
  break; case 'CAPTCHAFAIL': $result = CAPTCHAFAIL;
  break; case 'EMPTYUSERNAME': $result = EMPTYUSERNAME;
  break; case 'COUNTUSERNAME': $result = COUNTUSERNAME;
  
  break; case 'EMPTYEMAIL': $result = EMPTYEMAIL;
  break; case 'INVALIDEMAIL': $result = INVALIDEMAIL;
  break; case 'EXISTEMAIL': $result = EXISTEMAIL;
  
  break; case 'EMPTYPASS': $result = EMPTYPASS;
  break; case 'COUNTPASS': $result = COUNTPASS;
  break; case 'NOMATCHVPASS': $result = NOMATCHVPASS; }

  $out->set_dialog_box('ER', $result);

return; }

/**************************************************
 * State: Display                                 *
 **************************************************/

public function Display() {

  global $usr, $ses, $in, $out;

  $out->title = 'Register';
  $out->GetFile('register.htm');

  $this->set_dialog_box();
  $result = $out->get_encrpyted_data();
  
  $ses->SaveGuestSession($usr->client_ip, 'RANDOM', $result);
  $out->SetVariable('r', $result);

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

  $this->get_input_all();
  
  if ($in->input['o'] == 'REGISTER') {
    if ($this->Register() == FALSE)
      return FALSE; }

  $this->Display();

return TRUE; }

/**************************************************
 * End Class                                      *
 **************************************************/

};

$z = new MRegister();
$z->Create();

?>
