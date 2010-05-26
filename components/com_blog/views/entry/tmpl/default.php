<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$juser =& JFactory::getUser();

$entry_year = substr($this->row->publish_up, 0, 4);//intval(JHTML::_('date',$this->row->publish_up, '%Y', 0));
$entry_month = substr($this->row->publish_up, 5, 2);//intval(JHTML::_('date',$this->row->publish_up, '%B', 0));
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div>
<div id="content-header-extra">
	<p><a class="archive" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=archive'); ?>"><?php echo JText::_('Archive'); ?></a></p>
</div>

<div class="main section">
	<div class="aside">
<?php if ($this->authorized) { ?>
		<p><a class="add" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=new'); ?>"><?php echo JText::_('New entry'); ?></a></p>
<?php } ?>
<!-- 	<div class="blog-entries-years">
		<h4><?php echo JText::_('Entries By Year'); ?></h4>
		<ol>
<?php 
if ($this->firstentry) { 
	$start = intval(substr($this->firstentry,0,4));
	$now = date("Y");
	for ($i=$now, $n=$start; $i >= $n; $i--) 
	{
?>
			<li>
				<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&year='.$i); ?>"><?php echo $i; ?></a>
<?php
				if ($i == $entry_year) {
?>
				<ol>
					<li><a <?php if ($entry_month == '01') { echo 'class="active" '; } ?>href="<?php echo JRoute::_('index.php?option='.$this->option.'&year='.$i.'&month=01'); ?>"><?php echo JText::_('COM_BLOG_JANUARY'); ?></a></li>
					<li><a <?php if ($entry_month == '02') { echo 'class="active" '; } ?>href="<?php echo JRoute::_('index.php?option='.$this->option.'&year='.$i.'&month=02'); ?>"><?php echo JText::_('COM_BLOG_FEBRUARY'); ?></a></li>
					<li><a <?php if ($entry_month == '03') { echo 'class="active" '; } ?>href="<?php echo JRoute::_('index.php?option='.$this->option.'&year='.$i.'&month=03'); ?>"><?php echo JText::_('COM_BLOG_MARCH'); ?></a></li>
					<li><a <?php if ($entry_month == '04') { echo 'class="active" '; } ?>href="<?php echo JRoute::_('index.php?option='.$this->option.'&year='.$i.'&month=04'); ?>"><?php echo JText::_('COM_BLOG_APRIL'); ?></a></li>
					<li><a <?php if ($entry_month == '05') { echo 'class="active" '; } ?>href="<?php echo JRoute::_('index.php?option='.$this->option.'&year='.$i.'&month=05'); ?>"><?php echo JText::_('COM_BLOG_MAY'); ?></a></li>
					<li><a <?php if ($entry_month == '06') { echo 'class="active" '; } ?>href="<?php echo JRoute::_('index.php?option='.$this->option.'&year='.$i.'&month=06'); ?>"><?php echo JText::_('COM_BLOG_JUNE'); ?></a></li>
					<li><a <?php if ($entry_month == '07') { echo 'class="active" '; } ?>href="<?php echo JRoute::_('index.php?option='.$this->option.'&year='.$i.'&month=07'); ?>"><?php echo JText::_('COM_BLOG_JULY'); ?></a></li>
					<li><a <?php if ($entry_month == '08') { echo 'class="active" '; } ?>href="<?php echo JRoute::_('index.php?option='.$this->option.'&year='.$i.'&month=08'); ?>"><?php echo JText::_('COM_BLOG_AUGUST'); ?></a></li>
					<li><a <?php if ($entry_month == '09') { echo 'class="active" '; } ?>href="<?php echo JRoute::_('index.php?option='.$this->option.'&year='.$i.'&month=09'); ?>"><?php echo JText::_('COM_BLOG_SEPTEMBER'); ?></a></li>
					<li><a <?php if ($entry_month == '10') { echo 'class="active" '; } ?>href="<?php echo JRoute::_('index.php?option='.$this->option.'&year='.$i.'&month=10'); ?>"><?php echo JText::_('COM_BLOG_OCTOBER'); ?></a></li>
					<li><a <?php if ($entry_month == '11') { echo 'class="active" '; } ?>href="<?php echo JRoute::_('index.php?option='.$this->option.'&year='.$i.'&month=11'); ?>"><?php echo JText::_('COM_BLOG_NOVEMBER'); ?></a></li>
					<li><a <?php if ($entry_month == '12') { echo 'class="active" '; } ?>href="<?php echo JRoute::_('index.php?option='.$this->option.'&year='.$i.'&month=12'); ?>"><?php echo JText::_('COM_BLOG_DECEMBER'); ?></a></li>
				</ol>
<?php
				}
?>
			</li>
<?php 
	}
}
?>
		</ol>
	</div>- / .blog-entries-years -->
	<div class="blog-popular-entries">
		<h4><?php echo JText::_('Popular Entries'); ?></h4>
		<ol>
