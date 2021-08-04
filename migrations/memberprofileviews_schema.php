<?php
/**
*
* @package phpBB Extension - Member Profile Views
* @copyright (c) 2015 dmzx - https://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\memberprofileviews\migrations;

use phpbb\db\migration\migration;

class memberprofileviews_schema extends migration
{
	public function update_data()
	{
		return [
			// Add configs
			['config.add', ['profileviews_value', 100]],
		];
	}

	public function update_schema()
	{
		return [
			'add_tables'	=> [
				$this->table_prefix . 'memberprofileviews'	=> [
					'COLUMNS'	=> [
						'count_id'			=> ['UINT', null, 'auto_increment'],
						'counter_user'		=> ['UINT', null],
						'user_id'			=> ['UINT', null],
						'view_id'			=> ['UINT', null],
						'date'				=> ['TIMESTAMP', 0],
					],
					'PRIMARY_KEY'	=> 'count_id',
				],
			],
		];
	}

	public function revert_schema()
	{
		return 	[
			'drop_tables'	=> [
				$this->table_prefix . 'memberprofileviews',
			],
		];
	}
}
