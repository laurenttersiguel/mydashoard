<?php


                $config      = \Bkmvc\Core\Application::getInstance()->getLoader()->loadClass('Bkmvc\Core\Config', true);
                $address = $config->cfg("plugins.ldap.address",false);
                $port   = $config->cfg("plugins.ldap.port",false);
                $rootdn = $config->cfg("plugins.ldap.rootdn",false);
                $rootpw = $config->cfg("plugins.ldap.rootpw",false);
                $basedn = $config->cfg("plugins.ldap.basedn",false);
                $loginLdap = $config->cfg("plugins.ldap.loginField",false);

                $ldapconn = ldap_connect($address, $port);

                ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);

                $ldapbind = ldap_bind($ldapconn,$rootdn,$rootpw);

                $ldapResult = ldap_search($ldapconn, $basedn, $loginLdap."=".$login, $ldapAttributes);

                ldap_close($ldapconn);

                $res = array();
                foreach($ldapAttributes as $ldapAttribute) {
                        $res[$ldapAttribute] = $info[0][(string)$ldapAttribute][0];
                }
                return $res;

?>