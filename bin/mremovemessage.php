<?php

/**************************************************
 * Application: EnigmaV                           *
 * Author: Johnny L. de Alba                      *
 * Date: 09/14/2013                               *
 **************************************************/

include('cmessage.php');

define('DBCONNECTFAIL', 'Database Connection Failed.');
define('QUERYFAIL', 'We are unable to take your request at this time.');

define('PERMISSIONDENIED', 'You do not have permission to remove this message.');
define('REMOVEOK', 'Your message has been successfully removed.');

class MRemoveMessage {

public $result;
public $message;

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
 * State: ValidatePrivileges                      *
 **************************************************/

public function ValidatePrivileges() {

  global $in, $usr, $lmessage;

  if ($in->GetInput('id') == FALSE) return FALSE;
  if ($in->ValidateInt('id') == FALSE) return FALSE;

  $this->message = $lmessage->Join($in->input['id']);

  if ($this->message == FALSE) {
    $this->result = $lmessage->result;
  return FALSE; }

  if ($usr->client['type'] == 'ADN')
    return TRUE;
  
  if ($usr->client['id'] == $this->message['user_id'])
    return TRUE;

return FALSE; }

/**************************************************
 * Component: RemoveFile                          *
 **************************************************/

public function RemoveFile() {

  if (! ($this->message['type'] == 'IMG'))
    return TRUE;

  unlink('message/'.$this->message['pattachment']);
  unlink('message/feature/'.$this->message['pattachment']);
  unlink('message/highlight/'.$this->message['pattachment']);

return TRUE; }

/**************************************************
 * Component: RemoveAllChildren                   *
 **************************************************/

public function RemoveAllChildren() {

  global $lcomment, $lcounter;

  $result = $lcomment->RemoveAll($this->message['id']);

  if ($result == FALSE) {
    $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }

  $lcounter->table = 'message_rating';
  $result = $lcounter->RemoveAll($this->message['id']);

  if ($result == FALSE) {
    $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }

return TRUE; }

/**************************************************
 * Component: DecrementDirectory                  *
 **************************************************/

public function DecrementDirectory() {

  global $ldirectory, $lmessage;

  $directory = $ldirectory->Get($this->message['directory_id']);
  if ($directory['total'] > 0) $directory['total']--;

  $column = $lmessage->GetAll('directory_id', $directory['id'], 0, 1);

  if (! ($column == FALSE))
    $directory['modified'] = $column[0]['created'];
  else $directory['modified'] = $directory['created'];

  $result = $ldirectory->Modify($directory);

  if ($result == FALSE) {
    $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }

return TRUE; }

/**************************************************
 * Process: RemoveMessage                         *
 **************************************************/

public function RemoveMessage() {

  global $usr, $ses, $out, $lmessage;

  if ($ses->ValidateSeed($usr->client['id']) == FALSE)
    return TRUE;

  if ($this->RemoveFile() == FALSE) return FALSE;
  if ($this->RemoveAllChildren() == FALSE) return FALSE;
  if ($this->DecrementDirectory() == FALSE) return FALSE;

  $result = $lmessage->Remove($this->message['id']);

  if ($result == FALSE) {
    $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }
  
  $out->get_dialog('NC', REMOVEOK);

return FALSE; }

/**************************************************
 * State: Display                                 *
 **************************************************/

public function Display() {

  global $out, $ses, $usr, $cmessage;

  $out->GetFile('remove-message.htm');
  $cmessage->message_panel_row($this->message);

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

  if ($in->input['o'] == 'REMOVE')
    if ($this->RemoveMessage() == FALSE)
      return FALSE;

  $this->Display();

return TRUE; }

/**************************************************
 * End Class                                      *
 **************************************************/
};

$z = new MRemoveMessage();
$z->Create();

?>
