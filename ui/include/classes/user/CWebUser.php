<?php
/*
** Zabbix
** Copyright (C) 2001-2020 Zabbix SIA
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
**/


class CWebUser {

	public static $data = null;

	/**
	 * Flag used to not to extend session lifetime in checkAuthentication.
	 */
	static $extend_session = true;

	/**
	 * Disable automatic session extension.
	 */
	public static function disableSessionExtension() {
		self::$extend_session = false;
	}

	/**
	 * Tries to login a user and populates self::$data on success.
	 *
	 * @param string $login     user login
	 * @param string $password  user password
	 *
	 * @throws Exception if user cannot be logged in
	 *
	 * @return bool
	 */
	public static function login(string $login, string $password): bool {
		try {
			self::$data = API::User()->login([
				'user' => $login,
				'password' => $password,
				'userData' => true
			]);

			if (!self::$data) {
				throw new Exception();
			}

			if (self::$data['gui_access'] == GROUP_GUI_ACCESS_DISABLED) {
				error(_('GUI access disabled.'));
				throw new Exception();
			}

			$result = (bool) self::$data;

			if (isset(self::$data['attempt_failed']) && self::$data['attempt_failed']) {
				CProfile::init();
				CProfile::update('web.login.attempt.failed', self::$data['attempt_failed'], PROFILE_TYPE_INT);
				CProfile::update('web.login.attempt.ip', self::$data['attempt_ip'], PROFILE_TYPE_STR);
				CProfile::update('web.login.attempt.clock', self::$data['attempt_clock'], PROFILE_TYPE_INT);
				$result &= CProfile::flush();
			}

			return $result;
		}
		catch (Exception $e) {
			self::setDefault();
			return false;
		}
	}

	/**
	 * Log-out the current user.
	 */
	public static function logout() {
		self::$data['sessionid'] = CSessionHelper::getId();

		if (API::User()->logout([])) {
			self::$data = null;
			session_destroy();
		}
	}

	public static function checkAuthentication(string $sessionid): bool {
		try {
			self::$data = API::User()->checkAuthentication([
				'sessionid' => $sessionid,
				'extend' => self::$extend_session
			]);

			if (empty(self::$data)) {
				clear_messages(1);
				throw new Exception();
			}

			if (self::$data['gui_access'] == GROUP_GUI_ACCESS_DISABLED) {
				throw new Exception();
			}

			return true;
		}
		catch (Exception $e) {
			return false;
		}
	}

	public static function setDefault() {
		self::$data = [
			'alias' => ZBX_GUEST_USER,
			'userid' => 0,
			'lang' => 'en_gb',
			'type' => 0,
			'debug_mode' => false
		];
	}

	/**
	 * Returns the type of the current user.
	 *
	 * @static
	 *
	 * @return int
	 */
	public static function getType() {
		return self::$data ? self::$data['type'] : 0;
	}

	/**
	 * Returns true if debug mode is enabled.
	 *
	 * @return bool
	 */
	public static function getDebugMode() {
		return (self::$data && self::$data['debug_mode']);
	}

	/**
	 * Returns true if the current user is logged in.
	 *
	 * @return bool
	 */
	public static function isLoggedIn() {
		return (self::$data && self::$data['userid']);
	}

	/**
	 * Returns true if the user is not logged in or logged in as Guest.
	 *
	 * @return bool
	 */
	public static function isGuest() {
		return (self::$data && self::$data['alias'] == ZBX_GUEST_USER);
	}

	/**
	 * Return true if guest user has access to frontend.
	 *
	 * @return bool
	 */
	public static function isGuestAllowed() {
		$guest = DB::select('users', [
			'output' => ['userid'],
			'filter' => ['alias' => ZBX_GUEST_USER]
		]);

		return check_perm2system($guest[0]['userid'])
			&& getUserGuiAccess($guest[0]['userid']) != GROUP_GUI_ACCESS_DISABLED;
	}

	/**
	 * Returns refresh rate in seconds.
	 *
	 * @return int
	 */
	public static function getRefresh() {
		return timeUnitToSeconds(self::$data['refresh']);
	}

	/**
	 * Returns interface language attribute value for HTML lang tag.
	 *
	 * @return string
	 */
	public static function getLang() {
		return (self::$data) ? substr(self::$data['lang'], 0, strpos(self::$data['lang'], '_')) : 'en';
	}

	/**
	 * Get user ip address.
	 *
	 * @return string
	 */
	public static function getIp(): string {
		return (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && $_SERVER['HTTP_X_FORWARDED_FOR'] !== '')
			? $_SERVER['HTTP_X_FORWARDED_FOR']
			: $_SERVER['REMOTE_ADDR'];
	}
}
