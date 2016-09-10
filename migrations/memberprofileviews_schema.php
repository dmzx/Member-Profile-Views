<?php
/**
*
* @package phpBB Extension - Member Profile Views
* @copyright (c) 2015 dmzx - http://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\memberprofileviews\migrations;

class memberprofileviews_schema extends \phpbb\db\migration\migration
{
	public function update_data()
	{
		return array(
			// Add configs
			array('config.add', array('profileviews_value', 100)),
		);
	}

	public function update_schema()
	{
		return array(
			'add_tables'	=> array(
				$this->table_prefix . 'memberprofileviews'	=> array(
					'COLUMNS'	=> array(
						'count_id'			=> array('UINT', null, 'auto_increment'),
						'counter_user'		=> array('UINT', null),
						'user_id'			=> array('UINT', null),
						'view_id'			=> array('UINT', null),
						'date'				=> array('TIMESTAMP', 0),
					),
					'PRIMARY_KEY'	=> 'count_id',
				),
			),
		);
	}

	public function revert_schema()
	{
		return 	array(
			'drop_tables'	=> array(
				$this->table_prefix . 'memberprofileviews',
			),
		);
	}
}
