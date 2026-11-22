<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tools
[END_COT_EXT]
==================== */

/**
 * ============================================================
 * ДОКУМЕНТАЦИЯ ПО ХУКУ `tools` И ФАЙЛУ treecatspage_id.admin.php
 * ============================================================
 *
 * Хук `tools` — админ-страница плагина (admin.php?m=other&p=treecatspage_id).
 *
 * Вкладки:
 *   1. «Деревья» (tab=trees) — CRUD деревьев, код для шаблона
 *   2. «Whitelist» (tab=whitelist) — категории + своя иерархия (parent_id)
 *      и сортировка
 *   3. «Переводы» (tab=i18n) — i18n названий деревьев
 *
 * Иерархия:
 *   parent_id = 0          → корневой элемент
 *   parent_id = id другой  → дочерний / внучатый элемент
 *   Циклы запрещены (нельзя выбрать себя или своего потомка родителем).
 *
 * ============================================================
 */

/**
 * Tree Cats Page ID plugin - admin panel
 *
 * Filename: treecatspage_id.admin.php
 * Path:     plugins/treecatspage_id/treecatspage_id.admin.php
 *
 * Date: November 22, 2026
 *
 * @package treecatspage_id
 * @version 1.2.0
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_langfile('treecatspage_id', 'plug');
require_once cot_incfile('treecatspage_id', 'plug', 'functions');
require_once cot_incfile('forms');

$tab = cot_import('tab', 'G', 'ALP') ?: 'trees';
$treeId = cot_import('tree', 'G', 'INT');
$a = cot_import('a', 'G', 'ALP');

$i18nActive = treecatspage_id_i18n_enabled();
$activeLangs = $i18nActive ? treecatspage_id_get_active_langs() : [];
$defaultLang = treecatspage_id_get_default_lang();

if (($tab == 'whitelist' || $tab == 'i18n') && $treeId <= 0) {
    $firstTree = Cot::$db->query(
        "SELECT tree_id FROM " . Cot::$db->treecatspage_id_trees . " ORDER BY tree_id ASC LIMIT 1"
    )->fetchColumn();
    if ($firstTree) {
        cot_redirect(cot_url('admin', ['m' => 'other', 'p' => 'treecatspage_id', 'tab' => $tab, 'tree' => $firstTree], '', true));
    } else {
        cot_redirect(cot_url('admin', ['m' => 'other', 'p' => 'treecatspage_id', 'tab' => 'trees'], '', true));
    }
}

$t = new XTemplate(cot_tplfile('treecatspage_id.admin', 'plug', true));

$t->assign([
    'TAB_TREES_ACTIVE'     => $tab == 'trees' ? 'active' : '',
    'TAB_WHITELIST_ACTIVE' => $tab == 'whitelist' ? 'active' : '',
    'TAB_I18N_ACTIVE'      => $tab == 'i18n' ? 'active' : '',
    'URL_TREES'            => cot_url('admin', ['m' => 'other', 'p' => 'treecatspage_id', 'tab' => 'trees']),
    'URL_WHITELIST'        => cot_url('admin', ['m' => 'other', 'p' => 'treecatspage_id', 'tab' => 'whitelist', 'tree' => $treeId]),
    'URL_I18N'             => cot_url('admin', ['m' => 'other', 'p' => 'treecatspage_id', 'tab' => 'i18n', 'tree' => $treeId]),
]);

/* ========== Вкладка: Деревья ========== */
if ($tab == 'trees') {
    if ($a == 'update_tree' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $newTitle = cot_import('edit_tree_title', 'P', 'TXT', 255);
        $newDesc  = cot_import('edit_tree_desc', 'P', 'TXT');
        $countPages = cot_import('edit_tree_count_pages', 'P', 'BOL') ? 1 : 0;
        if ($treeId > 0 && !empty($newTitle)) {
            Cot::$db->update(
                Cot::$db->treecatspage_id_trees,
                [
                    'tree_title'       => $newTitle,
                    'tree_description' => $newDesc,
                    'tree_count_pages' => $countPages,
                    'tree_updated'     => time(),
                ],
                'tree_id = ?',
                [$treeId]
            );
            cot_message(Cot::$L['treecatspage_id_msg_tree_updated']);
        }
        cot_redirect(cot_url('admin', ['m'=>'other','p'=>'treecatspage_id','tab'=>'trees'], '', true));
    }

    if ($a == 'edit_tree' && $treeId > 0) {
        $treeToEdit = Cot::$db->query(
            "SELECT * FROM " . Cot::$db->treecatspage_id_trees . " WHERE tree_id = ?",
            [$treeId]
        )->fetch();
        if ($treeToEdit) {
            $countChecked = !empty($treeToEdit['tree_count_pages']);
            $t->assign([
                'EDIT_TREE_ID'       => $treeToEdit['tree_id'],
                'EDIT_TREE_TITLE'    => cot_inputbox('text', 'edit_tree_title', htmlspecialchars($treeToEdit['tree_title']), 'class="form-control"'),
                'EDIT_TREE_DESC'     => cot_inputbox('text', 'edit_tree_desc', htmlspecialchars($treeToEdit['tree_description']), 'class="form-control"'),
                'EDIT_TREE_COUNT_YES'=> $countChecked ? 'checked="checked"' : '',
                'EDIT_TREE_COUNT_NO' => !$countChecked ? 'checked="checked"' : '',
                'EDIT_TREE_FORM_URL' => cot_url('admin', ['m'=>'other','p'=>'treecatspage_id','tab'=>'trees','a'=>'update_tree','tree'=>$treeToEdit['tree_id']]),
            ]);
            $t->parse('MAIN.EDIT_TREE_FORM');
        }
    }

    if ($a == 'add' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = cot_import('tree_title', 'P', 'TXT', 255);
        $desc  = cot_import('tree_desc', 'P', 'TXT');
        $countPages = cot_import('tree_count_pages', 'P', 'BOL') ? 1 : 0;
        if (!empty($title)) {
            Cot::$db->insert(Cot::$db->treecatspage_id_trees, [
                'tree_title'       => $title,
                'tree_description' => $desc,
                'tree_count_pages' => $countPages,
                'tree_created'     => time(),
                'tree_updated'     => time(),
            ]);
            cot_message(Cot::$L['treecatspage_id_msg_tree_created']);
            cot_redirect(cot_url('admin', ['m' => 'other', 'p' => 'treecatspage_id', 'tab' => 'trees'], '', true));
        }
    }

    if ($a == 'delete' && $treeId > 0) {
        Cot::$db->delete(Cot::$db->treecatspage_id_i18n, 'tree_id = ?', [$treeId]);
        Cot::$db->delete(Cot::$db->treecatspage_id_cats, 'tree_id = ?', [$treeId]);
        Cot::$db->delete(Cot::$db->treecatspage_id_trees, 'tree_id = ?', [$treeId]);
        cot_message(Cot::$L['treecatspage_id_msg_tree_deleted']);
        cot_redirect(cot_url('admin', ['m' => 'other', 'p' => 'treecatspage_id', 'tab' => 'trees'], '', true));
    }

    $trees = Cot::$db->query("SELECT * FROM " . Cot::$db->treecatspage_id_trees . " ORDER BY tree_id")->fetchAll();
    foreach ($trees as $tree) {
        $whitelistCount = Cot::$db->query(
            "SELECT COUNT(*) FROM " . Cot::$db->treecatspage_id_cats . " WHERE tree_id = ?",
            [$tree['tree_id']]
        )->fetchColumn();

        $t->assign([
            'TREE_ID'           => $tree['tree_id'],
            'TREE_TITLE'        => htmlspecialchars($tree['tree_title']),
            'TREE_DESC'         => htmlspecialchars($tree['tree_description']),
            'TREE_COUNT_PAGES'  => !empty($tree['tree_count_pages']) ? 1 : 0,
            'TREE_COUNT_LABEL'  => !empty($tree['tree_count_pages'])
                ? Cot::$L['treecatspage_id_count_yes']
                : Cot::$L['treecatspage_id_count_no'],
            'TREE_WHITELIST_COUNT' => (int)$whitelistCount,
            'TREE_CODE'         => htmlspecialchars(
                "<!-- IF {PHP|cot_plugin_active('treecatspage_id')} -->\n"
                . "{PHP|cot_treecatspage_id_render(" . $tree['tree_id'] . ")}\n"
                . "<!-- ENDIF -->"
            ),
            'TREE_CODE_SIDEBAR' => htmlspecialchars(
                "<!-- IF {PHP|cot_plugin_active('treecatspage_id')} -->\n"
                . "{PHP|cot_treecatspage_id_render(" . $tree['tree_id'] . ", 'sidebar')}\n"
                . "<!-- ENDIF -->"
            ),
            'TREE_EDIT_URL'     => cot_url('admin', ['m' => 'other', 'p' => 'treecatspage_id', 'tab' => 'whitelist', 'tree' => $tree['tree_id']]),
            'TREE_RENAME_URL'   => cot_url('admin', ['m'=>'other','p'=>'treecatspage_id','tab'=>'trees','a'=>'edit_tree','tree'=>$tree['tree_id']]),
            'TREE_DELETE_URL'   => cot_confirm_url(cot_url('admin', ['m' => 'other', 'p' => 'treecatspage_id', 'tab' => 'trees', 'a' => 'delete', 'tree' => $tree['tree_id']])),
        ]);
        $t->parse('MAIN.TREES_ROW');
    }

    $t->assign([
        'ADD_TREE_FORM_URL' => cot_url('admin', ['m' => 'other', 'p' => 'treecatspage_id', 'tab' => 'trees', 'a' => 'add']),
        'ADD_TREE_TITLE'    => cot_inputbox('text', 'tree_title', '', 'class="form-control"'),
        'ADD_TREE_DESC'     => cot_inputbox('text', 'tree_desc', '', 'class="form-control"'),
    ]);
}

