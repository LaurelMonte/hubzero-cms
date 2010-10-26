<?php

class plgYSearchSiteMap extends YSearchPlugin
{
	public static function getName() { return 'Site Map'; }

	public static function onYSearch($request, &$results)
	{
                $terms = $request->get_term_ar();
                $weight = 'match(s.title, s.description) against (\''.join(' ', $terms['stemmed']).'\')';

                $addtl_where = array();
                foreach ($terms['mandatory'] as $mand)
                        $addtl_where[] = "(s.title LIKE '%$mand%' OR s.description LIKE '%$mand%')";
                foreach ($terms['forbidden'] as $forb)
                        $addtl_where[] = "(s.title NOT LIKE '%$forb%' AND s.description NOT LIKE '%$forb%')";

                $results->add(new YSearchResultSQL(
			"SELECT
				title, description, link, $weight as weight
			FROM 
				#__ysearch_site_map s
			WHERE $weight > 0".($addtl_where ? ' AND '.join(' AND ', $addtl_where) : '')
		));
	}

	public static function onYSearchAdministrate($context)
	{
		$dbh =& JFactory::getDBO();
		$dbh->setQuery('SELECT id, title, link, description FROM #__ysearch_site_map ORDER BY title');
		$map = $dbh->loadAssocList();
		$edit = NULL;
		if (array_key_exists('sitemap', $context) && array_key_exists('edit_id', $context['sitemap']) && (!array_key_exists('save_id', $context['sitemap']) || $context['sitemap']['save_id'] != $context['sitemap']['edit_id']))
			$edit = $context['sitemap']['edit_id'];

		$html = array();
		$html[] = '<p>The site search is aimed at accessing content, not structure. So, queries look for certain parts of the site may not work as well as one might hope, instead turning up tangentially related pieces of content. By encoding the site structure as content here the search has a better chance of doing the right thing.</p>';
		$html[] = '<form action="" method="post">';
		$html[] = '<input type="hidden" name="ysearch-task" value="SiteMap'.($edit ? 'SaveEdit' : 'Edit').'" />';	
		$html[] = '<table>';
		$html[] = '<thead>';
		$html[] = '	<tr><th>Title</th><th>Link</th><th>Description</th><th></th></tr>';
		$html[] = '</thead>';
		$html[] = '<tbody>';
		foreach ($map as $item)
		{
			$html[] = '<tr>';
			if ($edit == $item['id'])
			{
				$html[] = '<td><input name="sm-title" value="'.htmlentities(array_key_exists('sm-title', $_POST) ? $_POST['sm-title'] : $item['title']).'" /></td>';
				$html[] = '<td><input name="sm-link" value="'.htmlentities(array_key_exists('sm-link', $_POST) ? $_POST['sm-link'] : $item['link']).'" /></td>';
				$html[] = '<td><textarea cols="60" rows="3" name="sm-description">'.htmlentities(array_key_exists('sm-description', $_POST) ? $_POST['sm-description'] : $item['description']).'</textarea></td>';
				$html[] = '<td><input type="hidden" name="sm-id" value="'.$item['id'].'" /><input type="submit" name="save" value="Save" /><input type="submit" name="cancel" value="Cancel" /></td>';
			}
			else
			{
				$html[] = '<td>'.htmlentities($item['title']).'</td>';
				$html[] = '<td>'.htmlentities($item['link']).'</td>';
				$html[] = '<td>'.htmlentities($item['description']).'</td>';
				if ($edit)
					$html[] = '<td></td>';
				else
					$html[] = '<td><input type="hidden" name="ysearch-task" value="SiteMapEdit" /><input type="submit" name="edit-'.$item['id'].'" value="Edit" /><input type="submit" name="delete-'.$item['id'].'" value="Delete" /></td>';
			}
			$html[] = '</tr>';
		}
		if (!$edit)
		{
			$html[] = '<tr>';
			$html[] = '<td><input name="new-sm-title" value="'.htmlentities(array_key_exists('new-sm-title', $_POST) ? $_POST['new-sm-title'] : '').'" /></td>';
			$html[] = '<td><input name="new-sm-link" value="'.htmlentities(array_key_exists('new-sm-link', $_POST) ? $_POST['new-sm-link'] : '').'" /></td>';
			$html[] = '<td><textarea cols="60" rows="3" name="new-sm-description">'.htmlentities(array_key_exists('new-sm-description', $_POST) ? $_POST['new-sm-description'] : '').'</textarea></td>';
			$html[] = '<td><input type="submit" name="add" value="Add" /></td>';
			$html[] = '</tr>';
		}
		$html[] = '</tbody>';
		$html[] = '</table>';
		$html[] = '</form>';
		return array('Site Map', join("\n", $html));
	}

	private static function save_entry_from_post($update = false)
	{
		$dbh =& JFactory::getDBO();
		$fields = array('sm-title', 'sm-link', 'sm-description');
		if ($update)
			$fields[] = 'sm-id';

		foreach ($fields as $key)
		{
			if (!$update)
				$key = 'new-'.$key;
			if (!array_key_exists($key, $_POST) || empty($_POST[$key]))
				return array('sitemap', '<p class="error">Incomplete information: all fields are required to save a sitemap entry</p>', array());
		}

		$id = NULL;
		if ($update)
		{
			$dbh->execute('UPDATE #__ysearch_site_map SET title = '.$dbh->quote($_POST['sm-title']).', description = '.$dbh->quote($_POST['sm-description']).', link = '.$dbh->quote($_POST['sm-link']).' WHERE id = '.(int)$_POST['sm-id']);
			$id = (int)$_POST['sm-id'];
		}
		else
		{
			$dbh->execute('INSERT INTO #__ysearch_site_map(title, description, link) VALUES ('.$dbh->quote($_POST['new-sm-title']).', '.$dbh->quote($_POST['new-sm-description']).', '.$dbh->quote($_POST['new-sm-link']).')');
			unset($_POST['new-sm-title']);
			unset($_POST['new-sm-description']);
			unset($_POST['new-sm-link']);
			$id = $dbh->insertid();
		}
		return array('sitemap', '<p class="success">Site map entry saved</p>', array('save_id' => $id));
	}

	public static function onYSearchTaskSiteMapEdit()
	{
		if (array_key_exists('add', $_POST))
			return self::save_entry_from_post();
		foreach ($_POST as $k=>$v)
			if (preg_match('/(delete|edit)-(\d+)/', $k, $id))
				if ($id[1] == 'edit')
					return array('sitemap', '', array('edit_id' => (int)$id[2]));
				else
				{
					$dbh =& JFactory::getDBO();
					$dbh->execute('DELETE FROM #__ysearch_site_map WHERE id = '.(int)$id[2]);
					return array('sitemap', '<p class="success">Deletion successful</p>', array());
				}
		return array('sitemap', '', array());
	}

	public static function onYSearchTaskSiteMapSaveEdit()
	{
		if (array_key_exists('cancel', $_POST))
			return array('sitemap', '', array());
		
		return self::save_entry_from_post(true);
	}
}