<?php 
if ($this->popular) { 
	foreach ($this->popular as $row) 
	{
?>
			<li><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task='.JHTML::_('date',$row->publish_up, '%Y', 0).'/'.JHTML::_('date',$row->publish_up, '%m', 0).'/'.$row->alias); ?>"><?php echo stripslashes($row->title); ?></a></li>
<?php 
	}
}
?>
		</ol>
	</div><!-- / .blog-popular-entries -->
	<div class="blog-recent-entries">
		<h4><?php echo JText::_('Recent Entries'); ?></h4>
		<ol>
<?php 
if ($this->recent) { 
	foreach ($this->recent as $row) 
	{
?>
			<li><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task='.JHTML::_('date',$row->publish_up, '%Y', 0).'/'.JHTML::_('date',$row->publish_up, '%m', 0).'/'.$row->alias); ?>"><?php echo stripslashes($row->title); ?></a></li>
<?php 
	}
}
?>
		</ol>
	</div><!-- / .blog-recent-entries -->
</div><!-- / .aside -->
<div class="subject">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<?php 
if ($this->row) { 
?>

	<div class="entry" id="e<?php echo $this->row->id; ?>">
		<dl class="entry-meta">
			<dt class="date"><?php echo JHTML::_('date',$this->row->publish_up, '%d %b, %Y', 0); ?></dt>
			<dd class="time"><?php echo JHTML::_('date',$this->row->publish_up, '%I:%M %p', 0); ?></dd>
<?php if ($this->row->allow_comments == 1) { ?>
			<dd class="comments"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task='.JHTML::_('date',$this->row->publish_up, '%Y', 0).'/'.JHTML::_('date',$this->row->publish_up, '%m', 0).'/'.$this->row->alias.'#comments'); ?>"><?php echo JText::sprintf('COM_BLOG_NUM_COMMENTS', $this->comment_total); ?></a></dd>
<?php } else { ?>
			<dd class="comments"><?php echo JText::_('COM_BLOG_COMMENTS_OFF'); ?></dd>
<?php } ?>
        </dl>
		<h2 class="entry-title">
			<?php echo stripslashes($this->row->title); ?>
<?php if ($juser->get('id') == $this->row->created_by) { ?>
			<span class="state"><?php 
switch ($this->row->state) 
{
	case 1:
		echo JText::_('Public');
	break;
	case 2:
		echo JText::_('Registered members');
	break;
	case 0:
	default:
		echo JText::_('Private');
	break;
} 
?></span>
			<a class="edit" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=edit&entry='.$this->row->id); ?>" title="<?php echo JText::_('Edit'); ?>"><?php echo JText::_('Edit'); ?></a>
			<a class="delete" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=delete&entry='.$this->row->id); ?>" title="<?php echo JText::_('Delete'); ?>"><?php echo JText::_('Delete'); ?></a>
<?php } ?>
		</h2>
<?php 
if ($this->config->get('show_authors')) { 
	$creator =& JUser::getInstance( $this->row->created_by );
?>
		<p class="entry-author">Posted by <cite><a href="<?php echo JRoute::_('index.php?option=com_members&id='.$this->row->created_by); ?>"><?php echo stripslashes($creator->get('name')); ?></a></cite></p>
<?php 
} 
?>
		<div class="entry-content">
			<?php echo $this->row->content; ?>
			<?php echo $this->tags; ?>
		</div>
	</div><!-- / .entry -->
<?php
}
?>
</div><!-- / .subject -->
<!-- </div>/ .main section -->
<div class="clear"></div>
<?php if ($this->row->allow_comments == 1) { ?>
<div class="below section">
<h3>
	<a name="comments"></a>
	Comments on this entry
<?php
	$feed = JRoute::_('index.php?option='.$this->option.'&task='.JHTML::_('date',$this->row->publish_up, '%Y', 0).'/'.JHTML::_('date',$this->row->publish_up, '%m', 0).'/'.$this->row->alias.'/comments.rss');
	if (substr($feed, 0, 4) != 'http') {
		if (substr($feed, 0, 1) != DS) {
			$feed = DS.$feed;
		}
		$jconfig =& JFactory::getConfig();
		$feed = $jconfig->getValue('config.live_site').$feed;
	}
	$feed = str_replace('https:://','http://',$feed);
?>
	<a class="feed" href="<?php echo $feed; ?>" title="<?php echo JText::_('Comments RSS Feed'); ?>"><?php echo JText::_('Comments RSS Feed'); ?></a>
</h3>
<div class="aside">
	<p class="add"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task='.JHTML::_('date',$this->row->publish_up, '%Y', 0).'/'.JHTML::_('date',$this->row->publish_up, '%m', 0).'/'.$this->row->alias.'#post-comment'); ?>">Add a comment</a></p>
</div><!-- / .aside -->
<div class="subject">
<?php 
if ($this->comments) {
?>
	<ol class="comments">
<?php 
	$cls = 'even';
	ximport('wiki.parser');
	ximport('xprofile');
	
	$p = new WikiParser( stripslashes($this->row->title), $this->option, 'blog', $this->row->alias, 0, $this->config->get('uploadpath') );
	
	foreach ($this->comments as $comment) 
	{
		$cls = ($cls == 'even') ? 'odd' : 'even';
		
		if ($this->row->created_by == $comment->created_by) {
			$cls .= ' author';
		}
		
		$name = JText::_('COM_BLOG_ANONYMOUS');
		if (!$comment->anonymous) {
			//$xuser =& JUser::getInstance( $comment->created_by );
			$xuser = new XProfile();
			$xuser->load( $comment->created_by );
			if (is_object($xuser) && $xuser->get('name')) {
				$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$comment->created_by).'">'.stripslashes($xuser->get('name')).'</a>';
			}
		}
		
		if ($comment->reports) {
			$content = '<p class="warning">'.JText::_('COM_BLOG_COMMENT_REPORTED_AS_ABUSIVE').'</p>';
		} else {
			$content = $p->parse( "\n".stripslashes($comment->content) );
		}
?>
		<li class="comment <?php echo $cls; ?>" id="c<?php echo $comment->id; ?>">
			<a name="#c<?php echo $comment->id; ?>"></a>
			<p class="comment-member-photo">
				<img src="<?php echo BlogHelperMember::getMemberPhoto($xuser, $comment->anonymous); ?>" alt="" />
			</p>
			<div class="comment-content">
				<p class="comment-title">
					<strong><?php echo $name; ?></strong> 
					<a class="permalink" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task='.JHTML::_('date',$this->row->publish_up, '%Y', 0).'/'.JHTML::_('date',$this->row->publish_up, '%m', 0).'/'.$this->row->alias.'#c'.$comment->id); ?>" title="<?php echo JText::_('COM_BLOG_PERMALINK'); ?>">@ <span class="time"><?php echo JHTML::_('date',$comment->created, '%I:%M %p', 0); ?></span> on <span class="date"><?php echo JHTML::_('date',$comment->created, '%d %b, %Y', 0); ?></span></a>
				</p>
				<?php echo $content; ?>
<?php 		if (!$comment->reports) { ?>
				<p class="comment-options">
					<a class="abuse" href="<?php echo JRoute::_('index.php?option=com_support&task=reportabuse&category=blog&id='.$comment->id.'&parent='.$this->row->id); ?>">Report abuse</a> | 
<?php
$rtrn = JRoute::_('index.php?option='.$this->option.'&task='.JHTML::_('date',$this->row->publish_up, '%Y', 0).'/'.JHTML::_('date',$this->row->publish_up, '%m', 0).'/'.$this->row->alias.'?reply='.$comment->id.'#post-comment');
if ($juser->get('guest')) {
	$lnk = '/login?return='. base64_encode($rtrn);
} else {
	$lnk = $rtrn;
}
?>
					<a class="reply" href="<?php echo $lnk; ?>">Reply</a>
				</p>
<?php 		} ?>
			</div>
<?php
		if ($comment->replies) {
?>
			<ol class="comments">
<?php
			foreach ($comment->replies as $reply) 
			{
				$cls = ($cls == 'even') ? 'odd' : 'even';

				if ($this->row->created_by == $reply->created_by) {
					$cls .= ' author';
				}

				$name = JText::_('COM_BLOG_ANONYMOUS');
				if (!$reply->anonymous) {
					//$xuser =& JUser::getInstance( $reply->created_by );
					$xuser = new XProfile();
					$xuser->load( $reply->created_by );
					if (is_object($xuser) && $xuser->get('name')) {
						$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$reply->created_by).'">'.stripslashes($xuser->get('name')).'</a>';
					}
				}

				if ($reply->reports) {
					$content = '<p class="warning">'.JText::_('COM_BLOG_COMMENT_REPORTED_AS_ABUSIVE').'</p>';
				} else {
					$content = $p->parse( "\n".stripslashes($reply->content) );
				}
?>
				<li class="comment <?php echo $cls; ?>" id="c<?php echo $reply->id; ?>">
					<a name="#c<?php echo $reply->id; ?>"></a>
					<p class="comment-member-photo">
						<img src="<?php echo BlogHelperMember::getMemberPhoto($xuser, $reply->anonymous); ?>" alt="" />
					</p>
					<div class="comment-content">
						<p class="comment-title">
							<strong><?php echo $name; ?></strong> 
							<a class="permalink" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task='.JHTML::_('date',$this->row->publish_up, '%Y', 0).'/'.JHTML::_('date',$this->row->publish_up, '%m', 0).'/'.$this->row->alias.'#c'.$reply->id); ?>" title="<?php echo JText::_('COM_BLOG_PERMALINK'); ?>">@ <span class="time"><?php echo JHTML::_('date',$reply->created, '%I:%M %p', 0); ?></span> on <span class="date"><?php echo JHTML::_('date',$reply->created, '%d %b, %Y', 0); ?></span></a>
						</p>
						<?php echo $content; ?>
<?php 				if (!$reply->reports) { ?>
						<p class="comment-options">
							<a class="abuse" href="<?php echo JRoute::_('index.php?option=com_support&task=reportabuse&category=blog&id='.$reply->id.'&parent='.$this->row->id); ?>">Report abuse</a> | 
							<a class="reply" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task='.JHTML::_('date',$this->row->publish_up, '%Y', 0).'/'.JHTML::_('date',$this->row->publish_up, '%m', 0).'/'.$this->row->alias.'?reply='.$reply->id.'#post-comment'); ?>">Reply</a>
						</p>
<?php 				} ?>
					</div>
<?php
					if ($reply->replies) {
?>
					<ol class="comments">
<?php
					foreach ($reply->replies as $response) 
					{
						$cls = ($cls == 'even') ? 'odd' : 'even';

						if ($this->row->created_by == $response->created_by) {
							$cls .= ' author';
						}

						$name = JText::_('COM_BLOG_ANONYMOUS');
						if (!$response->anonymous) {
							//$xuser =& JUser::getInstance( $reply->created_by );
							$xuser = new XProfile();
							$xuser->load( $response->created_by );
							if (is_object($xuser) && $xuser->get('name')) {
								$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$response->created_by).'">'.stripslashes($xuser->get('name')).'</a>';
							}
						}

						if ($response->reports) {
							$content = '<p class="warning">'.JText::_('COM_BLOG_COMMENT_REPORTED_AS_ABUSIVE').'</p>';
						} else {
							$content = $p->parse( "\n".stripslashes($response->content) );
						}
?>
						<li class="comment <?php echo $cls; ?>" id="c<?php echo $response->id; ?>">
							<a name="#c<?php echo $response->id; ?>"></a>
							<p class="comment-member-photo">
								<img src="<?php echo BlogHelperMember::getMemberPhoto($xuser, $response->anonymous); ?>" alt="" />
							</p>
							<div class="comment-content">
								<p class="comment-title">
									<strong><?php echo $name; ?></strong> 
									<a class="permalink" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task='.JHTML::_('date',$this->row->publish_up, '%Y', 0).'/'.JHTML::_('date',$this->row->publish_up, '%m', 0).'/'.$this->row->alias.'#c'.$response->id); ?>" title="<?php echo JText::_('COM_BLOG_PERMALINK'); ?>">@ <span class="time"><?php echo JHTML::_('date',$response->created, '%I:%M %p', 0); ?></span> on <span class="date"><?php echo JHTML::_('date',$response->created, '%d %b, %Y', 0); ?></span></a>
								</p>
								<?php echo $content; ?>
<?php 					if (!$response->reports) { ?>
								<p class="comment-options">
									<a class="abuse" href="<?php echo JRoute::_('index.php?option=com_support&task=reportabuse&category=blog&id='.$response->id.'&parent='.$this->row->id); ?>">Report abuse</a>
								</p>
<?php 					} ?>
							</div>
						</li>
<?php
					}
?>
					</ol>
<?php
					}
?>
				</li>
<?php
			}
?>
			</ol>
<?php
		}
