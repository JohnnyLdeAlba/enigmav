<?php

/**************************************************
 * Application: EnigmaV                           *
 * Author: Johnny L. de Alba                      *
 * Date: 09/14/2013                               *
 **************************************************/

 include('cmessage.php');
 
define('DBCONNECTFAIL', 'Database Connection Failed.');
define('NOTLOGGEDIN', 'You must be logged in to use this feature.');
define('QUERYFAIL', 'We are unable to take your request at this time.');

define('EMPTYUSERNAME', 'The username field must not be empty.');
define('COUNTUSERNAME', 'Username\'s cannot be more than 16 characters in length.');

define('EMPTYEMAIL', 'The email field must not be empty.');
define('INVALIDEMAIL', 'The email address you entered is invalid.');
define('EXISTEMAIL', 'The email address you entered is already associated with another account.');

define('EMPTYPASS', 'The current password field must not be empty.');
define('COUNTPASS', 'The current password must contain between 6-16 characters.');
define('NOMATCHPASS', 'The password you entered does not match our records.');

define('COUNTNEWPASS', 'New passwords must be between 6-16 characters in length.');
define('EMPTYVPASS', 'The verify password field must not be empty.');
define('NOMATCHVPASS', 'Please retype your new password in the verify password field.');

define('UNREADFILE', 'Unable to upload avatar, the file is unreadable.');
define('INVALIDIMAGETYPE', 'Avatars must be of file type gif, jpg and png.');
define('AVATARWH', 'Avatars must have a height and width of 128 pixels.');

define('PERMISSIONDENIED', 'You do not have permission to use this feature.');
define('REMOVEAVOK', 'Your avatar has successfully been removed.');
define('MODIFYOK', 'Your account information has successfully been updated.');

class MAccount {

public $result;

/**************************************************
 * State: Initialize                              *
 **************************************************/

public function Initialize() {

  global $sql, $in, $out;

  $this->result = '';

  if ($in->GetInput('o') == FALSE) $in->input['o'] = 'DEFAULT';
  $in->input['o'] = strtoupper($in->input['o']);

  if ($sql->Connect() == FALSE) return FALSE;
  if ($sql->UseDatabase() == FALSE) return FALSE;

return TRUE; }

/**************************************************
 * State: ValidatePrivileges                      *
 **************************************************/

public function ValidatePrivileges() {

  global $usr, $in;

  if ($in->GetInput('id') == FALSE) return FALSE;
  if ($in->ValidateInt('id') == FALSE) return FALSE;

  $this->contact = $usr->GetUser('id', $in->input['id']);
  if ($this->contact == FALSE) return FALSE;

  if ($usr->client['id'] == $this->contact['id']) return TRUE;
  if ($usr->client['type'] == 'ADN') return TRUE;

return FALSE; }

/**************************************************
 * Component: get_input_password                  *
 **************************************************/

public function get_input_password() {

  global $in;

  if ($in->GetInput('current_password') == FALSE)
    $in->input['current_password'] = '';
  else $in->input['current_password'] =
    $in->PrepForStorage($in->input['current_password']);
  
  if ($in->GetInput('new_password') == FALSE)
    $in->input['new_password'] = '';
  else $in->input['new_password'] =
    $in->PrepForStorage($in->input['new_password']);
  
  if ($in->GetInput('verify_password') == FALSE)
    $in->input['verify_password'] = '';
  else $in->input['verify_password'] =
    $in->PrepForStorage($in->input['verify_password']);
	
return FALSE; }

/**************************************************
 * Component: get_input_permissions               *
 **************************************************/

public function get_input_permissions() {

  global $in;

  if ($in->GetInput('hidden') == FALSE)
    $in->input['hidden'] = 'V';
  else $in->input['hidden'] = 'H';
   
  if ($in->GetInput('locked') == FALSE)
    $in->input['locked'] = 'U';
  else $in->input['locked'] = 'L';
 
  $in->input['permissions'] = $in->input['hidden']
    .$in->input['locked'];
	
return FALSE; }

/**************************************************
 * Component: get_input_all                       *
 **************************************************/

