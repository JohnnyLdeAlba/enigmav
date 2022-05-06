<?php

/**************************************************
 * Application: EnigmaV                           *    
 * Author: Johnny L. de Alba                      *
 * Date: 09/08/2013                               *
 **************************************************/

include('cdirectory.php');

define('DBCONNECTFAIL', 'Database Connection Failed.');
define('QUERYFAIL', 'We are unable to take your request at this time.');

define('EMPTYLABEL', 'The label field must not be empty.');

define('UNREADIMAGE1', 'Unable to upload icon, the file is unreadable.');
define('UNREADIMAGE2', 'Unable to upload banner, the file is unreadable.');

define('INVALIDIMAGETYPE1', 'Icons must be of file type gif, jpg and png.');
define('INVALIDIMAGETYPE2', 'Banners must be of file type gif, jpg and png.');

define('INVALIDIMAGEWH1', 'Icons must have dimensions of 128 pixels by 128 of pixels.');
define('INVALIDIMAGEWH2', 'Banners must have dimensions of 800 pixels by 250 of pixels.');

define('PERMISSIONDENIED', 'You do not have permission to view this directory.');
define('REMOVEICOOK', 'Your directory icon has successfully been removed.');
define('REMOVEBANOK', 'Your directory banner has successfully been removed.');
define('MODIFYOK', 'Your directory has been successfully modified.');

class MModifyDirectory {

public $result;
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

  global $in, $usr, $ldirectory, $lmessage;

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
 * Component: get_input_all                       *
 **************************************************/

