<?php
/* Servers configuration */
$i = 0;

/* Server [1] — как в lr27.local: хост = имя модуля OSPanel. Сайт kp подключается с теми же параметрами (config/database.php). */
$i++;
$cfg['Servers'][$i]['verbose']         = 'MySQL-8.4';
$cfg['Servers'][$i]['host']            = 'MySQL-8.4';   /* Имя модуля в OSPanel — как в C:\OSPanel\home\lr27.local\public\db.php */
$cfg['Servers'][$i]['port']            = '3306';
$cfg['Servers'][$i]['socket']          = '';
$cfg['Servers'][$i]['auth_type']       = 'config';
$cfg['Servers'][$i]['user']            = 'root';
$cfg['Servers'][$i]['password']        = '';
$cfg['Servers'][$i]['AllowNoPassword'] = true;

/* Server: MariaDB-10.5 [2] */
$i++;
$cfg['Servers'][$i]['verbose']         = 'MariaDB-10.5';
$cfg['Servers'][$i]['host']            = 'MariaDB-10.5';
$cfg['Servers'][$i]['port']            = 3306;
$cfg['Servers'][$i]['socket']          = '';
$cfg['Servers'][$i]['auth_type']       = 'config';
$cfg['Servers'][$i]['user']            = 'root';
$cfg['Servers'][$i]['password']        = '';
$cfg['Servers'][$i]['AllowNoPassword'] = true;

/* Server: MariaDB-10.6 [3] */
$i++;
$cfg['Servers'][$i]['verbose']         = 'MariaDB-10.6';
$cfg['Servers'][$i]['host']            = 'MariaDB-10.6';
$cfg['Servers'][$i]['port']            = 3306;
$cfg['Servers'][$i]['socket']          = '';
$cfg['Servers'][$i]['auth_type']       = 'config';
$cfg['Servers'][$i]['user']            = 'root';
$cfg['Servers'][$i]['password']        = '';
$cfg['Servers'][$i]['AllowNoPassword'] = true;

/* Server: MariaDB-10.11 [4] */
$i++;
$cfg['Servers'][$i]['verbose']         = 'MariaDB-10.11';
$cfg['Servers'][$i]['host']            = 'MariaDB-10.11';
$cfg['Servers'][$i]['port']            = 3306;
$cfg['Servers'][$i]['socket']          = '';
$cfg['Servers'][$i]['auth_type']       = 'config';
$cfg['Servers'][$i]['user']            = 'root';
$cfg['Servers'][$i]['password']        = '';
$cfg['Servers'][$i]['AllowNoPassword'] = true;

/* Server: MariaDB-11.4 [5] */
$i++;
$cfg['Servers'][$i]['verbose']         = 'MariaDB-11.4';
$cfg['Servers'][$i]['host']            = 'MariaDB-11.4';
$cfg['Servers'][$i]['port']            = 3306;
$cfg['Servers'][$i]['socket']          = '';
$cfg['Servers'][$i]['auth_type']       = 'config';
$cfg['Servers'][$i]['user']            = 'root';
$cfg['Servers'][$i]['password']        = '';
$cfg['Servers'][$i]['AllowNoPassword'] = true;

/* Server: MariaDB-11.8 [6] */
$i++;
$cfg['Servers'][$i]['verbose']         = 'MariaDB-11.8';
$cfg['Servers'][$i]['host']            = 'MariaDB-11.8';
$cfg['Servers'][$i]['port']            = 3306;
$cfg['Servers'][$i]['socket']          = '';
$cfg['Servers'][$i]['auth_type']       = 'config';
$cfg['Servers'][$i]['user']            = 'root';
$cfg['Servers'][$i]['password']        = '';
$cfg['Servers'][$i]['AllowNoPassword'] = true;

/* Server: MySQL-5.7 [7] */
$i++;
$cfg['Servers'][$i]['verbose']         = 'MySQL-5.7';
$cfg['Servers'][$i]['host']            = 'MySQL-5.7';
$cfg['Servers'][$i]['port']            = 3306;
$cfg['Servers'][$i]['socket']          = '';
$cfg['Servers'][$i]['auth_type']       = 'config';
$cfg['Servers'][$i]['user']            = 'root';
$cfg['Servers'][$i]['password']        = '';
$cfg['Servers'][$i]['AllowNoPassword'] = true;

/* Server: MySQL-8.0 [8] */
$i++;
$cfg['Servers'][$i]['verbose']         = 'MySQL-8.0';
$cfg['Servers'][$i]['host']            = 'MySQL-8.0';
$cfg['Servers'][$i]['port']            = 3306;
$cfg['Servers'][$i]['socket']          = '';
$cfg['Servers'][$i]['auth_type']       = 'config';
$cfg['Servers'][$i]['user']            = 'root';
$cfg['Servers'][$i]['password']        = '';
$cfg['Servers'][$i]['AllowNoPassword'] = true;

/* Server: MySQL-8.4 [9] */
$i++;
$cfg['Servers'][$i]['verbose']         = 'MySQL-8.4';
$cfg['Servers'][$i]['host']            = 'MySQL-8.4';
$cfg['Servers'][$i]['port']            = 3306;
$cfg['Servers'][$i]['socket']          = '';
$cfg['Servers'][$i]['auth_type']       = 'config';
$cfg['Servers'][$i]['user']            = 'root';
$cfg['Servers'][$i]['password']        = '';
$cfg['Servers'][$i]['AllowNoPassword'] = true;

/* End of servers configuration */

$cfg['DefaultLang']                    = 'ru';
$cfg['ServerDefault']                  = 1;
$cfg['UploadDir']                      = '';
$cfg['SaveDir']                        = '';

$cfg['ActionLinksMode']                = 'icons';
$cfg['AjaxEnable']                     = true;
$cfg['blowfish_secret']                = 'c75609ce35eca302065d07c3a7d1b4e5';
$cfg['MaxRows']                        = 50;
$cfg['PmaNoRelation_DisableWarning']   = true;
$cfg['TableNavigationLinksMode']       = 'icons';
$cfg['TabsMode']                       = 'both';
$cfg['ShowDbStructureCharset']         = true;
$cfg['ShowDbStructureComment']         = true;
$cfg['ShowPhpInfo']                    = true;
$cfg['VersionCheck']                   = false;
