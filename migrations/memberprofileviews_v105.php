<?php
/**
*
* @package phpBB Extension - Member Profile Views
* @copyright (c) 2021 dmzx - https://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\memberprofileviews\migrations;

use phpbb\db\migration\migration;

class memberprofileviews_v105 extends migration
{
	static public function depends_on()
	{
		return [
			'\dmzx\memberprofileviews\migrations\memberprofileviews_v104',
		];
	}

	public function update_data()
	{
		return [
			['config.update', ['memberprofileviews_version', '1.0.5']],
		];
	}
}