?>
		</li>
<?php
	}
?>
	</ol>
<?php
} else {
?>
	<p class="no-comments">There are no comments at this time.</p>
<?php
} 
?>
</div><!-- / .subject -->

<div class="clear"></div>

<h3><a name="post-comment"></a>Post a comment</h3>

<div class="aside">
	<table class="wiki-reference" summary="Wiki Syntax Reference">
		<caption>Wiki Syntax Reference</caption>
		<tbody>
			<tr>
				<td>'''bold'''</td>
				<td><b>bold</b></td>
			</tr>
			<tr>
				<td>''italic''</td>
				<td><i>italic</i></td>
			</tr>
			<tr>
				<td>__underline__</td>
				<td><span style="text-decoration:underline;">underline</span></td>
			</tr>
			<tr>
				<td>{{{monospace}}}</td>
				<td><code>monospace</code></td>
			</tr>
			<tr>
				<td>~~strike-through~~</td>
				<td><del>strike-through</del></td>
			</tr>
			<tr>
				<td>^superscript^</td>
				<td><sup>superscript</sup></td>
			</tr>
			<tr>
				<td>,,subscript,,</td>
				<td><sub>subscript</sub></td>
			</tr>
		</tbody>
	</table>
</div><!-- / .aside -->
<div class="subject">
	<form method="post" action="<?php echo JRoute::_('index.php?option='.$this->option.'&task='.JHTML::_('date',$this->row->publish_up, '%Y', 0).'/'.JHTML::_('date',$this->row->publish_up, '%m', 0).'/'.$this->row->alias); ?>" id="commentform">
		<p class="comment-member-photo">
