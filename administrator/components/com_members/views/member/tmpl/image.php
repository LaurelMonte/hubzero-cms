<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$app =& JFactory::getApplication();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	<title><?php echo JText::_('MEMBER_PICTURE'); ?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<style type="text/css" media="screen">@import url(/templates/<?php echo $app->getTemplate(); ?>/css/main.css);</style>
	<style type="text/css" media="screen">
	body { min-width: 20px; background: #fff; margin: 0; padding: 0; }
	</style>
 </head>
 <body>
   <form action="index2.php" method="post" enctype="multipart/form-data" name="filelist" id="filelist">
	<table class="formed">
	 <thead>
	  <tr>
	   <th><label for="image"><?php echo JText::_('UPLOAD'); ?> <?php echo JText::_('WILL_REPLACE_EXISTING_IMAGE'); ?></label></th>
	  </tr>
	 </thead>
	 <tbody>
	  <tr>
	   <td>
	    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="no_html" value="1" />
		<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
		<input type="hidden" name="task" value="upload" />
		
		<input type="file" name="upload" id="upload" size="17" />&nbsp;&nbsp;&nbsp;
		<input type="submit" value="<?php echo JText::_('UPLOAD'); ?>" />
	   </td>
	  </tr>
	 </tbody>
	</table>
	<?php
		if ($this->getError()) {
			echo $this->getError();
		}
	?>
	<table class="formed">
	 <thead>
	  <tr>
	   <th colspan="4"><label for="image"><?php echo JText::_('MEMBER_PICTURE'); ?></label></th>
	  </tr>
	 </thead>
	 <tbody>
<?php
	$k = 0;

	if ($this->file && file_exists( $this->path.DS.$this->file )) {
		$this_size = filesize($this->path.DS.$this->file);
		list($width, $height, $type, $attr) = getimagesize($this->path.DS.$this->file);
?>
	  <tr>
	   <td rowspan="6"><img src="<?php echo '../'.$this->config->get('uploadpath').DS.$this->dir.DS.$this->file; ?>" alt="<?php echo JText::_('MEMBER_PICTURE'); ?>" id="conimage" /></td>
	   <td><?php echo JText::_('FILE'); ?>:</td>
	   <td><?php echo $this->file; ?></td>
	  </tr>
	  <tr>
	   <td><?php echo JText::_('SIZE'); ?>:</td>
	   <td><?php echo Hubzero_View_Helper_Html::formatsize($this_size); ?></td>
	  </tr>
	  <tr>
	   <td><?php echo JText::_('WIDTH'); ?>:</td>
	   <td><?php echo $width; ?> px</td>
	  </tr>
	  <tr>
	   <td><?php echo JText::_('HEIGHT'); ?>:</td>
	   <td><?php echo $height; ?> px</td>
	  </tr>
	  <tr>
	   <td><input type="hidden" name="currentfile" value="<?php echo $this->file; ?>" /></td>
	   <td><a href="index3.php?option=<?php echo $this->option; ?>&amp;task=deleteimg&amp;file=<?php echo $this->file; ?>&amp;id=<?php echo $this->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1">[ <?php echo JText::_('DELETE'); ?> ]</a></td>
	  </tr>
<?php } else { ?>
	  <tr>
	   <td colspan="4"><img src="<?php echo '..'.$this->config->get('defaultpic'); ?>" alt="<?php echo JText::_('NO_MEMBER_PICTURE'); ?>" />
		<input type="hidden" name="currentfile" value="" /></td>
	  </tr>
<?php } ?>
	 </tbody>
	</table>
	<?php echo JHTML::_( 'form.token' ); ?>
   </form>
 </body>
</html>