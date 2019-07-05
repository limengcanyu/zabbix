<?php
/*
** Zabbix
** Copyright (C) 2001-2019 Zabbix SIA
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

require_once dirname(__FILE__) . '/../include/CWebTest.php';

/**
 * @backup users
 */
class testFormUser extends CWebTest {

	public function getCreateData() {
		return [
			// Alias is already taken by another user.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Alias' => 'Admin',
						'Groups' => 'Zabbix administrators',
						'Password' => '123',
						'Password (once again)' => '123'
					],
					'error_details' => 'User with alias "Admin" already exists.'
				]
			],
			// Empty 'Alias' field.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Alias' => '',
						'Groups' => 'Zabbix administrators',
						'Password' => 'zabbix',
						'Password (once again)' => 'zabbix'
					],
					'error_title' => 'Page received incorrect data',
					'error_details' => 'Incorrect value for field "Alias": cannot be empty.'
				]
			],
			// Space as 'Alias' field value.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Alias' => '   ',
						'Groups' => 'Zabbix administrators',
						'Password' => 'zabbix',
						'Password (once again)' => 'zabbix'
					],
					'error_title' => 'Page received incorrect data',
					'error_details' => 'Incorrect value for field "Alias": cannot be empty.'
				]
			],
			// Empty 'Group' field.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Alias' => 'Negative_Test1',
						'Password' => 'zabbix',
						'Password (once again)' => 'zabbix'
					],
					'error_details' => 'Invalid parameter "/1/usrgrps": cannot be empty.'
				]
			],
			// 'Password' fields not specified.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Alias' => 'Negative_Test2',
						'Groups' => 'Zabbix administrators'
					],
					'error_details' => 'Incorrect value for field "passwd": cannot be empty.'
				]
			],
			// Empty 'Password (once again)' field.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Alias' => 'Negative_Test3',
						'Groups' => 'Zabbix administrators',
						'Password' => 'zabbix'
					],
					'error_title' => 'Cannot add user. Both passwords must be equal.'
				]
			],
			// Empty 'Password' field.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Alias' => 'Negative_Test4',
						'Groups' => 'Zabbix administrators',
						'Password (once again)' => 'zabbix'
					],
					'error_title' => 'Cannot add user. Both passwords must be equal.'
				]
			],
			// 'Password' and 'Password (once again)' do not match.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Alias' => 'Negative_Test5',
						'Groups' => 'Zabbix administrators',
						'Password' => 'PaSSwOrD',
						'Password (once again)' => 'password'
					],
					'error_title' => 'Cannot add user. Both passwords must be equal.'
				]
			],
			// Empty 'Refresh' field.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Alias' => 'Negative_Test6',
						'Groups' => 'Zabbix administrators',
						'Password' => 'zabbix',
						'Password (once again)' => 'zabbix',
						'Refresh' => ''
					],
					'error_details' => 'Invalid parameter "/1/refresh": cannot be empty.'
				]
			],
			// Digits in value of the 'Refresh' field.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Alias' => 'Negative_Test7',
						'Groups' => 'Zabbix administrators',
						'Password' => 'zabbix',
						'Password (once again)' => 'zabbix',
						'Refresh' => '123abc'
					],
					'error_details' => 'Invalid parameter "/1/refresh": a time unit is expected.'
				]
			],
			// Value of the 'Refresh' field too large.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Alias' => 'Negative_Test8',
						'Groups' => 'Zabbix administrators',
						'Password' => 'zabbix',
						'Password (once again)' => 'zabbix',
						'Refresh' => '3601'
					],
					'error_details' => 'Invalid parameter "/1/refresh": value must be one of 0-3600.'
				]
			],
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Alias' => 'Negative_Test_2h',
						'Groups' => 'Zabbix administrators',
						'Password' => 'zabbix',
						'Password (once again)' => 'zabbix',
						'Refresh' => '2h'
					],
					'error_details' => 'Invalid parameter "/1/refresh": value must be one of 0-3600.'
				]
			],
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Alias' => 'Negative_Test_61m',
						'Groups' => 'Zabbix administrators',
						'Password' => 'zabbix',
						'Password (once again)' => 'zabbix',
						'Refresh' => '61m'
					],
					'error_details' => 'Invalid parameter "/1/refresh": value must be one of 0-3600.'
				]
			],
			// Non-time unit value in 'Refresh' field.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Alias' => 'Negative_Test9',
						'Groups' => 'Zabbix administrators',
						'Password' => 'zabbix',
						'Password (once again)' => 'zabbix',
						'Refresh' => '00000000000001'
					],
					'error_details' => 'Invalid parameter "/1/refresh": a time unit is expected.'
				]
			],
			// 'Rows per page' field equal to '0'.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Alias' => 'Negative_Test10',
						'Groups' => 'Zabbix administrators',
						'Password' => 'zabbix',
						'Password (once again)' => 'zabbix',
						'Rows per page' => '0'
					],
					'error_title' => 'Page received incorrect data',
					'error_details' => 'Incorrect value "0" for "Rows per page" field: must be between 1 and 999999.'
				]
			],
			// Non-numeric value of 'Rows per page' field.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Alias' => 'Negative_Test11',
						'Groups' => 'Zabbix administrators',
						'Password' => 'zabbix',
						'Password (once again)' => 'zabbix',
						'Rows per page' => 'abc123'
					],
					'error_title' => 'Page received incorrect data',
					'error_details' => 'Incorrect value "0" for "Rows per page" field: must be between 1 and 999999.'
				]
			],
			// 'Autologout' below minimal value.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Alias' => 'Negative_Test12',
						'Groups' => 'Zabbix administrators',
						'Password' => 'zabbix',
						'Password (once again)' => 'zabbix'
					],
					'auto_logout' => [
						'checked' => true,
						'value' => '89'
					],
					'error_details' => 'Invalid parameter "/1/autologout": value must be one of 0, 90-86400.'
				]
			],
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Alias' => 'Negative_Test12_1m',
						'Groups' => 'Zabbix administrators',
						'Password' => 'zabbix',
						'Password (once again)' => 'zabbix'
					],
					'auto_logout' => [
						'checked' => true,
						'value' => '1m'
					],
					'error_details' => 'Invalid parameter "/1/autologout": value must be one of 0, 90-86400.'
				]
			],
			// 'Autologout' above maximal value.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Alias' => 'Negative_Test13',
						'Groups' => 'Zabbix administrators',
						'Password' => 'zabbix',
						'Password (once again)' => 'zabbix'
					],
					'auto_logout' => [
						'checked' => true,
						'value' => '86401'
					],
					'error_details' => 'Invalid parameter "/1/autologout": value must be one of 0, 90-86400.'
				]
			],
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Alias' => 'Negative_Test13_1441m',
						'Groups' => 'Zabbix administrators',
						'Password' => 'zabbix',
						'Password (once again)' => 'zabbix'
					],
					'auto_logout' => [
						'checked' => true,
						'value' => '1441m'
					],
					'error_details' => 'Invalid parameter "/1/autologout": value must be one of 0, 90-86400.'
				]
			],
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Alias' => 'Negative_Test13_25h',
						'Groups' => 'Zabbix administrators',
						'Password' => 'zabbix',
						'Password (once again)' => 'zabbix'
					],
					'auto_logout' => [
						'checked' => true,
						'value' => '25h'
					],
					'error_details' => 'Invalid parameter "/1/autologout": value must be one of 0, 90-86400.'
				]
			],
			// 'Autologout' with a non-numeric value.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Alias' => 'Negative_Test14',
						'Groups' => 'Zabbix administrators',
						'Password' => 'zabbix',
						'Password (once again)' => 'zabbix'
					],
					'auto_logout' => [
						'checked' => true,
						'value' => 'ninety'
					],
					'error_details' => 'Invalid parameter "/1/autologout": a time unit is expected.'
				]
			],
			// 'Autologout' with an empty value.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Alias' => 'Negative_Test15',
						'Groups' => 'Zabbix administrators',
						'Password' => 'zabbix',
						'Password (once again)' => 'zabbix'
					],
					'auto_logout' => [
						'checked' => true,
						'value' => ''
					],
					'error_details' => 'Invalid parameter "/1/autologout": cannot be empty.'
				]
			],
			// URL with a space in the middle.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Alias' => 'Negative_Test16',
						'Groups' => 'Zabbix administrators',
						'Password' => 'zabbix',
						'Password (once again)' => 'zabbix',
						'URL (after login)' => 'www.zab bix.com'
					],
					'error_details' => 'Invalid parameter "/1/url": unacceptible URL.'
				]
			],
			// External URL without protocol.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Alias' => 'Negative_Test17',
						'Groups' => 'Zabbix administrators',
						'Password' => 'zabbix',
						'Password (once again)' => 'zabbix',
						'URL (after login)' => 'zabbix.com'
					],
					'error_details' => 'Invalid parameter "/1/url": unacceptible URL.'
				]
			],
			// Internal URL without extention.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Alias' => 'Negative_Test18',
						'Groups' => 'Zabbix administrators',
						'Password' => 'zabbix',
						'Password (once again)' => 'zabbix',
						'URL (after login)' => 'sysmaps'
					],
					'error_details' => 'Invalid parameter "/1/url": unacceptible URL.'
				]
			],
			// Incorrect URL protocol.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Alias' => 'Negative_Test19',
						'Groups' => 'Zabbix administrators',
						'Password' => 'zabbix',
						'Password (once again)' => 'zabbix',
						'URL (after login)' => 'snmp://zabbix.com'
					],
					'error_details' => 'Invalid parameter "/1/url": unacceptible URL.'
				]
			],
			// Creating user by specifying only mandatory parameters.
			[
				[
					'expected' => TEST_GOOD,
					'fields' => [
						'Alias' => 'Mandatory_user',
						'Groups' => 'Guests',
						'Password' => 'zabbix',
						'Password (once again)' => 'zabbix'
					]
				]
			],
			// Creating a user with optional parameters specified (including autologout) using Cyrillic charatcers.
			[
				[
					'expected' => TEST_GOOD,
					'fields' => [
						'Alias' => 'Оверлорд',
						'Name' => 'Антон Антонович',
						'Surname' => 'Антонов',
						'Groups' => ['Zabbix administrators'],
						'Password' => 'абв',
						'Password (once again)' => 'абв',
						'Theme' => 'High-contrast dark',
						'Auto-login' => false,
						'Refresh' => '0',
						'Rows per page' => '999999',
						'URL (after login)' => 'https://zabbix.com'
					],
					'check_form' => true
				]
			],
			// Creating a user with punctuation symbols in password and optional parameters specified.
			[
				[
					'expected' => TEST_GOOD,
					'fields' => [
						'Alias' => 'Detailed user',
						'Name' => 'Bugs',
						'Surname' => 'Bunny',
						'Groups' => [
							'Selenium user group',
							'Zabbix administrators'
						],
						'Password' => '!@#$%^&*()_+',
						'Password (once again)' => '!@#$%^&*()_+',
						'Language' => 'English (en_US)',
						'Theme' => 'Dark',
						'Auto-login' => true,
						'Refresh' => '3600s',
						'Rows per page' => '1',
						'URL (after login)' => 'sysmaps.php'
					],
					'auto_logout' => [
						'checked' => true,
						'value' => '1d'
					],
					'check_form' => true,
					'check_user' => true
				]
			],
			// Verification that field password is not mandatory for users with LDAP authentification.
			[
				[
					'expected' => TEST_GOOD,
					'fields' => [
						'Alias' => 'LDAP_user',
						'Groups' => 'LDAP user group'
					]
				]
			],
			// Verification that field password is not mandatory for users with no access to frontend.
			[
				[
					'expected' => TEST_GOOD,
					'fields' => [
						'Alias' => 'No_frontend_user',
						'Groups' => 'No access to the frontend'
					]
				]
			]
		];
	}

	/**
	 * @dataProvider getCreateData
	 */
	public function testFormUser_Create($data) {
		$sql = 'SELECT * FROM users';
		$old_hash = CDBHelper::getHash($sql);
		$this->page->login()->open('users.php?form=create');
		$form = $this->query('name:userForm')->asForm()->waitUntilVisible()->one();
		$form->fill($data['fields']);
		if (array_key_exists('auto_logout', $data)) {
			$this->setAutoLogout($data['auto_logout']);
		}
		$form->submit();
		$this->page->waitUntilReady();
		// Verify that the user was created.
		$this->assertUserMessage($data, 'User added', 'Cannot add user');
		if ($data['expected'] === TEST_BAD) {
			$this->assertEquals($old_hash, CDBHelper::getHash($sql));
		}
		if (CTestArrayHelper::get($data, 'check_form', false)) {
			$this->assertFormFields($data);
		}
		if (CTestArrayHelper::get($data, 'check_user', false)) {
			$this->assertUserParameters($data);
		}
	}

	/*
	 * Check the field values after creating or updating user.
	 */
	private function assertFormFields($data) {
		$userid = CDBHelper::getValue('SELECT userid FROM users WHERE alias ='.zbx_dbstr($data['fields']['Alias']));
		$this->page->open('users.php?form=update&userid='.$userid);
		$form_update = $this->query('name:userForm')->asForm()->waitUntilVisible()->one();
		// Verify that fields are updated.
		$check_fields = ['Alias', 'Name', 'Surname', 'Language', 'Theme', 'Refresh', 'Rows per page', 'URL (after login)'];
		foreach ($check_fields as $field_name) {
			if (array_key_exists($field_name, $data['fields'])) {
				$this->assertEquals($data['fields'][$field_name], $form_update->getField($field_name)->getValue());
			}
		}
		$this->assertEquals($data['fields']['Groups'], $form_update->getField('Groups')->getSelected());
		if (CTestArrayHelper::get($data, 'auto_logout.checked', false)) {
			$this->assertTrue($form_update->getField('Auto-login')->isChecked(false));
		}
		else {
			$this->assertTrue($form_update->getField('Auto-login')->isChecked($data['fields']['Auto-login']));
		}
	}

	/*
	 * Login as user and check user profile parameters in UI.
	 */
	private function assertUserParameters($data) {
		try {
			$db_theme = CDBHelper::getValue('SELECT theme FROM users WHERE alias ='.zbx_dbstr($data['fields']['Alias']));
			// Logout.
			$this->query('xpath://a[@class="top-nav-signout"]')->one()->click();
			$this->page->logout();
			// Log in with the created or updated user.
			$password = CTestArrayHelper::get($data['fields'], 'Password', $data['fields']['Password'] = 'zabbix');
			$this->userLogin($data['fields']['Alias'],$password);
			// Verification of URL after login.
			$this->assertContains($data['fields']['URL (after login)'], $this->page->getCurrentURL());
			// Verification of the number of rows per page parameter.
			$rows = $this->query('name:frm_maps')->asTable()->waitUntilVisible()->one()->getRows();
			$this->assertEquals($data['fields']['Rows per page'], $rows->count());
			// Verification of default theme.
			$color = $this->query('tag:body')->one()->getCSSValue('background-color');
			$stylesheet = $this->query('xpath://link[@rel="stylesheet"]')->one();
			$file = explode('/', $stylesheet->getAttribute('href'));
			if ($data['fields']['Theme'] === 'Dark') {
				$this->assertEquals('dark-theme', $db_theme);
				$this->assertEquals('dark-theme.css', end($file));
				$this->assertEquals('rgba(14, 16, 18, 1)', $color);
			}
			else if ($data['fields']['Theme'] === 'High-contrast light') {
				$this->assertEquals('hc-light', $db_theme);
				$this->assertEquals('hc-light.css', end($file));
				$this->assertEquals('rgba(255, 255, 255, 1)', $color);
			}
			$this->page->logout();
		}
		catch (\Exception $e) {
			$this->page->logout();
			throw $e;
		}
	}

	public function getUpdateData() {
		return [
			// Alias is already taken by another user.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Alias' => 'Admin'
					],
					'error_details' => 'User with alias "Admin" already exists.'
				]
			],
			// Empty 'Group' field.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Groups' => ''
					],
					'error_details' => 'Invalid parameter "/1/usrgrps": cannot be empty.'
				]
			],
			// Empty 'Password (once again)' field.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Password' => 'zabbix'
					],
					'error_title' => 'Cannot update user. Both passwords must be equal.'
				]
			],
			// Empty 'Password' field.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Password (once again)' => 'zabbix'
					],
					'error_title' => 'Cannot update user. Both passwords must be equal.'
				]
			],
			// 'Password' and 'Password (once again)' do not match.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Groups' => 'Zabbix administrators',
						'Password' => 'PaSSwOrD',
						'Password (once again)' => 'password'
					],
					'error_title' => 'Cannot update user. Both passwords must be equal.'
				]
			],
			// Empty 'Refresh' field.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Groups' => 'Zabbix administrators',
						'Password' => 'zabbix',
						'Password (once again)' => 'zabbix',
						'Refresh' => ''
					],
					'error_details' => 'Invalid parameter "/1/refresh": cannot be empty.'
				]
			],
			// Digits in value of the 'Refresh' field.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Refresh' => '123abc'
					],
					'error_details' => 'Invalid parameter "/1/refresh": a time unit is expected.'
				]
			],
			// Value of the 'Refresh' field too large.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Refresh' => '3601'
					],
					'error_details' => 'Invalid parameter "/1/refresh": value must be one of 0-3600.'
				]
			],
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Refresh' => '61m'
					],
					'error_details' => 'Invalid parameter "/1/refresh": value must be one of 0-3600.'
				]
			],
			// Non time unit value in 'Refresh' field.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Refresh' => '00000000000001'
					],
					'error_details' => 'Invalid parameter "/1/refresh": a time unit is expected.'
				]
			],
			//	'Rows per page' field equal to '0'.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Rows per page' => '0'
					],
					'error_title' => 'Page received incorrect data',
					'error_details' => 'Incorrect value "0" for "Rows per page" field: must be between 1 and 999999.'
				]
			],
			//	Non-numeric value of 'Rows per page' field.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'Rows per page' => 'abc123'
					],
					'error_title' => 'Page received incorrect data',
					'error_details' => 'Incorrect value "0" for "Rows per page" field: must be between 1 and 999999.'
				]
			],
			// 'Autologout' below minimal value.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [],
					'auto_logout' => [
						'checked' => true,
						'value' => '89'
					],
					'error_details' => 'Invalid parameter "/1/autologout": value must be one of 0, 90-86400.'
				]
			],
			// 'Autologout' above maximal value.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [],
					'auto_logout' => [
						'checked' => true,
						'value' => '86401'
					],
					'error_details' => 'Invalid parameter "/1/autologout": value must be one of 0, 90-86400.'
				]
			],
			[
				[
					'expected' => TEST_BAD,
					'fields' => [],
					'auto_logout' => [
						'checked' => true,
						'value' => '1m'
					],
					'error_details' => 'Invalid parameter "/1/autologout": value must be one of 0, 90-86400.'
				]
			],
			[
				[
					'expected' => TEST_BAD,
					'fields' => [],
					'auto_logout' => [
						'checked' => true,
						'value' => '1441m'
					],
					'error_details' => 'Invalid parameter "/1/autologout": value must be one of 0, 90-86400.'
				]
			],
			[
				[
					'expected' => TEST_BAD,
					'fields' => [],
					'auto_logout' => [
						'checked' => true,
						'value' => '25h'
					],
					'error_details' => 'Invalid parameter "/1/autologout": value must be one of 0, 90-86400.'
				]
			],
			// 'Autologout' with a non-numeric value.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [],
					'auto_logout' => [
						'checked' => true,
						'value' => 'ninety'
					],
					'error_details' => 'Invalid parameter "/1/autologout": a time unit is expected.'
				]
			],
			// 'Autologout' with an empty value.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [],
					'auto_logout' => [
						'checked' => true,
						'value' => ''
					],
					'error_details' => 'Invalid parameter "/1/autologout": cannot be empty.'
				]
			],
			// URL with a space in the middle.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'URL (after login)' => 'www.zab bix.com'
					],
					'error_details' => 'Invalid parameter "/1/url": unacceptible URL.'
				]
			],
			// External URL without protocol.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'URL (after login)' => 'zabbix.com'
					],
					'error_details' => 'Invalid parameter "/1/url": unacceptible URL.'
				]
			],
			// Internal URL without extention.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'URL (after login)' => 'sysmaps'
					],
					'error_details' => 'Invalid parameter "/1/url": unacceptible URL.'
				]
			],
			// Incorrect URL protocol.
			[
				[
					'expected' => TEST_BAD,
					'fields' => [
						'URL (after login)' => 'snmp://zabbix.com'
					],
					'error_details' => 'Invalid parameter "/1/url": unacceptible URL.'
				]
			],
			// Updating all fields (except password) of an existing user.
			[
				[
					'expected' => TEST_GOOD,
					'user_to_update' => 'disabled-user',
					'fields' => [
						'Alias' => 'Updated_user_1',
						'Name' => 'Test_Name',
						'Surname' => 'Test_Surname',
						'Groups' => [
							'Selenium user group'
						],
						'Language' => 'English (en_US)',
						'Theme' => 'Dark',
						'Auto-login' => true,
						'Refresh' => '60m',
						'Rows per page' => '1',
						'URL (after login)' => 'sysmaps.php'
					],
					'auto_logout' => [
						'checked' => true,
						'value' => '24h'
					],
					'check_form' => true
				]
			],
			[
				[
					'expected' => TEST_GOOD,
					'fields' => [
						'Alias' => 'Updated_user',
						'Name' => 'Road',
						'Surname' => 'Runner',
						'Groups' => [
							'Selenium user group'
						],
						'Language' => 'English (en_US)',
						'Theme' => 'High-contrast light',
						'Auto-login' => true,
						'Refresh' => '1h',
						'Rows per page' => '1',
						'URL (after login)' => 'sysmaps.php'
					],
					'check_form' => true,
					'check_user' => true
				]
			],
			// User update without any actual changes.
			[
				[
					'expected' => TEST_GOOD,
					'fields' => [
						'Alias' => 'test-user'
					],
					'blank_update' => true
				]
			]
		];
	}

	/**
	 * @dataProvider getUpdateData
	 */
	public function testFormUser_Update($data) {
		if (CTestArrayHelper::get($data, 'blank_update', false) === true) {
			$update_user = CTestArrayHelper::get($data, 'fields.Alias');
		}
		else {
			$update_user = CTestArrayHelper::get($data, 'user_to_update', 'Tag-user');
		}
		$sql = 'SELECT * FROM users';
		$old_hash = CDBHelper::getHash($sql);

		$this->page->login()->open('users.php?ddreset=1');
		$this->query('link', $update_user)->waitUntilVisible()->one()->click();
		// Update user parameters.
		$form = $this->query('name:userForm')->asForm()->one();
		if (array_key_exists('Password', $data['fields']) || array_key_exists('Password (once again)', $data['fields'])) {
			$form->query('button:Change password')->one()->click();
		}
		if (CTestArrayHelper::get($data, 'blank_update', false) === false) {
			$form->fill($data['fields']);
			if (array_key_exists('auto_logout', $data)) {
				$this->setAutoLogout($data['auto_logout']);
			}
		}
		$form->submit();
		$this->page->waitUntilReady();
		// Verify if the user was updated.
		$this->assertUserMessage($data, 'User updated', 'Cannot update user');
		if ($data['expected'] === TEST_BAD || CTestArrayHelper::get($data, 'blank_update', false) == true) {
			$this->assertEquals($old_hash, CDBHelper::getHash($sql));
		}
		if (CTestArrayHelper::get($data, 'check_form', false)) {
			$this->assertFormFields($data);
		}
		if (CTestArrayHelper::get($data, 'check_user', false)) {
			$this->assertUserParameters($data);
		}
	}

	public function testFormUser_PasswordUpdate() {
		$data = [
			'alias' => 'user-zabbix',
			'old_password' => 'zabbix',
			'new_password' => 'zabbix_new',
			'error_message' => 'Login name or password is incorrect.',
			'attempt_message' => '1 failed login attempt logged. Last failed attempt was from'
		];
		$this->page->login()->open('users.php?ddreset=1');
		$this->query('link', $data['alias'])->waitUntilVisible()->one()->click();
		$form_update = $this->query('name:userForm')->asForm()->one();
		$form_update->query('button:Change password')->one()->click();

		// Change user password and log out.
		$form_update->fill([
			'Password' => $data['new_password'],
			'Password (once again)' => $data['new_password']
		]);
		$form_update->submit();
		try {
			$this->query('class:top-nav-signout')->one()->click();
			$this->page->logout();

			// Atempt to sign in with old password.
			$this->userLogin($data['alias'],$data['old_password']);
			$message = $this->query('class:red')->one()->getText();
			$this->assertEquals($message, $data['error_message']);

			// Sign in with new password.
			$this->userLogin($data['alias'],$data['new_password']);
			$attempt_message = CMessageElement::find()->one();
			$this->assertTrue($attempt_message->hasLine($data['attempt_message']));
			$this->page->logout();
		}
		catch (\Exception $e) {
			// Logout to execute remaining tests.
			$this->page->logout();
			throw $e;
		}
	}

	public function getDeleteData() {
		return [
			[
				[
					'expected' => TEST_GOOD,
					'fields' => [
						'Alias' => 'no-access-to-the-frontend'
					]
				]
			],
			// Attempt to delete internal user guest.
			[
				[
					'expected' => TEST_BAD,
					'username' => 'guest',
					'error_details' => 'Cannot delete Zabbix internal user "guest", try disabling that user.'
				]
			],
			// Attempt to delete a user that owns a map.
			[
				[
					'expected' => TEST_BAD,
					'username' => 'user-zabbix',
					'parameters' => [
						'DB_table' => 'sysmaps',
						'column' => 'name',
						'value' => 'Local network'
					],
					'error_details' => 'User "user-zabbix" is map "Local network" owner.'
				]
			],
			// Attempt to delete a user that owns a screen.
			[
				[
					'expected' => TEST_BAD,
					'username' => 'test-user',
					'parameters' => [
						'DB_table' => 'screens',
						'column' => 'name',
						'value' => 'Zabbix server'
					],
					'error_details' => 'User "test-user" is screen "Zabbix server" owner.'
				]
			],
			// Attempt to delete a user that owns a slide show.
			[
				[
					'expected' => TEST_BAD,
					'username' => 'admin-zabbix',
					'parameters' => [
						'DB_table' => 'slideshows',
						'column' => 'name',
						'value' => 'Test slide show 1'
					],
					'error_details' => 'User "admin-zabbix" is slide show "Test slide show 1" owner.'
				]
			],
			// Attempt to delete a user that is mentioned in an action.
			[
				[
					'expected' => TEST_BAD,
					'username' => 'user-for-blocking',
					'parameters' => [
						'DB_table' => 'opmessage_usr',
						'column' => 'operationid',
						'value' => '19'
					],
					'User "user-zabbix" is used in "Trigger action 4" action.'
				]
			]
		];
	}

	/**
	 * @dataProvider getDeleteData
	 */
	public function testFormUser_Delete($data) {
		// Defined required variables.
		if (array_key_exists('username', $data)) {
			$username = $data['username'];
		}
		else {
			$username = $data['fields']['Alias'];
		}
		$this->page->login()->open('users.php');
		$this->query('link', $username)->one()->click();
		$userid = CDBHelper::getValue('SELECT userid FROM users WHERE alias =' . zbx_dbstr($username));
		// Link user with map, screen, slideshow, action to validate user deletion.
		if (array_key_exists('parameters', $data)) {
			DBexecute(
					'UPDATE '.$data['parameters']['DB_table'].' SET userid ='.zbx_dbstr($userid).
					' WHERE '.$data['parameters']['column'].'='.zbx_dbstr($data['parameters']['value'])
			);
		}
		// Attempt to delete the user from user update view and verify result.
		$this->query('button:Delete')->one()->click();
		$this->page->acceptAlert();
		$this->page->waitUntilReady();
		// Validate if the user was deleted.
		$this->assertUserMessage($data, 'User deleted', 'Cannot delete user');
		if ($data['expected'] === TEST_BAD) {
			$this->assertEquals(1, CDBHelper::getCount('SELECT userid FROM users WHERE alias =' . zbx_dbstr($username)));
		}
	}

	public function testFormUser_SelfDeletion() {
		$this->page->login()->open('users.php?form=update&userid=1');
		$this->assertTrue($this->query('button:Delete')->one()->isEnabled(false));
	}

	public function testFormUser_Cancel() {
		$data = [
			'Alias' => 'user-cancel',
			'Password' => 'zabbix',
			'Password (once again)' => 'zabbix',
			'Groups' => 'Guests'
		];
		$sql_users = 'SELECT * FROM users ORDER BY userid';
		$user_hash = CDBHelper::getHash($sql_users);
		$this->page->login()->open('users.php?form=create');

		// Check cancellation when creating users.
		$form_create = $this->query('name:userForm')->asForm()->waitUntilVisible()->one();
		$form_create->fill($data);
		$this->query('button:Cancel')->one()->click();
		$cancel_url = $this->page->getCurrentURL();
		$this->assertContains('users.php?cancel=1', $cancel_url);
		$this->assertEquals($user_hash, CDBHelper::getHash($sql_users));

		// Check Cancellation when updating users.
		$this->page->open('users.php?form=update&userid=1');
		$this->query('id:name')->one()->fill('Boris');
		$this->query('button:Cancel')->one()->click();
		$this->assertEquals($user_hash, CDBHelper::getHash($sql_users));
	}

	private function assertUserMessage($data, $good_title, $bad_title) {
		$message = CMessageElement::find()->one();
		switch ($data['expected']) {
			case TEST_GOOD:
				$this->assertTrue($message->isGood());
				$this->assertEquals($good_title, $message->getTitle());
				$user_count = CDBHelper::getCount('SELECT userid FROM users WHERE alias ='.zbx_dbstr($data['fields']['Alias']));
				if ($good_title === 'User deleted') {
					$this->assertTrue($user_count === 0);
				}
				else {
					$this->assertTrue($user_count === 1);
				}
				break;
			case TEST_BAD:
				$this->assertTrue($message->isBad());
				$this->assertEquals(CTestArrayHelper::get($data, 'error_title', $data['error_title'] = $bad_title), $message->getTitle());
				if (array_key_exists('error_details', $data)) {
					$this->assertTrue($message->hasLine($data['error_details']));
				}
				break;
		}
	}

	private function setAutoLogout($data) {
		$form = $this->query('name:userForm')->asForm()->one();
		$auto_logout = $form->getFieldElements('Auto-logout');
		/*
		 * Auto-logout fields consists of multiple elements, the following of which will be used:
		 *		0 - Checkbox element
		 *		3 - Text input element
		 */
		$auto_logout->get(0)->asCheckbox()->set($data['checked']);
		if (array_key_exists('value', $data)) {
			$auto_logout->get(3)->overwrite($data['value']);
		}
		// Verify that Auto-login is unchecked after setting Auto-logout.
		$this->assertTrue($form->getField('Auto-login')->isChecked(false));
	}

	private function userLogin($alias,$password) {
		$this->query('id:name')->waitUntilVisible()->one()->fill($alias);
		$this->query('id:password')->one()->fill($password);
		$this->query('button:Sign in')->one()->click();
	}
}
