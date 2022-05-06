<?php

/**************************************************
 * Application: EnigmaV                           *
 * Library: LibSession                            *
 * Author: Johnny L. de Alba                      *
 * Date: 12/30/2011                               *
 **************************************************/

class LibSession {

public $table;
public $result;

public function Create() {

  $this->table = 'session';
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
    $sql->Column('parent_id',
      'INT UNSIGNED',
      'NOT NULL');
    $sql->Next();
    $sql->Column('name',
      'VARCHAR(64)',
      'NOT NULL');
    $sql->Next();
    $sql->Column('data',
      'VARCHAR(64)',
      'NOT NULL');
    $sql->Next();
    $sql->Column('ip',
      'VARCHAR(16)',
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

    $sql->Add('parent_id');
    $sql->Next(); $sql->Add('name');
    $sql->Next(); $sql->Add('data');
    $sql->Next(); $sql->Add('ip');
    $sql->Next(); $sql->Add('created');
    $sql->Next(); $sql->Add('modified');

    $sql->Value();
    $count = 0;

    $sql->Add($row[$count]);

    $sql->Next(); $sql->Add(sprintf(
    "'%s'", $row[++$count]));
    $sql->Next(); $sql->Add(sprintf(
    "'%s'", $row[++$count]));
    $sql->Next(); $sql->Add(sprintf(
    "'%s'", $row[++$count]));
    $sql->Next(); $sql->Add(sprintf(
    "'%s'", $row[++$count]));
    $sql->Next(); $sql->Add(sprintf(
    "'%s'", $row[++$count]));

  $sql->EndInsert();

  if ($sql->Query() == FALSE) {
    $this->result = $sql->result;
  return FALSE; }

return TRUE; }

/**************************************************
 * Method: UpdateRow                              *
 **************************************************/

public function UpdateRow($row) {

  global $sql;

  $sql->SetTable($this->table);
  $sql->BeginUpdate();

    $sql->Assign('parent_id',
    $row['parent_id']);

    $sql->Next(); $sql->Assign('name',
    sprintf("'%s'", $row['name']));
    $sql->Next(); $sql->Assign('data',
    sprintf("'%s'", $row['data']));
    $sql->Next(); $sql->Assign('ip',
    sprintf("'%s'", $row['ip']));
    $sql->Next(); $sql->Assign('created',
    sprintf("'%s'", $row['created']));
    $sql->Next(); $sql->Assign('modified',
    sprintf("'%s'", $row['modified']));

    $sql->Where();
    $sql->Assign('id', $row['id']);
  $sql->EndUpdate();

  if ($sql->Query() == FALSE) {
    $this->result = $sql->result;
  return FALSE; }

return TRUE; }

/**************************************************
 * Method: CreateSession                          *
 **************************************************/

public function CreateSession($parent_id,
  $name, $data) {

  $created = date('Y-m-d H:i:s');
  $modified = $created;

  $row = Array($parent_id, $name, $data,
    '', $created, $modified);

  if ($this->InsertRow($row) == FALSE)
    return FALSE;

return TRUE; }

/**************************************************
 * Method: CreateGuestSession                     *
 **************************************************/

public function CreateGuestSession($ip,
  $name, $data) {

  $created = date('Y-m-d H:i:s');
  $modified = $created;

  $row = Array(0, $name, $data,
    $ip, $created, $modified);

  if ($this->InsertRow($row) == FALSE)
    return FALSE;

return TRUE; }

/**************************************************
 * Method: GetSession                             *
 **************************************************/

public function GetSession($parent_id, $name) {

  global $sql;

  $sql->SetTable($this->table);
  $row = $sql->SelectRowWith('name',
    sprintf("'%s'", $name),
    'parent_id', $parent_id);

  if ($row == FALSE) {
    $this->result = $sql->result;
  return FALSE; }

return $row; }

/**************************************************
 * Method: GetGuestSession                        *
 **************************************************/

public function GetGuestSession($ip, $name) {

  global $sql;

  $sql->SetTable($this->table);
  $row = $sql->SelectRowWith('name',
    sprintf("'%s'", $name), 'ip',
    sprintf("'%s'", $ip));

  if ($row == FALSE) {
    $this->result = $sql->result;
  return FALSE; }

return $row; }

/**************************************************
 * Method: SaveSession                            *
 **************************************************/

public function SaveSession($parent_id,
  $name, $data) {

  global $sql;

  if ($this->UpdateCache() == FALSE) return FALSE;

  if ($parent_id == 0) return FALSE;
  $row = $this->GetSession($parent_id, $name);

  if ($row == FALSE) {
  if ($this->result == 'FETCHFAIL') {
    $this->CreateSession($parent_id,
      $name, $data);
  return TRUE; }

  $this->result = $sql->result;
  return FALSE; } 

  $row['data'] = $data;
  $row['modified'] = date('Y-m-d H:i:s');

  if ($this->UpdateRow($row) == FALSE)
    return FALSE;

return TRUE; }

/**************************************************
 * Method: SaveGuestSession                       *
 **************************************************/

public function SaveGuestSession($ip,
  $name, $data) {

  global $sql;

  if ($this->UpdateCache() == FALSE) return FALSE;
  $row = $this->GetGuestSession($ip, $name);

  if ($row == FALSE) {
  if ($this->result == 'FETCHFAIL') {
    $this->CreateGuestSession($ip,
    $name, $data);
  return TRUE; }

  $this->result = $sql->result;
  return FALSE; } 

  $row['data'] = $data;
  $row['modified'] = date('Y-m-d H:i:s');

  if ($this->UpdateRow($row) == FALSE)
    return FALSE;

return TRUE; }

/**************************************************
 * Method: GenerateSeed                           *
 **************************************************/

public function GenerateSeed($parent_id) {

  srand(); $seed = md5(rand());
  $this->SaveSession($parent_id, 'SEED', $seed);

return $seed; }

/**************************************************
 * Method: GenerateGuestSeed                      *
 **************************************************/

public function GenerateGuestSeed($ip) {

  srand(); $seed = md5(rand());
  $this->SaveGuestSession($ip, 'SEED', $seed);

return $seed; }

/**************************************************
 * Method: ValidateSeed                           *
 * Description: Used On Forms To Only Allow       *
 *   Permission For A Single Submition.           *
 **************************************************/

public function ValidateSeed($parent_id) {

  $row = $this->GetSession($parent_id, 'SEED');
  $this->GenerateSeed($parent_id);

  if (!empty($_POST['s'])) $seed = $_POST['s'];
  else if (!empty($_GET['s'])) $seed = $_GET['s'];

  else { if (!empty($_POST['seed'])) $seed = $_POST['seed'];
    else if (!empty($_GET['seed'])) $seed = $_GET['seed'];
    else { $this->result = 'EMPTYSEED'; return FALSE; }}
  
  if ($row['data'] != $seed) {
    $this->result = 'INVALIDSEED';
  return FALSE; }

return TRUE; }

/**************************************************
 * Method: ValidateGuestSeed                      *
 **************************************************/

public function ValidateGuestSeed($ip) {

  $row = $this->GetGuestSession($ip, 'SEED');
  $this->GenerateGuestSeed($ip);

  if (!empty($_POST['s'])) $seed = $_POST['s'];
  else if (!empty($_GET['s'])) $seed = $_GET['s'];

  else { if (!empty($_POST['seed'])) $seed = $_POST['seed'];
    else if (!empty($_GET['seed'])) $seed = $_GET['seed'];
    else { $this->result = 'EMPTYSEED'; return FALSE; }}
  
  if ($seed != $row['data']) {
    $this->result = 'INVALIDSEED';
  return FALSE; }

return TRUE; }

/**************************************************
 * Method: SetFloodDetect                         *
 **************************************************/

public function SetFloodDetect($parent_id) {

  $this->SaveSession($parent_id, 'FLOOD', time());
 
return TRUE; }
 
/**************************************************
 * Method: FloodDetected                          *
 **************************************************/

public function FloodDetected($parent_id) {

  $result = $this->GetSession($parent_id, 'FLOOD');
  
  if ($result == FALSE) return FALSE;
  $elapsed = time()-((int)$result['data']);

  if ($elapsed <  60) {
    $this->result = 'FLOODDETECT';
  return TRUE; }
 
return FALSE; }
 
/**************************************************
 * Method: SetRecoveryPeriod                      *
 **************************************************/

public function SetRecoveryPeriod($ip) {

  $this->SaveGuestSession($ip, 'RECPERIOD', time());
 
return TRUE; }
 
/**************************************************
 * Method: RecoveryPeriod                         *
 **************************************************/

public function RecoveryPeriod($ip) {

  $result = $this->GetGuestSession($ip, 'RECPERIOD');
  
  if ($result == FALSE) return FALSE;
  $elapsed = time()-((int)$result['data']);

  if ($elapsed <  1440) {
    $this->result = 'RECPEROID';
  return TRUE; }
 
return FALSE; }
 
/**************************************************
 * Method: UpdateCache                            *
 **************************************************/

public function UpdateCache() {

  global $sql;

  $expired = sprintf("%s-%s %s", date('Y-m'),
    date('d')-1, date('H:i:s'));

  $sql->SetTable($this->table);
  $sql->BeginDelete();
    $sql->Where();
    $sql->Add(sprintf("created < '%s'", $expired));
  $sql->EndDelete();

  if ($sql->Query() == FALSE)
    return FALSE;

return TRUE; }

/**************************************************
 * End Class                                      *
 **************************************************/

};

?>
