<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$text = ( $this->task == 'editfaq' ? JText::_( 'EDIT' ) : JText::_( 'NEW' ) );

JToolBarHelper::title( JText::_('KNOWLEDGE_BASE').': '.JText::_('ARTICLE').': <small><small>[ '. $text.' ]</small></small>', 'addedit.png' );
JToolBarHelper::save( 'savefaq', JText::_('SAVE') );
JToolBarHelper::cancel();

$mod_date = NULL;
$create_date = NULL;
if (intval( $this->row->modified ) <> 0) {
	$mod_date = JHTML::_('date',$this->row->modified, '%Y-%m-%d');
}
if (intval( $this->row->created ) <> 0) {
	$create_date = JHTML::_('date',$this->row->created, '%Y-%m-%d');
}

jimport('joomla.html.editor');
$editor =& JEditor::getInstance();
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;

	if (pressbutton =='resethits') {
		if (confirm( <?php echo JText::_('RESET_HITS_WARNING'); ?> )){
			submitform( pressbutton );
			return;
		} else {
			return;
		}
	}

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	// do field validation
<?php if ( !$this->cid && $this->row->category == 1 ) { ?>
	if (form.title.value == ''){
		alert( <?php echo JText::_('ERROR_MISSING_TITLE'); ?> );
<?php } else { ?>
	if (form.title.value == ''){
		alert( <?php echo JText::_('ERROR_MISSING_QUESTION'); ?> );
	} else if (form.fulltext.value == ''){
		alert( <?php echo JText::_('ERROR_MISSING_ANSWER'); ?> );
<?php } ?>
	} else {
		submitform( pressbutton );
	}
}
</script>

<form action="index.php" method="post" name="adminForm">
	<div class="col width-60">
		<fieldset class="adminform">
			<legend><?php echo JText::_('DETAILS'); ?></legend>
		
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label><?php echo JText::_('ALIAS'); ?>:</label></td>
						<td><input type="text" name="alias" size="30" maxlength="100" value="<?php echo stripslashes($this->row->alias); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('CATEGORY'); ?>: *</label></td>
						<td><?php echo KbHtml::sectionSelect( $this->sections, $this->row->section, 'section' ); ?></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('SUB_CATEGORY'); ?>:</label></td>
						<td><?php echo KbHtml::sectionSelect( $this->categories, $this->row->category, 'category' ); ?></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('QUESTION'); ?>: *</label></td>
						<td><input type="text" name="title" size="30" maxlength="100" value="<?php echo stripslashes($this->row->title); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('LONG_QUESTION'); ?>:</label></td>
						<td><?php
						echo $editor->display('introtext', stripslashes($this->row->introtext), '360px', '200px', '50', '10');
						?></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('ANSWER'); ?>: *</label></td>
						<td><?php
						echo $editor->display('fulltext', stripslashes($this->row->fulltext), '360px', '200px', '50', '10');
						?></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40">
		<fieldset class="adminform">
			<legend><?php echo JText::_('PARAMETERS'); ?></legend>
			
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="state"><?php echo JText::_('PUBLISHED'); ?>:</label></td>
						<td><input type="checkbox" name="state" value="1" <?php echo $this->row->state ? 'checked="checked"' : ''; ?> /></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('ACCESS_LEVEL'); ?>:</label></td>
						<td><?php echo JHTML::_('list.accesslevel', $this->row); ?></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('CHANGE_CREATOR'); ?>:</label></td>
						<td><?php echo JHTML::_('list.users', 'created_by', $this->row->created_by, 0, '', 'name', 1); ?></td>
					</tr>
					<tr>
						<td class="key"><label for="created"><?php echo JText::_('CREATED'); ?>:</label></td>
						<td><input type="text" name="created" id="created" size="25" maxlength="19" value="<?php echo $this->row->created; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('STATE'); ?>:</td>
						<td><?php echo ($this->row->state == 1) ? JText::_('PUBLISHED') : JText::_('UNPUBLISHED'); ?></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('HITS'); ?>:</td>
						<td><?php echo $this->row->hits; ?>
						<?php if ( $this->row->hits ) { ?>
						<input type="button" name="reset_hits" id="reset_hits" value="<?php echo JText::_('RESET_HITS'); ?>" onclick="submitbutton('resethits');" />
						<?php } ?>
						</td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('HELPFUL'); ?>:</td>
						<td>+<?php echo $this->row->helpful; ?> -<?php echo $this->row->nothelpful; ?>
						<?php if ( $this->row->helpful > 0 || $this->row->nothelpful > 0 ) { ?>
						<input type="button" name="reset_helpful" value="<?php echo JText::_('RESET_HELPFUL'); ?>" onclick="submitbutton('resethelpful');" />
						<?php } ?>
						</td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('CREATED'); ?>:</td>
						<td><?php echo ($this->row->created != '0000-00-00 00:00:00') ? $create_date.'</td></tr><tr><td class="key">'.JText::_('BY').':</td><td>'.$this->row->created_by : JText::_('NEW'); ?></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('LAST_MODIFIED'); ?>:</td>
						<td><?php echo ($this->row->modified != '0000-00-00 00:00:00') ? $mod_date.'</td></tr><tr><td class="key">'.JText::_('BY').':</td><td>'.$this->row->modified_by : JText::_('NOT_MODIFIED');?></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>
	
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="savefaq" />
</form>