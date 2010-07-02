<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_('HUB Configuration').': '.JText::_('Registration'), 'addedit.png' );
JToolBarHelper::preferences('com_hub', '550');
JToolBarHelper::save('savereg');
JToolBarHelper::cancel();

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm">
	<fieldset class="adminform">
		<table class="admintable">
			<thead>
				<tr>
					<th>Field/Area</th>
					<th>Create</th>
					<th>Proxy</th>
					<th>Update</th>
					<th>Edit</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
<?php
foreach ($this->a as $field => $value) 
{
	if (substr($field, 0, strlen('registration')) == 'registration') {
		$create = strtoupper(substr($value, 0, 1));
		$proxy  = strtoupper(substr($value, 1, 1));
		$update = strtoupper(substr($value, 2, 1));
		$edit   = strtoupper(substr($value, 3, 1));

		$field = str_replace('registration', '', $field);
?>
<tr>
	<td class="key"><?php echo $field; ?></td>
	<td>
		<select name="settings[<?php echo $field; ?>][create]">
			<option value="O"<?php if ($create == 'O') { echo ' selected="selected"'; }?>>Optional</option>
			<option value="R"<?php if ($create == 'R') { echo ' selected="selected"'; }?>>Required</option>
			<option value="H"<?php if ($create == 'H') { echo ' selected="selected"'; }?>>Hide</option>
			<option value="U"<?php if ($create == 'U') { echo ' selected="selected"'; }?>>Read only</option>
		</select>
	</td>
	<td>
		<select name="settings[<?php echo $field; ?>][proxy]">
			<option value="O"<?php if ($proxy == 'O') { echo ' selected="selected"'; }?>>Optional</option>
			<option value="R"<?php if ($proxy == 'R') { echo ' selected="selected"'; }?>>Required</option>
			<option value="H"<?php if ($proxy == 'H') { echo ' selected="selected"'; }?>>Hide</option>
			<option value="U"<?php if ($proxy == 'U') { echo ' selected="selected"'; }?>>Read only</option>
		</select>
	</td>
	<td>
		<select name="settings[<?php echo $field; ?>][update]">
			<option value="O"<?php if ($update == 'O') { echo ' selected="selected"'; }?>>Optional</option>
			<option value="R"<?php if ($update == 'R') { echo ' selected="selected"'; }?>>Required</option>
			<option value="H"<?php if ($update == 'H') { echo ' selected="selected"'; }?>>Hide</option>
			<option value="U"<?php if ($update == 'U') { echo ' selected="selected"'; }?>>Read only</option>
		</select>
	</td>
	<td>
		<select name="settings[<?php echo $field; ?>][edit]">
			<option value="O"<?php if ($edit == 'O') { echo ' selected="selected"'; }?>>Optional</option>
			<option value="R"<?php if ($edit == 'R') { echo ' selected="selected"'; }?>>Required</option>
			<option value="H"<?php if ($edit == 'H') { echo ' selected="selected"'; }?>>Hide</option>
			<option value="U"<?php if ($edit == 'U') { echo ' selected="selected"'; }?>>Read only</option>
		</select>
	</td>
	<td>
		<?php
		if ($field == 'Organization') {
			echo '<a href="index.php?option=com_hub&amp;task=orgs">Edit Organization List</a>';
		} else {
			echo '&nbsp;';
		}
		?>
	</td>
</tr>
<?php
	}	
}
?>
			</tbody>
		</table>
		<input type="hidden" name="option" value="com_hub" />
		<input type="hidden" name="task" value="savereg" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</fieldset>
</form>