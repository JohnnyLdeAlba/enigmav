<?php

/**************************************************
 * Application: EnigmaV                           *
 * Library: LSubscriber                           *
 * Author: Johnny L. de Alba                      *
 * Date: 02/22/2012                               *
 **************************************************/

class LSubscriber {

public $table;
public $result;

public $subscriber;

public function Create() {

  $this->table = 'subscriber';
  $this->result = '';
  $this->subscriber = NULL;

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
    $sql->Column('type',
      'VARCHAR(3)',
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
    $sql->Next(); $sql->Add('type');

    $sql->Next(); $sql->Add('created');
    $sql->Next(); $sql->Add('modified');

    $sql->Value(); $count = 0;

    $sql->Add($row[$count]);
    $sql->Next(); $sql->Add($row[++$count]);
    $sql->Next(); $sql->AddString($row[++$count]);

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
    $sql->Next(); $sql->AssignString('type', $row['type']);

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
 * Method: CreateSubscriber                       *
 **************************************************/

public function CreateSubscriber(
  $parent_id, $uid, $type) {

  $created = date('Y-m-d H:i:s');
  $modified = $created;

  $row = Array($parent_id, $uid, $type,
    $created, $modified);

  if ($this->InsertRow($row) == FALSE)
    return FALSE;

return TRUE; }

/**************************************************
 * Method: CountSubscribers                       *
 **************************************************/

public function CountSubscribers($id, $type) {

  global $sql;

  $sql->SetTable($this->table);
  $sql->BeginSelect();
    $sql->Where();
      $sql->Assign('parent_id', $id);
    $sql->With();
      $sql->AssignString('type', $type);
  $sql->EndSelect();

return $sql->CountRows(); }


/**************************************************
 * Method: GetSubscriber                          *
 **************************************************/

public function GetSubscriber($parent_id, $uid) {

  global $sql;

  $sql->SetTable($this->table);
  $row = $sql->SelectRowWith('parent_id',
    $parent_id, 'uid', $uid);

  if ($row == FALSE) {
    $this->result = $sql->result;
  return FALSE; }

return $row; }

/**************************************************
 * Method: GetAll                                 *
 **************************************************/

public function GetAll($parent_id, $type, $start, $total) {

  global $sql;

  $sql->SetTable($this->table);
  $sql->BeginSelect();
    $sql->Where();
      $sql->Assign('parent_id', $parent_id);
    $sql->With();
    $sql->AssignString('type', $type);
    $sql->Offset($start, $total);
  $sql->EndSelect();

  $column = $sql->FetchColumn();
  if ($column == FALSE) return FALSE;

return $column; }

/**************************************************
 * Method: SaveSubscriber                         *
 **************************************************/

public function SaveSubscriber($parent_id, $uid, $type) {

  global $sql;

  if ($parent_id == 0) return FALSE;
  $row = $this->GetSubscriber($parent_id, $uid);

  if ($row == FALSE) {
  if ($this->result == 'FETCHFAIL') {
    $this->CreateSubscriber($parent_id, $uid, $type);
  return TRUE; }

  $this->result = $sql->result;
  return FALSE; } 

  if ($row['type'] == $type)
    return FALSE;

  $row['type'] = $type;
  $row['modified'] = date('Y-m-d H:i:s');

  if ($this->UpdateRow($row) == FALSE)
    return FALSE;

return TRUE; }

/**************************************************
 * Method: RemoveSubscriber                       *
 **************************************************/

public function RemoveSubscriber($id) {

  global $sql;

  $sql->SetTable($this->table);
  $result = $sql->DeleteRow($id);

  if ($result == FALSE) {
    $this->result = $sql->result;
  return FALSE; }

return TRUE; }

/**************************************************
 * Method: ReadPermission                         *
 **************************************************/

public function ReadPermission($permissions) {

  $read = substr($permissions, 0, 1);
  if ($read == 'P') return FALSE;

  if ($this->subscriber == NULL)
    if ($read == 'C') return FALSE;
  if ($this->subscriber['type'] == 'RQS')
    if ($read == 'C') return FALSE;

return TRUE; }

/**************************************************
 * Method: UpdateSubscriber                       *
 **************************************************/

public function UpdateSubscriber() {

  global $usr;

  if ($usr->client == NULL) return TRUE;

  $subscriber = $this->GetSubscriber(
    $usr->contact['id'], $usr->client['id']);

  if ($subscriber == FALSE) {
    $this->subscriber = NULL;
  return FALSE; }

  $this->subscriber = $subscriber;

return TRUE; }

/**************************************************
 * End Class                                      *
 **************************************************/
};

$lsubscriber = new LSubscriber();
$lsubscriber->Create();

?>
