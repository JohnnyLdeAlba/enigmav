<?php

/**************************************************
 * Application: EnigmaV                           *  
 * Author: Johnny L. de Alba                      *
 * Date: 09/08/2013                               *
 **************************************************/

include('cmessage.php');

define('DBCONNECTFAIL', 'Database Connection Failed.');
define('QUERYFAIL', 'We are unable to take your request at this time.');
define('FLOODDETECT', 'You must wait 1 minute before posting again.');

define('CHARCOUNT', 'Title field cannot have more than 64 characters.');
define('ILLEGALCHARS', 'Illegal characters detected: &gt;, &lt;, &amp;.');
define('INVALIDUTUBE', 'The youtube url you entered is invalid.');

define('UNREADIMAGE', 'Unable to upload image, the file is unreadable.');
define('INVALIDIMAGETYPE', 'Images must be of file type gif, jpg and png.');

define('EMPTYTITLE', 'The title field must not be empty.');
define('EMPTYMESSAGE', 'The message field must not be empty.');

define('PERMISSIONDENIED', 'You do not have permission to create a message.');

class MCreateMessage {

public $result;
public $directory;

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

  global $in, $usr, $ldirectory;

  if ($in->GetInput('id') == FALSE) return FALSE;
  if ($in->ValidateInt('id') == FALSE) return FALSE;

  $this->directory = $ldirectory->Get($in->input['id']);

  if ($this->directory == FALSE) {
    $this->result = $ldirectory->result;
  return FALSE; }

  if (substr($this->directory['permissions'], 1, 1) == 'U')
    return TRUE;
  
  if ($usr->client['type'] == 'ADN')
    return TRUE;

return FALSE; }

/**************************************************
 * Component: get_input_all                       *
 **************************************************/

 public function get_input_all() {
 
  global $in;
 
  if ($in->GetInput('title') == FALSE)
    $in->input['title'] = '';
  else $in->input['title'] = $in->PrepForStorage($in->input['title']);
	
  if ($in->GetInput('data') == FALSE)
    $in->input['data'] = '';
  else $in->input['data'] = $in->PrepForStorage($in->input['data']);

  if ($in->GetInput('hidden') == FALSE)
    $in->input['hidden'] = 'V';
  else $in->input['hidden'] = 'H';
   
  if ($in->GetInput('locked') == FALSE)
    $in->input['locked'] = 'U';
  else $in->input['locked'] = 'L';
 
  $in->input['permissions'] = $in->input['hidden']
    .$in->input['locked'];
	
return TRUE; }

 /**************************************************
 * Component: validate_input_all                  *
 **************************************************/

public function validate_input_all() {

  global $in;

  if (empty($in->input['title']) == TRUE) {
    $this->result = 'EMPTYTITLE';
  return FALSE; }

  if (strlen($in->input['title']) >= 64) {
    $this->result = 'CHARCOUNT';
  return FALSE; }
 
  if (empty($in->input['data']) == TRUE) {
    $this->result = 'EMPTYMESSAGE';
  return FALSE; }
 
return TRUE; }
 
/**************************************************
 * Component: get_video_pattachment               *
 **************************************************/

public function get_video_pattachment() {

  global $in;

  if ($in->GetInput('video') == FALSE)
    return TRUE;

  if ($in->YouTubeCode('video') == FALSE) {
    $this->result = $in->result; // INVALIDUTUBE
  return FALSE; }

  $in->input['type'] = 'UTB';
  $in->input['pattachment'] = $in->input['video'];

return TRUE; }

/**************************************************
 * Component: get_image_pattachment                  *
 **************************************************/

public function get_image_pattachment() {

  global $in;

  $in->input['image'] = $in->GetImage('pattachment');

  if ($in->input['image'] == FALSE) {
    if ($in->result == 'NOFILE') return TRUE;
    $this->result = $in->result; // UNREADFILE, INVALIDIMAGETYPE
  return FALSE; }

  $in->input['type'] = 'IMG';
  
return TRUE; }

/**************************************************
 * Component: set_image_pattachment               *
 **************************************************/

public function set_image_pattachment($message_id) {

  global $in, $lmessage;

  $filename = sprintf("%s.%s", md5($message_id), $in->input['image']['type']);
  copy($in->input['image']['filename'], 'message/'.$filename);

  $in->CreateThumbFromImage($in->input['image'], 640, 'message/feature/'.$filename);
  $in->CreateImagePreview($in->input['image'], 400, 'message/highlight/'.$filename);
  
  $row = $lmessage->Get($message_id);

  $row['type'] = 'IMG';
  $row['pattachment'] = $filename;

  $result = $lmessage->Modify($row);
  if ($result == FALSE) { $this->result = 'QUERYFAIL';
    return FALSE; }

return TRUE; }

/**************************************************
 * Component: IncrementDirectory                  *
 **************************************************/

public function IncrementDirectory() {

  global $ldirectory;

  $this->directory['modified'] = date('Y-m-d H:i:s');
  $this->directory['total']++;

  $result = $ldirectory->Modify($this->directory);
  if ($result == FALSE) { $this->result = 'QUERYFAIL';
    return FALSE; }

return TRUE; }

/**************************************************
 * Component: SaveMessage                         *
 **************************************************/