/* ========== Вкладка: Whitelist + иерархия ========== */
if ($tab == 'whitelist' && $treeId > 0) {
    $tree = Cot::$db->query(
        "SELECT * FROM " . Cot::$db->treecatspage_id_trees . " WHERE tree_id = ?",
        [$treeId]
    )->fetch();
    if (!$tree) {
        cot_die_message(404);
    }

    // Сохранение настройки подсчёта страниц с вкладки whitelist
    if ($a == 'save_tree_settings' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $countPages = cot_import('tree_count_pages', 'P', 'BOL') ? 1 : 0;
        Cot::$db->update(
            Cot::$db->treecatspage_id_trees,
            [
                'tree_count_pages' => $countPages,
                'tree_updated'     => time(),
            ],
            'tree_id = ?',
            [$treeId]
        );
        cot_message(Cot::$L['treecatspage_id_msg_tree_updated']);
        cot_redirect(cot_url('admin', ['m'=>'other','p'=>'treecatspage_id','tab'=>'whitelist','tree'=>$treeId], '', true));
    }

    // Добавление категорий
    if ($a == 'add_cats' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $cats = cot_import('cats', 'P', 'ARR');
        $parentForNew = cot_import('add_parent_id', 'P', 'INT');
        if ($parentForNew < 0) {
            $parentForNew = 0;
        }
        if ($parentForNew > 0) {
            $ok = Cot::$db->query(
                "SELECT COUNT(*) FROM " . Cot::$db->treecatspage_id_cats . " WHERE id = ? AND tree_id = ?",
                [$parentForNew, $treeId]
            )->fetchColumn();
            if (!$ok) {
                $parentForNew = 0;
            }
        }

        if (is_array($cats) && !empty($cats)) {
            $maxSort = (int)Cot::$db->query(
                "SELECT MAX(cat_sort) FROM " . Cot::$db->treecatspage_id_cats .
                " WHERE tree_id = ? AND parent_id = ?",
                [$treeId, $parentForNew]
            )->fetchColumn();
            $sort = $maxSort + 1;
            foreach ($cats as $catCode) {
                $catCode = trim((string)$catCode);
                if ($catCode === '' || !isset(Cot::$structure['page'][$catCode])) {
                    continue;
                }
                try {
                    Cot::$db->insert(Cot::$db->treecatspage_id_cats, [
                        'tree_id'   => $treeId,
                        'cat_code'  => $catCode,
                        'parent_id' => $parentForNew,
                        'cat_sort'  => $sort++,
                    ]);
                } catch (Exception $e) {
                    // дубликат
                }
            }
            Cot::$db->update(
                Cot::$db->treecatspage_id_trees,
                ['tree_updated' => time()],
                'tree_id = ?',
                [$treeId]
            );
            cot_message(Cot::$L['treecatspage_id_msg_cats_added']);
        }
        cot_redirect(cot_url('admin', ['m'=>'other','p'=>'treecatspage_id','tab'=>'whitelist','tree'=>$treeId], '', true));
    }

    // Удаление категории (и всех потомков)
    if ($a == 'delete_cat') {
        $catId = cot_import('cat_id', 'G', 'INT');
        if ($catId > 0) {
            $allRows = treecatspage_id_get_cats_raw($treeId);
            $toDelete = treecatspage_id_get_descendant_ids($catId, $allRows);
            $toDelete[] = $catId;
            $placeholders = implode(',', array_fill(0, count($toDelete), '?'));
            Cot::$db->delete(
                Cot::$db->treecatspage_id_cats,
                "id IN ($placeholders) AND tree_id = " . (int)$treeId,
                $toDelete
            );
            Cot::$db->update(
                Cot::$db->treecatspage_id_trees,
                ['tree_updated' => time()],
                'tree_id = ?',
                [$treeId]
            );
            cot_message(Cot::$L['treecatspage_id_msg_cat_deleted']);
        }
        cot_redirect(cot_url('admin', ['m'=>'other','p'=>'treecatspage_id','tab'=>'whitelist','tree'=>$treeId], '', true));
    }

    // Сохранение иерархии и сортировки
    if ($a == 'save_hierarchy' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $parents = cot_import('parent', 'P', 'ARR');
        $sorts   = cot_import('sort', 'P', 'ARR');
        $allRows = treecatspage_id_get_cats_raw($treeId);
        $byId = [];
        foreach ($allRows as $r) {
            $byId[(int)$r['id']] = $r;
        }

        if (is_array($parents) || is_array($sorts)) {
            $ids = array_unique(array_merge(
                is_array($parents) ? array_keys($parents) : [],
                is_array($sorts) ? array_keys($sorts) : []
            ));
            foreach ($ids as $id) {
                $id = (int)$id;
                if ($id <= 0 || !isset($byId[$id])) {
                    continue;
                }

                $newParent = isset($parents[$id]) ? (int)$parents[$id] : (int)$byId[$id]['parent_id'];
                $newSort   = isset($sorts[$id]) ? (int)$sorts[$id] : (int)$byId[$id]['cat_sort'];

                if ($newParent === $id) {
                    $newParent = 0;
                }
                $descendants = treecatspage_id_get_descendant_ids($id, $allRows);
                if (in_array($newParent, $descendants, true)) {
                    $newParent = 0;
                }
                if ($newParent > 0 && !isset($byId[$newParent])) {
                    $newParent = 0;
                }

                Cot::$db->update(
                    Cot::$db->treecatspage_id_cats,
                    [
                        'parent_id' => $newParent,
                        'cat_sort'  => $newSort,
                    ],
                    'id = ? AND tree_id = ?',
                    [$id, $treeId]
                );
            }
            Cot::$db->update(
                Cot::$db->treecatspage_id_trees,
                ['tree_updated' => time()],
                'tree_id = ?',
                [$treeId]
            );
            cot_message(Cot::$L['treecatspage_id_msg_hierarchy_saved']);
        }
        cot_redirect(cot_url('admin', ['m'=>'other','p'=>'treecatspage_id','tab'=>'whitelist','tree'=>$treeId], '', true));
    }

    $allRows = treecatspage_id_get_cats_raw($treeId);
    $hierarchy = treecatspage_id_get_hierarchy($treeId);

    $renderRows = function ($items, $level = 0) use (&$renderRows, $t, $treeId, $allRows) {
        foreach ($items as $row) {
            $itemId = (int)$row['id'];
            $catTitle = isset(Cot::$structure['page'][$row['cat_code']])
                ? Cot::$structure['page'][$row['cat_code']]['title']
                : Cot::$L['treecatspage_id_cat_not_found'];

            $forbidden = treecatspage_id_get_descendant_ids($itemId, $allRows);
            $forbidden[] = $itemId;

            $parentOptions = '<option value="0"' . ((int)$row['parent_id'] === 0 ? ' selected' : '') . '>'
                . Cot::$L['treecatspage_id_top_level'] . '</option>';
            foreach ($allRows as $opt) {
                if (in_array((int)$opt['id'], $forbidden, true)) {
                    continue;
                }
                $optTitle = isset(Cot::$structure['page'][$opt['cat_code']])
                    ? Cot::$structure['page'][$opt['cat_code']]['title']
                    : $opt['cat_code'];
                $selected = ((int)$opt['id'] === (int)$row['parent_id']) ? ' selected' : '';
                $parentOptions .= '<option value="' . (int)$opt['id'] . '"' . $selected . '>'
                    . htmlspecialchars($optTitle . ' (' . $opt['cat_code'] . ')') . '</option>';
            }

            $indent = str_repeat('— ', $level);

            $t->assign([
                'CAT_ID'            => $itemId,
                'CAT_CODE'          => htmlspecialchars($row['cat_code']),
                'CAT_TITLE'         => htmlspecialchars($catTitle),
                'CAT_INDENT'        => $indent,
                'CAT_LEVEL'         => $level,
                'CAT_SORT'          => (int)$row['cat_sort'],
                'CAT_PARENT_SELECT' => $parentOptions,
                'CAT_DELETE_URL'    => cot_confirm_url(cot_url('admin', [
                    'm'=>'other','p'=>'treecatspage_id','tab'=>'whitelist',
                    'tree'=>$treeId,'a'=>'delete_cat','cat_id'=>$itemId
                ])),
            ]);
            $t->parse('MAIN.WHITELIST_ROW');

            if (!empty($row['children'])) {
                $renderRows($row['children'], $level + 1);
            }
        }
    };

    if (!empty($hierarchy)) {
        $renderRows($hierarchy, 0);
    }

    $existingCodes = array_column($allRows, 'cat_code');
    $availableCats = [];
    if (!empty(Cot::$structure['page'])) {
        foreach (Cot::$structure['page'] as $code => $cat) {
            if (!in_array($code, $existingCodes, true)) {
                $availableCats[$code] = $cat['title'] . ' (' . $code . ')';
            }
        }
    }

    $addParentOptions = '<option value="0">' . Cot::$L['treecatspage_id_top_level'] . '</option>';
    foreach ($allRows as $opt) {
        $optTitle = isset(Cot::$structure['page'][$opt['cat_code']])
            ? Cot::$structure['page'][$opt['cat_code']]['title']
            : $opt['cat_code'];
        $addParentOptions .= '<option value="' . (int)$opt['id'] . '">'
            . htmlspecialchars($optTitle . ' (' . $opt['cat_code'] . ')') . '</option>';
    }

    $countChecked = !empty($tree['tree_count_pages']);
    $t->assign([
        'TREE_ID'              => $treeId,
        'TREE_TITLE'           => htmlspecialchars($tree['tree_title']),
        'TREE_COUNT_YES'       => $countChecked ? 'checked="checked"' : '',
        'TREE_COUNT_NO'        => !$countChecked ? 'checked="checked"' : '',
        'SAVE_TREE_SETTINGS_URL' => cot_url('admin', ['m'=>'other','p'=>'treecatspage_id','tab'=>'whitelist','tree'=>$treeId,'a'=>'save_tree_settings']),
        'ADD_CATS_FORM_URL'    => cot_url('admin', ['m'=>'other','p'=>'treecatspage_id','tab'=>'whitelist','tree'=>$treeId,'a'=>'add_cats']),
        'SAVE_HIERARCHY_URL'   => cot_url('admin', ['m'=>'other','p'=>'treecatspage_id','tab'=>'whitelist','tree'=>$treeId,'a'=>'save_hierarchy']),
        'ADD_CATS_SELECT'      => cot_selectbox(
            '',
            'cats[]',
            array_keys($availableCats),
            array_values($availableCats),
            false,
            'class="form-select" multiple size="10"'
        ),
        'ADD_PARENT_SELECT'    => '<select name="add_parent_id" class="form-select">' . $addParentOptions . '</select>',
        'HAS_WHITELIST'        => !empty($allRows) ? 1 : 0,
        'HAS_AVAILABLE'        => !empty($availableCats) ? 1 : 0,
    ]);
}

