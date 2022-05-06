<?php

/**************************************************
 * Application: EnigmaV                           * 
 * Author: Johnny L. de Alba                      *
 * Date: 09/08/2013                               *
 **************************************************/

include('cdirectory.php');
include('cmessage.php');
include('ccomment.php');

define('DBCONNECTFAIL', 'Database Connection Failed.');
define('QUERYFAIL', 'We are unable to take your request at this time.');
define('FLOODDETECT', 'You must wait 1 minute before posting again.');

define('ILLEGALCHARS', 'Illegal characters detected: &gt;, &lt;, &amp;.');
define('INVALIDUTUBE', 'The youtube url you entered is invalid.');

define('UNREADIMAGE', 'Unable to upload image, the file is unreadable.');
define('INVALIDIMAGETYPE', 'Images must be of file type gif, jpg and png.');
define('EMPTYMESSAGE', 'The comment field must not be empty.');

define('PERMISSIONDENIED', 'You do not have permission to view this message.');
define('CREATEOK', 'Your comment has been successfully posted.');

class MViewMessage {

public $result;

public $message;
public $directory;

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

  global $in, $usr, $lmessage, $ldirectory;

  if ($in->GetInput('id') == FALSE) return FALSE;
  if ($in->ValidateInt('id') == FALSE) return FALSE;

  $this->message = $lmessage->Join($in->input['id']);
  if ($this->message == FALSE) return FALSE;

  $this->directory = $ldirectory->Get($this->message['directory_id']);
  if ($this->directory == FALSE) return FALSE;
  
  if (empty($usr->client) == FALSE) {
  
  if ($usr->client['id'] == $this->message['user_id'])
	return TRUE;
  else if ($usr->client['type'] == 'ADN')
    return TRUE; }
  
  if (substr($this->message['permissions'], 0, 1) == 'H')
    return FALSE;

  if (substr($this->directory['permissions'], 0, 1) == 'H')
    return FALSE;

return TRUE; }

/**************************************************
 * Method: Process                                *
 **************************************************/

 public function Process() {

  global $in, $lmessage, $cmessage;
   
  if (empty($in->input['o']))
    return TRUE;
 
  switch ($in->input['o']) {
  
  case 'CREATE': {
    if ($this->CreateComment() == FALSE)
	  return FALSE;
  break; }
  
  case 'INCREMENT': {
    $cmessage->IncrementRating();
  break; }
  
  case 'DECREMENT': {
    $cmessage->DecrementRating();
  break; }}

  $this->message = $lmessage->Join($this->message['id']);
  
return TRUE; }
 
/**************************************************
 * Component: IncrementMessage                    *
 **************************************************/

public function IncrementMessage() {

  global $lmessage;

  $this->message['modified'] = date('Y-m-d H:i:s');
  $this->message['total']++;

  $result = $lmessage->Modify($this->message);
  
  if ($result == FALSE) {
    $this->result = 'QUERYFAIL';
  return FALSE; }

return TRUE; }

/**************************************************
 * Component: SaveComment                        *
 **************************************************/

public function SaveComment($message_id) {

  global $usr, $in, $lcomment;

  $row = array($this->message['id'],
    $this->message['directory_id'],
    $usr->client['id'], 0, 0, '', 'MSG',
	$in->input['data'],
	'', '', '', '', '');

  $message_id = $lcomment->Create($row);
  if ($message_id == FALSE) {
    $this->result = 'QUERYFAIL';
  return FALSE; }

  if ($this->IncrementMessage() == FALSE)
    return FALSE;

return $message_id; }

/**************************************************
 * State: CreateComment                           *
 **************************************************/

public function CreateComment() {

  global $in, $out, $usr, $ses;

  if ($usr->client == NULL) return TRUE;
  
  if (substr($this->message['permissions'], 1, 1) == 'L')
    return TRUE;

  if ($in->GetInput('data') == FALSE) {
    $this->result = 'EMPTYMESSAGE';
  return TRUE; }

  $in->input['data'] = $in->PrepForStorage($in->input['data']);
  
  if ($ses->ValidateSeed($usr->client['id']) == FALSE)
    return TRUE;
  
  if ($ses->FloodDetected($usr->client['id']) == TRUE) {
    $this->result = 'FLOODDETECT';
  return TRUE; }
  
  $result = $this->SaveComment($this->message['id']);

  if ($result == FALSE) {
    $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }

  $in->input['data'] = '';
  
  $ses->SetFloodDetect($usr->client['id']);
  $this->result = 'CREATEOK';
  
return TRUE; }

/**************************************************
 * Component: get_template_directory              *
 **************************************************/

