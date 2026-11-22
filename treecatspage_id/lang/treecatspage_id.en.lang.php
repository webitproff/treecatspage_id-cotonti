<?php
/**
 * Tree Cats Page ID - English Language File
 *
 * @package treecatspage_id
 * @version 1.2.0
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

$L['info_name'] = 'Tree Cats Page ID';
$L['info_desc'] = 'Page categories tree with whitelist and custom hierarchy. Multiple independent trees.';

$L['info_notes'] = 'Call in template: <code>{PHP|cot_treecatspage_id_render(1)}</code> '
    . 'or with template: <code>{PHP|cot_treecatspage_id_render(1, \'sidebar\')}</code>';

$L['treecatspage_id_tab_trees']     = 'Trees';
$L['treecatspage_id_tab_whitelist'] = 'Whitelist & hierarchy';
$L['treecatspage_id_tab_i18n']      = 'Translations';

$L['treecatspage_id_trees_list']    = 'Trees list';
$L['treecatspage_id_add_tree']      = 'Create tree';
$L['treecatspage_id_tree_title']    = 'Title';
$L['treecatspage_id_tree_desc']     = 'Description';
$L['treecatspage_id_tree_id']       = 'ID';
$L['treecatspage_id_tree_whitelist']= 'Categories';
$L['treecatspage_id_tree_code']     = 'Template code';
$L['treecatspage_id_tree_actions']  = 'Actions';
$L['treecatspage_id_edit']          = 'Edit';
$L['treecatspage_id_rename']        = 'Rename';
$L['treecatspage_id_delete']        = 'Delete';
$L['treecatspage_id_save']          = 'Save';
$L['treecatspage_id_add']           = 'Add';
$L['treecatspage_id_back']          = 'Back to list';

$L['treecatspage_id_whitelist_title'] = 'Whitelist and hierarchy';
$L['treecatspage_id_whitelist_empty'] = 'Whitelist is empty. Add categories.';
$L['treecatspage_id_available_cats']  = 'Available categories';
$L['treecatspage_id_add_selected']    = 'Add selected';
$L['treecatspage_id_select_cats']     = 'Select categories';
$L['treecatspage_id_cat_code']        = 'Code';
$L['treecatspage_id_cat_title']       = 'Title';
$L['treecatspage_id_cat_sort']        = 'Sort';
$L['treecatspage_id_cat_not_found']   = '(category not found)';
$L['treecatspage_id_no_available']    = 'All categories are already added.';
$L['treecatspage_id_parent']          = 'Parent';
$L['treecatspage_id_top_level']       = '— Root (top level) —';
$L['treecatspage_id_add_as_child']    = 'Add as child of';
$L['treecatspage_id_save_hierarchy']  = 'Save hierarchy and sort';

$L['treecatspage_id_i18n_title']      = 'Tree translations';
$L['treecatspage_id_i18n_disabled']   = 'Multilingual is disabled in plugin settings.';
$L['treecatspage_id_i18n_default']    = 'Default language (translations are not stored)';
$L['treecatspage_id_i18n_title_field']= 'Title';
$L['treecatspage_id_i18n_desc_field'] = 'Description';

$L['treecatspage_id_count_pages'] = 'Count pages in categories';
$L['treecatspage_id_count_yes'] = 'Yes';
$L['treecatspage_id_count_no'] = 'No';
$L['treecatspage_id_count_hint'] = 'If «No» — the COUNT SQL query is skipped, counters in template will be 0.';

$L['treecatspage_id_msg_tree_created']    = 'Tree created successfully';
$L['treecatspage_id_msg_tree_updated']    = 'Tree updated';
$L['treecatspage_id_msg_tree_deleted']    = 'Tree deleted';
$L['treecatspage_id_msg_cats_added']      = 'Categories added to whitelist';
$L['treecatspage_id_msg_cat_deleted']     = 'Category (and descendants) removed from whitelist';
$L['treecatspage_id_msg_hierarchy_saved'] = 'Hierarchy and sort order saved';
$L['treecatspage_id_msg_i18n_saved']      = 'Translations saved';
