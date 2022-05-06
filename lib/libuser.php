<?php

/**************************************************
 * Application: EnigmaV                           *
 * Library: LibUser                               *
 * Author: Johnny L. de Alba                      *
 * Date: 12/29/2011                               *
 **************************************************/

class LibUser {

public $client_id;

public $client;
public $client_ip;

public $table;
public $result;

public function Create() {

  $this->client = NULL;
  $this->contact = NULL;

  $this->client_ip = $_SERVER['REMOTE_ADDR'];

  $this->table = 'user';
  $this->result = '';

return; }

/**************************************************
 * Method: CreateTable                            *
 **************************************************/

public function CreateTable() {

  global $sql;

  $sql->SetTable($this->table);
  $sql->BeginTable();
    $sql->Column('id',
      'INT UNSIGNED',
      'AUTO_INCREMENT');
    $sql->Next();
    $sql->Column('email',
      'VARCHAR(64)',
      'NOT NULL');
    $sql->Next();
    $sql->Column('password',
      'VARCHAR(64)',
      'NOT NULL');
    $sql->Next();
    $sql->Column('fullname',
      'VARCHAR(64)',
      'NOT NULL');
    $sql->Next();
    $sql->Column('sex',
      'VARCHAR(1)',
      'NOT NULL');
    $sql->Next();
    $sql->Column('birthday',
      'VARCHAR(10)',
      'NOT NULL');
    $sql->Next();
    $sql->Column('current_city',
      'VARCHAR(64)',
      'NOT NULL');
    $sql->Next();
    $sql->Column('current_state',
      'VARCHAR(64)',
      'NOT NULL');
    $sql->Next();
    $sql->Column('employer',
      'VARCHAR(64)',
      'NOT NULL');
    $sql->Next();
    $sql->Column('position',
      'VARCHAR(64)',
      'NOT NULL');
    $sql->Next();
    $sql->Column('highschool',
      'VARCHAR(64)',
      'NOT NULL');
    $sql->Next();
    $sql->Column('college',
      'VARCHAR(64)',
      'NOT NULL');
    $sql->Next();
    $sql->Column('major',
      'VARCHAR(64)',
      'NOT NULL');
    $sql->Next();
    $sql->Column('degree',
      'VARCHAR(64)',
      'NOT NULL');
    $sql->Next();
    $sql->Column('avatar',
      'VARCHAR(64)',
      'NOT NULL');
    $sql->Next();
    $sql->Column('banner',
      'VARCHAR(64)',
      'NOT NULL');
    $sql->Next();
    $sql->Column('type',
      'VARCHAR(3)',
      'NOT NULL');
    $sql->Next();
    $sql->Column('permissions',
      'VARCHAR(3)',
      'NOT NULL');
    $sql->Next();
    $sql->Column('ip',
      'VARCHAR(16)',
      'NOT NULL');
    $sql->Next();
    $sql->Column('session_id',
      'VARCHAR(64)',
      'NOT NULL');
    $sql->Next();
    $sql->Column('created',
      'DATETIME',
      'NOT NULL');
    $sql->Next();
    $sql->Column('modified',
      'DATETIME',
      'NOT NULL');
    $sql->Next();
    $sql->PrimaryKey('id');
  $sql->EndTable();

  if ($sql->Query() == FALSE) {
    $this->result = $sql->result;
  return FALSE; }

return TRUE; }

/**************************************************
 * Method: InsertRow                              *
 **************************************************/

public function InsertRow($row) {

  global $sql;

  $sql->SetTable($this->table);
  $sql->BeginInsert();

    $sql->Add('email');
    $sql->Next(); $sql->Add('password');
    $sql->Next(); $sql->Add('fullname');
    $sql->Next(); $sql->Add('sex');
    $sql->Next(); $sql->Add('birthday');
    $sql->Next(); $sql->Add('current_city');
    $sql->Next(); $sql->Add('current_state');
    $sql->Next(); $sql->Add('employer');
    $sql->Next(); $sql->Add('position');
    $sql->Next(); $sql->Add('highschool');
    $sql->Next(); $sql->Add('college');
    $sql->Next(); $sql->Add('major');
    $sql->Next(); $sql->Add('degree');
    $sql->Next(); $sql->Add('avatar');
    $sql->Next(); $sql->Add('banner');
    $sql->Next(); $sql->Add('type');
    $sql->Next(); $sql->Add('permissions');
    $sql->Next(); $sql->Add('ip');
    $sql->Next(); $sql->Add('session_id');
    $sql->Next(); $sql->Add('created');
    $sql->Next(); $sql->Add('modified');

    $sql->Value();
    $count = 0;

    $sql->AddString($row[$count]);
    $sql->Next(); $sql->AddString($row[++$count]);
    $sql->Next(); $sql->AddString($row[++$count]);
    $sql->Next(); $sql->AddString($row[++$count]);
    $sql->Next(); $sql->AddString($row[++$count]);
    $sql->Next(); $sql->AddString($row[++$count]);
    $sql->Next(); $sql->AddString($row[++$count]);
    $sql->Next(); $sql->AddString($row[++$count]);
    $sql->Next(); $sql->AddString($row[++$count]);
    $sql->Next(); $sql->AddString($row[++$count]);
    $sql->Next(); $sql->AddString($row[++$count]);
    $sql->Next(); $sql->AddString($row[++$count]);
    $sql->Next(); $sql->AddString($row[++$count]);
    $sql->Next(); $sql->AddString($row[++$count]);
    $sql->Next(); $sql->AddString($row[++$count]);
    $sql->Next(); $sql->AddString($row[++$count]);
    $sql->Next(); $sql->AddString($row[++$count]);
    $sql->Next(); $sql->AddString($row[++$count]);
    $sql->Next(); $sql->AddString($row[++$count]);
    $sql->Next(); $sql->AddString($row[++$count]);
    $sql->Next(); $sql->AddString($row[++$count]);
	
  $sql->EndInsert();

  if ($sql->Query() == FALSE) {
    $this->result = $sql->result;
  return FALSE; }

return TRUE; }

/**************************************************
 * Method: UpdateRow                              *
 * Note: If The Password Has Changed Be Sure To   *
 *   Encrypt It Using md5() Before Storing It In  *
 *   Database.                                    *
 **************************************************/

public function UpdateRow($row) {

  global $sql;

  $sql->SetTable($this->table);
  $sql->BeginUpdate();

    $sql->AssignString('email', $row['email']);
    $sql->Next(); $sql->AssignString('password', $row['password']);
    $sql->Next(); $sql->AssignString('fullname', $row['fullname']);
    $sql->Next(); $sql->AssignString('sex', $row['sex']);
    $sql->Next(); $sql->AssignString('birthday', $row['birthday']);
    $sql->Next(); $sql->AssignString('current_city', $row['current_city']);
    $sql->Next(); $sql->AssignString('current_state', $row['current_state']);
    $sql->Next(); $sql->AssignString('employer', $row['employer']);
    $sql->Next(); $sql->AssignString('position', $row['position']);
    $sql->Next(); $sql->AssignString('highschool', $row['highschool']);
    $sql->Next(); $sql->AssignString('college', $row['college']);
    $sql->Next(); $sql->AssignString('major', $row['major']);
    $sql->Next(); $sql->AssignString('degree', $row['degree']);
    $sql->Next(); $sql->AssignString('avatar', $row['avatar']);
    $sql->Next(); $sql->AssignString('banner', $row['banner']);
    $sql->Next(); $sql->AssignString('type', $row['type']);
    $sql->Next(); $sql->AssignString('permissions', $row['permissions']);
    $sql->Next(); $sql->AssignString('ip', $row['ip']);
    $sql->Next(); $sql->AssignString('session_id', $row['session_id']);
    $sql->Next(); $sql->AssignString('created', $row['created']);
    $sql->Next(); $sql->AssignString('modified', $row['modified']);

    $sql->Where();
    $sql->Assign('id', $row['id']);
  $sql->EndUpdate();

  if ($sql->Query() == FALSE) {
    $this->result = $sql->result;
  return FALSE; }

return TRUE; }

/**************************************************
 * Method: GetUser                                *
 **************************************************/

public function GetUser($id, $value) {

  global $sql;

  if ($id != 'id') $value = sprintf("'%s'", $value);
 
  $sql->SetTable($this->table);
  $user = $sql->SelectRow($id, $value);

  if ($user == FALSE) {
    $this->result = $sql->result;
  return FALSE; }

return $user; }

/**************************************************
 * Method: GetAll                                 *
 **************************************************/

public function GetAll($start, $total) {

  global $sql;

  $sql->SetTable($this->table);
  $sql->BeginSelect();
    $sql->Where();
      $sql->Add("permissions LIKE 'A_' ");
      $sql->OrderBy('fullname', 'ASC');
    $sql->Offset($start, $total);
  $sql->EndSelect();

  $column = $sql->FetchColumn();
  if ($column == FALSE) return FALSE;

return $column; }

/**************************************************
 * Method: CountAll                               *
 **************************************************/

public function CountAll($id, $value) {

  global $sql;

  $sql->SetTable($this->table);
  $sql->BeginSelect();
    $sql->Where();
      $sql->Assign($id, $value);
  $sql->EndSelect();

return $sql->CountRows(); }

/**************************************************
 * Method: CreateUser                             *
 **************************************************/

public function CreateUser($email,
  $password, $fullname) {

  global $sql;

  $user = $this->GetUser('email', $email);
  
  if ($user == FALSE) {
  if ($this->result == 'QUERYFAIL')
    return FALSE; }

  else { $this->result = 'EXISTEMAIL';
    return FALSE; }

  $password = md5($password);
  srand(); $session_id = md5(rand());

  $created = date('Y-m-d H:i:s');
  $modified = $created;

  $row = Array($email, $password, $fullname,
    '', '', '', '', '', '', '', '', '', '', '', '',
	'USR', '', $this->client_ip, $session_id, $created, $modified);

  if ($this->InsertRow($row) == FALSE)
    return FALSE;

return mysql_insert_id($sql->resource); }

/**************************************************
 * Method: ModifyUser                             *
 **************************************************/

public function ModifyUser($row) {

  global $sql;

  $user = $this->GetUser("email", $row['email']);
  if ($user == FALSE) {

  if ($sql->result == 'QUERYFAIL') {
    $this->result = $sql->result;
  return FALSE; }}

  if (! ($row['id'] == $user['id']) &&
  ($row['email'] == $user['email'])) {
    $this->result = 'EXISTEMAIL';
  return FALSE; }

  if ($this->UpdateRow($row) == FALSE)
    return FALSE;

return true; }

/**************************************************
 * Method: CreateSession                          *
 **************************************************/

public function CreateSession($row, $persistent) {

  srand(); $row['session_id'] = md5(rand());
  $row['ip'] = $this->client_ip;
  $row['modified'] = date('Y-m-d H:i:s');

  if ($this->UpdateRow($row) == FALSE)
    return FALSE;

  $expiration = 0;
  if ($persistent == 'TRUE') 
    $expiration = (time()+3600*24*30);

  setcookie('session_id', $row['session_id'],
    $expiration);

  $this->client = $row;

return TRUE; }

/**************************************************
 * Method: Login                                  *
 **************************************************/

public function Login($email, $password,
  $persistent) {

  global $sql;

  $user = $this->GetUser('email', $email);
  if ($user == FALSE) {
    $this->result = $sql->result;

  if ($this->result == 'FETCHFAIL')
    $this->result = 'NOEXISTEMAIL';
  return FALSE; }

  if ($user['type'] == 'DSB') {
    $this->result = 'DISABLEDUSER';
    return FALSE; }

  $password = md5($password);
  if ($password != $user['password']) {
    $this->result = 'NOMATCHPASS';
  return FALSE; }

  if ($this->CreateSession($user,
    $persistent) == FALSE)
  return FALSE;

return TRUE; }

/**************************************************
 * Method: Logout                                 *
 **************************************************/

public function Logout() {

  setcookie('session_id', '');
  $this->client = NULL;

return; }

/**************************************************
 * Method: UpdateSession                          *
 **************************************************/

public function UpdateSession() {

  global $sql;

  if (empty($_COOKIE['session_id'])) {
    $this->result = 'EMPTYSESSIONID';
  return FALSE; }

  $session_id = $_COOKIE['session_id'];
  $row = $this->GetUser('session_id', $session_id);

  if ($row == FALSE) {

  if ($this->result == 'FETCHFAIL')
    $this->result = 'NOEXISTSESSIONID';
  return FALSE; }

  $row['ip'] = $this->client_ip;
  $row['modified'] = date('Y-m-d H:i:s');

  if ($this->UpdateRow($row) == FALSE)
    return FALSE;

  $this->client = $row;

return TRUE; }

/**************************************************
 * Method: GetPermission                          *
 **************************************************/

public function GetPermission($permission, $id) {
  return substr($permission, $id-1, 1); }

/**************************************************
 * Component: get_client_controlpanel             *
 **************************************************/

public function get_client_controlpanel() {

  global $out;

  if ($this->client == NULL) {

    $out->ShowClass('controlpanel.guest');
    $out->SetClass('controlpanel.client', '');

  return; }

  $out->SetVariable('client.id', $this->client['id']);
  $out->SetVariable('client.fullname', $this->client['fullname']);

  $out->SetClass('controlpanel.guest', '');
  $out->ShowClass('controlpanel.client');
  $out->SetClass('controlpanel.contact', '');

return; }

/**************************************************
 * Component: get_contact_controlpanel            *
 **************************************************/

public function get_contact_controlpanel() {

  global $out;

  if ($this->client == NULL) {

    $out->ShowClass('controlpanel.guest');
    $out->SetClass('controlpanel.client', '');
    $out->SetClass('controlpanel.contact', '');

  return; }

  if (($this->client['id'] == $this->contact['id']) || ($this->client['type'] == 'ADN')) {

    $out->SetClass('controlpanel.guest', '');
    $out->SetClass('controlpanel.client', '');

    $out->ShowClass('controlpanel.contact');
    $out->ShowClass('contact.profile');

  return; }

  $out->SetVariable('client.id', $this->client['id']);
  $out->SetVariable('client.fullname', $this->client['fullname']);

  $out->SetClass('controlpanel.guest', '');
  $out->ShowClass('controlpanel.client');
  $out->SetClass('controlpanel.contact', '');

return; }

/**************************************************
 * End Class                                      *
 **************************************************/
};

?>
