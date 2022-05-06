<?php

/**************************************************
 * Application: EnigmaV                           *
 * Author: Johnny L. de Alba                      *
 * Date: 09/07/2013                               *
 **************************************************/

include('cdirectory.php');
include('cmessage.php');

define('DBCONNECTFAIL', 'Database Connection Failed.');
define('QUERYFAIL', 'We are unable to take your request at this time.');
define('PERMISSIONDENIED', 'You do not have permission to view this folder.');

class MViewAll {

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

  global $ldirectory;

  $this->directory = $ldirectory->Get(1);
  if ($this->directory == FALSE) {
    $this->result = $ldirectory->result;
  return FALSE; }

return TRUE; }

/**************************************************
 * Component: get_template_directory              *
 **************************************************/

public function get_template_directory() {

  global $out, $usr, $cdirectory;

  $out->title = sprintf("%s", $this->directory['title']);
  
  $out->GetFile('directory.htm');
  $cdirectory->header($this->directory);
  
  $output = $out->GetOutput();
  $out->GetFile('view-index.htm');

  $result = $out->GetOutput();
  $out->SetOutput($output);
  
  $out->SetVariable('document.output', $result);

return; }

/**************************************************
 * Component: message_html_create                 *
 **************************************************/

public function message_html_create() {

  global $out, $usr;

  if ($usr->client == NULL) {
    $out->SetClass('message.create', '');
  return FALSE; }

  if (substr($this->directory['permissions'], 1, 1) == 'L') {
    $out->SetClass('message.create', '');
  return FALSE; }

  $out->ShowClass('message.create');

return TRUE; }

/**************************************************
 * Component: directory_html_create               *
 **************************************************/

public function directory_html_create() {

  global $usr, $out;
  
  if ($usr->client == NULL) {
    $out->SetClass('directory.create', '');
	$out->SetClass('directory.authority', '');
  return FALSE; }
  
  if (! ($usr->client['type'] == 'ADN')) {
    $out->SetClass('directory.create', '');
	$out->SetClass('directory.authority', '');
  return FALSE; }
  
  $out->ShowClass('directory.create', '');
  $out->ShowClass('directory.authority');

return TRUE; }


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
 * Component: directory_html_page                 *
 **************************************************/

public function directory_html_page() {

  global $in, $out;

  $current = $in->input['p']+1;
  $total = sprintf('%d', ($this->directory['total']/11)+1);

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

  global $in, $out, $usr, $ses, $lmessage, $ldirectory;
  global $cdirectory, $cmessage;

  $this->get_input_page();
  $this->get_template_directory();

  $this->directory_html_page();
  $this->directory_html_create();
  $this->message_html_create();

  $column = $lmessage->GetAllRecent($in->input['p']*8, 8);
  $cmessage->message_panel_table($column);

  $column = $ldirectory->GetAll('parent_id', 1, 0, 10);
  $cdirectory->directory_panel_table($column);
  
  $out->SetVariable('directory.id', $this->directory['id']);
  
  $s = $ses->GenerateSeed($usr->client['id']);
  $out->SetVariable('s', $s);
  
  $out->Display('DEFAULT');

return FALSE; }

/**************************************************
 * Method: Create                                 *
 **************************************************/

public function Create() {

  global $in, $out, $usr;
  global $cmessage, $ccomment;

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

  $this->Display();
  
return TRUE; }

/**************************************************
 * End Class                                      *
 **************************************************/
};

$z = new MViewAll();
$z->Create();

?>
