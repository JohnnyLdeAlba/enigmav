<?php

/**************************************************
 * Application: EnigmaV                           *
 * Module: MProfile                               *
 * Author: Johnny L. de Alba                      *
 * Date: 02/21/2011                               *
 **************************************************/

define('DBCONNECTFAIL', 'Database Connection Failed.');
define('NOTLOGGEDIN', 'You must be logged in to use this feature.');
define('QUERYFAIL', 'We are unable to take your request at this time.');

define('CHARCOUNT', 'Fields cannot have more than 64 characters.');
define('ILLEGALCHARS', 'Illegal characters detected: &gt;, &lt;, &amp;.');

define('UNREADBANNER', 'Unable to upload banner, the file is unreadable.');
define('INVALIDBANNERTYPE', 'Banners must be of file type gif, jpg and png.');
define('BANNERWH', 'Banners must have dimensions of 800 pixels by 250 of pixels.');

define('PERMISSIONDENIED', 'You do not have permission to use this feature.');
define('REMOVEBANOK', 'Your banner has successfully been removed.');
define('MODIFYOK', 'Your profile information has successfully been updated.');

class MProfile {

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
 * State: ValidatePrivileges                      *
 **************************************************/

public function ValidatePrivileges() {

  global $usr, $in;

  if ($in->GetInput('id') == FALSE) return FALSE;
  if ($in->ValidateInt('id') == FALSE) return FALSE;

  $usr->contact = $usr->GetUser('id', $in->input['id']);
  if ($usr->contact == FALSE) return FALSE;

  if ($usr->client['id'] == $usr->contact['id']) return TRUE;
  if ($usr->client['type'] == 'ADN') return TRUE;

return TRUE; }

/**************************************************
 * State: RemoveBanner                            *
 **************************************************/

public function RemoveBanner() {

  global $in, $out, $usr, $ses;

  if ($ses->ValidateSeed($usr->client['id']) == FALSE) return TRUE;

  if (! ($usr->contact['banner'] == ''))
    unlink('usr/b/'.$usr->contact['banner']);

  $usr->contact['banner'] = '';

  if ($usr->ModifyUser($usr->contact) == FALSE) {
    $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }

  $this->result = 'REMOVEBANOK';

return TRUE; }

/**************************************************
 * Component: GetInput                            *
 **************************************************/

public function GetInput($id) {

  global $in;

  if ($in->GetInput($id) == TRUE) {

  if (strlen($in->input[$id]) >= 64) {
    $this->result = 'CHARCOUNT';
  return FALSE; }

  if ($in->IllegalCharacters($id) == TRUE) {
    $this->result = 'ILLEGALCHARS';
  return FALSE; }}

return TRUE; }

/**************************************************
 * Component: update_image_banner                 *
 **************************************************/

public function update_image_banner() {

  global $in, $usr;

  $handle = $in->GetImage('banner');

  if ($handle == FALSE) {
    if ($in->result == 'NOFILE') return TRUE;
      $this->result = $in->result; // UNREADFILE, INVALIDIMAGETYPE
  return FALSE; }

  if ($in->FixedDimensions($handle, 800, 250) == FALSE) {
    $this->result = 'BANNERWH';
  return FALSE; }

  if (! ($usr->contact['banner'] == ''))
    unlink('usr/b/'.$usr->contact['banner']);

  $filename = sprintf("%s.%s", $usr->contact['id'], $handle['type']);
  copy($handle['filename'], 'usr/b/'.$filename);

  $usr->contact['banner'] = $filename;

return TRUE; }

/**************************************************
 * State: ModifyProfile                           *
 **************************************************/

public function ModifyProfile() {

  global $in, $usr, $ses;

  $this->state = 'DISPLAY';

  if ($ses->ValidateSeed($usr->client['id']) == FALSE) return TRUE;

  if ($this->GetInput('birthday') == FALSE) return TRUE;
  if ($this->GetInput('current_city') == FALSE) return TRUE;
  if ($this->GetInput('current_state') == FALSE) return TRUE;
  if ($this->GetInput('employer') == FALSE) return TRUE;
  if ($this->GetInput('position') == FALSE) return TRUE;
  if ($this->GetInput('highschool') == FALSE) return TRUE;
  if ($this->GetInput('college') == FALSE) return TRUE;
  if ($this->GetInput('major') == FALSE) return TRUE;
  if ($this->GetInput('degree') == FALSE) return TRUE;

  $usr->contact['birthday'] = $in->input['birthday'];
  $usr->contact['current_city'] = $in->input['current_city'];
  $usr->contact['current_state'] = $in->input['current_state'];
  $usr->contact['employer'] = $in->input['employer'];
  $usr->contact['position'] = $in->input['position'];
  $usr->contact['highschool'] = $in->input['highschool'];
  $usr->contact['college'] = $in->input['college'];
  $usr->contact['major'] = $in->input['major'];
  $usr->contact['degree'] = $in->input['degree'];

  if ($this->update_image_banner() == FALSE) return TRUE;

  if ($usr->ModifyUser($usr->contact) == FALSE) {
    $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }

  $this->result = 'MODIFYOK';

return TRUE; }

/**************************************************
 * Component: set_messagebox                      *
 **************************************************/

public function set_messagebox() {

  global $out;

  if ($this->result == '') {
    $out->SetVariable('messagebox', '');
  return; }

  if ($this->result == 'MODIFYOK') {
    $out->set_messagebox('NC', MODIFYOK);
  return; }

  if ($this->result == 'REMOVEBANOK') {
    $out->set_messagebox('NC', REMOVEBANOK);
  return; }

  $result = '';
  switch ($this->result) {

  case 'CHARCOUNT': $result = CHARCOUNT;
  break; case 'ILLEGALCHARS': $result = ILLEGALCHARS;
  break; case 'UNREADFILE': $result = UNREADBANNER;
  break; case 'INVALIDIMAGETYPE': $result = INVALIDBANNERTYPE;
  break; case 'BANNERWH': $result = BANNERWH; }

  $out->set_messagebox('ER', $result);

return; }

/**************************************************
 * Component: contact_image_defaultbanner         *
 **************************************************/

public function contact_image_defaultbanner() {

  global $out;

  $output = $out->GetOutput(); $out->SetOutput('');

  $out->BeginTag('div');
    $out->Assign('class', 'contact.smbanner');
  $out->EndTag();

  $out->BeginTag('div');
    $out->Assign('style', 'padding-top: 87px;');
  $out->EndTag();

    $out->Add('No Profile Banner');

  $out->BeginTag('/div'); $out->EndTag();
  $out->BeginTag('/div'); $out->EndTag();

  $result = $out->GetOutput(); $out->SetOutput($output);
  $out->SetVariable('contact.banner', $result);

return; }

/**************************************************
 * Component: contact_html_banner                 *
 **************************************************/

public function contact_image_banner() {

  global $in, $out, $usr;

  if ($usr->contact['banner'] == '') {
    $this->contact_image_defaultbanner();
  return; }

  $output = $out->GetOutput(); $out->SetOutput('');

  $out->BeginTag('img');
    $out->Assign('src', 'usr/b/'.$usr->contact['banner']);
    $out->Assign('class', 'contact.smbanner');
    $out->Assign('alt', ''); $out->Add(' /');
  $out->EndTag();

  $out->BeginTag('br /'); $out->EndTag();

  $out->BeginTag('a');
    $out->Assign('href', sprintf("profile.php?"
      ."id=%s&o=remove-banner&s=%s",
      $usr->contact['id'], $in->input['s']));
  $out->EndTag();

    $out->Add('Remove Banner');
  $out->BeginTag('/a'); $out->EndTag();

  $result = $out->GetOutput(); $out->SetOutput($output);
  $out->SetVariable('contact.banner', $result);

return; }

/**************************************************
 * State: Display                                 *
 **************************************************/

public function Display() {

  global $usr, $ses, $in, $out;

  $out->title = 'Account Settings';
  $out->GetFile('profile.htm');

  $this->set_messagebox();
  $out->set_permissions($usr->contact['permissions']);

  $in->input['s'] = $ses->GenerateSeed($usr->client['id']);
  $out->SetVariable('s', $in->input['s']);

  $out->SetVariable('client.fullname', $usr->client['fullname']);
  $out->SetVariable('client.id', $usr->client['id']);

  $this->contact_image_banner($usr->contact);
  $out->SetVariable('contact.id', $usr->contact['id']);
  $out->SetVariable('contact.fullname', $usr->contact['fullname']);
  $out->SetVariable('contact.birthday', $usr->contact['birthday']);
  $out->SetVariable('contact.current_city', $usr->contact['current_city']);
  $out->SetVariable('contact.current_state', $usr->contact['current_state']);
  $out->SetVariable('contact.employer', $usr->contact['employer']);
  $out->SetVariable('contact.position', $usr->contact['position']);
  $out->SetVariable('contact.highschool', $usr->contact['highschool']);
  $out->SetVariable('contact.college', $usr->contact['college']);
  $out->SetVariable('contact.major', $usr->contact['major']);
  $out->SetVariable('contact.degree', $usr->contact['degree']);

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
    $out->get_dialog('ER', NOTLOGGEDIN);
  return FALSE; }

  if ($this->ValidatePrivileges() == FALSE) {
    $out->get_dialog('NC', PERMISSIONDENIED);
  return FALSE; }

  if ($in->input['o'] == 'MODIFY') {
    if ($this->ModifyProfile() == FALSE)
  return FALSE; }

  else if ($in->input['o'] == 'REMOVE-BANNER') {
    if ($this->RemoveBanner() == FALSE)
  return FALSE; }

  $this->Display();

return TRUE; }

/**************************************************
 * End Class                                      *
 **************************************************/

};

$z = new MProfile();
$z->Create();

?>
