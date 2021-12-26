<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.SISUserLock
 *
 * @copyright	Copyright © 2021 SergioIglesiasNET - All rights reserved.
 * @license		GNU General Public License v2.0
 * @author 		Sergio Iglesias (@sergiois)
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Router\Route;

defined ('_JEXEC') or die();

class PlgSystemSisuserlock extends CMSPlugin
{
	protected $app;
	
	function onBeforeRender()
	{
		$user = Factory::getUser();
		$db = Factory::getDBo();
		$locked = false;
		
		if ($this->app->isClient('administrator') || !$user->id || !count((array)$this->params->get('lockedfields')) || ($this->app->input->get('option') == 'com_users' && $this->app->input->get('view') == 'profile'))
        {
			return;
		}

		$query = $db->getQuery(true);
		$query
        	->select('value')
            ->from('#__fields_values')
            ->where('item_id = '.$user->id)
			->where('field_id IN ('.implode(',',$this->params->get('lockedfields')).')');
        $db->setQuery($query);
        $user_fields = $db->loadAssocList();
		if(count((array)$this->params->get('lockedfields')) > count($user_fields))
		{
			$locked = true;
		}

		if($locked)
		{
			$this->app->enqueueMessage($this->params->get('errormessage'),'error');
			$this->app->redirect(Route::_('index.php?Itemid='.$this->params->get('menuredirect'),false));
		}
	}
}
