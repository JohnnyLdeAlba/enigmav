<?php

/**************************************************
 * Application: EnigmaV                           *
 * Author: Johnny L. de Alba                      *
 * Date: 09/14/2013                               *
 **************************************************/

define('DBCONNECTFAIL', 'Database Connection Failed.');
define('NOTLOGGEDIN', 'You must be logged in to use this feature.');
define('QUERYFAIL', 'We are unable to take your request at this time.');

define('EMPTYEMAIL', 'The email field must not be empty.');
define('INVALIDEMAIL', 'The email address you entered is invalid.');
define('EMPTYPASS', 'The password field must not be empty.');
define('COUNTPASS', 'The password must contain between 6-16 characters.');

define('LOGINFAIL', 'The email address or password you entered does not match our records.');
define('LOGGEDOUT', 'You have successfully logged out.');

class MLogin {

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

  if ($in->GetInput('email') == FALSE) $in->input['email'] = '';
    else $in->input['email'] = $in->PrepForStorage($in->input['email']);
	
  if ($in->GetInput('password') == FALSE) $in->input['password'] = '';
    else $in->input['password'] = $in->PrepForStorage($in->input['password']);

return TRUE; }

/**************************************************
 * Component: get_input_password                  *
 **************************************************/

public function get_input_password() {

  global $in, $usr;

  if ($in->input['password'] == FALSE) {
    $this->result = 'EMPTYPASS';
  return FALSE; }

  if ($in->CharacterCount('password', 6, 16) == FALSE) {
    $this->result = 'COUNTPASS';
  return FALSE; }

return TRUE; }

/**************************************************
 * State: Login                                   *
 **************************************************/

public function Login() {

  global $in, $usr, $ses;

  if ($ses->ValidateGuestSeed($usr->client_ip) == FALSE)
    return TRUE;

  if (empty($in->input['email']) == TRUE) {
      $this->result = 'EMPTYEMAIL';
  return TRUE; }

  if ($in->ValidateEmail('email') == FALSE) {
    $this->result = 'INVALIDEMAIL';
  return TRUE; }

  if ($this->get_input_password() == FALSE)
    return TRUE;

  if ($in->GetInput('persistent') == FALSE)
    $in->input['persistent'] = FALSE;
  else $in->input['persistent'] = TRUE;

  if ($usr->Login($in->input['email'], $in->input['password'],
    $in->input['persistent']) == FALSE) {

  if ($usr->result == 'QUERYFAIL') {
    $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }

    $this->result = 'LOGINFAIL';
  return TRUE; }

  $this->result = 'LOGINOK';

return TRUE; }

/**************************************************
 * State: Logout                                  *
 **************************************************/

public function Logout() {

  global $usr;

  if ($usr->client == NULL) return TRUE;

  $usr->Logout();
  $this->result = 'LOGGEDOUT';

return TRUE; }

/**************************************************
 * Component: set_dialog_box                      *
 **************************************************/

public function set_dialog_box() {

  global $out, $usr;

  if ($this->result == '') {
    $out->SetVariable('dialog_box', '');
  return; }

  if ($this->result == 'LOGGEDOUT') {
     $out->set_dialog_box('NC', LOGGEDOUT);
  return; }

  $result = '';
  switch ($this->result) {

  case 'EMPTYEMAIL': $result = EMPTYEMAIL;
  break; case 'INVALIDEMAIL': $result = INVALIDEMAIL;
  break; case 'EMPTYPASS': $result = EMPTYPASS;
  break; case 'COUNTPASS': $result = COUNTPASS;
  break; case 'LOGINFAIL': $result = LOGINFAIL; }

  $out->set_dialog_box('ER', $result);

return; }

/**************************************************
 * State: Display                                 *
 **************************************************/

public function Display() {

  global $usr, $ses, $in, $out;

  $out->title = 'Welcome, Please Login';
  $out->GetFile('login.htm');

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

  $this->get_input_all();
  
  if ($in->input['o'] == 'LOGIN') {
    if ($this->Login() == FALSE)
  return FALSE; }

  else if ($in->input['o'] == 'LOGOUT') {
    if ($this->Logout() == FALSE)
  return FALSE; }

  if (!($usr->client == NULL) || ($this->result == 'LOGINOK')) {
    header('Location: index.php');
  return; }

  $this->Display();

return TRUE; }

/**************************************************
 * End Class                                      *
 **************************************************/

};

$z = new MLogin();
$z->Create();

?>
