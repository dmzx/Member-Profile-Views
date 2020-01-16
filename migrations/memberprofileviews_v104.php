<?php
/**
*
* @package phpBB Extension - Member Profile Views
* @copyright (c) 2020 dmzx - https://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\memberprofileviews\migrations;

class memberprofileviews_v104 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return [
			'\dmzx\memberprofileviews\migrations\memberprofileviews_schema',
		];
	}

	public function update_data()
	{
		return [
			['config.add', ['memberprofileviews_version', '1.0.4']],
		];
	}
}
