<?php

/**************************************************
 * Application: EnigmaV                           *         
 * Module: CMessage (ComMessage)                  *
 * Author: Johnny L. de Alba                      *
 * Date: 02/11/2011                               *
 **************************************************/

class CMessage {

public $result;

/**************************************************
 * Component: message_row_uprivate               *
 **************************************************/

public function message_row_uprivate($message, $read) {

  global $out;

  $out->SetVariable('message.avatar', 'default/128/avatar.png');
  $out->SetClass('message.image', '');
  $out->SetClass('message.utube', '');

  $out->SetVariable('message.uid', $message['parent_id']);
  $out->SetVariable('message.fullname', 'Anonymous');
  $out->SetVariable('message.id', $message['id']);
  $out->SetVariable('message.created', 'xxxx-xx-xx xx:xx:xx');
  $out->SetVariable('message.rating', 'X');
  $out->SetVariable('message.total', 'X');
  $out->SetVariable('message.title', 'Private');
  $out->SetVariable('ufolder.name', 'Private');

  if ($read == 'P') $out->SetVariable('message.data', 'Private Message.');
  else if ($read == 'C') $out->SetVariable('message.data', 'Contacts Only.');

return; }

/**************************************************
 * Component: message_text_read                  *
 **************************************************/

public function message_text_read($read) {

  global $out;

  if ($read == 'P') { $out->SetVariable('message.read', 'Private');
    $out->SetVariable('message.style', 'uprivate.body'); return; }

  else if ($read == 'C') { $out->SetVariable('message.read', 'Contacts');
    $out->SetVariable('message.style', 'usubscriber.body'); return; }

  else { $out->SetVariable('message.read', 'Public');
    $out->SetVariable('message.style', 'message.body'); }

return; }

/**************************************************
 * Component: message_user_admin                 *
 **************************************************/

public function message_user_admin($message) {

  global $usr;

  if ($usr->client == NULL) return FALSE;
  if ($usr->client['id'] == $message['user_id']) return TRUE;
  if ($usr->client['id'] == $message['parent_id']) return TRUE;
  if ($usr->client['type'] == 'ADN') return TRUE;

return FALSE; }

/**************************************************
 * Component: message_user_privileges            *
 **************************************************/

public function message_user_privileges($message) {

  global $usr, $out;

  $read = $usr->GetPermission($message['permissions'], 1);
  $this->message_text_read($read);

  if ($this->message_user_admin($message) == TRUE) {
    $out->ShowClass('message.auth');
  return TRUE; }

  $out->SetClass('message.auth', '');

return TRUE; }

/**************************************************
 * Component: ufolder_field_name                  *
 **************************************************/

public function ufolder_field_name($message) {

  global $out, $usr, $ufld;

  $out->SetVariable('directory.label', $message['directory_label']);
  $out->SetVariable('directory.id', $message['directory_id']);

return; }

/**************************************************
 * Component: user_image_avatar                   *
 **************************************************/

public function user_image_avatar($id, $user_avatar) {

  global $out;

  if ($user_avatar == '') {
    $out->SetVariable($id.'.avatar', 'default/128/avatar.png');
  return; }

  $out->SetVariable($id.'.avatar', 'usr/av/'.$user_avatar);

return; }

/**************************************************
 * Component: message_object_pattachment         *
 **************************************************/

public function message_object_pattachment($message) {

  global $out;

  if ($message['type'] == 'UTB') {
    $out->SetClass('message.image', '');
    $out->ShowClass('message.utube');
    $out->SetVariable('message.pattachment',
	  $message['pattachment']);
   FALSE; }

  $pattachment = 'default.png';
  
  if ($message['type'] == 'IMG')
	$pattachment = $message['pattachment'];
  
  $out->SetClass('message.utube', '');
  $out->ShowClass('message.image');
  $out->SetVariable('message.pattachment',
    $pattachment);

return FALSE; }

/**************************************************
 * Component: message_object_pattachment         *
 **************************************************/

public function message_text_title($message) {

  global $out;

  if ($message['title'] == '') {
    $out->SetClass('message.title', '');
  return; }

  $out->ShowClass('message.title');
  $out->SetVariable('message.title', $message['title']);

return; }

/**************************************************
 * Component: message_preview_title               *
 **************************************************/

public function message_preview_title($title) {

  global $out;

  $pattern[0] = "/\[\/?[^\[\]]*\]/s";
  $pattern[1] = "/\r/s";
  
  $title = preg_replace($pattern[0], '', $title);
  $title = preg_replace($pattern[1], ' ', $title);
  
  $title = substr($title, 0, 24);


return $title.'...'; }

/**************************************************
 * Component: message_preview_data               *
 **************************************************/

public function message_preview_data($data) {

  global $out;

  $pattern[0] = "/\[\/?[^\[\]]*\]/s";
  $pattern[1] = "/\r/s";
  
  $data = preg_replace($pattern[0], '', $data);
  $data = preg_replace($pattern[1], ' ', $data);
  
  $data = substr($data, 0, 192);


return $data.'...'; }

/**************************************************
 * Component: message_panel_row                  *
 **************************************************/

public function message_panel_row($message, $truncate = TRUE) {

  global $usr, $in, $out;

  if ($this->message_user_privileges($message) == FALSE) return;

  $this->ufolder_field_name($message);
  $this->user_image_avatar('message', $message['user_avatar']);
  $this->message_object_pattachment($message);

  $out->SetVariable('message.uid', $message['user_id']);
  $out->SetVariable('message.fullname', $message['user_fullname']);
  $out->SetVariable('message.id', $message['id']);

  $out->SetVariable('message.directory_id', $message['directory_id']);
  $out->SetVariable('message.directory_label', $message['directory_label']);
  
  $result = $out->get_date($message['created']);
  $out->SetVariable('message.created', $result);

  $out->SetVariable('message.rating', $message['rating']);
  $out->SetVariable('message.total', $message['total']);


  $pattern = "/\[keywords:[A-Za-z0-9, ^\[\]]*\]/s";
  $message['data'] = preg_replace($pattern, '', $message['data']);

  if ($truncate == TRUE) { 
  
    $message['title'] = $this->message_preview_title($message['title']);
    $message['data'] = $this->message_preview_data($message['data']); }
	
  else {
  
    $message['title'] = $out->PrepForDisplay($message['title']);
    $message['data'] = $out->PrepForDisplay($message['data']); }
  $this->message_text_title($message);
  $out->SetVariable('message.data', $message['data']);

return; }

/**************************************************
 * Component: message_panel_table                *
 **************************************************/

public function message_panel_table($column) {

  global $out, $in, $lmessage;

  if ($column == FALSE) {
    $out->ShowClass('nomessage');
    $out->SetClass('message', '');
  return; }

  $value = '';
  $total = count($column);

  $layout = $out->GetClass('message');
  $document = $out->GetOutput();
  $out->SetOutput('');

  for ($count = 0; $count < $total; $count++) {

    $output = $out->GetOutput();
    $out->SetOutput($layout);

	if (($count % 2) == 0)
	  $out->SetVariable('clear', 'clear: left;');
	else $out->SetVariable('clear', '');
	  
    $this->message_panel_row($column[$count]);

    $result = $out->GetOutput();
  $out->SetOutput($output.$result); }

  $result = $out->GetOutput();
  $out->SetOutput($document);

  $out->SetClass('message', $result);
  $out->SetClass('nomessage', '');

return; }

/**************************************************
 * Component: IncrementRating                     *
 **************************************************/

public function IncrementRating() {

  global $in, $usr, $ses, $lcounter, $lmessage;

  if ($usr->client == NULL) return FALSE; 
  if ($ses->ValidateSeed($usr->client['id']) == FALSE) return FALSE;

  $message = $lmessage->Get($in->input['id']);
  if ($message == FALSE) return FALSE;

  $lcounter->table = 'message_rating';
  if ($lcounter->SaveCounter($message['id'],
    $usr->client['id'], 1) == FALSE)
  return FALSE;

  $message['rating']++;
  $lmessage->Modify($message);

return TRUE; }

/**************************************************
 * Component: DecrementRating                     *
 **************************************************/

public function DecrementRating() {

  global $in, $usr, $ses, $lcounter, $lmessage;

  if ($usr->client == NULL) return FALSE;
  if ($ses->ValidateSeed($usr->client['id']) == FALSE) return FALSE;

  $message = $lmessage->Get($in->input['id']);
  if ($message == FALSE) return FALSE;
  if ($message['rating'] == 0) return FALSE;

  $lcounter->table = 'message_rating';
  if ($lcounter->SaveCounter($message['id'],
    $usr->client['id'], 0) == FALSE)
  return FALSE;

  $message['rating']--;
  $lmessage->Modify($message);

return TRUE; }

/**************************************************
 * End Class                                      *
 **************************************************/
};

$cmessage = new CMessage();