<?php
			if (!$juser->get('guest')) {
				$jxuser = new XProfile();
				$jxuser->load( $juser->get('id') );
				$thumb = BlogHelperMember::getMemberPhoto($jxuser, 0);
			} else {
				$config =& JComponentHelper::getParams( 'com_members' );
				$thumb = $config->get('defaultpic');
				if (substr($thumb, 0, 1) != DS) {
					$thumb = DS.$dfthumb;
				}
				$thumb = BlogHelperMember::thumbit($thumb);
			}
?>
			<img src="<?php echo $thumb; ?>" alt="" />
		</p>
		<fieldset>
<?php
		if (!$juser->get('guest')) {
			if ($this->replyto->id) {
				ximport('Hubzero_View_Helper_Html');
				$name = JText::_('COM_BLOG_ANONYMOUS');
				if (!$this->replyto->anonymous) {
					//$xuser =& JUser::getInstance( $reply->created_by );
					$xuser = new XProfile();
					$xuser->load( $this->replyto->created_by );
					if (is_object($xuser) && $xuser->get('name')) {
						$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$this->replyto->created_by).'">'.stripslashes($xuser->get('name')).'</a>';
					}
				}
?>
			<blockquote cite="c<?php echo $this->replyto->id ?>">
				<p>
					<strong><?php echo $name; ?></strong> 
					@ <span class="time"><?php echo JHTML::_('date',$this->replyto->created, '%I:%M %p', 0); ?></span> on <span class="date"><?php echo JHTML::_('date',$this->replyto->created, '%d %b, %Y', 0); ?></span>
				</p>
				<p><?php echo Hubzero_View_Helper_Html::shortenText(stripslashes($this->replyto->content), 300, 0); ?></p>
			</blockquote>
<?php
			}
		}
