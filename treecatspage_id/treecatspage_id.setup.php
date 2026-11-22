<?php
/* ====================
[BEGIN_COT_EXT]
Code=treecatspage_id
Name=Tree Cats Page ID
Category=navigation-structure
Description=Дерево категорий модуля Page с белым списком. Несколько независимых деревьев с собственным ID и whitelist категорий.
Version=1.2.0
Date=November 22, 2026
Author=Webitproff
Copyright=Copyright (c) Webitproff
Notes=
Auth_guests=R
Lock_guests=12345A
Auth_members=R
Lock_members=12345A
Requires_modules=page
Requires_plugins=
Order=50
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
treecatspage_id_i18n_use=01:radio::0:Включить мультиязычность для названий деревьев
treecatspage_id_i18n_lang_code_default=02:string::ru:Код основного языка (для него переводы не хранятся)
treecatspage_id_i18n_lang_code_first_use=03:radio::0:Использовать первый дополнительный язык
treecatspage_id_i18n_lang_code_first=04:string::en:Код первого дополнительного языка
treecatspage_id_i18n_lang_code_second_use=05:radio::0:Использовать второй дополнительный язык
treecatspage_id_i18n_lang_code_second=06:string::uk:Код второго дополнительного языка
[END_COT_EXT_CONFIG]
==================== */

/**
 * Tree Cats Page ID
 * Plugin treecatspage_id for Cotonti v1+, PHP 8.4+
 * Filename: treecatspage_id.setup.php
 * Purpose: Setup & Config File. Register data in $db_core, $db_auth and $db_config for the Plugin
 * Date: November 22, 2026
 *
 * @package treecatspage_id
 * @version 1.0.0
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026
 * @license BSD
 */
?>
