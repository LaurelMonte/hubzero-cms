<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );


JToolBarHelper::title( '<a href="index.php?option='.$this->option.'">'.JText::_( 'Answers Manager' ).'</a>', 'addedit.png' );
JToolBarHelper::preferences($this->option, '550');
JToolBarHelper::spacer();
JToolBarHelper::addNew( 'newq', 'New Question');
JToolBarHelper::deleteList( '', 'remove', 'Delete Question' );

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm">
	<fieldset id="filter">
		<label>
			<?php echo JText::_('Filter by:'); ?>
			<select name="filterby" onchange="document.adminForm.submit( );">
				<option value="open"<?php if ($this->filters['filterby'] == 'open') { echo ' selected="selected"'; } ?>>Open Questions</option>
				<option value="closed"<?php if ($this->filters['filterby'] == 'closed') { echo ' selected="selected"'; } ?>>Closed Questions</option>
				<option value="all"<?php if ($this->filters['filterby'] == 'all') { echo ' selected="selected"'; } ?>>All Questions</option>
			</select>
		</label> 

		<label>
			<?php echo JText::_('Sort by:'); ?>
			<select name="sortby" onchange="document.adminForm.submit( );">
				<option value="rewards"<?php if ($this->filters['sortby'] == 'rewards') { echo ' selected="selected"'; } ?>>Rewards</option>
				<option value="votes"<?php if ($this->filters['sortby'] == 'votes') { echo ' selected="selected"'; } ?>>Recommendations</option>
                <option value="date"<?php if ($this->filters['sortby'] == 'date') { echo ' selected="selected"'; } ?>>Date</option>
			</select>
		</label> 
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->results );?>);" /></th>
				<th><?php echo JText::_('ID'); ?></th>
                <th><?php echo JText::_('Subject'); ?></th>
				<th><?php echo JText::_('State'); ?></th>
				<th><?php echo JText::_('Created'); ?></th>
				<th><?php echo JText::_('Created by'); ?></th>
				<th><?php echo JText::_('Answers'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count( $this->results ); $i < $n; $i++) 
{
	$row =& $this->results[$i];
	switch ($row->state) 
	{
		case '1':
			$task = 'open';
			$img = 'publish_x.png';
			$alt = JText::_( 'Closed' );
			break;
		case '0':
			$task = 'close';
			$img = 'publish_g.png';
			$alt = JText::_( 'Open' );
			break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
            	<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" /></td>
				<td><?php echo $row->id ?></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=editq&amp;id[]=<?php echo $row->id; ?>" title="Edit this question"><?php echo stripslashes($row->subject); ?></a></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=<?php echo $task;?>&amp;id[]=<?php echo $row->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="Set this to <?php echo $task;?>"><img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" /></span></a></td>
				<td><?php echo JHTML::_('date', $row->created, '%d %b, %Y') ?></td>
				<td><?php echo $row->created_by; if ($row->anonymous) { echo ' (anon)'; } ?></td>
<?php if ($row->answers > 0) { ?>
				<td><a href="index.php?option=<?php echo $option ?>&amp;task=answers&amp;qid=<? echo $row->id; ?>" title="View the answers for this Question"><?php echo $row->answers; ?> response<?php if ($row->answers != 1) { echo 's'; } ?></a></td>
<?php } else { ?>
				<td><?php echo $row->answers; ?></td>
<?php } ?>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>

<p>State: (click icon above to toggle state)</p>
<ul class="key">
	<li class="published"><img src="images/publish_g.png" width="16" height="16" border="0" alt="Open" /> = Open Question</li>
	<li class="unpublished"><img src="images/publish_x.png" width="16" height="16" border="0" alt="Closed" /> = Closed Question</li>
</ul>
