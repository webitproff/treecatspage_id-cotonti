<?php
/**
 * Tree Cats Page ID - Russian Language File
 * Filename: treecatspage_id.ru.lang.php
 *
 * @package treecatspage_id
 * @version 1.2.0
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

$L['info_name'] = 'Tree Cats Page ID';
$L['info_desc'] = 'Дерево категорий модуля Page с белым списком и собственной иерархией. Несколько независимых деревьев.';

$L['info_notes'] = 'Вызов в шаблоне: <code>{PHP|cot_treecatspage_id_render(1)}</code> '
    . 'или с шаблоном: <code>{PHP|cot_treecatspage_id_render(1, \'sidebar\')}</code>';

$L['treecatspage_id_tab_trees']     = 'Деревья';
$L['treecatspage_id_tab_whitelist'] = 'Whitelist и иерархия';
$L['treecatspage_id_tab_i18n']      = 'Переводы';

$L['treecatspage_id_trees_list']    = 'Список деревьев';
$L['treecatspage_id_add_tree']      = 'Создать дерево';
$L['treecatspage_id_tree_title']    = 'Название';
$L['treecatspage_id_tree_desc']     = 'Описание';
$L['treecatspage_id_tree_id']       = 'ID';
$L['treecatspage_id_tree_whitelist']= 'Категорий';
$L['treecatspage_id_tree_code']     = 'Код для шаблона';
$L['treecatspage_id_tree_actions']  = 'Действия';
$L['treecatspage_id_edit']          = 'Редактировать';
$L['treecatspage_id_rename']        = 'Переименовать';
$L['treecatspage_id_delete']        = 'Удалить';
$L['treecatspage_id_save']          = 'Сохранить';
$L['treecatspage_id_add']           = 'Добавить';
$L['treecatspage_id_back']          = 'Назад к списку';

$L['treecatspage_id_whitelist_title'] = 'Белый список и иерархия';
$L['treecatspage_id_whitelist_empty'] = 'Whitelist пуст. Добавьте категории.';
$L['treecatspage_id_available_cats']  = 'Доступные категории';
$L['treecatspage_id_add_selected']    = 'Добавить выбранные';
$L['treecatspage_id_select_cats']     = 'Выберите категории';
$L['treecatspage_id_cat_code']        = 'Код';
$L['treecatspage_id_cat_title']       = 'Название';
$L['treecatspage_id_cat_sort']        = 'Сорт.';
$L['treecatspage_id_cat_not_found']   = '(категория не найдена)';
$L['treecatspage_id_no_available']    = 'Все категории уже добавлены.';
$L['treecatspage_id_parent']          = 'Родитель';
$L['treecatspage_id_top_level']       = '— Корень (верхний уровень) —';
$L['treecatspage_id_add_as_child']    = 'Добавить как дочерний к';
$L['treecatspage_id_save_hierarchy']  = 'Сохранить иерархию и сортировку';

$L['treecatspage_id_i18n_title']      = 'Переводы дерева';
$L['treecatspage_id_i18n_disabled']   = 'Мультиязычность отключена в настройках плагина.';
$L['treecatspage_id_i18n_default']    = 'Основной язык (переводы не хранятся)';
$L['treecatspage_id_i18n_title_field']= 'Название';
$L['treecatspage_id_i18n_desc_field'] = 'Описание';

$L['treecatspage_id_count_pages'] = 'Вести подсчёт элементов в категории';
$L['treecatspage_id_count_yes'] = 'Да';
$L['treecatspage_id_count_no'] = 'Нет';
$L['treecatspage_id_count_hint'] = 'Если «Нет» — SQL-запрос подсчёта страниц не выполняется, счётчики в шаблоне будут 0.';

$L['treecatspage_id_msg_tree_created']    = 'Дерево успешно создано';
$L['treecatspage_id_msg_tree_updated']    = 'Дерево обновлено';
$L['treecatspage_id_msg_tree_deleted']    = 'Дерево удалено';
$L['treecatspage_id_msg_cats_added']      = 'Категории добавлены в whitelist';
$L['treecatspage_id_msg_cat_deleted']     = 'Категория (и потомки) удалены из whitelist';
$L['treecatspage_id_msg_hierarchy_saved'] = 'Иерархия и сортировка сохранены';
$L['treecatspage_id_msg_i18n_saved']      = 'Переводы сохранены';
