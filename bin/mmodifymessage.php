<?php

/**************************************************
 * Application: EnigmaV                           *   
 * Author: Johnny L. de Alba                      *
 * Date: 09/07/2013                               *
 **************************************************/

include('cmessage.php');

define('DBCONNECTFAIL', 'Database Connection Failed.');
define('QUERYFAIL', 'We are unable to take your request at this time.');

define('CHARCOUNT', 'Title field cannot have more than 64 characters.');
define('ILLEGALCHARS', 'Illegal characters detected: &gt;, &lt;, &amp;.');
define('INVALIDUTUBE', 'The youtube url you entered is invalid.');

define('UNREADIMAGE', 'Unable to upload image, the file is unreadable.');
define('INVALIDIMAGETYPE', 'Images must be of file type gif, jpg and png.');

define('EMPTYTITLE', 'The title field must not be empty.');
define('EMPTYMESSAGE', 'The message field must not be empty.');

define('REMOVEATTACH', 'Your attachment has been removed.');
define('PERMISSIONDENIED', 'You do not have permission to modify this message.');
define('MODIFYOK', 'Your message has been successfully modified.');

class MModifyMessage {

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

  if ($usr->client['type'] == 'ADN') return TRUE;
  if ($usr->client['id'] == $this->message['user_id'])
    return TRUE;

return FALSE; }

/**************************************************
 * Component: get_input_all                       *
 **************************************************/

 public function get_input_all() {
 
  global $in;
 
  if ($in->GetInput('title') == FALSE)
    $in->input['title'] = $this->message['title'];
  else $in->input['title'] = $in->PrepForStorage($in->input['title']);
	
  if ($in->GetInput('data') == FALSE)
    $in->input['data'] = $this->message['data'];
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
 * State: RemoveAttach                            *
 **************************************************/

public function RemoveAttach() {

  global $out, $ses, $usr, $lmessage;

  if ($ses->ValidateSeed($usr->client['id']) == FALSE)
    return TRUE;

  $this->RemoveFile();
	
  $this->message['type'] = 'MSG';
  $this->message['pattachment'] = '';

  if ($lmessage->Modify($this->message) == FALSE) {
    $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }

  $this->result = 'REMOVEATTACH';

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

  $this->RemoveFile();
  
  $this->message['type'] = 'UTB';
  $this->message['pattachment'] = $in->input['video'];

return TRUE; }

/**************************************************
 * Component: update_image_pattachment            *
 **************************************************/

public function update_image_pattachment() {

  global $in;

  $handle = $in->GetImage('pattachment');

  if ($handle == FALSE) {
    if ($in->result == 'NOFILE') return TRUE;
    $this->result = $in->result; // UNREADFILE, INVALIDIMAGETYPE
  return FALSE; }

  $this->RemoveFile();

  $filename = sprintf("%s.%s", md5($this->message['id']), $handle['type']);
  copy($handle['filename'], 'message/'.$filename);

  $in->CreateThumbFromImage($handle, 560, 'message/feature/'.$filename);
  $in->CreateImagePreview($handle, 400, 'message/highlight/'.$filename);

  $this->message['type'] = 'IMG';
  $this->message['pattachment'] = $filename;

return TRUE; }

/**************************************************
 * State: ModifyMessage                           *
 **************************************************/

public function ModifyMessage() {

  global $in, $out, $usr, $ses, $lmessage;

  if ($usr->client == NULL)
    return TRUE;

  if ($this->validate_input_all() == FALSE)
    return TRUE;
	
  if ($ses->ValidateSeed($usr->client['id']) == FALSE)
    return TRUE;

  if ($this->get_video_pattachment() == FALSE) return TRUE;
  if ($this->update_image_pattachment() == FALSE) return TRUE;

  $this->message['title'] = $in->input['title'];
  $this->message['data'] = $in->input['data'];
  $this->message['permissions'] = $in->input['permissions'];
  
  if ($lmessage->Modify($this->message) == FALSE) {
    $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }

  $this->result = 'MODIFYOK';

return TRUE; }

/**************************************************
 * Component: set_dialog_box                      *
 **************************************************/

public function set_dialog_box() {

  global $out;

  if ($this->result == '') { $out->SetVariable('dialog_box', ''); return; }
  if ($this->result == 'MODIFYOK') { $out->set_dialog_box('NC', MODIFYOK); return; }
  if ($this->result == 'CREATEOK') { $out->set_dialog_box('NC', CREATEOK); return; }

  
  $result = '';
  switch ($this->result) {

  case 'EMPTYTITLE': $result = EMPTYTITLE;
  break; case 'EMPTYMESSAGE': $result = EMPTYMESSAGE;
  break; case 'INVALIDUTUBE': $result = INVALIDUTUBE;
  break; case 'CHARCOUNT': $result = CHARCOUNT;
  break; case 'ILLEGALCHARS': $result = ILLEGALCHARS;
  break; case 'REMOVEATTACH': $result = REMOVEATTACH;
  
  break; case 'UNREADFILE': $result = UNREADIMAGE;
  break; case 'INVALIDIMAGETYPE': $result = INVALIDIMAGETYPE; }

  $out->set_dialog_box('ER', $result);

return FALSE; }

/**************************************************
 * Component: set_output_attachment               *
 **************************************************/
 
public function set_output_attachment() {
 
  global $out; 
 
  switch ($this->message['type']) {
   
  case 'IMG': {
    $out->SetClass('message.utube', '');
	$out->ShowClass('message.image');
  break; }
	
  case 'UTB': {
    $out->SetClass('message.image', '');
	$out->ShowClass('message.utube');
  break; }
  
  default: {
    $out->SetClass('message.image', '');
	$out->SetClass('message.utube', '');
  break; }}
  
  $out->SetVariable('message.pattachment',
    $this->message['pattachment']);
  
return FALSE; }

/**************************************************
 * Component: set_checkbox_permissions            *
 **************************************************/

public function set_checkbox_permissions() {

  global $out;

  if (substr($this->message['permissions'], 0, 1) == 'H')
    $out->SetVariable('message.hidden', 'checked');
  else $out->SetVariable('message.hidden', '');
  
  if (substr($this->message['permissions'], 1, 1) == 'L')
    $out->SetVariable('message.locked', 'checked');
  else $out->SetVariable('message.locked', '');
  
return FALSE; }

/**************************************************
 * State: Display                                 *
 **************************************************/

public function Display() {

  global $out, $in, $usr, $ses, $cmessage;

  $out->title = "Modify Message";
  $out->GetFile('modify-message.htm');
  $this->set_dialog_box();

  $cmessage->user_image_avatar('client', $usr->client['avatar']);

  $out->SetVariable('client.id', $usr->client['id']);
  $out->SetVariable('client.fullname', $usr->client['fullname']);
  $out->SetVariable('message.id', $this->message['id']);
  
  $this->set_output_attachment();
  $out->SetVariable('message.title', $in->input['title']);
  $out->SetVariable('message.data', $in->input['data']);
  $this->set_checkbox_permissions();

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

  $this->get_input_all();
  
  switch ($in->input['o']) {
  
  case 'MODIFY': {
    if ($this->ModifyMessage() == FALSE)
      return FALSE;
  break; }
  
  case 'REMOVE-ATTACH': {
    if ($this->RemoveAttach() == FALSE)
      return FALSE;
  break; }}

  $this->Display();

return TRUE; }

/**************************************************
 * End Class                                      *
 **************************************************/
};

$z = new MModifyMessage();
$z->Create();

?>