?>
			<label>
				Your <?php echo ($this->replyto->id) ? 'reply' : 'comments'; ?>: <span class="required">required</span>
<?php
			if (!$juser->get('guest')) {
?>
				<textarea name="comment[content]" id="comment-content" rows="15" cols="40"></textarea>
<?php
			} else { 
				$rtrn = JRoute::_('index.php?option='.$this->option.'&task='.JHTML::_('date',$this->row->publish_up, '%Y', 0).'/'.JHTML::_('date',$this->row->publish_up, '%m', 0).'/'.$this->row->alias.'#post-comment');
?>
				<p class="warning">
					You must <a href="/login?return=<?php echo base64_encode($rtrn); ?>">log in</a> to post comments.
				</p>
<?php
			}
?>
			</label>
			
<?php if (!$juser->get('guest')) { ?>
			<label id="comment-anonymous-label">
				<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1" />
				Post anonymously
			</label>

			<p class="submit">
				<input type="submit" name="submit" value="Submit" />
			</p>
<?php } ?>
			<input type="hidden" name="comment[id]" value="0" />
			<input type="hidden" name="comment[entry_id]" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="comment[parent]" value="<?php echo $this->replyto->id; ?>" />
			<input type="hidden" name="comment[created]" value="" />
			<input type="hidden" name="comment[created_by]" value="<?php echo $juser->get('id'); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="savecomment" />
			
			<div class="sidenote">
				<p>
					<strong>Please keep comments relevant to this entry.</strong>
				</p>
				<p>
					Line breaks and paragraphs are automatically converted. URLs (starting with http://) or email addresses will automatically be linked. <a href="#">Wiki syntax</a> is supported.
				</p>
			</div>
		</fieldset>
	</form>
</div><!-- / .subject -->

</div>
<?php } ?>
</div><!-- / .main section -->