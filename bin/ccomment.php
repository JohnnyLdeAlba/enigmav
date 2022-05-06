<?php

/**************************************************
 * Application: EnigmaV                           *         
 * Module: CComment                               *
 * Author: Johnny L. de Alba                      *
 * Date: 08/25/2013                               *
 **************************************************/

class CComment {

public $result;
public $message;

/**************************************************
 * Component: user_image_avatar                   *
 **************************************************/

public function user_image_avatar($id, $user_avatar) {

  global $out;

  if ($user_avatar == '') {
    $out->SetVariable($id.'.avatar', 'default/128/default.png');
  return; }

  $out->SetVariable($id.'.avatar', 'usr/av/'.$user_avatar);

return; }

/**************************************************
 * Component: umessage_object_pattachment         *
 **************************************************/

public function comment_object_pattachment($comment) {

  global $out;

  if ($comment['type'] == 'IMG') {
    $out->SetClass('comment.utube', '');
    $out->ShowClass('comment.image');
    $out->SetVariable('comment.pattachment', $comment['pattachment']);
  return; }

  else if ($comment['type'] == 'UTB') {
    $out->SetClass('comment.image', '');
    $out->ShowClass('comment.utube');
    $out->SetVariable('comment.pattachment', $comment['pattachment']);
  return; }

  $out->SetClass('comment.image', '');
  $out->SetClass('comment.utube', '');

return; }

/**************************************************
 * Component: comment_user_admin                 *
 **************************************************/

public function comment_user_admin($comment) {

  global $usr;

  if ($usr->client == NULL) return FALSE;
  if ($usr->client['id'] == $comment['user_id']) return TRUE;
  if ($usr->client['type'] == 'ADN') return TRUE;

return FALSE; }

/**************************************************
 * Component: comment_panel_row                  *
 **************************************************/

public function comment_panel_row($comment) {

  global $usr, $in, $out;

  if ($this->comment_user_admin($comment) == TRUE)
    $out->ShowClass('comment.auth');
  else $out->SetClass('comment.auth', '');

  $this->user_image_avatar('comment', $comment['user_avatar']);
  $this->comment_object_pattachment($comment);

  $out->SetVariable('comment.uid', $comment['user_id']);
  $out->SetVariable('comment.fullname', $comment['user_fullname']);
  $out->SetVariable('comment.id', $comment['id']);
  $out->SetVariable('comment.created', $out->get_date($comment['created']));
  $out->SetVariable('comment.rating', $comment['rating']);

  $out->SetVariable('comment.data',
  $out->PrepForDisplay($comment['data']));

return; }

/**************************************************
 * Component: comment_panel_table                *
 **************************************************/

public function comment_panel_table($column) {

  global $out, $in, $umsg;

  if ($column == FALSE) {
    $out->ShowClass('nocomment');
    $out->SetClass('comment', '');
  return; }

  $layout = $out->GetClass('comment');
  $output = $out->GetOutput();

  $value = '';
  $total = count($column);

  for ($count = 0; $count < $total; $count++) {

    $out->SetOutput($layout);
    $this->comment_panel_row($column[$count]);

  $value.= $out->GetOutput(); }

  $out->SetOutput($output);
  $out->SetClass('comment', $value);
  $out->SetClass('nocomment', '');

return; }

/**************************************************
 * Component: IncrementRating                     *
 **************************************************/

public function IncrementRating() {

  global $in, $usr, $ses, $lcounter, $lcomment;

  if ($usr->client == NULL) return FALSE; 
  if ($ses->ValidateSeed($usr->client['id']) == FALSE) return FALSE;

  if ($in->GetInput('mid') == FALSE) return FALSE;
  if ($in->ValidateInt('mid') == FALSE) return FALSE;

  $comment = $lcomment->Get($in->input['mid']);
  if ($comment == FALSE) return FALSE;

  $lcounter->table = 'comment_rating';
  if ($lcounter->SaveCounter($comment['id'],
    $usr->client['id'], 1) == FALSE)
  return FALSE;

  $comment['rating']++;
  $lcomment->Modify($comment);

return TRUE; }

/**************************************************
 * Component: DecrementRating                     *
 **************************************************/

public function DecrementRating() {

  global $in, $usr, $ses, $lcounter, $lcomment;

  if ($usr->client == NULL) return FALSE;
  if ($ses->ValidateSeed($usr->client['id']) == FALSE) return FALSE;

  if ($in->GetInput('mid') == FALSE) return FALSE;
  if ($in->ValidateInt('mid') == FALSE) return FALSE;

  $comment = $lcomment->Get($in->input['mid']);
  if ($comment == FALSE) return FALSE;
  if ($comment['rating'] == 0) return FALSE;

  $lcounter->table = 'comment_rating';
  if ($lcounter->SaveCounter($comment['id'],
    $usr->client['id'], 0) == FALSE)
  return FALSE;

  $comment['rating']--;
  $lcomment->Modify($comment);

return TRUE; }

/**************************************************
 * End Class                                      *
 **************************************************/
};

$ccomment = new CComment();
