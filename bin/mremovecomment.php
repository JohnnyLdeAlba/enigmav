<?php

/**************************************************
 * Application: EnigmaV                           *
 * Author: Johnny L. de Alba                      *
 * Date: 09/14/2013                               *
 **************************************************/

include('ccomment.php');

define('DBCONNECTFAIL', 'Database Connection Failed.');
define('QUERYFAIL', 'We are unable to take your request at this time.');

define('PERMISSIONDENIED', 'You do not have permission to remove this comment.');
define('REMOVEOK', 'Your comment has been successfully removed.');

class MRemoveComment {

public $result;
public $comment;

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

  global $in, $usr, $lcomment;

  if ($in->GetInput('id') == FALSE) return FALSE;
  if ($in->ValidateInt('id') == FALSE) return FALSE;

  $this->comment = $lcomment->Join($in->input['id']);

  if ($this->comment == FALSE) {
    $this->result = $lcomment->result;
  return FALSE; }

  if ($usr->client['type'] == 'ADN')
    return TRUE;
  
  if ($usr->client['id'] == $this->comment['user_id'])
    return TRUE;

return FALSE; }

/**************************************************
 * Component: DecrementMessage                    *
 **************************************************/

public function DecrementMessage() {

  global $lmessage, $lcomment;

  $message = $lmessage->Get($this->comment['parent_id']);
  if ($message['total'] > 0) $message['total']--;

  $result = $lmessage->Modify($message);

  if ($result == FALSE) {
    $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }

return TRUE; }

/**************************************************
 * Process: RemoveComment                         *
 **************************************************/

public function RemoveComment() {

  global $usr, $ses, $out, $lcomment;

  if ($ses->ValidateSeed($usr->client['id']) == FALSE)
    return TRUE;

  if ($this->DecrementMessage() == FALSE)
    return FALSE;

  $result = $lcomment->Remove($this->comment['id']);

  if ($result == FALSE) {
    $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }
  
  $out->get_dialog('NC', REMOVEOK);

return FALSE; }

/**************************************************
 * State: Display                                 *
 **************************************************/

public function Display() {

  global $out, $ses, $usr, $ccomment;

  $out->GetFile('remove-comment.htm');
  $ccomment->comment_panel_row($this->comment);

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
    if ($this->RemoveComment() == FALSE)
      return FALSE;

  $this->Display();

return TRUE; }

/**************************************************
 * End Class                                      *
 **************************************************/
};

$z = new MRemoveComment();
$z->Create();

?>