public function SaveMessage($directory_id) {

  global $usr, $in, $out, $lmessage;

  $row = array(0,
    $directory_id,
	$usr->client['id'], 0, 0,
    $in->input['title'],
    $in->input['type'],
    $in->input['data'],
    $in->input['pattachment'], '',
  $in->input['permissions'], '', '');

  $message_id = $lmessage->Create($row);
  
  if ($message_id == FALSE) {
    $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }

  if ($this->IncrementDirectory() == FALSE)
    return FALSE;

return $message_id; }

/**************************************************
 * State: get_template_createok                   *
 **************************************************/

public function get_template_createok($id) {

  global $out;

  $out->GetFile('create-message-ok.htm');
  $out->SetVariable('message.id', $id);
  $out->Display('DEFAULT');

return FALSE; }

/**************************************************
 * State: CreateMessage                           *
 **************************************************/

public function CreateMessage() {

  global $in, $usr, $ses;

  if ($usr->client == NULL)
    return TRUE;

  if (substr($this->directory['permissions'], 1, 1) == 'L') {
    $out->get_dialog('NC', PERMISSIONDENIED);
  return FALSE; }
  
  if ($this->validate_input_all() == FALSE)
    return TRUE;

  if ($ses->ValidateSeed($usr->client['id']) == FALSE)
    return TRUE;
	
  if ($ses->FloodDetected($usr->client['id']) == TRUE) {
    $this->result = 'FLOODDETECT';
  return TRUE; }
  
  $in->input['type'] = 'MSG';
  $in->input['pattachment'] = '';

  if ($this->get_video_pattachment() == FALSE) return TRUE;
  if ($this->get_image_pattachment() == FALSE) return TRUE;

  $message_id = $this->SaveMessage($this->directory['id']);
  if ($message_id == FALSE) return FALSE;

  if ($in->input['type'] == 'IMG')
    $this->set_image_pattachment($message_id);

  $ses->SetFloodDetect($usr->client['id']);
  $this->result = 'CREATEOK';
  
  $this->get_template_createok($message_id);
  
return FALSE; }

/**************************************************
 * Component: set_dialog_box                      *
 **************************************************/

public function set_dialog_box() {

  global $out;

  if ($this->result == '') { $out->SetVariable('dialog_box', ''); return FALSE; }

  $result = '';
  switch ($this->result) {

  case 'FLOODDETECT': $result = FLOODDETECT;
  break; case 'EMPTYTITLE': $result = EMPTYTITLE;
  break; case 'EMPTYMESSAGE': $result = EMPTYMESSAGE;
  break; case 'INVALIDUTUBE': $result = INVALIDUTUBE;
  break; case 'CHARCOUNT': $result = CHARCOUNT;
  break; case 'ILLEGALCHARS': $result = ILLEGALCHARS;

  break; case 'UNREADFILE': $result = UNREADIMAGE;
  break; case 'INVALIDIMAGETYPE': $result = INVALIDIMAGETYPE; }

  $out->set_dialog_box('ER', $result);

return FALSE; }

/**************************************************
 * Component: set_input_value                     *
 **************************************************/

public function set_input_value($id) {

  global $in, $out;

  if (empty($in->input[$id]) == FALSE)
    $in->input[$id] = $in->PrepForStorage($in->input[$id]);
  else $in->input[$id] = '';

  $out->SetVariable('message.'.$id, $in->input[$id]);
  
return FALSE; }

/**************************************************
 * Component: set_checkbox_permissions            *
 **************************************************/

public function set_checkbox_permissions() {

  global $in, $out;

  if (substr($in->input['permissions'], 0, 1) == 'H')
    $out->SetVariable('message.hidden', 'checked');
  else $out->SetVariable('message.hidden', '');
  
  if (substr($in->input['permissions'], 1, 1) == 'L')
    $out->SetVariable('message.locked', 'checked');
  else $out->SetVariable('message.locked', '');
  
return FALSE; }

/**************************************************
 * State: Display                                 *
 **************************************************/

public function Display() {

  global $out, $in, $usr, $ses, $cmessage;

  $out->title = "Create Message";
  $out->GetFile('create-message.htm');
  $this->set_dialog_box();

  $cmessage->user_image_avatar('client', $usr->client['avatar']);
  
  $out->SetVariable('client.id', $usr->client['id']);
  $out->SetVariable('client.fullname', $usr->client['fullname']);
  $out->SetVariable('directory.id', $this->directory['id']);

  $out->SetVariable('message.title', $in->input['title']);
  $out->SetVariable('message.data', $in->input['data']);
  $this->set_checkbox_permissions();

  $s = $ses->GenerateSeed($usr->client['id']);
  $out->SetVariable('s', $s);
  
  $out->Display('DEFAULT');

return FALSE; }

/**************************************************
 * Method: Create                                 *
 **************************************************/

public function Create() {

  global $in, $out, $usr;
  global $cmessage, $cucomment;

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

  $this->get_input_all();
  
  if ($in->input['o'] == 'CREATE')
    if ($this->CreateMessage() == FALSE)
      return FALSE;

  $this->Display();

return TRUE; }

/**************************************************
 * End Class                                      *
 **************************************************/
};

$z = new MCreateMessage();
$z->Create();

?>
