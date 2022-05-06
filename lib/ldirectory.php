<?php

/**************************************************
 * Application: EnigmaV                           *
 * Library: LDirectory                            *
 * Author: Johnny L. de Alba                      *
 * Date: 08/24/2013                               *
 **************************************************/

class LDirectory {

public $table;
public $result;

public function Initialize() {

  $this->table = 'directory';
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
    $sql->Column('total',
      'INT UNSIGNED',
      'NOT NULL');
    $sql->Next();
    $sql->Column('label',
      'VARCHAR(64)',
      'NOT NULL');
    $sql->Next();
    $sql->Column('description',
      'MEDIUMTEXT',
      'NOT NULL');
    $sql->Next();
    $sql->Column('icon',
      'VARCHAR(64)',
      'NOT NULL');
    $sql->Next();
    $sql->Column('banner',
      'VARCHAR(64)',
      'NOT NULL');
    $sql->Next();
    $sql->Column('permissions',
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
    $sql->Next(); $sql->Add('total');
    $sql->Next(); $sql->Add('label');
    $sql->Next(); $sql->Add('description');
    $sql->Next(); $sql->Add('icon');
    $sql->Next(); $sql->Add('banner');
    $sql->Next(); $sql->Add('permissions');
    $sql->Next(); $sql->Add('created');
    $sql->Next(); $sql->Add('modified');

    $sql->Value();
    $count = 0;

    $sql->Add($row[$count]);
    $sql->Next(); $sql->Add($row[++$count]);
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
 **************************************************/

public function UpdateRow($row) {

  global $sql;

  $sql->SetTable($this->table);
  $sql->BeginUpdate();

    $sql->Assign('parent_id',
      $row['parent_id']);
    $sql->Next();
    $sql->Assign('total',
      $row['total']);
    $sql->Next();
    $sql->AssignString('label',
      $row['label']);
    $sql->Next();
    $sql->AssignString('description',
      $row['description']);
    $sql->Next();
    $sql->AssignString('icon',
      $row['icon']);
    $sql->Next();
    $sql->AssignString('banner',
      $row['banner']);
    $sql->Next();
    $sql->AssignString('permissions',
      $row['permissions']);
    $sql->Next();
    $sql->AssignString('created',
      $row['created']);
    $sql->Next();
    $sql->AssignString('modified',
      $row['modified']);

    $sql->Where();
    $sql->Assign('id', $row['id']);
  $sql->EndUpdate();

  if ($sql->Query() == FALSE) {
    $this->result = $sql->result;
  return FALSE; }

return TRUE; }

/**************************************************
 * Method: Create                                 *
 **************************************************/

public function Create($parent_id, $label, $description, $icon,
	$banner, $permissions) {

  global $sql;
	
  $created = date('Y-m-d H:i:s');
  $modified = $created;

  $row = Array($parent_id, 0, $label, $description,
    $icon, $banner, $permissions, $created, $modified);

  if ($this->InsertRow($row) == FALSE)
    return FALSE;

return mysql_insert_id($sql->resource); }

/**************************************************
 * Method: Get                                    *
 **************************************************/

public function Get($id) {

  global $sql;

  $sql->SetTable($this->table);
  $row = $sql->SelectRow('id', $id);

  if ($row == FALSE) {
    $this->result = $sql->result;
  return FALSE; }

return $row; }

/**************************************************
 * Method: GetAll                                 *
 **************************************************/

public function GetAll($parent_id) {

  global $sql;

  $sql->SetTable($this->table);
  $sql->BeginSelect();
    $sql->Where();
      $sql->Assign('parent_id', $parent_id);
    $sql->OrderBy('label', 'ASC');
  $sql->EndSelect();

  $column = $sql->FetchColumn();
  if ($column == FALSE) return FALSE;

return $column; }

/**************************************************
 * Method: Modify                                 *
 **************************************************/

public function Modify($row) {

  global $sql;

  if ($this->UpdateRow($row) == FALSE)
    return FALSE;

return TRUE; }

/**************************************************
 * Method: Remove                                 *
 **************************************************/

public function Remove($id) {

  global $sql;

  $sql->SetTable($this->table);
  $result = $sql->DeleteRow($id);

  if ($result == FALSE) {
    $this->result = $sql->result;
  return FALSE; }

return TRUE; }

/**************************************************
 * End Class                                      *
 **************************************************/

};

$ldirectory = new LDirectory();
$ldirectory->Initialize();

?>
