<?php

/**************************************************
 * Application: EnigmaV                           *    
 * Author: Johnny L. de Alba                      *
 * Date: 09/14/2013                               *
 **************************************************/

include('cdirectory.php');

define('DBCONNECTFAIL', 'Database Connection Failed.');
define('QUERYFAIL', 'We are unable to take your request at this time.');

define('DIRNOTEMPTY', 'The directory must be empty before it can be removed.');
define('REMOVEOK', 'Your directory has successfully been removed.');

class MRemoveDirectory {

public $result;
public $directory;

/**************************************************
 * State: Initialize                              *
 **************************************************/

public function Initialize() {

  global $sql, $in, $out;

  $this->result = '';

  if ($in->GetInput('o') == FALSE)
    $in->input['o'] = 'DEFAULT';
	
  $in->input['o'] = strtoupper($in->input['o']);

  if ($sql->Connect() == FALSE) return FALSE;
  if ($sql->UseDatabase() == FALSE) return FALSE;

return TRUE; }

/**************************************************
 * State: UpdateSession                           *
 **************************************************/

public function UpdateSession() {

  global $out, $usr;

  if ($usr->UpdateSession() == FALSE)
    if ($usr->result == 'QUERYFAIL') {
      $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }

return TRUE; }

/**************************************************
 * State: ValidatePrivileges                      *
 **************************************************/

public function ValidatePrivileges() {

  global $in, $usr, $ldirectory;

  if ($in->GetInput('id') == FALSE) return FALSE;
  if ($in->ValidateInt('id') == FALSE) return FALSE;

  $this->directory = $ldirectory->Get($in->input['id']);
  
  if ($this->directory == FALSE) {
    $this->result = $ldirectory->result;
  return FALSE; }

  if ($usr->client['type'] != 'ADN')
    return FALSE;
  
return TRUE; }

/**************************************************
 * State: RemoveDirectory                         *
 **************************************************/

public function RemoveDirectory() {

  global $in, $out, $usr, $ses, $ldirectory;

  if ($ses->ValidateSeed($usr->client['id']) == FALSE) return TRUE;

  if (! ($this->directory['pattachment'] == ''))
    unlink('directory/icon/'.$this->directory['pattachment']);
	
  if (! ($this->directory['sattachment'] == ''))
    unlink('directory/banner/'.$this->directory['sattachment']);

  if ($ldirectory->Remove($this->directory['id']) == FALSE) {
    $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }

  $out->get_dialog('NC', REMOVEOK);

return FALSE; }

/**************************************************
 * State: Display                                 *
 **************************************************/

public function Display() {

  global $out, $in, $usr, $ses, $cdirectory;

  $out->GetFile('remove-directory.htm');

  $modified = $out->get_date($this->directory['modified']);
  
  $out->SetVariable('directory.id', $this->directory['id']);
  $out->SetVariable('directory.label', $this->directory['title']);
  
  $out->SetVariable('directory.modified', $modified);
  $out->SetVariable('directory.data', $this->directory['data']);

  $cdirectory->directory_image_pattachment($this->directory);
  
  $s = $ses->GenerateSeed($usr->client['id']);
  $out->SetVariable('s', $s);

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
  
  if ($this->directory['total'] > 0) {
    $out->get_dialog('ER', DIRNOTEMPTY);
  return FALSE; }
  
  if ($in->input['o'] == 'REMOVE')
    if ($this->RemoveDirectory() == FALSE)
	  return FALSE;
   
  $this->Display();

return TRUE; }

/**************************************************
 * End Class                                      *
 **************************************************/
};

$mremovedirectory = new MRemoveDirectory();
$mremovedirectory->Create();
