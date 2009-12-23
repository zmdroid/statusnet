<?php
/*
 * StatusNet - the distributed open-source microblogging tool
 * Copyright (C) 2008, 2009, StatusNet, Inc.
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
 */

if (!defined('STATUSNET') && !defined('LACONICA')) { exit(1); }

class CasloginAction extends Action
{
    function handle($args)
    {
        parent::handle($args);
        if (common_is_real_login()) {
            $this->clientError(_m('Already logged in.'));
        } else {
            global $casSettings;
            phpCAS::client(CAS_VERSION_2_0,$casSettings['server'],$casSettings['port'],$casSettings['path']);
            phpCAS::setNoCasServerValidation();
            phpCAS::handleLogoutRequests();
            phpCAS::forceAuthentication();
            global $casTempPassword;
            $casTempPassword = common_good_rand(16);
            $user = common_check_user(phpCAS::getUser(), $casTempPassword);
            if (!$user) {
                $this->serverError(_('Incorrect username or password.'));
                return;
            }

            // success!
            if (!common_set_user($user)) {
                $this->serverError(_('Error setting user. You are probably not authorized.'));
                return;
            }

            common_real_login(true);

            $url = common_get_returnto();

            if ($url) {
                // We don't have to return to it again
                common_set_returnto(null);
            } else {
                $url = common_local_url('all',
                                    array('nickname' =>
                                          $user->nickname));
            }

            common_redirect($url, 303);

        }
    }
}
