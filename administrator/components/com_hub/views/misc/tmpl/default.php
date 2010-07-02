<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_('HUB Configuration').': '.JText::_('Misc. Settings') );
JToolBarHelper::addNew();
JToolBarHelper::editList();
JToolBarHelper::deleteList();

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

	<table class="adminlist" summary="A list of variables and their values.">
	 <thead>
	  <tr>
	   <th class="aRight"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
	   <th>Variable</th>
	   <th width="100%">Value</th>
	  </tr>
	 </thead>
	<tfoot>
		<tr>
			<td colspan="3"><?php echo $this->pageNav->getListFooter(); ?></td>
		</tr>
	</tfoot>
	 <tbody>
<?php
$k = 0;
$keys =  array_keys($this->rows);


$i = $this->pageNav->limitstart;
$n = $this->pageNav->limit;
$count = count($keys);
$end = $i + $n;
if ($end > $count)
    $end = $count;

for (; $i < $end; $i++) 
{
	$value = $this->rows[$keys[$i]];
	$name = $keys[$i];
?>
	  <tr class="<?php echo "row$k"; ?>">
	   <td class="aRight"><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo "$name" ?>" onclick="isChecked(this.checked);" /></td>
	   <td><a href="index.php?option=com_hub&amp;task=edit&amp;name=<?php echo $name;?>" title="Edit this variable"><?php echo stripslashes($name); ?></a></td>
	   <td><?php echo stripslashes($value); ?></td>
	  </tr>
<?php
	$k = 1 - $k;
}
?>
	 </tbody>
	</table>
    <p style="text-align:center;">Note: These variable settings can be overridden with the file <span style="text-decoration:underline;">hubconfiguration-local.php</span></p>
	
	<input type="hidden" name="option" value="com_hub" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>