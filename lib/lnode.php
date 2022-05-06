<?php

/**************************************************
 * Application: EnigmaV                           *
 * Library: LNode                                 *
 * Author: Johnny L. de Alba                      *
 * Date: 02/26/2011                               *
 **************************************************/

class LNode {

public $table;
public $result;

public function Initialize($table) {

  $this->table = $table;
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
    $sql->Column('directory_id',
      'INT UNSIGNED',
      'NOT NULL');
    $sql->Next();
    $sql->Column('user_id',
      'INT UNSIGNED',
      'NOT NULL');
    $sql->Next();
    $sql->Column('rating',
      'INT UNSIGNED',
      'NOT NULL');
    $sql->Next();
    $sql->Column('total',
      'INT UNSIGNED',
      'NOT NULL');
    $sql->Next();
    $sql->Column('title',
      'CHAR(64)',
      'NOT NULL');
    $sql->Next();
    $sql->Column('type',
      'VARCHAR(3)',
      'NOT NULL');
    $sql->Next();
    $sql->Column('data',
      'MEDIUMTEXT',
      'NOT NULL');
    $sql->Next();
    $sql->Column('pattachment',
      'VARCHAR(64)',
      'NOT NULL');
    $sql->Next();
    $sql->Column('sattachment',
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
    $sql->Next(); $sql->Add('directory_id');
    $sql->Next(); $sql->Add('user_id');
    $sql->Next(); $sql->Add('rating');
    $sql->Next(); $sql->Add('total');
    $sql->Next(); $sql->Add('title');
    $sql->Next(); $sql->Add('type');
    $sql->Next(); $sql->Add('data');
    $sql->Next(); $sql->Add('pattachment');
    $sql->Next(); $sql->Add('sattachment');
    $sql->Next(); $sql->Add('permissions');
    $sql->Next(); $sql->Add('created');
    $sql->Next(); $sql->Add('modified');

    $sql->Value();
    $count = 0;

    $sql->Add($row[$count]);
    $sql->Next(); $sql->Add($row[++$count]);
    $sql->Next(); $sql->Add($row[++$count]);
    $sql->Next(); $sql->Add($row[++$count]);
    $sql->Next(); $sql->Add($row[++$count]);
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
 **************************************************/

public function UpdateRow($row) {

  global $sql;

  $sql->SetTable($this->table);
  $sql->BeginUpdate();

    $sql->Assign('parent_id',
      $row['parent_id']);
    $sql->Next();
    $sql->Assign('directory_id',
      $row['directory_id']);
    $sql->Next();
    $sql->Assign('user_id',
      $row['user_id']);
    $sql->Next();
    $sql->Assign('rating',
      $row['rating']);
    $sql->Next();
    $sql->Assign('total',
      $row['total']);
    $sql->Next();
    $sql->AssignString('title',
      $row['title']);
    $sql->Next();
    $sql->AssignString('type',
      $row['type']);
    $sql->Next();
    $sql->AssignString('data',
      $row['data']);
    $sql->Next();
    $sql->AssignString('pattachment',
      $row['pattachment']);
    $sql->Next();
    $sql->AssignString('sattachment',
      $row['sattachment']);
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

public function Create($row) {

  global $sql;

  $row[11] = date('Y-m-d H:i:s');
  $row[12] = $row[11];

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
 * Method: Join                                   *
 **************************************************/

public function Join($id) {

  global $sql;

  $sql->SetOutput('');

  $table = $sql->Prefix.$this->table;

  $utable = $sql->Prefix.'user';
  $dtable = $sql->Prefix.'directory';

  $sql->Add(sprintf("SELECT %s.*, ", $table));
    $sql->Add(sprintf("%s.fullname as user_fullname, ", $utable));
    $sql->Add(sprintf("%s.avatar as user_avatar, ", $utable));
    $sql->Add(sprintf("%s.title as directory_label, ", $dtable));
    $sql->Add(sprintf("%s.permissions as directory_permissions ", $dtable));

  $sql->Add(sprintf("FROM %s, %s, %s ", $table, $utable, $dtable));
    $sql->Where();

  $sql->Add(sprintf("(%s.id = %s.user_id AND ", $utable, $table));
  $sql->Add(sprintf("(%s.id = %s.directory_id OR ", $dtable, $table));
  $sql->Add(sprintf("%s.directory_id = 0)) AND ", $table));

    $sql->Assign(sprintf("%s.id", $table), $id);
  $sql->EndSelect();

  $row = $sql->FetchRow();
  if ($row == FALSE) { $this->result = $sql->result;
    return FALSE; }

return $row; }

/**************************************************
 * Method: JoinUser                               *
 **************************************************/

public function JoinUser($id) {

  global $sql;

  $sql->SetOutput('');

  $table = $sql->Prefix.$this->table;
  $utable = $sql->Prefix.'user';

  $sql->Add(sprintf("SELECT %s.*, ", $table));
    $sql->Add(sprintf("%s.fullname as user_fullname, ", $utable));
    $sql->Add(sprintf("%s.avatar as user_avatar ", $utable));

  $sql->Add(sprintf("FROM %s, %s ", $table, $utable));
    $sql->Where();

  $sql->Add(sprintf("%s.id = %s.user_id AND ", $utable, $table));
  $sql->Assign(sprintf("%s.id", $table), $id);

  $sql->EndSelect();

  $row = $sql->FetchRow();
  if ($row == FALSE) { $this->result = $sql->result;
    return FALSE; }

return $row; }

/**************************************************
 * Method: GetAll                                 *
 **************************************************/

public function GetAll($id, $value, $start, $total) {

  global $sql;

  $table = $sql->Prefix.$this->table;
  
  $sql->SetTable($this->table);
  $sql->BeginSelect();
    $sql->Where();
      $sql->Assign($id, $value);
	  $sql->Add(sprintf(" AND permissions NOT LIKE 'H_' ", $table));
    $sql->OrderBy('title', 'ASC');
    $sql->Offset($start, $total);
  $sql->EndSelect();

  $column = $sql->FetchColumn();
  if ($column == FALSE) return FALSE;

return $column; }

/**************************************************
 * Method: JoinAll                                *
 **************************************************/

public function JoinAll($id, $value, $start, $total) {

  global $sql;

  $sql->SetOutput('');

  $table = $sql->Prefix.$this->table;

  $utable = $sql->Prefix.'user';
  $dtable = $sql->Prefix.'directory';

  $sql->Add(sprintf("SELECT %s.*, ", $table));
    $sql->Add(sprintf("%s.fullname as user_fullname, ", $utable));
    $sql->Add(sprintf("%s.avatar as user_avatar, ", $utable));
    $sql->Add(sprintf("%s.title as directory_label ", $dtable));

  $sql->Add(sprintf("FROM %s, %s, %s ", $table, $utable, $dtable));
    $sql->Where();

  $sql->Add(sprintf("(%s.id = %s.user_id AND ", $utable, $table));
  $sql->Add(sprintf("%s.id = %s.directory_id) AND ", $dtable, $table));

  $sql->Assign(sprintf("%s.%s", $table, $id), $value);
    $sql->OrderBy($table.'.id', 'DESC');
    $sql->Offset($start, $total);
  $sql->EndSelect();

  $column = $sql->FetchColumn();
  if ($column == FALSE) return FALSE;

return $column; }

/**************************************************
 * Method: JoinUserAll                            *
 **************************************************/

public function JoinUserAll($id, $value, $start, $total) {

  global $sql;

  $sql->SetOutput('');

  $table = $sql->Prefix.$this->table;
  $utable = $sql->Prefix.'user';

  $sql->Add(sprintf("SELECT %s.*, ", $table));
    $sql->Add(sprintf("%s.fullname as user_fullname, ", $utable));
    $sql->Add(sprintf("%s.avatar as user_avatar ", $utable));

  $sql->Add(sprintf("FROM %s, %s ", $table, $utable));
    $sql->Where();

  $sql->Add(sprintf("%s.id = %s.user_id AND ", $utable, $table));
  $sql->Assign(sprintf("%s.%s", $table, $id), $value);

    $sql->OrderBy($table.'.id', 'DESC');
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
 * Method: Modify                                 *
 **************************************************/

public function Modify($row) {

  global $sql;

  $row['modified'] = date('Y-m-d H:i:s');
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
    $this->result = $sql>result;
  return FALSE; }

return TRUE; }

/**************************************************
 * Method: Remove                                 *
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
 * Method: GetAllRecent                           *
 **************************************************/

public function GetAllRecent($start, $total) {

  global $sql;

  $sql->SetOutput('');

  $table = $sql->Prefix.$this->table;

  $utable = $sql->Prefix.'user';
  $dtable = $sql->Prefix.'directory';

  $sql->Add(sprintf("SELECT %s.*, ", $table));
    $sql->Add(sprintf("%s.fullname as user_fullname, ", $utable));
    $sql->Add(sprintf("%s.avatar as user_avatar, ", $utable));
    $sql->Add(sprintf("%s.title as directory_label ", $dtable));

  $sql->Add(sprintf("FROM %s, %s, %s ", $table, $utable, $dtable));
    $sql->Where();

  $sql->Add(sprintf("(%s.id = %s.user_id AND ", $utable, $table));
  $sql->Add(sprintf("%s.id = %s.directory_id) AND ", $dtable, $table));

  $sql->Add(sprintf("%s.permissions NOT LIKE 'H_' ", $table));
  $sql->Add(sprintf("ORDER BY %s.created DESC, %s.id DESC", $table, $table));
    $sql->Offset($start, $total);
  $sql->EndSelect();

  $column = $sql->FetchColumn();
  if ($column == FALSE) return FALSE;

return $column; }

/**************************************************
 * Method: GetHighestRated                        *
 **************************************************/

public function GetHighestRated($start, $total) {

  global $sql;

  $sql->SetOutput('');

  $table = $sql->Prefix.$this->table;

  $utable = $sql->Prefix.'user';
  $dtable = $sql->Prefix.'directory';

  $sql->Add(sprintf("SELECT %s.*, ", $table));
    $sql->Add(sprintf("%s.fullname as user_fullname, ", $utable));
    $sql->Add(sprintf("%s.avatar as user_avatar, ", $utable));
    $sql->Add(sprintf("%s.title as directory_label ", $dtable));

  $sql->Add(sprintf("FROM %s, %s, %s ", $table, $utable, $dtable));
    $sql->Where();

  $sql->Add(sprintf("(%s.id = %s.user_id AND ", $utable, $table));
  $sql->Add(sprintf("%s.id = %s.directory_id) AND ", $dtable, $table));

  $sql->Add(sprintf("%s.permissions LIKE 'A_' ", $table));
  $sql->Add(sprintf("ORDER BY %s.rating DESC, %s.id DESC", $table, $table));
    $sql->Offset($start, $total);
  $sql->EndSelect();

  $column = $sql->FetchColumn();
  if ($column == FALSE) return FALSE;

return $column; }

/**************************************************
 * Component: set_message_title                  *
 **************************************************/

public function set_message_title($title) {

  global $out;

  if (strlen($title) < 60) {
    $title = preg_replace("/\[\/?[^\[\]]*\]/s", '', $title);
  return trim(htmlentities($title)); }

  $title = preg_replace("/\[\/?[^\[\]]*\]/s", '', $title);
  $title = htmlentities(substr($title, 0, 60));

  $word = preg_split("/[\s]+/", $title);
  $total = count($word); $title = $word[0];

  if ($total > 1) for ($count = 1; $count < $total-1; $count++)
    $title.= ' '.$word[$count];

return trim($title.'...'); }

/**************************************************
 * End Class                                      *
 **************************************************/
};

$ldirectory = new LNode(); $ldirectory->Initialize('directory');
$lmessage = new LNode(); $lmessage->Initialize('message');
$lcomment = new LNode(); $lcomment->Initialize('comment');

?>
