<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'Support' ).': <small><small>[ '.JText::_( 'REPORT_ABUSE' ).' ]</small></small>', 'addedit.png' );
JToolBarHelper::save();
JToolBarHelper::cancel();

$reporter =& JUser::getInstance($this->report->created_by);

$link = '';

if (is_object($this->reported)) {
	$author =& JUser::getInstance($this->reported->author);
	
	if (is_object($author) && $author->get('username')) {
		$this->title .= ' by '.$author->get('username'); 
	} else {
		$this->title .= ' by '.JText::_('UNKNOWN'); 
	}
	$this->title .= ($this->reported->anon) ? '('.JText::_('ANONYMOUS').')':'';
	
	$link = '../'.$this->reported->href;
}

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
	<table class="adminlist">
		<thead>
			<tr>
				<td colspan="2"><?php echo JText::_('ITEM_REPORTED_AS_ABUSIVE'); ?></td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<h4><?php echo '<a href="'.$link.'">'.$this->title.'</a>: ' ?></h4>
					<p><?php echo (is_object($this->reported)) ? stripslashes($this->reported->text) : ''; ?></p>
                    <?php if (is_object($this->reported) && isset($this->reported->subject) && $this->reported->subject!='') { echo '<p>'.stripslashes($this->reported->subject) .'</p>';   }?>
					<p style="color:#999;">
						<?php echo JText::_('REPORTED_BY'); ?> <?php echo (is_object($reporter) && $reporter->get('username')) ? $reporter->get('username') : JText::_('UNKNOWN'); ?>, <?php echo JText::_('RECEIVED'); ?> <?php echo JHTML::_('date', $this->report->created, '%d %b, %Y'); ?>: 
						<?php 
						if ($this->report->report) {
							echo stripslashes($this->report->report);
						} else {
							echo stripslashes($this->report->subject);
						}
						?>
					</p>
				</td>
				<td >
				<?php if ($this->report->state==0) { ?>
					<?php echo JText::_('TAKE_ACTION'); ?>:<br />
					<label><input type="radio" name="task" value="releasereport" /> <?php echo JText::_('RELEASE_ITEM'); ?></label><br />
					<label><input type="radio" name="task" value="deletereport" /> <?php echo JText::_('DELETE_ITEM'); ?> (Append explanation below - optional)</label><br />
                    <label><textarea name="note" id="note" rows="5" cols="25" style="width: 100%;"></textarea></label><br />
					<label><input type="radio" name="task" value="abusereports" /> <?php echo JText::_('DECIDE_LATER'); ?></label>
				<?php } else { ?>
					<input type="hidden" name="task" value="view" />
				<?php } ?>
				</td>
			</tr>
		</tbody>
	</table>
	
	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="id" value="<?php echo $this->report->id ?>" />
	<input type="hidden" name="parentid" value="<?php echo $this->parentid ?>" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>