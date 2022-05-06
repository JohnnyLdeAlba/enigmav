<?php

/**************************************************
 * Application: EnigmaV                           *
 * Module: MInstall                               *
 * Author: Johnny L. de Alba                      *
 * Date: 02/17/2012                               *
 **************************************************/

class MInstall {

public $state;
public $result;

public $d;

public function Create() {

  global $sql;

  $this->state = 'INITIALIZE';
  $this->result = '';

  $sql->GetInput('d');
  $sql->GetInput('p');

  $this->d = $sql->input['d'];
  $this->p = $sql->input['p'];

  if ($this->d == NULL)
    $this->d = 'DEFAULT';
  if ($this->p == NULL)
    $this->p = 'DEFAULT';

  $this->d = strtoupper($this->d);
  $this->p = strtoupper($this->p);

return; }

/**************************************************
 * State: Initialize                              *
 **************************************************/

public function Initialize() {

  global $sql;

  if ($sql->Connect() == FALSE) {
    $this->result = $sql->result;
    $this->state = 'ERROR';
  return FALSE; }

  $this->state = 'CREATEDATABASE';

return TRUE; }

/**************************************************
 * State: CreateDatabase                          *
 **************************************************/

public function CreateDatabase() {

  global $sql;

  $sql->DropDatabase();
  $sql->CreateDatabase();

  if ($sql->UseDatabase() == FALSE) {
    $this->result = $sql->result;
    $this->state = 'ERROR';
  return FALSE; }

  $this->state = 'CREATETABLE';

return TRUE; }

/**************************************************
 * State: CreateTable                             *
 **************************************************/

public function CreateTable() {

  global $usr;
  global $ses, $fld, $umsg, $ucom, $lcounter;

  if ($usr->CreateTable() == FALSE) {
    $this->result = $usr->result;
    $this->state = 'ERROR';
  return FALSE; }

  if ($ses->CreateTable() == FALSE) {
    $this->result = $ses->result;
    $this->state = 'ERROR';
  return FALSE; }

  if ($fld->CreateTable() == FALSE) {
    $this->result = $fld->result;
    $this->state = 'ERROR';
  return FALSE; }

  if ($umsg->CreateTable() == FALSE) {
    $this->result = $umsg->result;
    $this->state = 'ERROR';
  return FALSE; }

  if ($ucom->CreateTable() == FALSE) {
    $this->result = $ucom->result;
    $this->state = 'ERROR';
  return FALSE; }

  $lcounter->table = 'umessage_rating';
  if ($lcounter->CreateTable() == FALSE) {
    $this->result = $lcounter->result;
    $this->state = 'ERROR';
  return FALSE; }

  $lcounter->table = 'ucomment_rating';
  if ($lcounter->CreateTable() == FALSE) {
    $this->result = $lcounter->result;
    $this->state = 'ERROR';
  return FALSE; }

  $this->state = 'CREATEDATA';

return TRUE; }

/**************************************************
 * State: CreateData                              *
 **************************************************/

public function CreateData() {

  global $sql;
  global $usr;
  global $ses, $fld, $umsg;

  $result = $usr->CreateUser('johnny.dealba@gmail.com',
    'password', 'Johnny L. de Alba');

  if ($result == FALSE) {
    $this->result =$usr->result;
    $this->state = 'ERROR';
  return FALSE; }

  $sql->SetTable('user');
  $row = $sql->SelectRow('id', $result);
  if ($row == FALSE) {
    $this->result = $sql->result;
    $this->state = 'ERROR';
  return FALSE; }

  $row['type'] = 'ADN';
  if ($usr->UpdateRow($row) == FALSE) {
    $this->result = $usr->result;
    $this->state = 'ERROR';
  return FALSE; }

  $this->state = 'DISPLAY';

return TRUE; }

/**************************************************
 * Template: ShowNotice                           *
 **************************************************/

public function ShowNotice() {

  global $NetworkName;
  global $NetworkDomain;

  global $sql;
  global $usr;
  global $tpl;

  $row = $sql->SelectRow('id',
    $usr->client_id);

  $tpl->title = "Notice";
  $tpl->GetFile('notice.htm');

  $tpl->SetClass('loggedin', '');
  $tpl->ShowClass('loggedout');

  $tpl->SetVariable('messagebox.text', 'INSTALLSUCCESS');
  $tpl->SetVariable('y_href', 'Register for an account');
  $tpl->SetVariable('z_href', 'Login to your account');

  $tpl->SetVariable('y_url', 'register.php');
  $tpl->SetVariable('z_url', 'login.php');
  $tpl->Display($this->d);

  $this->state = 'QUIT';

return; }

/**************************************************
 * Template: ShowError                            *
 **************************************************/

public function ShowError() {

  global $tpl;

  $tpl->title = "Error";
  $tpl->GetFile('error.htm');
  $tpl->SetVariable('messagebox.text', $this->result);
  $tpl->SetVariable('href', 'Home');
  $tpl->SetVariable('url', 'index.php');
  $tpl->Display($this->d);

  $this->state = 'QUIT';

return ; }

/**************************************************
 * Main: Begin                                    *
 **************************************************/

public function Begin() {

while($this->state != 'QUIT') {
switch ($this->state) {

  case 'INITIALIZE':
    $this->Initialize();
  break;

  case 'CREATEDATABASE':
    $this->CreateDatabase();
  break;

  case 'CREATETABLE':
    $this->CreateTable();
  break;

  case 'CREATEDATA':
    $this->CreateData();
  break;

  case 'DISPLAY':
    $this->ShowNotice();
  break;

  case 'ERROR':
    $this->ShowError();
  break;

  default: $this->state = 'QUIT';
    break;

}} mysql_close();
return; }


/**************************************************
 * End Class                                      *
 **************************************************/
};

$z = new MInstall();
$z->Create();
$z->Begin();

?>
