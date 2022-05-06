<?php

/**************************************************
 * Application: EnigmaV                           *
 * Author: Johnny L. de Alba                      *
 * Date: 12/29/2011                               *
 **************************************************/

class LOutput {

public $Theme;

public $title;
public $keywords;
public $description;

public $output;

public function Create() {

  global $TemplateTheme;
  $this->Theme = $TemplateTheme;

  $this->title = '';
  $this->keywords = '';
  $this->description = '';

  $this->output = '';

return; }

/**************************************************
 * Description: Template Output Methods.          *
 **************************************************/

public function SetOutput($output) {
  $this->output =& $output;
return; }

public function &GetOutput() {
return $this->output; }

public function Add($output) {
  $this->output.= $output;
return; }

function PrintOutput() {
  $this->output = preg_replace("/^\s*[\r\n]/m",
  '', $this->output);
  echo $this->output;
return; }

/**************************************************
 * Description: Template Rendering Methods.       *
 **************************************************/

function SetVariable($id, $value) {

  $this->output = preg_replace(sprintf('/variable:%s/',
    $id), $value, $this->output);

return; }

function GetClass($id) {

  $pattern = sprintf('/class:%s(.*);class:%s/s', $id, $id);
  preg_match($pattern, $this->output, $result);

return $result[1]; }

function SetClass($id, $value) {

  $pattern = sprintf('/class:%s(.*);class:%s/s', $id, $id);
  $this->output = preg_replace($pattern, $value,
    $this->output);

return; }

function ShowClass($id) {

  $this->output = preg_replace(
    sprintf('/;class:%s/', $id),
  '', $this->output);
  $this->output = preg_replace(
    sprintf('/class:%s/', $id),
  '', $this->output);

return; }

/**************************************************
 * Description: Template Input Methods.           *
 **************************************************/

function GetFile($filename) {

  $handle = fopen($this->Theme.$filename, 'r');
    $this->output = fread($handle,
    filesize($this->Theme.$filename));
  fclose($handle);

return; }

function Index() {

  global $NetworkName;

  $this->SetVariable('document.title',
    sprintf("%s - %s", $NetworkName, $this->title));

  if (!empty($this->keywords))
    $this->SetVariable('document.keywords',
    $this->get_meta_tag('keywords', $this->keywords));
  else $this->SetVariable('document.keywords', '');

  if (!empty($this->description))
    $this->SetVariable('document.description',
    $this->get_meta_tag('description', $this->description));
  else $this->SetVariable('document.description', '');

  $this->PrintOutput();

return; }

function Display($type = 'default') {

  global $NetworkName;

  $this->SetVariable('document.title',
    sprintf("%s - %s", $NetworkName, $this->title));

  if (!empty($this->keywords))
    $this->SetVariable('document.keywords',
    $this->get_meta_tag('keywords', $this->keywords));
  else $this->SetVariable('document.keywords', '');

  if (!empty($this->description))
    $this->SetVariable('document.description',
    $this->get_meta_tag('description', $this->description));
  else $this->SetVariable('document.description', '');

  $this->PrintOutput();

return; }

/**************************************************
 * Description: Template Html Methods.            *
 **************************************************/

public function BeginTag($id) {
  $this->output.= "<".$id;
return; }

public function Assign($id, $value) {
  $this->output.= sprintf(" %s='%s'", $id, $value);
return; }

public function Escape($id, $value) {
  $this->output.= sprintf(' %s="%s"', $id, $value);
return; }

public function EndTag() {
  $this->output.= ">\n";
return; }

/**************************************************
 * Description: Template Display Methods.         *
 **************************************************/

public function PrepForDisplay($output) {

  $output = nl2br($output);

  $output = str_replace("[1]", "<span class='s1'>", $output);
  $output = str_replace("[2]", "<span class='s2'>", $output);
  $output = str_replace("[3]", "<span class='s3'>", $output);
  $output = str_replace("[4]", "<span class='s4'>", $output);

  $output = str_replace("[red]", "<span style='color: #aa2222;'>", $output);
  $output = str_replace("[b]", "<span class='bold'>", $output);
  $output = str_replace("[i]", "<span class='italic'>", $output);
  $output = str_replace("[bi]", "<span class='bitalic'>", $output);
  $output = str_replace("[u]", "<span class='underline'>", $output);

  $output = preg_replace("/\[(https?:\/\/[^\'\"\[\]]*)\]([^\[\]]*)\[\/https?\]/s", "<a href='$1'>$2</a>", $output);
  $output = preg_replace("/\[\/[b|i|bi|u|1-4|red]*\]/s", "</span>", $output);

return $output; }

/**************************************************
 * Component: get_meta_tag                        *
 **************************************************/

public function get_meta_tag($id, $value) {

  $output = $this->GetOutput(); $this->SetOutput('');

  $this->BeginTag('meta');
    $this->Assign('name', $id);
    $this->Escape('content', $value);
    $this->Add(' /');
  $this->EndTag();

  $result = $this->GetOutput(); $this->SetOutput($output);

return $result; }

/**************************************************
 * Component: set_meta_description                  *
 **************************************************/

public function set_meta_description($description) {

  if (strlen($description) < 120) {
    $description = preg_replace("/\[\/?[^\[\]]*\]/s", '', $description);
    $description = preg_replace("/[\'\"]+/s", '', $description);
    $description = htmlentities($description);
  return trim($description); }

  $description = preg_replace("/\[\/?[^\[\]]*\]/s", '', $description);
    $description = preg_replace("/[\'\"]+/s", '', $description);
  $description = htmlentities(substr($description, 0, 120));

  $word = preg_split("/[\s]+/", $description);
  $total = count($word); $description = $word[0];

  if ($total > 1) for ($count = 1; $count < $total-1; $count++)
    $description.= ' '.$word[$count];

return trim($description.'...'); }

/**************************************************
 * Component: set_meta_description                  *
 **************************************************/

public function set_meta_keywords($keywords) {

  $pattern = "/\[keywords:([A-Za-z0-9, ^\[\]]*)\]/s";
  if (preg_match($pattern, $keywords, $result) == FALSE)
    return '';

return $result[1]; }

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
 * Component: field_text_messagebox               *
 **************************************************/

public function field_text_messagebox($result) {

  if ($result == '') {
    $this->SetClass('messagebox', '');
    $this->SetClass('noticebox', '');
  return; }

  $this->SetVariable('messagebox.text',
    $result);

  $this->ShowClass('messagebox');
  $this->SetClass('noticebox', '');

return; }

/**************************************************
 * Component: field_text_noticebox                *
 **************************************************/

public function field_text_noticebox($result) {

  $this->SetVariable('noticebox.text', $result);

  $this->ShowClass('noticebox');
  $this->SetClass('messagebox', '');

return; }

/**************************************************
 * Component: field_text_seed                     *
 **************************************************/

public function field_text_seed() {

  global $usr, $ses;

  $seed = $ses->GenerateSeed($usr->client['id']);
  $this->SetVariable('seed', $seed);

return; }

/**************************************************
 * Template: get_dialog                           *
 **************************************************/

public function get_dialog($type, $message) {

  $this->GetFile('dialog.htm');

  if ($type == 'NC') { $this->title = 'Notice'; $style = 'dialog_notice'; }
  else { $this->title = 'Error'; $style = 'dialog_warning'; }

  $this->SetVariable('title', $this->title);
  $this->SetVariable('style', $style);
  $this->SetVariable('message', $message);

  $this->Display('DEFAULT');

return; }

/**************************************************
 * Template: set_messagebox                       *
 **************************************************/

public function set_dialog_box($type, $message) {

  if ($type == 'NC') $style = 'dialog_notice';
  else $style = 'dialog_warning';

  $output = $this->GetOutput();
  $this->SetOutput('');

  $this->BeginTag('div');
    $this->Assign('class', $style);
  $this->EndTag();

  $this->BeginTag('img');
    $this->Assign('src', 'default/32/important.png');
    $this->Assign('class', 'dialog_box.icon');
    $this->Add(' /');
  $this->EndTag();
    $this->Add($message);
  $this->BeginTag('/div');
  $this->EndTag();

  $result = $this->GetOutput(); $this->SetOutput($output);
  $this->SetVariable('dialog_box', $result);

return; }

public function set_permissions($permissions) {

  $read = substr($permissions, 0, 1);
  $write = substr($permissions, 1, 1);

  $this->SetVariable('permission.read', $read);
  $this->SetVariable('permission.write', $write);

  switch ($read) { case 'P': $result = 'Private';
    break; case 'C': $result = 'Contacts Only'; 
    break; default: case 'A': $result = 'Public';
  break; }

  $this->SetVariable('permission.fieldread', $result);

  switch ($write) { case 'L': $result = 'Locked'; 
    break; default: case 'U': $result = 'Unlocked';
  break; }

  $this->SetVariable('permission.fieldwrite', $result);

return; }

public function get_date($date) {

  global $out;

  $month = array('January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December');

  list($date, $time) = explode(' ', $date);
  
  $date = explode('-', $date);
  $time = explode(':', $time);
  
  if ($time[0] >= 12) $period = "PM";
  else $period = "AM";
  
  $hour = $time[0] % 12;
  if ($hour == 0) $hour = 12;
  
  $result = sprintf("%s %s, %s at %s:%s:%s %s", $month[(int)$date[1]-1],
    (int)$date[2], $date[0], $hour, $time[1], $time[2], $period);

 return $result; }

 /**************************************************
 * Component: get_encrpyted_data                   *
 **************************************************/

public function get_encrpyted_data() {

  $string = "QWERTYUIOPASDFGHJKLZXCVBNMMmnbvcxzlkjhgfdsapoiuytrewq1234509876";
  $total = strlen($string);
  
  $data = '';
  
  for ($count = 0; $count < 8; $count++) {
  
  $result = (time()*($count+rand()))%$total;
  $data.= substr($string, $result, 1); }

return $data; }
 
/**************************************************
 * End Class                                      *
 **************************************************/
};

$out = new LOutput();
$out->Create();

$tpl =& $out;

?>
