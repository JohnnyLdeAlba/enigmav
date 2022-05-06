<?php

/**************************************************
 * Application: EnigmaV                           *
 * Library: LCounter                              *
 * Author: Johnny L. de Alba                      *
 * Date: 02/17/2012                               *
 **************************************************/

class LCounter {

public $table;
public $result;

public function Create() {

  $this->table = 'counter';
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
    $sql->Column('uid',
      'INT UNSIGNED',
      'NOT NULL');
    $sql->Next();
    $sql->Column('counter',
      'INT UNSIGNED',
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
    $sql->Next(); $sql->Add('uid');
    $sql->Next(); $sql->Add('counter');

    $sql->Next(); $sql->Add('ip');
    $sql->Next(); $sql->Add('created');
    $sql->Next(); $sql->Add('modified');

    $sql->Value(); $count = 0;

    $sql->Add($row[$count]);
    $sql->Next(); $sql->Add($row[++$count]);
    $sql->Next(); $sql->Add($row[++$count]);

    $sql->Next(); $sql->Add(sprintf("'%s'", $row[++$count]));
    $sql->Next(); $sql->Add(sprintf("'%s'", $row[++$count]));
    $sql->Next(); $sql->Add(sprintf("'%s'", $row[++$count]));

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

    $sql->Assign('parent_id', $row['parent_id']);
    $sql->Next(); $sql->Assign('uid', $row['uid']);
    $sql->Next(); $sql->Assign('counter', $row['counter']);

    $sql->Next(); $sql->Assign('ip', sprintf("'%s'", $row['ip']));
    $sql->Next(); $sql->Assign('created', sprintf("'%s'", $row['created']));
    $sql->Next(); $sql->Assign('modified', sprintf("'%s'", $row['modified']));

    $sql->Where();
    $sql->Assign('id', $row['id']);
  $sql->EndUpdate();

  if ($sql->Query() == FALSE) {
    $this->result = $sql->result;
  return FALSE; }

return TRUE; }

/**************************************************
 * Method: CreateCounter                          *
 **************************************************/

public function CreateCounter($parent_id,
  $uid, $counter) {

  $created = date('Y-m-d H:i:s');
  $modified = $created;

  $row = Array($parent_id, $uid, $counter, '',
    $created, $modified);

  if ($this->InsertRow($row) == FALSE)
    return FALSE;

return TRUE; }

/**************************************************
 * Method: CreateGuestCounter                     *
 **************************************************/

public function CreateGuestCounter($parent_id,
  $ip, $counter) {

  $created = date('Y-m-d H:i:s');
  $modified = $created;

  $row = Array($parent_id, 0, $counter, $ip,
    $created, $modified);

  if ($this->InsertRow($row) == FALSE)
    return FALSE;

return TRUE; }

/**************************************************
 * Method: GetCounter                             *
 **************************************************/

public function GetCounter($parent_id, $uid) {

  global $sql;

  $sql->SetTable($this->table);
  $row = $sql->SelectRowWith('parent_id',
    $parent_id, 'uid', $uid);

  if ($row == FALSE) {
    $this->result = $sql->result;
  return FALSE; }

return $row; }

/**************************************************
 * Method: GetGuestCounter                        *
 **************************************************/

public function GetGuestCounter($parent_id, $ip) {

  global $sql;

  $sql->SetTable($this->table);
  $row = $sql->SelectRowWith('parent_id',
    $parent_id, 'ip', sprintf("'%s'", $ip));

  if ($row == FALSE) {
    $this->result = $sql->result;
  return FALSE; }

return $row; }

/**************************************************
 * Method: SaveCounter                            *
 **************************************************/

public function SaveCounter($parent_id, $uid, $counter) {

  global $sql;

  if ($parent_id == 0) return FALSE;
  $row = $this->GetCounter($parent_id, $uid);

  if ($row == FALSE) {
  if ($this->result == 'FETCHFAIL') {
    $this->CreateCounter($parent_id, $uid, $counter);
  return TRUE; }

  $this->result = $sql->result;
  return FALSE; } 

  if ($row['counter'] == $counter)
    return FALSE;

  $row['counter'] = $counter;
  $row['modified'] = date('Y-m-d H:i:s');

  if ($this->UpdateRow($row) == FALSE)
    return FALSE;

return TRUE; }

/**************************************************
 * Method: SaveGuestCounter                       *
 **************************************************/

public function SaveGuestCounter($parent_id, $ip, $counter) {

  global $sql;

  if ($this->UpdateCache()) return FALSE;
  $row = $this->GetGuestCounter($parent_id, $ip);

  if ($row == FALSE) {
  if ($this->result == 'FETCHFAIL') {
    $this->CreateGuestCounter($parent_id, $ip, $counter);
  return TRUE; }

  $this->result = $sql->result;
  return FALSE; } 

  $row['counter'] = $counter;
  $row['modified'] = date('Y-m-d H:i:s');

  if ($this->UpdateRow($row) == FALSE)
    return FALSE;

return TRUE; }

/**************************************************
 * Method: RemoveAllCounters                      *
 **************************************************/

public function RemoveAll($parent_id) {

  global $sql;

  $sql->SetTable($this->table);
  $result = $sql->DeleteAllRows('parent_id', $parent_id);

  if ($result == FALSE) {
    $this->result = $sql->result;
  return FALSE; }

return TRUE; }

/**************************************************
 * End Class                                      *
 **************************************************/
};

$lcounter = new LCounter();

?>
