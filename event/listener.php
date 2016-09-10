<?php
/**
*
* @package phpBB Extension - Member Profile Views
* @copyright (c) 2015 dmzx - http://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\memberprofileviews\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var string */
	protected $root_path;

	/** @var string phpBB admin path */
	protected $phpbb_admin_path;

	/** @var string */
	protected $php_ext;

	/** @var string */
	protected $memberprofileviews_table;

	/**
	* Constructor
	*
	* @param \phpbb\config\config				$config
	* @param \phpbb\user						$user
	* @param \phpbb\template\template			$template
	* @param \phpbb\db\driver\driver_interface	$db
	* @param									$root_path
	* @param									$phpbb_admin_path
	* @param									$php_ext
	* @param									$memberprofileviews_table
	*
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\user $user, \phpbb\template\template $template, \phpbb\db\driver\driver_interface $db, $root_path, $phpbb_admin_path, $php_ext, $memberprofileviews_table)
	{
		$this->config 						= $config;
		$this->user							= $user;
		$this->template						= $template;
		$this->db							= $db;
		$this->root_path 					= $root_path;
		$this->phpbb_admin_path 			= $phpbb_admin_path;
		$this->php_ext 						= $php_ext;
		$this->memberprofileviews_table 	= $memberprofileviews_table;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.memberlist_view_profile'		=> 'memberlist_view_profile',
		);
	}

	public function memberlist_view_profile($event)
	{
		// Add lang file
		$this->user->add_lang_ext('dmzx/memberprofileviews', 'common');
		$value = $this->config['profileviews_value'];

		$member = $event['member'];
		$user_id = $member['user_id'];
		$user_ids = $this->user->data['user_id'];
		$time = time();

		if (($this->user->data['user_id'] != $user_id ) && ($this->user->data['user_id'] != ANONYMOUS) && (!$this->user->data['is_bot']))
		{
			$sql = 'SELECT *
				FROM ' . $this->memberprofileviews_table . '
				WHERE user_id = ' . (int) $user_ids . '
					AND view_id = ' . (int) $user_id;
			$result = $this->db->sql_query($sql);

			if ($row = $this->db->sql_fetchrow($result))
			{
				$counter_user = $row['counter_user'] + 1;
				$sql = array(
					'counter_user'	=> (int) $counter_user,
					'date'			=> $time

				);
				$sql = 'UPDATE ' . $this->memberprofileviews_table . '
					SET ' . $this->db->sql_build_array('UPDATE', $sql) . '
					WHERE user_id = ' . (int) $user_ids . '
						AND view_id = ' . (int) $user_id;
				$this->db->sql_query($sql);
			}
			else
			{
				$sql = array(
					'counter_user'	=> 1,
					'user_id'		=> $user_ids,
					'view_id'		=> $user_id,
					'date'			=> $time
				);
				$sql = 'INSERT INTO ' . $this->memberprofileviews_table	. ' ' . $this->db->sql_build_array('INSERT', $sql);
			}
			$this->db->sql_query($sql);
		}

		$query = 'SELECT m.user_id, m.date, m.counter_user, u.username, u.user_colour, u.user_id, u.user_avatar, u.user_avatar_type, u.user_avatar_height, u.user_avatar_width
			FROM ' . $this->memberprofileviews_table . ' m, ' . USERS_TABLE . ' u
			WHERE m.view_id = ' . (int) $user_id . '
				AND m.user_id = u.user_id
			GROUP BY m.user_id
			ORDER BY m.date DESC';
		$totalviews = $this->db->sql_query_limit($query, $value);

		while ($totalviewsmember = $this->db->sql_fetchrow($totalviews))
		{
			$username = $totalviewsmember['username'];
			$user_colour = ($totalviewsmember['user_colour']) ? ' style="color:#' . $totalviewsmember['user_colour'] . '" class="username-coloured"' : '';
			$user_id_member = $totalviewsmember['user_id'];
			$user_time = ($totalviewsmember['date']) ? ' title="' .	$this->user->format_date($totalviewsmember['date']) . ' ': '';
			$url = append_sid("{$this->root_path}memberlist.{$this->php_ext}?mode=viewprofile&amp;u={$user_id_member}");
			$avatar = phpbb_get_user_avatar($totalviewsmember);

			$this->template->assign_block_vars('member_viewed',array(
				'USERNAME'			=> $username,
				'USERNAME_COLOUR'	=> $user_colour,
				'TIME'				=> $user_time,
				'URL'				=> $url,
				'AVATAR'			=> empty($avatar) ? '<img src="' . $this->phpbb_admin_path . 'images/no_avatar.gif" width="60px;" height="60px;" alt="" />' : $avatar,
				'COUNTER'			=> $totalviewsmember['counter_user'],
			));
		}

		$sql = 'SELECT SUM(counter_user) AS total_views
			FROM ' . $this->memberprofileviews_table . '
			WHERE view_id = ' . (int) $user_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$total_views = (int) $row['total_views'];
		$this->db->sql_freeresult($result);

		$this->template->assign_vars(array(
			'MEMBER_PROFILE_VIEW'			=> true,
			'MEMBER_PROFILE_VIEWS'			=> $total_views,
			'MEMBER_PROFILE_TEXT'			=> $this->user->lang('MEMBER_PROFILE_TEXT', $value),
		));
	}
}