 public function get_input_all() {
 
  global $in;
 
  if ($in->GetInput('fullname') == FALSE)
    $in->input['fullname'] = $this->contact['fullname'];
  else $in->input['fullname'] =
    $in->PrepForStorage($in->input['fullname']);
	
  if ($in->GetInput('email') == FALSE)
    $in->input['email'] = $this->contact['email'];
  else $in->input['email'] =
    $in->PrepForStorage($in->input['email']);
	
  $this->get_input_password();
  $this->get_input_permissions();

  
 return TRUE; }

/**************************************************
 * Component: validate_input_password()           *
 **************************************************/

public function validate_input_password() {

  global $in, $usr;

  if ($usr->client['type'] == 'ADN') return TRUE;

  if (empty($in->input['current_password']) == TRUE) {
    $this->result = 'EMPTYPASS';
  return FALSE; }

  if ($in->CharacterCount('current_password', 6, 16) == FALSE) {
    $this->result = 'COUNTPASS';
  return TRUE; }
  
  $in->input['current_password'] =
  md5($in->input['current_password']);

  if (! ($in->input['current_password'] ==
    $this->contact['password'])) {
      $this->result = 'NOMATCHPASS';
  return FALSE; }

return TRUE; }

/**************************************************
 * Component: validate_input_vpassword()          *
 **************************************************/

public function validate_input_vpassword() {

  global $in;

  if (empty($in->input['new_password']) == TRUE)
    return TRUE;

  if ($in->CharacterCount('new_password', 6, 16) == FALSE) {
    $this->result = 'COUNTNEWPASS';
  return FALSE; }
  
  if (empty($in->input['verify_password']) == TRUE) {
    $this->result = 'EMPTYVPASS';
  return FALSE; }

  if ($in->input['new_password'] != $in->input['verify_password']) {
    $this->result = 'NOMATCHVPASS';
  return FALSE; }

  $in->input['new_password'] = md5($in->input['new_password']);
  $this->contact['password'] = $in->input['new_password'];

return TRUE; }

/**************************************************
 * Component: update_image_avatar                 *
 **************************************************/

public function update_image_avatar() {

  global $in;

  $this->contact['avatar'];
  $handle = $in->GetImage('avatar');

  if ($handle == FALSE) {
    if ($in->result == 'NOFILE') return TRUE;
      $this->result = $in->result; // UNREADFILE, INVALIDIMAGETYPE
  return FALSE; }

  if ($in->FixedDimensions($handle, 128, 128) == FALSE) {
    $this->result = 'AVATARWH';
  return FALSE; }

  if (! ($this->contact['avatar'] == ''))
    unlink('usr/av/'.$this->contact['avatar']);

  $filename = sprintf("%s.%s", $this->contact['id'], $handle['type']);
  copy($handle['filename'], 'usr/av/'.$filename);

  $this->contact['avatar'] = $filename;
  
return TRUE; }

/**************************************************
 * State: ModifyAccount                           *
 **************************************************/

public function ModifyAccount() {

  global $in, $usr, $ses;

  if ($ses->ValidateSeed($usr->client['id']) == FALSE)
    return TRUE;

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

  if ($this->validate_input_password() == FALSE) return TRUE;
  if ($this->validate_input_vpassword() == FALSE) return TRUE;
  if ($this->update_image_avatar() == FALSE) return TRUE;

  $previous_email = $this->contact['email'];
  
  $this->contact['fullname'] = $in->input['fullname'];
  $this->contact['email'] = $in->input['email'];
  $this->contact['permissions'] = $in->input['permissions'];

  $result = $usr->ModifyUser($this->contact);

  if ($result == FALSE) {
  
  if ($usr->result == 'QUERYFAIL') {
    $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }
  
    $this->contact['email'] = $previous_email;
  
    $this->result = 'EXISTEMAIL';
  return TRUE; }
  
  $usr->UpdateSession();
  $this->result = 'MODIFYOK';

return TRUE; }

/**************************************************
 * State: RemoveAvatar                            *
 **************************************************/

public function RemoveAvatar() {

  global $in, $out, $usr, $ses;

  $this->state = 'DISPLAY';

  if ($ses->ValidateSeed($usr->client['id']) == FALSE) return TRUE;

  if (! ($this->contact['avatar'] == ''))
    unlink('usr/av/'.$this->contact['avatar']);

  $this->contact['avatar'] = '';

  if ($usr->ModifyUser($this->contact) == FALSE) {
    $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }

  $usr->UpdateSession();
  $this->result = 'REMOVEAVOK';

return TRUE; }

/**************************************************
 * Component: set_dialog_box                      *
 **************************************************/

public function set_dialog_box() {

  global $out;

  if ($this->result == '') {
    $out->SetVariable('dialog_box', '');
  return FALSE; }

  if ($this->result == 'MODIFYOK') {
    $out->set_dialog_box('NC', MODIFYOK);
  return FALSE; }

  if ($this->result == 'REMOVEAVOK') {
    $out->set_dialog_box('NC', REMOVEAVOK);
  return FALSE; }

  $result = '';
  switch ($this->result) {

  case 'EMPTYUSERNAME': $result = EMPTYUSERNAME;
  break; case 'COUNTUSERNAME': $result = COUNTUSERNAME;
  
  break; case 'EMPTYEMAIL': $result = EMPTYEMAIL;
  break; case 'INVALIDEMAIL': $result = INVALIDEMAIL;
  break; case 'EXISTEMAIL': $result = EXISTEMAIL;
  
  break; case 'EMPTYPASS': $result = EMPTYPASS;
  break; case 'COUNTPASS': $result = COUNTPASS;
  break; case 'NOMATCHPASS': $result = NOMATCHPASS;
  
  break; case 'COUNTNEWPASS': $result = COUNTNEWPASS;
  break; case 'EMPTYVPASS': $result = EMPTYVPASS;
  break; case 'NOMATCHVPASS': $result = NOMATCHVPASS;

  break; case 'UNREADFILE': $result = UNREADFILE;
  break; case 'INVALIDIMAGETYPE': $result = INVALIDIMAGETYPE;
  break; case 'AVATARWH': $result = AVATARWH; }

  $out->set_dialog_box('ER', $result);

return FALSE; }

/**************************************************
 * Component: set_checkbox_permissions            *
 **************************************************/

public function set_checkbox_permissions() {

  global $out;

  if (substr($this->contact['permissions'], 0, 1) == 'H')
    $out->SetVariable('contact.hidden', 'checked');
  else $out->SetVariable('contact.hidden', '');
  
  if (substr($this->contact['permissions'], 1, 1) == 'L')
    $out->SetVariable('contact.locked', 'checked');
  else $out->SetVariable('contact.locked', '');
  
return FALSE; }

/**************************************************
 * State: Display                                 *
 **************************************************/

public function Display() {

  global $usr, $ses, $in, $out, $cmessage;

  $out->title = 'Account Settings';
  $out->GetFile('account.htm');

  $this->set_dialog_box();

  $cmessage->user_image_avatar('client', $usr->client['avatar']);
  $out->SetVariable('client.fullname', $usr->client['fullname']);
  $out->SetVariable('client.id', $usr->client['id']);

  $cmessage->user_image_avatar('contact', $this->contact['avatar']);
  $out->SetVariable('contact.id', $this->contact['id']);
  $out->SetVariable('contact.fullname', $this->contact['fullname']);
  $out->SetVariable('contact.email', $this->contact['email']);

  if ($this->contact['avatar'] == '')
    $out->SetClass('contact.remove_avatar', '');
  else $out->ShowClass('contact.remove_avatar');
  
  $this->set_checkbox_permissions();
  
  $in->input['s'] = $ses->GenerateSeed($usr->client['id']);
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
    $out->get_dialog('ER', NOTLOGGEDIN);
  return FALSE; }

  if ($this->ValidatePrivileges() == FALSE) {
    $out->get_dialog('NC', PERMISSIONDENIED);
  return FALSE; }

  $this->get_input_all();
  
  if ($in->input['o'] == 'MODIFY') {
    if ($this->ModifyAccount() == FALSE)
  return FALSE; }

  else if ($in->input['o'] == 'REMOVE-AVATAR') {
    if ($this->RemoveAvatar() == FALSE)
  return FALSE; }

  $this->Display();

return TRUE; }

/**************************************************
 * End Class                                      *
 **************************************************/

};

$z = new MAccount();
$z->Create();

?>
