<?php

/**************************************************
 * Application: EnigmaV                           *         
 * Module: MContact                               *
 * Author: Johnny L. de Alba                      *
 * Date: 03/22/2011                               *
 **************************************************/

include('comcontact.php');
include('comufolder.php');
include('comumessage.php');

define('DBCONNECTFAIL', 'Database Connection Failed.');
define('QUERYFAIL', 'We are unable to take your request at this time.');
define('PERMISSIONDENIED', 'You do not have permission to view this folder.');

define('NOTLOGGEDIN', 'You must be logged in to use this feature.');
define('MAXREQUESTS', 'You have reached the maximum amount of subscription requests.');
define('MAXAWAITING', 'This contact cannot accept anymore subscribers at this time.');

class MDisplay {

public $result;

/**************************************************
 * State: Initialize                              *
 **************************************************/

public function Initialize() {

  global $sql, $in, $out;

  $this->result = '';

  if ($in->GetInput('o') == FALSE) $in->input['o'] = 'INDEX';
    $in->input['o'] = strtolower($in->input['o']);

  if ($sql->Connect() == FALSE) return FALSE;
  if ($sql->UseDatabase() == FALSE) return FALSE;

return TRUE; }

/**************************************************
 * State: UpdateSession                           *
 **************************************************/

public function UpdateSession() {

  global $out, $usr, $lsubscriber;

  if ($usr->UpdateSession() == FALSE)
    if ($usr->result == 'QUERYFAIL') {
      $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }

return TRUE; }

/**************************************************
 * Component: UpdateSubscriber                    *
 **************************************************/

public function UpdateSubscriber() {

  global $out, $usr, $lsubscriber;

  if ($lsubscriber->UpdateSubscriber() == FALSE)
    if ($lsubscriber->result == 'QUERYFAIL') {
      $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }

return TRUE; }

/**************************************************
 * State: ValidatePrivileges                      *
 **************************************************/

public function ValidatePrivileges() {

  global $usr, $in;
  global $lsubscriber;

  if ($in->GetInput('id') == FALSE) return FALSE;
  if ($in->ValidateInt('id') == FALSE) return FALSE;

  $usr->contact = $usr->GetUser('id', $in->input['id']);
  if ($usr->contact == FALSE) { $this->result = $usr->result;
    return FALSE; }

  if ($this->UpdateSubscriber() == FALSE) return FALSE;

  if ($usr->client['id'] == $usr->contact['id']) return TRUE;
  if ($usr->client['type'] == 'ADN') return TRUE;

  if ($lsubscriber->ReadPermission($usr->contact['permissions']) == FALSE)
    return FALSE;

return TRUE; }

/**************************************************
 * Component: set_messagebox                      *
 **************************************************/

public function set_messagebox() {

  global $out;

  if ($this->result == '') {
    $out->SetVariable('messagebox', '');
  return; }

  $result = '';
  switch ($this->result) {
  case 'NOTLOGGEDIN': $result = EMPTYMESSAGE;
  break; case 'MAXREQUESTS': $result = MAXREQUESTS;
  break; case 'MAXAWAITING': $result = MAXAWAITING; }

  $out->set_messagebox('ER', $result);

return; }

/**************************************************
 * State: get_header                              *
 **************************************************/

public function get_header() {

  global $out, $usr;

  $title = $out->GetClass('document.title');
  $out->SetClass('document.title', '');

  $out->SetVariable('title', $title);
  $out->title = sprintf("%s - %s", $title, $usr->contact['fullname']);

  $out->keywords = $out->GetClass('document.keywords');
  $out->SetClass('document.keywords', '');

  $out->description = $out->GetClass('document.description');
  $out->SetClass('document.description', '');

return; }

/**************************************************
 * State: get_menupanel                           *
 **************************************************/

public function get_menupanel() {

  global $out, $in, $usr;

  $output = $out->GetOutput();
  $out->GetFile(sprintf("htm/%s-menupanel.htm", $usr->contact['id']));
  $result = $out->GetOutput();

  $out->SetOutput($output);
  $out->SetVariable('contact.menupanel', $result);

return; }

/**************************************************
 * State: Display                                 *
 **************************************************/

public function Display() {

  global $out, $in, $usr, $ccontact;

  $out->GetFile('contact.htm');
  $output = $out->GetOutput();

  $out->GetFile(sprintf("htm/%s-%s.htm", $usr->contact['id'], $in->input['o']));
  $result = $out->GetOutput();

  $out->SetOutput($output);
  $out->SetVariable('document.output', $result);

  $this->get_header();
  $this->set_messagebox();

  $this->get_menupanel();
  $ccontact->contact_panel_user();

  $out->Display();

return; }

/**************************************************
 * Method: Create                                 *
 **************************************************/

public function Create() {

  global $in, $out, $usr;
  global $lsubscriber, $cumessage;

  if ($this->Initialize() == FALSE) {
    $out->get_dialog('ER', DBCONNECTFAIL);
  return FALSE; }

  if ($this->UpdateSession() == FALSE) return FALSE;

  if ($this->ValidatePrivileges() == FALSE) {
    $out->get_dialog('NC', PERMISSIONDENIED);
  return FALSE; }

  $this->Display();
return TRUE; }

/**************************************************
 * End Class                                      *
 **************************************************/
};

$z = new MDisplay();
$z->Create();

?>
