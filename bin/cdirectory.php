<?php

/**************************************************
 * Application: EnigmaV                           *
 * Author: Johnny L. de Alba                      *
 * Date: 09/07/2013                               *
 **************************************************/

class CDirectory {

/**************************************************
 * Component: directory_image_pattachment         *
 **************************************************/

public function directory_image_pattachment($directory) {

  global $out;

  if ($directory['pattachment'] == '') $result = 'default/128/directory.png';
  else $result = 'directory/icon/'.$directory['pattachment'];

  $out->SetVariable('directory.pattachment', $result);

return FALSE; }

/**************************************************
 * Component: directory_default_banner            *
 **************************************************/

public function directory_default_banner() {

  global $out;

  $output = $out->GetOutput(); $out->SetOutput('');

  $out->BeginTag('div');
    $out->Assign('class', 'directory.banner');
  $out->EndTag();

  $out->BeginTag('div');
    $out->Assign('style', 'padding-top: 118px;');
  $out->EndTag();

  $out->Add('No Banner');
  $out->BeginTag('/div'); $out->EndTag();
  $out->BeginTag('/div'); $out->EndTag();

  $result = $out->GetOutput(); $out->SetOutput($output);
  $out->SetVariable('directory.banner', $result);

return FALSE; }

/**************************************************
 * Component: directory_generic_banner            *
 **************************************************/

public function directory_generic_banner() {

  global $out;

  $output = $out->GetOutput(); $out->SetOutput('');

  $out->BeginTag('img');
    $out->Assign('src', 'default/header.png');
    $out->Assign('class', 'directory.banner');
    $out->Assign('alt', ''); $out->Add(' /');
  $out->EndTag();

  $result = $out->GetOutput(); $out->SetOutput($output);
  $out->SetVariable('directory.banner', $result);


return FALSE; }

/**************************************************
 * Component: directory_image_banner              *
 **************************************************/

public function directory_image_banner($directory) {

  global $out, $usr;

  if ($directory['sattachment'] == '') {
    $this->directory_generic_banner();
  return; }

  $output = $out->GetOutput(); $out->SetOutput('');

  $out->BeginTag('img');
    $out->Assign('src', 'directory/banner/'.$directory['sattachment']);
    $out->Assign('class', 'directory.banner');
    $out->Assign('alt', ''); $out->Add(' /');
  $out->EndTag();

  $result = $out->GetOutput(); $out->SetOutput($output);
  $out->SetVariable('directory.banner', $result);

return FALSE; }

/**************************************************
 * Component: directory_default_smallbanner       *
 **************************************************/

public function directory_default_smallbanner() {

  global $out;

  $output = $out->GetOutput(); $out->SetOutput('');

  $out->BeginTag('div');
    $out->Assign('class', 'directory.smallbanner');
  $out->EndTag();

  $out->BeginTag('div');
    $out->Assign('style', 'padding-top: 55px;');
  $out->EndTag();

  $out->Add('No Banner');
  $out->BeginTag('/div'); $out->EndTag();
  $out->BeginTag('/div'); $out->EndTag();

  $result = $out->GetOutput(); $out->SetOutput($output);
  $out->SetVariable('directory.smallbanner', $result);

return FALSE; }

/**************************************************
 * Component: directory_image_banner              *
 **************************************************/

public function directory_image_smallbanner($directory) {

  global $out, $usr;

  if ($directory['sattachment'] == '') {
    $this->directory_default_smallbanner();
  return FALSE; }

  $output = $out->GetOutput(); $out->SetOutput('');

  $out->BeginTag('img');
    $out->Assign('src', 'directory/banner/'.$directory['sattachment']);
    $out->Assign('class', 'directory.smallbanner');
    $out->Assign('alt', ''); $out->Add(' /');
  $out->EndTag();

  $result = $out->GetOutput(); $out->SetOutput($output);
  $out->SetVariable('directory.smallbanner', $result);

return FALSE; }


/**************************************************
 * Component: directory_panel_row                 *
 **************************************************/

public function directory_panel_row($directory) {
 
  global $out;
  
  $this->directory_image_pattachment($directory);
  $out->SetVariable('directory.id', $directory['id']);
  $out->SetVariable('directory.label', $directory['title']);
  $out->SetVariable('directory.total', $directory['total']);

  $date = $out->get_date($directory['modified']);
  $out->SetVariable('directory.modified', $date);
  $out->SetVariable('directory.data', $directory['data']);
   
return FALSE; }
 
/**************************************************
 * Component: directory_panel_table               *
 **************************************************/

public function directory_panel_table($column) {

  global $out, $cdirectory;

  if ($column == FALSE) {
    $out->ShowClass('nodirectory');
    $out->SetClass('directory', '');
  return FALSE; }

  $template = $out->GetClass('directory');

  $document = $out->GetOutput();
  $out->SetOutput('');

  $total = count($column);
  $count = 0; $data = '';

  for ($x = 0; $x < $total/3; $x++) {

    $out->BeginTag('div');
      $out->Assign('class', 'upanel.table');
    $out->EndTag();

  for ($y = 0; $y < 3; $y++) {

    if ($count == $total) break;

    $output = $out->GetOutput();
    $out->SetOutput($template);

    $this->directory_panel_row($column[$count]);

    $result = $out->GetOutput();
    $out->SetOutput($output.$result);

  $count++; }

    $out->BeginTag('/div');
    $out->EndTag(); }

  $result = $out->GetOutput();
  $out->SetOutput($document);
  
  $out->SetClass('directory', $result);
  $out->SetClass('nodirectory', '');

return FALSE; }

/**************************************************
 * Component: get_account_settings                *
 **************************************************/

public function get_account_settings() {

  global $out, $usr;

  if ($usr->client == NULL) {

    $out->ShowClass('account_settings.guest');
    $out->SetClass('account_settings.client', '');
    $out->SetClass('account_settings.profile', '');

  return; }

  $out->SetVariable('client.id', $usr->client['id']);
  $out->SetVariable('client.fullname', $usr->client['fullname']);

  $out->SetClass('account_settings.guest', '');
  $out->ShowClass('account_settings.client');

return; }

/**************************************************
 * Component: header                              *
 **************************************************/

public function header($directory) {

  global $out;

  $this->directory_image_pattachment($directory);
  $this->directory_image_banner($directory);

  $out->SetVariable('directory.id', $directory['id']);
  $out->SetVariable('directory.label', $directory['title']);
  $out->SetVariable('directory.description', $directory['data']);

  $this->get_account_settings();

return; }

/**************************************************
 * End Class                                      *
 **************************************************/
};

$cdirectory = new CDirectory();
