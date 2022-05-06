<?php

/**************************************************
 * Application: EnigmaV                           *         
 * Module: MIndex                                 *
 * Author: Johnny L. de Alba                      *
 * Date: 01/14/2011                               *
 **************************************************/

include('comcontact.php');
include('comumessage.php');

define('DBCONNECTFAIL', 'Database Connection Failed.');
define('NOTLOGGEDIN', 'You must be logged in to use this feature.');
define('QUERYFAIL', 'We are unable to take your request at this time.');

define('EMPTYEMAIL', 'The email field must not be empty.');
define('EMPTYPASS', 'The password field must not be empty.');
define('CHARCOUNT', 'Fields cannot have more than 64 characters.');
define('ILLEGALCHARS', 'Illegal characters detected: &gt;, &lt;, &amp;.');

define('LOGINFAIL', 'The username or password you entered does not match our records.');
define('LOGGEDOUT', 'You have successfully logged out.');

class MIndex {

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
 * Component: set_messagebox                      *
 **************************************************/

public function set_messagebox() {

  global $out, $usr;

  if ($this->result == '') {
    $out->SetVariable('messagebox', '');
  return; }

  $result = '';
  switch ($this->result) {

  case 'EMPTYEMAIL': $result = EMPTYEMAIL;
  break; case 'EMPTYPASS': $result = EMPTYPASS;
  break; case 'CHARCOUNT': $result = CHARCOUNT;
  break; case 'LOGINFAIL': $result = LOGINFAIL; }

  $out->set_messagebox('ER', $result);

return; }

/**************************************************
 * Component: get_input_sesssion                  *
 **************************************************/

public function get_input_sesssion() {

  global $out, $usr, $ses;

  if ($usr->client == NULL) return;

  $s = $ses->GenerateSeed($usr->client['id']);
  $out->SetVariable('s', $s);

return; }

/**************************************************
 * Component: get_input_page                      *
 **************************************************/

public function get_input_page() {

  global $in;

  if ($in->GetInput('p') == FALSE) {
    $in->input['p'] = 0;
  return; }

  if ($in->ValidateInt('p') == FALSE) {
    $in->input['p'] = 0;
  return; }

return; }

/**************************************************
 * Component: client_panel_control                *
 **************************************************/

public function client_panel_control() {

  global $out, $usr;

  if ($usr->client == NULL) {
    $out->ShowClass('guestpanel');
    $out->SetClass('clientpanel', '');
  return; }

  $out->SetVariable('client.id', $usr->client['id']);
  $out->SetVariable('client.fullname', $usr->client['fullname']);

  $out->ShowClass('clientpanel');
  $out->SetClass('guestpanel', '');

return; }

/**************************************************
 * Component: umessage_html_previous              *
 **************************************************/

public function umessage_html_previous() {

  global $in, $out;

  $output = $out->GetOutput(); $out->SetOutput('');

  $result = sprintf("index.php?p=%s", $in->input['p']-1);

  $out->BeginTag('div');
    $out->Assign('class', 'contentarea.button');
    $out->Assign('style', 'margin: 0px 8px 0px 0px;');
  $out->EndTag();

  $out->BeginTag('a');
    $out->Assign('href', $result);
    $out->Assign('rel', 'nofollow');
  $out->EndTag();

  $out->Add('Previous');
  $out->BeginTag('/a'); $out->EndTag();

  $out->BeginTag('/div'); $out->EndTag();

  $result = $out->GetOutput(); $out->SetOutput($output);
  $out->SetVariable('umessage.previous', $result);

return; }

/**************************************************
 * Component: umessage_html_next                  *
 **************************************************/

public function umessage_html_next() {

  global $in, $out;

  $output = $out->GetOutput(); $out->SetOutput('');

  $result = sprintf("index.php?p=%s", $in->input['p']+1);

  $out->BeginTag('div');
    $out->Assign('class', 'contentarea.button');
    $out->Assign('style', 'margin: 0px 8px 0px 0px;');
  $out->EndTag();

  $out->BeginTag('a');
    $out->Assign('href', $result);
    $out->Assign('rel', 'nofollow');
  $out->EndTag();

  $out->Add('Next');
  $out->BeginTag('/a'); $out->EndTag();

  $out->BeginTag('/div'); $out->EndTag();

  $result = $out->GetOutput(); $out->SetOutput($output);
  $out->SetVariable('umessage.next', $result);

return; }

/**************************************************
 * Component: umessage_html_page                  *
 **************************************************/

public function umessage_html_page() {

  global $out, $in, $umsg;

  $current = $in->input['p']+1;

  $output = $out->GetOutput(); $out->SetOutput('');

  $out->BeginTag('div');
    $out->Assign('class', 'contentarea.button');
    $out->Assign('style', 'margin: 0px 8px 0px 0px;');
  $out->EndTag();
    $out->Add('Page '.$current);
  $out->BeginTag('/div'); $out->EndTag();

  $result = $out->GetOutput(); $out->SetOutput($output);

  $out->SetVariable('umessage.current', $result);

  if ($current <= 1)
    $out->SetVariable('umessage.previous', '');
  else $this->umessage_html_previous();
  
return; }

/**************************************************
 * Component: umessage_panel_table                *
 **************************************************/

public function umessage_panel_table($column) {

  global $out, $in, $umsg, $comumessage;

  if ($column == FALSE) {
    $out->ShowClass('noumessage');
    $out->SetClass('umessage', '');
  return; }

  $value = '';
  $total = count($column);

  if ($total == 9) { unset($column[8]); $this->umessage_html_next(); }
    else $out->SetVariable('umessage.next', '');

  $layout = $out->GetClass('umessage');
  $document = $out->GetOutput();
  $out->SetOutput('');

  for ($count = 0, $x = 0; $x < 2; $x++) {
    if ($count >= $total) break;

    $out->BeginTag('div');
      $out->Assign('class', 'upanel.column');
    $out->EndTag();

  for ($y = 0; $y < 4; $y++) {
    if ($count >= $total) break;

      $output = $out->GetOutput();
      $out->SetOutput($layout);

        $comumessage->umessage_panel_row($column[$count]);

      $result = $out->GetOutput();
      $out->SetOutput($output.$result);

  $count++; }

  $out->BeginTag('/div'); $out->EndTag(); }

  $result = $out->GetOutput();
  $out->SetOutput($document);

  $out->SetClass('umessage', $result);
  $out->SetClass('noumessage', '');

return; }

/**************************************************
 * State: Display                                 *
 **************************************************/

public function Display() {

  global $usr, $in, $out, $umsg;

  $out->GetFile('view-index.htm');
  $out->title = 'Social Information Network';

  $this->get_input_sesssion();
  $this->get_input_page();

  $this->client_panel_control();

  $column = $umsg->GetAllRecent($in->input['p']*8, 9);

  $this->umessage_panel_table($column);
  $this->umessage_html_page();
  $out->Index();

return; }

/**************************************************
 * Method: Create                                 *
 **************************************************/

public function Create() {

  global $in, $out, $usr;
  global $comumessage;

  if ($this->Initialize() == FALSE) {
    $out->get_dialog('ER', DBCONNECTFAIL);
  return FALSE; }

  if ($usr->UpdateSession() == FALSE) {
    if ($usr->result == 'QUERYFAIL') {
      $out->get_dialog('ER', QUERYFAIL);
  return FALSE; }}

  if ($in->input['o'] == 'INCREMENT-MR')
    $comumessage->IncrementRating();
  else if ($in->input['o'] == 'DECREMENT-MR')
    $comumessage->DecrementRating();

  $this->Display();

return TRUE; }

/**************************************************
 * End Class                                      *
 **************************************************/
};

$z = new MIndex();
$z->Create();
