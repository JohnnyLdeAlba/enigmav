<?php

/**************************************************
 * Application: EnigmaV                           *
 * Author: Johnny L. de Alba                      *
 * Date: 09/14/2013                               *
 **************************************************/

 include('cmessage.php');
 
define('DBCONNECTFAIL', 'Database Connection Failed.');
define('QUERYFAIL', 'We are unable to take your request at this time.');
define('PERMISSIONDENIED', 'You do not have permission to use view this contact.');

class MContact {

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

return TRUE; }

/**************************************************
 * State: Display                                 *
 **************************************************/

public function Display() {

  global $usr, $ses, $in, $out, $cmessage;

  $out->title = sprintf('Contact :: ', $this->contact['fullname']);
  $out->GetFile('contact.htm');

  $cmessage->user_image_avatar('contact', $this->contact['avatar']);
  
  $created = $out->get_date($this->contact['created']);
  $modified = $out->get_date($this->contact['modified']);

  $type = 'default/level-user.png';
  if ($this->contact['type'] == 'ADN')
    $type = 'default/level-admin.png';
  
  $out->SetVariable('contact.id', $this->contact['id']);
  $out->SetVariable('contact.fullname', $this->contact['fullname']);
  $out->SetVariable('contact.email', $this->contact['email']);
  
  $out->SetVariable('contact.type', $type);
  $out->SetVariable('contact.created', $created);
  $out->SetVariable('contact.modified', $modified);

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

  if ($this->ValidatePrivileges() == FALSE) {
    $out->get_dialog('NC', PERMISSIONDENIED);
  return FALSE; }

  $this->Display();

return TRUE; }

/**************************************************
 * End Class                                      *
 **************************************************/

};

$z = new MContact();
$z->Create();

?>