 public function get_input_all() {
 
  global $in;
 
  if ($in->GetInput('label') == FALSE)
    $in->input['label'] = $this->directory['title'];
  else $in->input['label'] = $in->PrepForStorage($in->input['label']);
	
  if ($in->GetInput('description') == FALSE)
    $in->input['description'] = $this->directory['data'];
  else $in->input['description'] = $in->PrepForStorage($in->input['description']);

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
 * Component: get_image_icon                    *
 **************************************************/

public function get_image_icon() {

  global $in;

  $handle = $in->GetImage('icon');

  if ($handle == FALSE) {
    if ($in->result == 'NOFILE') return 'NOFILE';
    $this->result = $in->result.'1'; // UNREADFILE, INVALIDIMAGETYPE
  return FALSE; }

  if ($in->FixedDimensions($handle, 128, 128) == FALSE) {
    $this->result = 'INVALIDIMAGEWH1';
  return FALSE; }

return $handle; }

public function set_image_icon($directory_id, $handle) {

  $filename = sprintf("%s.%s", md5($directory_id), $handle['type']);
  copy($handle['filename'], 'directory/icon/'.$filename);

return $filename; }

/**************************************************
 * Component: get_image_banner                    *
 **************************************************/

public function get_image_banner() {

  global $in;

  $handle = $in->GetImage('banner');

  if ($handle == FALSE) {
    if ($in->result == 'NOFILE') return 'NOFILE';
    $this->result = $in->result.'2'; // UNREADFILE, INVALIDIMAGETYPE
  return FALSE; }

  if ($in->FixedDimensions($handle, 800, 250) == FALSE) {
    $this->result = 'INVALIDIMAGEWH2';
  return FALSE; }

return $handle; }

public function set_image_banner($directory_id, $handle) {

  $filename = sprintf("%s.%s", md5($directory_id), $handle['type']);
  copy($handle['filename'], 'directory/banner/'.$filename);

return $filename; }

/**************************************************
 * Component: set_dialog_box                      *
 **************************************************/

public function set_dialog_box() {

  global $out;

  if ($this->result == '') {
    $out->SetVariable('dialog_box', '');
  return; }

  if ($this->result == 'MODIFYOK') {
    $out->set_dialog_box('NC', MODIFYOK);
  return; }

  if ($this->result == 'REMOVEICOOK') {
    $out->set_dialog_box('NC', REMOVEICOOK);
  return; }
  
  if ($this->result == 'REMOVEBANOK') {
    $out->set_dialog_box('NC', REMOVEBANOK);
  return; }
  
  $result = '';
  switch ($this->result) {

  case 'EMPTYLABEL': $result = EMPTYLABEL;

  break; case 'UNREADFILE1': $result = UNREADIMAGE1;
  break; case 'UNREADFILE2': $result = UNREADIMAGE2;
  break; case 'INVALIDIMAGETYPE1': $result = INVALIDIMAGETYPE1;
  break; case 'INVALIDIMAGETYPE2': $result = INVALIDIMAGETYPE2;
  break; case 'INVALIDIMAGEWH1': $result = INVALIDIMAGEWH1;
  break; case 'INVALIDIMAGEWH2': $result = INVALIDIMAGEWH2; }

  $out->set_dialog_box('ER', $result);

return; }

/**************************************************
 * State: RemoveIcon                              *
 **************************************************/

public function RemoveIcon() {

  global $in, $out, $usr, $ses, $ldirectory;

  if ($ses->ValidateSeed($usr->client['id']) == FALSE) return TRUE;

  if (! ($this->directory['pattachment'] == ''))
    unlink('directory/icon/'.$this->directory['pattachment']);

  $this->directory['pattachment'] = '';

  if ($ldirectory->Modify($this->directory) == FALSE) {
    $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }

  $this->result = 'REMOVEICOOK';

return TRUE; }
 
/**************************************************
 * State: RemoveBanner                            *
 **************************************************/

public function RemoveBanner() {

  global $in, $out, $usr, $ses, $ldirectory;

  if ($ses->ValidateSeed($usr->client['id']) == FALSE) return TRUE;

  if (! ($this->directory['sattachment'] == ''))
    unlink('directory/banner/'.$this->directory['sattachment']);

  $this->directory['sattachment'] = '';

  if ($ldirectory->Modify($this->directory) == FALSE) {
    $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }

  $this->result = 'REMOVEBANOK';

return TRUE; }
 
/**************************************************
 * State: ModifyDirectory                         *
 **************************************************/

public function ModifyDirectory() {

  global $in, $out, $usr, $ses, $ldirectory;

  if ($usr->client == NULL) return TRUE;
  if ($usr->client['type'] != 'ADN') return TRUE;
  
  if ($ses->ValidateSeed($usr->client['id']) == FALSE)
    return TRUE;

  if (empty($in->input['label']) == TRUE) {
    $this->result = 'EMPTYLABEL';
  return TRUE; }

  $data_image_icon = $this->get_image_icon();
  if ($data_image_icon == FALSE) return TRUE;
  
  $data_image_banner = $this->get_image_banner();
  if ($data_image_banner == FALSE) return TRUE;

  if (! ($data_image_icon == 'NOFILE'))
    $this->directory['pattachment'] = $this->set_image_icon(
  $this->directory['id'], $data_image_icon);

  if (! ($data_image_banner == 'NOFILE'))
    $this->directory['sattachment'] = $this->set_image_banner(
  $this->directory['id'], $data_image_banner);
  
  $this->directory['title'] = $in->input['label'];
  $this->directory['data'] = $in->input['description'];
  $this->directory['permissions'] = $in->input['permissions'];
  
  $result = $ldirectory->Modify($this->directory);

  if ($result == FALSE) {
    $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }

  $this->result = 'MODIFYOK';
	
return TRUE; }

/**************************************************
 * Component: set_checkbox_permissions            *
 **************************************************/

public function set_checkbox_permissions() {

  global $out;

  if (substr($this->directory['permissions'], 0, 1) == 'H')
    $out->SetVariable('directory.hidden', 'checked');
  else $out->SetVariable('directory.hidden', '');
  
  if (substr($this->directory['permissions'], 1, 1) == 'L')
    $out->SetVariable('directory.locked', 'checked');
  else $out->SetVariable('directory.locked', '');
  
return FALSE; }

/**************************************************
 * State: Display                                 *
 **************************************************/

public function Display() {

  global $out, $in, $usr, $ses, $cdirectory;

  $out->title = "Modify Directory";
  $out->GetFile('modify-directory.htm');
  
  $this->set_dialog_box();
  $cdirectory->directory_image_pattachment($this->directory);
  $cdirectory->directory_image_smallbanner($this->directory);
  
  $out->SetVariable('directory.id', $in->input['id']);
  $out->SetVariable('directory.label', $in->input['label']);
  $out->SetVariable('directory.description', $in->input['description']);
  $this->set_checkbox_permissions();

  if ($this->directory['pattachment'] == '')
    $out->SetClass('directory.remove_icon', '');
  else $out->ShowClass('directory.remove_icon');
	
  if ($this->directory['sattachment'] == '')
    $out->SetClass('directory.remove_banner', '');
  else $out->ShowClass('directory.remove_banner');
	
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
    if ($this->ModifyDirectory() == FALSE)
      return FALSE;
  break; }
   
  case 'REMOVE-ICON': {
    if ($this->RemoveIcon() == FALSE)
      return FALSE;
  break; }
   
  case 'REMOVE-BANNER': {
    if ($this->RemoveBanner() == FALSE)
      return FALSE;
  break; }}
  
  $this->Display();

return TRUE; }

/**************************************************
 * End Class                                      *
 **************************************************/
};

$mmodifydirectory = new MModifyDirectory();
$mmodifydirectory->Create();