/* ========== Вкладка: Переводы (i18n) ========== */
if ($tab == 'i18n' && $treeId > 0) {
    $tree = Cot::$db->query(
        "SELECT * FROM " . Cot::$db->treecatspage_id_trees . " WHERE tree_id = ?",
        [$treeId]
    )->fetch();
    if (!$tree) {
        cot_die_message(404);
    }

    if ($a == 'save_i18n' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        if ($i18nActive && !empty($activeLangs)) {
            foreach ($activeLangs as $lang) {
                if ($lang === $defaultLang) continue;
                $titleVal = cot_import('title_' . $lang, 'P', 'TXT', 255);
                $descVal  = cot_import('desc_' . $lang, 'P', 'TXT');
                treecatspage_id_i18n_save($treeId, 'tree_title', $lang, $titleVal);
                treecatspage_id_i18n_save($treeId, 'tree_description', $lang, $descVal);
            }
            cot_message(Cot::$L['treecatspage_id_msg_i18n_saved']);
        }
        cot_redirect(cot_url('admin', ['m'=>'other','p'=>'treecatspage_id','tab'=>'i18n','tree'=>$treeId], '', true));
    }

    if ($i18nActive && !empty($activeLangs)) {
        foreach ($activeLangs as $lang) {
            if ($lang === $defaultLang) continue;
            $t->assign([
                'LANG_CODE'   => htmlspecialchars($lang),
                'TITLE_VALUE' => htmlspecialchars(treecatspage_id_i18n_load($treeId, 'tree_title', $lang) ?? ''),
                'DESC_VALUE'  => htmlspecialchars(treecatspage_id_i18n_load($treeId, 'tree_description', $lang) ?? ''),
            ]);
            $t->parse('MAIN.I18N_LANG_FIELDS');
        }
    }

    $t->assign([
        'TREE_ID'       => $treeId,
        'TREE_TITLE'    => htmlspecialchars($tree['tree_title']),
        'TREE_DESC'     => htmlspecialchars($tree['tree_description']),
        'I18N_ACTIVE'   => $i18nActive ? 1 : 0,
        'I18N_FORM_URL' => cot_url('admin', ['m'=>'other','p'=>'treecatspage_id','tab'=>'i18n','tree'=>$treeId,'a'=>'save_i18n']),
    ]);
}

cot_display_messages($t);
$t->parse('MAIN');
$pluginBody = $t->text('MAIN');
