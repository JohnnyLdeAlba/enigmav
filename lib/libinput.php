<?php

/**************************************************
 * Application: EnigmaV                           *
 * Library: LibInput                              *
 * Author: Johnny L. de Alba                      *
 * Date: 02/21/2011                               *
 **************************************************/

class LibInput {

public $result;
public $input;

/**************************************************
 * Method: Create                                 *
 **************************************************/

public function Create() {

  $this->result = '';
  $this->input = array();

return TRUE; }

public function PrepForStorage($output) {

  $output = trim($output);
  $output = preg_replace("/(\r\n|\n|\r)/", "\r", $output);
  $output = htmlentities($output, ENT_QUOTES, "UTF-8");

return $output; }

public function PrepForDisplay($output) {

  $output = htmlentities($output);
  $output = nl2br($output);

return $output; }

/**************************************************
 * Method: GetInput                               *
 **************************************************/

public function GetInput($id) {

  if (empty($_POST[$id]) == FALSE) {
    $this->input[$id] = $_POST[$id];
  return TRUE; }

  if (empty($_GET[$id]) == FALSE) {
    $this->input[$id] = $_GET[$id];
  return TRUE; }

  $this->input[$id] = NULL;
  $this->result = 'EMPTYVAR';

return FALSE; }

/**************************************************
 * Method: IllegalCharacters                      *
 **************************************************/

public function IllegalCharacters($id) {

  if (preg_match("/[<>&]+/", $this->input[$id]) == TRUE) {
    $this->result = 'ILLEGALCHARS';
  return TRUE; }

return FALSE; }

/**************************************************
 * Method: CharacterCount                         *
 **************************************************/

public function CharacterCount($id, $start, $stop) {

  $pattern = sprintf("/^.{%s,%s}$/", $start, $stop);

  if (preg_match($pattern,
    $this->input[$id]) == FALSE) {
    $this->result = 'CHARCOUNT';
  return FALSE; }

return TRUE; }

/**************************************************
 * Method: ValidateInt                            *
 **************************************************/

public function ValidateInt($id) {

  if ($this->input[$id] === 0)
    return TRUE;

  if (preg_match("/^[0-9]+$/",
    $this->input[$id]) == FALSE) {
    $this->result = 'INVALIDINT';
  return FALSE; }

return TRUE; }

/**************************************************
 * Method: YouTubeCode                            *
 **************************************************/

public function YouTubeCode($id) {

  $pattern = '/v=([a-zA-Z0-9\-_]+)/';

  if (preg_match($pattern, $this->input[$id],
    $result) == FALSE) {
    $this->result = 'INVALIDUTUBE';
  return FALSE; }

  $this->input[$id] = $result[1];

return TRUE; }

/**************************************************
 * Method: ValidateEmail                          *
 **************************************************/

public function ValidateEmail($id) {

  $pattern = "/^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]+$/";

  if (preg_match($pattern,
    $this->input[$id]) == FALSE) {
    $this->result = 'INVALIDEMAIL';
  return FALSE; }

return TRUE; }

/**************************************************
 * Component: ReadPermission                      *
 **************************************************/

public function ReadPermission($permissions, $subscriber) {

  $read = substr($permissions, 0, 1);

  if ($read == 'P') return FALSE;
  if ($subscriber == NULL)
    if ($read == 'C') return FALSE;

return TRUE; }

/**************************************************
 * Component: InputPermissions                    *
 **************************************************/

public function InputPermissions() {

  global $in;

  $in->GetInput('read');
  $in->GetInput('write');

  switch ($in->input['read']) {
    case 'C': case 'P': break;
    default: $in->input['read'] = 'A';
  break; }

  switch ($in->input['write']) {
    case 'L': break;
    default: $in->input['write'] = 'U';
  break; }

  $in->input['permissions'] =
  $in->input['read'].$in->input['write'];

return TRUE; }

/**************************************************
 * Method: ValidateFile                           *
 **************************************************/

public function ValidateFile($id) {

  if (empty($_FILES[$id]) == TRUE) {
    $this->result = 'NOFILE';
  return FALSE; }

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
 * Method: GetImageType                           *
 **************************************************/

public function GetImageType($type) {

  $result = FALSE;

  switch ($type) {
    case IMAGETYPE_GIF: $result = 'gif';
    break; case IMAGETYPE_JPEG: $result = 'jpg';
    break; case IMAGETYPE_PNG: $result = 'png';
  break; }

  if ($result == FALSE) {
    $this->result = 'INVALIDIMAGETYPE';
  return FALSE; }

return $result; }

/**************************************************
 * Method: GetImage                               *
 **************************************************/

public function GetImage($id) {

  if ($this->ValidateFile($id) == FALSE)
    return FALSE;

  $temporary = $_FILES[$id]['tmp_name'];
  $data = getimagesize($temporary);

  if ($data == FALSE) {
    $this->result = 'UNREADFILE';
  return FALSE; }

  $type = $this->GetImageType($data[2]);
  if ($type == FALSE) return FALSE;

return array('filename' => $temporary, 'type' => $type,
  'width' => $data[0], 'height' => $data[1]); }

/**************************************************
 * Method: FixedDimensions                        *
 **************************************************/

public function FixedDimensions($image, $width, $height) {

  if ($image['width'] != $width) {
    $this->result = 'IMAGEWIDTH';
  return FALSE; }

  if ($image['height'] != $height) {
    $this->result = 'IMAGEHEIGHT';
  return FALSE; }

return TRUE; }

/**************************************************
 * Method: SaveImage                              *
 **************************************************/

public function SaveImage($image, $destination) {

  if (copy($image['filename'], $destination) == FALSE) {
    $this->result = 'FILECOPYFAIL';
  return FALSE; }

return TRUE; }

/**************************************************
 * Method: CreateSurfaceFromImage                 *
 **************************************************/

public function CreateSurfaceFromImage($image) {

  switch ($image['type']) {

  case 'gif': $surface = imagecreatefromgif(
    $image['filename']); break;
  case 'jpg': $surface = imagecreatefromjpeg(
    $image['filename']); break;
  case 'png': $surface = imagecreatefrompng(
    $image['filename']); }

return $surface; }

/**************************************************
 * Method: CreateThumbFromImage                   *
 **************************************************/

public function CreateThumbFromImage($image,
  $width, $destination) {

  $height = $width/1.33;
  
  $ratio = $image['width']/$width;
  $resize_width = $width;
  $resize_height = $image['height']/$ratio;
  
  $source = $this->CreateSurfaceFromImage($image);
  if ($resize_height < 1) $resize_height = 1;
  
  $result = imagecreatetruecolor($resize_width, $resize_height);
  imagecopyresized($result, $source, 0, 0, 0, 0,
    $resize_width, $resize_height, $image['width'], $image['height']);
  
  imagedestroy($source);
  $source = $result;

  if (imagepng($source, $destination) == FALSE) {
    $this->result = 'IMAGERESIZEFAIL';
  return FALSE; }

  imagedestroy($source);

return TRUE; }

/**************************************************
 * Method: CreateImagePreview                     *
 **************************************************/

public function CreateImagePreview($image,
  $width, $destination) {

  $height = $width/1.33;
  
  if ($image['width'] > $image['height']) {
  
    $ratio = $image['width']/$width;
    $resize_width = $width;
    $resize_height = $image['height']/$ratio; }
	
  else {
  
    $ratio = $image['height']/$height;
    $resize_height = $height;
    $resize_width = $image['width']/$ratio; }
	
  $source = $this->CreateSurfaceFromImage($image);

  if ($resize_width < 1) $resize_width = 1;
  if ($resize_height < 1) $resize_height = 1;
  
  $result = imagecreatetruecolor($resize_width, $resize_height);
  imagecopyresized($result, $source, 0, 0, 0, 0,
    $resize_width, $resize_height, $image['width'], $image['height']);
  
  imagedestroy($source);
  $source = $result;
  
  $x = 0; $y = 0;
  
  if ($resize_width < $width) $x = ($width/2)-($resize_width/2);
  if ($resize_height < $height) $y = ($height/2)-($resize_height/2);
  
  $target = imagecreatetruecolor($width, $height);
  $color = imagecolorallocate($target, 255, 255, 255);
  
  imagefilledrectangle($target, 0, 0, $width, $height, $color);
  imagecopy($target, $source, $x, $y, 0, 0, $resize_width, $resize_height);

  if (imagepng($target, $destination) == FALSE) {
    $this->result = 'IMAGERESIZEFAIL';
  return FALSE; }

  imagedestroy($source);
  imagedestroy($target);
  
return TRUE; }

/**************************************************
 * End Class                                      *
 **************************************************/
}