public function get_template_directory() {

  global $out, $lmessage, $usr, $cdirectory;

  $out->title = $this->message['title'];
  $out->GetFile('directory.htm');
  $cdirectory->header($this->directory);
  
  $out->keywords = $out->set_meta_keywords($this->message['data']);
  $out->description = $out->set_meta_description($this->message['data']);
  
  $output = $out->GetOutput();
  $out->GetFile('view-message.htm');

  $result = $out->GetOutput();
  $out->SetOutput($output);
  
  $out->SetVariable('document.output', $result);

return; }

/**************************************************
 * Component: set_dialog_box                      *
 **************************************************/

public function set_dialog_box() {

  global $out;

  if ($this->result == '') {
    $out->SetVariable('dialog_box', '');
  return; }

  if ($this->result == 'CREATEOK') {
    $out->set_dialog_box('NC', CREATEOK);
  return; }

  $result = '';
  switch ($this->result) {

  case 'EMPTYMESSAGE': $result = EMPTYMESSAGE;
  break; case 'INVALIDUTUBE': $result = INVALIDUTUBE;
  break; case 'ILLEGALCHARS': $result = ILLEGALCHARS;
  break; case 'UNREADFILE': $result = UNREADIMAGE;
  break; case 'INVALIDIMAGETYPE': $result = INVALIDIMAGETYPE;
  break; case 'FLOODDETECT': $result = FLOODDETECT; }

  $out->set_dialog_box('ER', $result);

return; }

/**************************************************
 * Component: comment_panel_create               *
 **************************************************/

public function comment_panel_create() {

  global $out, $usr, $cmessage;

  if ($usr->client == NULL) {
    $out->SetClass('comment.create', '');
  return; }

  if (substr($this->message['permissions'], 1, 1) == 'L') {
    $out->SetClass('comment.create', '');
  return; }

  $out->ShowClass('comment.create');
  
  $out->SetVariable('message.id', $this->message['id']);
  $out->SetVariable('client.fullname', $usr->client['fullname']);
  $cmessage->user_image_avatar('client', $usr->client['avatar']);

return; }

/**************************************************
 * Component: get_input_page                      *
 **************************************************/

public function get_input_page() {

  global $in;

  if ($in->GetInput('p') == TRUE)
  if ($in->ValidateInt('p') == TRUE)
    return TRUE;

  $in->input['p'] = 0;
  
return TRUE; }

/**************************************************
 * Component: comment_html_page                   *
 **************************************************/

public function comment_html_page() {

  global $in, $out;

  $current = $in->input['p']+1;
  $total = sprintf('%d', ($this->message['total']/11)+1);

  $out->SetVariable('page.current', $current);
  $out->SetVariable('page.total', $total);

  if ($current > 1) {
  
    $out->ShowClass('page.previous');
    $out->SetVariable('page.previous', $in->input['p']-1);
	
  } else $out->SetClass('page.previous', '');

  if ($current < $total) {
  
    $out->ShowClass('page.next');
    $out->SetVariable('page.next', $in->input['p']+1);
	
  } else $out->SetClass('page.next', '');

return FALSE; }

/**************************************************
 * State: Display                                 *
 **************************************************/

public function Display() {

  global $in, $out, $usr, $ses, $lcomment;
  global $cmessage, $ccomment;

  $this->get_template_directory();
  $this->set_dialog_box();
  
  $this->get_input_page();
  
  $this->comment_html_page();
  $this->comment_panel_create();
  
  $column = $lcomment->JoinUserAll('parent_id',
    $this->message['id'], $in->input['p']*10, 10);

  $cmessage->message_panel_row($this->message, FALSE);
  $ccomment->comment_panel_table($column);
  
  if (empty($in->input['data']) == TRUE)
    $in->input['data'] = '';

  $out->SetVariable('comment.data', $in->input['data']);

  $s = $ses->GenerateSeed($usr->client['id']);
  $out->SetVariable('s', $s);
  
  $out->Display('DEFAULT');

return; }

/**************************************************
 * Method: Create                                 *
 **************************************************/

public function Create() {

  global $out, $usr;

  if ($this->Initialize() == FALSE)
    return FALSE;

  if ($usr->UpdateSession() == FALSE) {
    if ($usr->result == 'QUERYFAIL') {
      $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }}

  if ($this->ValidatePrivileges() == FALSE) {
    $out->get_dialog('NC', PERMISSIONDENIED);
  return FALSE; }

  if ($this->Process() == FALSE)
    return FALSE;
  
  $this->Display();

return TRUE; }

/**************************************************
 * End Class                                      *
 **************************************************/
};

$z = new MViewMessage();
$z->Create();

?>
