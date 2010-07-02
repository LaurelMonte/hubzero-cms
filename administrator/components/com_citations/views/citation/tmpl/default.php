<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$text = ( $this->task == 'edit' ? JText::_( 'EDIT' ) : JText::_( 'NEW' ) );

JToolBarHelper::title( JText::_( 'CITATION' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
JToolBarHelper::save();
JToolBarHelper::cancel();

$types = array('article','book','booklet','conference','inbook','incollection','inproceedings','magazine','manual','mastersthesis','misc','phdthesis','proceedings','techreport','unpublished','patent appl','chapter','notes','letter','manuscript');

jimport('joomla.html.editor');
$editor =& JEditor::getInstance();

?>
<script type="text/javascript" src="../includes/js/mootools.js"></script>
<script type="text/javascript" src="components/com_citations/citations.js"></script>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.getElementById('adminForm');
	
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	
	// form field validation
	if (form.title.value == '') {
		alert( '<?php echo JText::_('CITATION_MUST_HAVE_TITLE'); ?>' );
	} else {
		submitform( pressbutton );
	}
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="col width-70">
		<fieldset class="adminform">
			<legend><?php echo JText::_('DETAILS'); ?></legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="type"><?php echo JText::_('TYPE'); ?>:</label></td>
						<td colspan="3"><select name="citation[type]" id="type"><?php  
						for ($i=0, $n=count( $types ); $i < $n; $i++)
						{
							echo '<option value="'.$types[$i].'"';
							if ($this->row->type == $types[$i]) {
								echo ' selected="selected"';
							}
							echo '>'.$types[$i].'</option>';
						}
						?></select></td>
					</tr>
					<tr>
						<td class="key"><label for="cite"><?php echo JText::_('CITE_KEY'); ?>:</label></td>
						<td>
							<input type="text" name="citation[cite]" id="cite" size="30" maxlength="250" value="<?php echo $this->row->cite; ?>" />
							<br /><span style="font-size: 90%;color:#aaa;"><?php echo JText::_('CITE_KEY_EXPLANATION'); ?></span>
						</td>
						<td class="key"><label for="ref_type"><?php echo JText::_('REF_TYPE'); ?>:</label></td>
						<td><input type="text" name="citation[ref_type]" id="ref_type" size="11" maxlength="50" value="<?php echo $this->row->ref_type; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="date_submit"><?php echo JText::_('DATE_SUBMITTED'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[date_submit]" id="date_submit" size="30" maxlength="250" value="<?php echo $this->row->date_submit; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="date_accept"><?php echo JText::_('DATE_ACCEPTED'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[date_accept]" id="date_accept" size="30" maxlength="250" value="<?php echo $this->row->date_accept; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="date_publish"><?php echo JText::_('DATE_PUBLISHED'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[date_publish]" id="date_publish" size="30" maxlength="250" value="<?php echo $this->row->date_publish; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="year"><?php echo JText::_('YEAR'); ?>:</label></td>
						<td><input type="text" name="citation[year]" id="year" size="4" maxlength="4" value="<?php echo $this->row->year; ?>" /></td>
						<td class="key"><label for="month"><?php echo JText::_('MONTH'); ?>:</label></td>
						<td><input type="text" name="citation[month]" id="month" size="11" maxlength="50" value="<?php echo $this->row->month; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="author"><?php echo JText::_('AUTHORS'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[author]" id="author" size="30" value="<?php echo htmlentities($this->row->author,ENT_COMPAT,'UTF-8'); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="editor"><?php echo JText::_('EDITORS'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[editor]" id="editor" size="30" maxlength="250" value="<?php echo htmlentities($this->row->editor,ENT_COMPAT,'UTF-8'); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="title"><?php echo JText::_('TITLE_CHAPTER'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[title]" id="title" size="30" maxlength="250" value="<?php echo htmlentities($this->row->title,ENT_COMPAT,'UTF-8'); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="booktitle"><?php echo JText::_('BOOK_TITLE'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[booktitle]" id="booktitle" size="30" maxlength="250" value="<?php echo htmlentities($this->row->booktitle,ENT_COMPAT,'UTF-8'); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="journal"><?php echo JText::_('JOURNAL'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[journal]" id="journal" size="30" maxlength="250" value="<?php echo htmlentities($this->row->journal,ENT_COMPAT,'UTF-8'); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="volume"><?php echo JText::_('VOLUME'); ?>:</label></td>
						<td><input type="text" name="citation[volume]" id="volume" size="11" maxlength="11" value="<?php echo $this->row->volume; ?>" /></td>
						<td class="key"><label for="number"><?php echo JText::_('ISSUE'); ?>:</label></td>
						<td><input type="text" name="citation[number]" id="number" size="11" maxlength="50" value="<?php echo $this->row->number; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="pages"><?php echo JText::_('PAGES'); ?>:</label></td>
						<td><input type="text" name="citation[pages]" id="pages" size="11" maxlength="250" value="<?php echo $this->row->pages; ?>" /></td>
						<td class="key"><label for="isbn"><?php echo JText::_('ISBN'); ?>:</label></td>
						<td><input type="text" name="citation[isbn]" id="isbn" size="11" maxlength="50" value="<?php echo $this->row->isbn; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="doi"><?php echo JText::_('DOI'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[doi]" id="doi" size="30" maxlength="250" value="<?php echo $this->row->doi; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="series"><?php echo JText::_('SERIES'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[series]" id="series" size="30" maxlength="250" value="<?php echo $this->row->series; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="edition"><?php echo JText::_('EDITION'); ?>:</label></td>
						<td colspan="3">
							<input type="text" name="citation[edition]" id="edition" size="30" maxlength="250" value="<?php echo $this->row->edition; ?>" /> 
							<br /><span style="font-size: 90%;color:#aaa;"><?php echo JText::_('EDITION_EXPLANATION'); ?></span>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="school"><?php echo JText::_('SCHOOL'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[school]" id="school" size="30" maxlength="250" value="<?php echo $this->row->school; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="publisher"><?php echo JText::_('PUBLISHER'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[publisher]" id="publisher" size="30" maxlength="250" value="<?php echo $this->row->publisher; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="institution"><?php echo JText::_('INSTITUTION'); ?>:</label></td>
						<td colspan="3">
							<input type="text" name="citation[institution]" id="institution" size="30" maxlength="250" value="<?php echo $this->row->institution; ?>" /> 
							<br /><span style="font-size: 90%;color:#aaa;"><?php echo JText::_('INSTITUTION_EXPLANATION'); ?></span>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="address"><?php echo JText::_('ADDRESS'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[address]" id="address" size="30" maxlength="250" value="<?php echo $this->row->address; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="location"><?php echo JText::_('LOCATION'); ?>:</label></td>
						<td colspan="3">
							<input type="text" name="citation[location]" id="location" size="30" maxlength="250" value="<?php echo $this->row->location; ?>" /> 
			   				<span style="font-size: 90%;color:#aaa;"><?php echo JText::_('LOCATION_EXPLANATION'); ?></span>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="howpublished"><?php echo JText::_('PUBLISH_METHOD'); ?>:</label></td>
						<td colspan="3">
							<input type="text" name="citation[howpublished]" id="howpublished" size="30" maxlength="250" value="<?php echo $this->row->howpublished; ?>" /> 
							<br /><span style="font-size: 90%;color:#aaa;"><?php echo JText::_('PUBLISH_METHOD_EXPLANATION'); ?></span>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="url"><?php echo JText::_('URL'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[url]" id="url" size="30" maxlength="250" value="<?php echo $this->row->url; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="eprint"><?php echo JText::_('EPRINT'); ?>:</label></td>
						<td colspan="3">
							<input type="text" name="citation[eprint]" id="eprint" size="30" maxlength="250" value="<?php echo $this->row->eprint; ?>" />
							<br /><span style="font-size: 90%;color:#aaa;"><?php echo JText::_('EPRINT_EXPLANATION'); ?></span>
						</td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('NOTES'); ?>:</td>
						<td colspan="3">
							<?php echo $editor->display('citation[note]', stripslashes($this->row->note), '360px', '200px', '50', '10'); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-30">
		<fieldset class="adminform">
			<legend><?php echo JText::_('CITATION_FOR'); ?></legend>
			
			<table class="admintable" id="assocs">
				<thead>
					<tr>
						<th><?php echo JText::_('ID'); ?></th>
						<th><?php echo JText::_('TYPE'); ?></th>
						<th><?php echo JText::_('TABLE'); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="3"><a href="#" onclick="Citations.addRow('assocs');return false;"><?php echo JText::_('ADD_A_ROW'); ?></a></td>
					</tr>
				</tfoot>
				<tbody>
				<?php
						$assocs = $this->assocs;
						$r = count($assocs);
						if ($r > 5) {
							$n = $r;
						} else {
							$n = 5;
						}
						for ($i=0; $i < $n; $i++) 
						{
							if ($r == 0 || !isset($assocs[$i])) {
								$assocs[$i] = new stdClass;
								$assocs[$i]->id = NULL;
								$assocs[$i]->cid = NULL;
								$assocs[$i]->oid = NULL;
								$assocs[$i]->type = NULL;
								$assocs[$i]->table = NULL;
							}
							echo '  <tr>'."\n";
							echo '   <td><input type="text" name="assocs['.$i.'][oid]" value="'.$assocs[$i]->oid.'" size="5" /></td>'."\n";
							echo '   <td><input type="text" name="assocs['.$i.'][type]" value="'.$assocs[$i]->type.'" size="5" /></td>'."\n";
							echo '   <td><select name="assocs['.$i.'][table]">'."\n";
							echo ' <option value=""';
							echo ($assocs[$i]->table == '') ? ' selected="selected"': '';
							echo '>'.JText::_('SELECT').'</option>'."\n";
							echo ' <option value="content"';
							echo ($assocs[$i]->table == 'content') ? ' selected="selected"': '';
							echo '>'.JText::_('CONTENT').'</option>'."\n";
							echo ' <option value="resource"';
							echo ($assocs[$i]->table == 'resource') ? ' selected="selected"': '';
							echo '>'.JText::_('RESOURCE').'</option>'."\n";
							echo ' <option value="topic"';
							echo ($assocs[$i]->table == 'topic') ? ' selected="selected"': '';
							echo '>'.JText::_('TOPIC').'</option>'."\n";
							echo '</select>'."\n";
							echo '<input type="hidden" name="assocs['.$i.'][id]" value="'.$assocs[$i]->id.'" />'."\n";
							echo '<input type="hidden" name="assocs['.$i.'][cid]" value="'.$assocs[$i]->cid.'" /></td>'."\n";
							echo '  </tr>'."\n";
						}
				?>
				</tbody>
			</table>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('AFFILIATION'); ?></legend>
			
			<table class="adminform">
				<tbody>
					<tr>
						<td>
							<label>
								<input type="checkbox" name="citation[affiliated]" id="affiliated" value="1"<?php if ($this->row->affiliated) { echo ' checked="checked"'; } ?> />
								<?php echo JText::_('AFFILIATED_WITH_YOUR_ORG'); ?>
							</label>
						</td>
					</tr>
					<tr>
						<td>
							<label>
								<input type="checkbox" name="citation[fundedby]" id="fundedby" value="1"<?php if ($this->row->fundedby) { echo ' checked="checked"'; } ?> />
								<?php echo JText::_('FUNDED_BY_YOUR_ORG'); ?>
							</label>
						</td>
					</tr>
				</tbody>
			</table>
			
			<input type="hidden" name="citation[uid]" value="<?php echo $this->row->uid; ?>" />
			<input type="hidden" name="citation[created]" value="<?php echo $this->row->created; ?>" />
			<input type="hidden" name="citation[id]" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="save" />
		</fieldset>
	</div>
	<div class="clr"></div>
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>