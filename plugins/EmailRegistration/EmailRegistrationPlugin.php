<?php
/**
 * StatusNet - the distributed open-source microblogging tool
 * Copyright (C) 2011, StatusNet, Inc.
 *
 * Email-based registration, as on the StatusNet OnDemand service
 *
 * PHP version 5
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category  Email registration
 * @package   StatusNet
 * @author    Evan Prodromou <evan@status.net>
 * @copyright 2011 StatusNet, Inc.
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html AGPL 3.0
 * @link      http://status.net/
 */

if (!defined('STATUSNET')) {
    // This check helps protect against security problems;
    // your code file can't be executed directly from the web.
    exit(1);
}

/**
 * Email based registration plugin
 *
 * @category  Email registration
 * @package   StatusNet
 * @author    Brion Vibber <brionv@status.net>
 * @author    Evan Prodromou <evan@status.net>
 * @copyright 2011 StatusNet, Inc.
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html AGPL 3.0
 * @link      http://status.net/
 */
class EmailRegistrationPlugin extends Plugin
{
    function onAutoload($cls)
    {
        $dir = dirname(__FILE__);

        switch ($cls)
        {
        case 'EmailregisterAction':
            include_once $dir . '/' . strtolower(mb_substr($cls, 0, -6)) . '.php';
            return false;
        case 'EmailRegistrationForm':
        case 'ConfirmRegistrationForm':
            include_once $dir . '/' . strtolower($cls) . '.php';
            return false;
        default:
            return true;
        }
    }

    function onArgsInitialize(&$args)
    {
        if (array_key_exists('action', $args) && $args['action'] == 'register') {
            // YOINK!
            $args['action'] = 'emailregister';
        }
        return true;
    }

    function onLoginAction($action, &$login)
    {
        if ($action == 'emailregister') {
            $login = true;
            return false;
        }
        return true;
    }

    function onStartLoadDoc(&$title, &$output)
    {
        $dir = dirname(__FILE__);

        // @todo FIXME: i18n issue.
        $docFile = DocFile::forTitle($title, $dir.'/doc-src/');

        if (!empty($docFile)) {
            $output = $docFile->toHTML();
            return false;
        }

        return true;
    }

    function onPluginVersion(&$versions)
    {
        $versions[] = array('name' => 'EmailRegistration',
                            'version' => STATUSNET_VERSION,
                            'author' => 'Evan Prodromou',
                            'homepage' => 'http://status.net/wiki/Plugin:EmailRegistration',
                            'rawdescription' =>
                            // TRANS: Plugin description.
                            _m('Use email only for registration.'));
        return true;
    }
}