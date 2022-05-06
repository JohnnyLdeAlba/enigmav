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
define('CREATEOK', 'Your directory has been successfully created.');

class MCreateDirectory {

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
 * Component: get_input_all                       *
 **************************************************/

 public function get_input_all() {
 
  global $in;
 
  if ($in->GetInput('label') == FALSE)
    $in->input['label'] = '';
  else $in->input['label'] = $in->PrepForStorage($in->input['label']);
	
  if ($in->GetInput('description') == FALSE)
    $in->input['description'] = '';
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
 * State: get_template_createok                   *
 **************************************************/

public function get_template_createok($id) {

  global $out;

  $out->GetFile('create-directory-ok.htm');
  $out->SetVariable('directory.id', $id);
  $out->Display('DEFAULT');

return FALSE; }

/**************************************************
 * State: CreateDirectory                         *
 **************************************************/

public function CreateDirectory() {

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

  $row = array(1, 1, $usr->client['id'], 0, 0,
    $in->input['label'], 'DIR', $in->input['description'],
	'', '', $in->input['permissions'], '', '');
  
  $directory_id = $ldirectory->Create($row);
  if ($directory_id == FALSE) {
    $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }

  $directory = $ldirectory->Get($directory_id);

  if (! ($data_image_icon == 'NOFILE'))
    $directory['pattachment'] = $this->set_image_icon(
  $directory_id, $data_image_icon);

  if (! ($data_image_banner == 'NOFILE'))
    $directory['sattachment'] = $this->set_image_banner(
  $directory_id, $data_image_banner);

  $ldirectory->Modify($directory);
  $this->result = 'CREATEOK';
	
  $this->get_template_createok($directory_id);
	
return FALSE; }

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
 * Component: set_checkbox_permissions            *
 **************************************************/

public function set_checkbox_permissions() {

  global $in, $out;

  if (substr($in->input['permissions'], 0, 1) == 'H')
    $out->SetVariable('directory.hidden', 'checked');
  else $out->SetVariable('directory.hidden', '');
  
  if (substr($in->input['permissions'], 1, 1) == 'L')
    $out->SetVariable('directory.locked', 'checked');
  else $out->SetVariable('directory.locked', '');
  
return FALSE; }

/**************************************************
 * State: Display                                 *
 **************************************************/

public function Display() {

  global $out, $in, $usr, $ses, $cdirectory;

  $out->title = "Create Directory";
  $out->GetFile('create-directory.htm');
  
  $this->set_dialog_box();
  $cdirectory->directory_default_smallbanner();
  
  $out->SetVariable('directory.label', $in->input['label']);
  $out->SetVariable('directory.description', $in->input['description']);
  
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
  
  if ($usr->client['type'] != 'ADN') {
    $out->get_dialog('NC', PERMISSIONDENIED);
  return FALSE; }
  
  $this->get_input_all();
  
  if ($in->input['o'] == 'CREATE')
    if ($this->CreateDirectory() == FALSE)
      return FALSE;
  
  $this->Display();

return TRUE; }

/**************************************************
 * End Class                                      *
 **************************************************/
};

$mcreatedirectory = new MCreateDirectory();
$mcreatedirectory->Create();
