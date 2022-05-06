<?php 

/**************************************************
 * Application: EnigmaV                           *
 * Library: LibMySql                              *
 * Author: Johnny L. de Alba                      *
 * Date: 12/29/2011                               *
 **************************************************/

class LibMySql {

public $Prefix;
public $Host;
public $Username;
public $Password;
public $Database;

public $resource;
public $result;
public $table;
public $input;
public $output;

public function Create() {

  global $MySqlPrefix;
  global $MySqlHost;
  global $MySqlUsername;
  global $MySqlPassword;
  global $MySqlDatabase;

  $this->Prefix = $MySqlPrefix;
  $this->Host = $MySqlHost;
  $this->Username = $MySqlUsername;
  $this->Password = $MySqlPassword;
  $this->Database = $MySqlDatabase;
  
  $this->result = '';
  $this->table = '';
  $this->input = Array();

return; }

/**************************************************
 * Description: MySql Syntax Rendering Methods.   *
 **************************************************/

public function SetTable($table) {
  $this->table = $table;
return; }

public function SetOutput($output) {
  $this->output = $output;
return; }

public function GetOutput() {
return $this->output; }

public function Add($output) {
  $this->output.= $output;
return; }

public function AddString($output) {

  $output = mysqli_real_escape_string($this->resource, $output);
  $this->output.= sprintf("'%s'", $output);

return; }

/**************************************************
 * Description: MySql Connection Methods.         *
 **************************************************/

public function Connect() {

  $this->resource = mysqli_connect($this->Host,
    $this->Username, $this->Password);
	
  if ($this->resource == FALSE) {
    $this->result = 'CONNECTDBFAIL';
  return FALSE; }

  mysqli_set_charset('utf8', $this->resource);
  
return TRUE; }

public function UseDatabase() {

  $output = sprintf("USE %s;", $this->Database);
  if (mysqli_query($this->resource, $output) == FALSE) {
    $this->result = "SELECTDBFAIL";
  return FALSE; }

return TRUE; }

public function Close() {
  mysqli_close();
return; }

/**************************************************
 * Description: MySql Creation Methods.           *
 **************************************************/

public function CreateDatabase() {
  $output = sprintf("CREATE DATABASE %s;",
    $this->Database);
  mysqli_query($output);
return; }

public function DropDatabase() {
  $output = sprintf("DROP DATABASE IF EXISTS %s;",
    $this->Database);
  mysqli_query($output);
return; }

/**************************************************
 * Method: Query                                  *
 **************************************************/

public function Query() {

  $statement = mysqli_stmt_init($this->resource);
  $result = mysqli_stmt_prepare($statement, $this->output);
  if ($result == FALSE) {
    $this->result = 'QUERYFAIL';
  return FALSE; }

  mysqli_stmt_execute($statement);
  $statement->close();
  
return TRUE; }

public function Exec($output) {
  $this->SetOutput($output);
return $this->Query(); }

/**************************************************
 * Method: CountRows                              *
 **************************************************/

public function CountRows() {

  $result = mysqli_query($this->output);
  if ($result == FALSE) {
    $this->result = 'QUERYFAIL';
  return FALSE; }

return mysqli_num_rows($result); }

/**************************************************
 * Method: FetchRow                               *
 **************************************************/

public function FetchRow() {

  $statement = mysqli_stmt_init($this->resource);
  $result = mysqli_stmt_prepare($statement, $this->output);
  if ($result == FALSE) {
    $this->result = 'QUERYFAIL';
  return FALSE; }

  mysqli_stmt_execute($statement);
  $result = mysqli_stmt_get_result($statement);

  $row = mysqli_fetch_assoc($result);
  $statement->close();
  
  if ($row == FALSE) {
    $this->result = 'FETCHFAIL';
  return FALSE; }

  mysqli_free_result($result);
  
return $row; }

/**************************************************
 * Method: FetchColumn                            *
 **************************************************/

public function FetchColumn() {

  $statement = mysqli_stmt_init($this->resource);
  $result = mysqli_stmt_prepare($statement, $this->output);
  if ($result == FALSE) {
    $this->result = 'QUERYFAIL';
  return FALSE; }

  mysqli_stmt_execute($statement);
  $result = mysqli_stmt_get_result($statement);

  $column = FALSE;
  while ($row = mysqli_fetch_assoc($result))
    $column[] = $row;
  
  $statement->close();
  if ($column == FALSE) {
    $this->result = 'FETCHFAIL';
  return FALSE; }

  mysqli_free_result($result);
  
return $column; }

/**************************************************
 * Method: SelectRow                              *
 **************************************************/

public function SelectRow($id, $value) {

  $this->BeginSelect();
    $this->Where();
    $this->Assign($id, $value);
  $this->EndSelect();

  $row = $this->FetchRow();
  if ($row == FALSE) return FALSE;
  
return $row; }

/**************************************************
 * Method: SelectRowWith                          *
 **************************************************/

public function SelectRowWith($x_id, $x_value,
  $y_id, $y_value) {

  $this->BeginSelect();
    $this->Where();
    $this->Assign($x_id, $x_value);
    $this->With();
    $this->Assign($y_id, $y_value);
  $this->EndSelect();

  $row = $this->FetchRow();
  if ($row == FALSE) return FALSE;

return $row; }

/**************************************************
 * Method: SelectColumn                           *
 **************************************************/

public function SelectColumn($id, $value) {

  $this->BeginSelect();
    $this->Where();
    $this->Assign($id, $value);
  $this->EndSelect();

  $column = $this->FetchColumn();
  if ($column == FALSE) return FALSE;

return $column; }

/**************************************************
 * Method: DeleteRow                              *
 **************************************************/

public function DeleteRow($id) {

  $this->BeginDelete();
    $this->Where();
    $this->Assign('id', $id);
  $this->EndDelete();

  if ($this->Query() == FALSE)
    return FALSE;

return TRUE; }

/**************************************************
 * Method: DeleteAllRows                          *
 **************************************************/

public function DeleteAllRows($id, $value) {

  $this->BeginDelete();
    $this->Where();
    $this->Assign($id, $value);
  $this->EndDelete();

  if ($this->Query() == FALSE)
    return FALSE;

return TRUE; }

/**************************************************
 * Description: MySql Table Syntax Methods.       *
 **************************************************/

public function BeginTable() {
  $this->output = sprintf("CREATE TABLE %s (",
  $this->Prefix.$this->table);
return; }

public function Column($id, $type, $value) {
  $this->output.= sprintf("%s %s %s",
  $id, $type, $value);
return; }

public function PrimaryKey($id) {
  $this->output.= sprintf(" PRIMARY KEY(%s)", $id);
return; }

public function EndTable() {
  $this->output.= ") ENGINE = MyISAM;";
return; }

/**************************************************
 * Description: MySql Insert Syntax Methods.      *
 **************************************************/

public function BeginInsert() {
  $this->output = sprintf("INSERT INTO %s (",
  $this->Prefix.$this->table);
return; }

public function Value() {
  $this->output.= ") VALUE (";
return; }

public function EndInsert() {
  $this->output.= ");";
return; }

/**************************************************
 * Description: MySql Update Syntax Methods.      *
 **************************************************/

public function BeginUpdate() {
  $this->output = sprintf("UPDATE %s SET ",
  $this->Prefix.$this->table);
return; }

public function Assign($id, $value) {
  $this->output.= sprintf("%s = %s",
  $id, $value);
return; }

public function AssignString($id, $value) {
  $value = mysqli_real_escape_string($this->resource, $value);
  $this->output.= sprintf("%s = '%s'", $id, $value);
return; }

public function Next() {
  $this->output.= ", ";
return; }

public function EndUpdate() {
  $this->output.= ";";
return; }

/**************************************************
 * Description: MySql Select Syntax Methods.      *
 **************************************************/

public function BeginSelect() {
  $this->output = sprintf("SELECT * FROM %s ",
  $this->Prefix.$this->table);
return; }

public function With() {
  $this->output.= " AND ";
return; }

public function Where() {
  $this->output.= " WHERE ";
return; }

public function OrderBy($id, $type = 'ASC') {
  $this->output.= sprintf(" ORDER BY %s %s",
  $id, $type);
return; }

public function Limit($total) {
  $this->output.= " LIMIT ".$total;
return; }

public function Offset($start, $total) {
  $this->output.= sprintf(" LIMIT %s, %s",
  $start, $total);
return; }

public function EndSelect() {
  $this->output.= ";";
return; }

/**************************************************
 * Description: MySql Delete Syntax Methods.      *
 **************************************************/

public function BeginDelete() {
  $this->output = sprintf("DELETE FROM %s",
  $this->Prefix.$this->table);
return; }

public function EndDelete() {
  $this->output.= ";";
return; }

/**************************************************
 * Method: ValidateFile                           *
 **************************************************/

public function ValidateFile($id) {

  // put an empty conditional here
  $result = $_FILES[$id]['error'];

  if ($result == UPLOAD_ERR_NO_FILE) {
    $this->result = 'NOFILE';
    return FALSE; }
  if ($result == UPLOAD_ERR_FORM_SIZE) {
    $this->result = 'MAXSIZEFILE';
    return FALSE; }
  if ($result != UPLOAD_ERR_OK) {
    $this->result = 'UPLOADERROR';
  return FALSE; } 

return TRUE; } 

/**************************************************
 * Method: GetInput                               *
 **************************************************/

public function GetInput($id) {

  if (!empty($_POST[$id])) {
    $this->input[$id] = $_POST[$id];
  return TRUE; }

  else if (!empty($_GET[$id])) {
    $this->input[$id] = $_GET[$id];
  return TRUE; }

  else $this->input[$id] = NULL;

return FALSE; }

/**************************************************
 * Method: GetString64E                           *
 **************************************************/

public function GetString64E($id) {

  $uppercase = strtoupper($id);
  $uppercase = str_replace('_', '', $uppercase);

  if ($this->GetInput($id) == FALSE) {
    $this->input[$id] = '';
  return TRUE; }

  if (preg_match("/.{0,64}/",
    $this->input[$id]) == FALSE) {
    $this->result = 'COUNT'.$uppercase;
  return FALSE; }

  if (preg_match("/[&<>\'\"\\\]/",
    $this->input[$id]) == TRUE) {
    $this->result = 'ILLEGAL'.$uppercase;
  return FALSE; }

return TRUE; }

/**************************************************
 * Method: GetString64                            *
 **************************************************/

public function GetString64($id) {

  $uppercase = strtoupper($id);
  $uppercase = str_replace('_', '', $uppercase);

  if ($this->GetInput($id) == FALSE) {
    $this->result = 'EMPTY'.$uppercase;
  return FALSE; }

  if (preg_match("/.{0,64}/",
    $this->input[$id]) == FALSE) {
    $this->result = 'COUNT'.$uppercase;
  return FALSE; }

  if (preg_match("/[&<>\'\"\\/]/",
    $this->input[$id]) == TRUE) {
    $this->result = 'ILLEGAL'.$uppercase;
  return FALSE; }

return TRUE; }

/**************************************************
 * Method: GetInteger                             *
 **************************************************/

public function GetInteger($id) {

  $uppercase = strtoupper($id);
  $uppercase = str_replace('_', '', $uppercase);

  if ($this->GetInput($id) == FALSE) {
    $this->result = 'EMPTY'.$uppercase;
  return FALSE; }

  if (preg_match("/^[0-9]+$/",
    $this->input[$id]) == FALSE) {
    $this->result = 'NAN'.$uppercase;
  return FALSE; }

return TRUE; }

/**************************************************
 * Method: ValidateEmail                          *
 **************************************************/

public function ValidateEmail($email) {

  $pattern = "/^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]+$/";

  if (preg_match($pattern, $email) == FALSE) {
    $this->result = 'INVALIDEMAIL';
  return FALSE; }

return TRUE; }

/**************************************************
 * End Class                                      *
 **************************************************/
};

?>
