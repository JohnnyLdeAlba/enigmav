function HTMLInterface() {

  this.output = '';

  this.SetOutput = function(output) {
    this.output = output;
  return; }

  this.GetOutput = function() {
  return this.output; } 

  this.Display = function() {
    document.write(this.output);
  return; } 

  this.Add = function(output) {
    this.output+= output;
  return; }

  this.Assign = function(id, value) {
    this.output+= id+"'"+value+"'";
  return; }

  this.BeginTag = function(id) {
    this.output+= '<'+id;
  return; }

  this.EndTag = function() {
    this.output+= ">\n";
  return; }

return; }

/**************************************************
 * Function: GrowTextArea                         *
 **************************************************/

function GrowTextArea(id) {

  var textarea = document.getElementById(id);
  var total = 1;

  function UpdateTextArea() {

    var line = textarea.value.split('\n');
    textarea.rows = line.length;

  return; }

  textarea.onkeyup = function() { UpdateTextArea(); return; }

return; }

/**************************************************
 * Function: ToggleDisplay                        *
 **************************************************/

function ToggleDisplay(id) {

  var element = document.getElementById(id);
  if (element.style.display == 'none') {
    element.style.display = 'block';
  return; }

  element.style.display = 'none';
return; }

/**************************************************
 * Interface: Selectable                          *
 **************************************************/

function Selectable(id) {

  var selectpanel = document.getElementById(id);
  var selectin = selectpanel.getElementsByTagName('input')[0];

  var selectfield = null;
  var selectable = null;

  var result = selectpanel.childNodes;
  for (var count = 0; count < result.length; count++) {

  if (result[count].className == 'selectfield')
    selectfield = result[count];
  if (result[count].className == 'selectable')
    selectable = result[count]; }

  var selected = selectfield.getElementsByTagName('span')[0];
  var selectitem = selectable.getElementsByTagName('div');

  /**************************************************
   * Local: ToggleDisplay                           *
   **************************************************/
  function ToggleDisplay() {

    if (selectable.style.display == 'none') {
      selectable.style.display = 'block';
    return; }

    selectable.style.display = 'none';
  return; }

  /**************************************************
   * Local: SetCaption                              *
   **************************************************/
  function SetCaption(selectitem) {

    selected.innerHTML = selectitem.innerHTML;
    selectin.value = selectitem.getAttribute('value');
    ToggleDisplay();

  return; }

  selectfield.onmousedown = function() {
    ToggleDisplay();
  return; }

  for (var count = 0; count < selectitem.length; count++) {

    selectitem[count].onmouseover = function() {
      this.style.backgroundColor = '#cccccc';
    return; }

    selectitem[count].onmouseout = function() {
      this.style.backgroundColor = '#eeeeee';
    return; } 

    selectitem[count].onclick = function() {
      SetCaption(this);
    return; }}

return; }

/**************************************************
 * Function: AttachSelect                           *
 **************************************************/

function AttachSelect() {

  var application = document.getElementById('application');
  var submit = document.getElementById('submit');
  
  var select_attachment = document.getElementById('select_attachment');
  var remove_attachment = document.getElementById('remove_attachment');

  var select_image = document.getElementById('select_image');
  var select_video = document.getElementById('select_video');
  
  var selected_image = document.getElementById('selected_image');
  var selected_video = document.getElementById('selected_video');
  
  var attachment = document.getElementsByName('pattachment')[0];
  var filename_image = document.getElementById('filename_image');

  /**************************************************
   * Local: TrimFilename                            *
   **************************************************/
  function TrimFilename(filename) {

    result = '...'+filename.substring(
    filename.length-16, filename.length);

  return result; }

  submit.onclick = function() {
	application.submit();
  return false; }
  
  select_image.onclick = function() {
    attachment.click();
  return false; }

  select_video.onclick = function() {
    select_attachment.style.display = 'none';
    remove_attachment.style.display = 'block';
	
	selected_image.style.display = 'none';
	selected_video.style.display = 'block';
  return false; }
  
  attachment.onchange = function() {
    select_attachment.style.display = 'none';
    remove_attachment.style.display = 'block';
	
	selected_image.style.display = 'block';
	selected_video.style.display = 'none';

    filename_image.innerHTML = TrimFilename(this.value);
  return false; }

  remove_attachment.onclick = function() {
    select_attachment.style.display = 'block';
    remove_attachment.style.display = 'none';
	
	selected_image.style.display = 'none';
	selected_video.style.display = 'none';
	
	attachment.value = '';
    filename_image.innerHTML = '';
  return false; }

return; }
