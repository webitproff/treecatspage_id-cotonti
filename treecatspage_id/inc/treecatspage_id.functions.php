<?php
/**
 * Tree Cats Page ID plugin - helper functions
 *
 * File: treecatspage_id.functions.php
 * Path: plugins/treecatspage_id/inc/treecatspage_id.functions.php
 *
 * Tree Cats Page ID plugin for Cotonti v1.+, PHP 8.4+, MySQL 8.4
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
require_once cot_langfile('page', 'module');
require_once cot_incfile('page', 'module');
require_once cot_incfile('page', 'module', 'functions');
require_once cot_incfile('extrafields');

// Регистрируем таблицы плагина
Cot::$db->registerTable('treecatspage_id_trees');
Cot::$db->registerTable('treecatspage_id_cats');
Cot::$db->registerTable('treecatspage_id_i18n');

/**
 * Проверяет, включена ли мультиязычность для плагина
 *
 * @return bool
 */
function treecatspage_id_i18n_enabled()
{
    return !empty(Cot::$cfg['plugin']['treecatspage_id']['treecatspage_id_i18n_use']);
}

/**
 * Возвращает массив активных дополнительных языков
 *
 * @return array
 */
function treecatspage_id_get_active_langs()
{
    $defaultLang = !empty(Cot::$cfg['plugin']['treecatspage_id']['treecatspage_id_i18n_lang_code_default'])
        ? Cot::$cfg['plugin']['treecatspage_id']['treecatspage_id_i18n_lang_code_default']
        : Cot::$cfg['defaultlang'];

    $activeLangs = [];

    $pairs = [
        ['use' => 'treecatspage_id_i18n_lang_code_first_use',  'code' => 'treecatspage_id_i18n_lang_code_first'],
        ['use' => 'treecatspage_id_i18n_lang_code_second_use', 'code' => 'treecatspage_id_i18n_lang_code_second'],
    ];

    foreach ($pairs as $pair) {
        if (
            !empty(Cot::$cfg['plugin']['treecatspage_id'][$pair['use']])
            && !empty(Cot::$cfg['plugin']['treecatspage_id'][$pair['code']])
        ) {
            $lang = Cot::$cfg['plugin']['treecatspage_id'][$pair['code']];
            if ($lang !== $defaultLang) {
                $activeLangs[] = $lang;
            }
        }
    }

    return $activeLangs;
}

/**
 * Возвращает код языка по умолчанию
 *
 * @return string
 */
function treecatspage_id_get_default_lang()
{
    return !empty(Cot::$cfg['plugin']['treecatspage_id']['treecatspage_id_i18n_lang_code_default'])
        ? Cot::$cfg['plugin']['treecatspage_id']['treecatspage_id_i18n_lang_code_default']
        : Cot::$cfg['defaultlang'];
}

/**
 * Загружает перевод значения поля дерева
 *
 * @param int    $treeId
 * @param string $fieldName
 * @param string $lang
 * @return string|null
 */
function treecatspage_id_i18n_load($treeId, $fieldName, $lang)
{
    return Cot::$db->query(
        "SELECT value FROM " . Cot::$db->treecatspage_id_i18n .
        " WHERE tree_id = ? AND field_name = ? AND lang = ?",
        [(int)$treeId, $fieldName, $lang]
    )->fetchColumn() ?: null;
}

/**
 * Сохраняет или удаляет перевод
 *
 * @param int    $treeId
 * @param string $fieldName
 * @param string $lang
 * @param mixed  $value
 */
function treecatspage_id_i18n_save($treeId, $fieldName, $lang, $value)
{
    if ($value === null || $value === '') {
        Cot::$db->delete(
            Cot::$db->treecatspage_id_i18n,
            "tree_id = ? AND field_name = ? AND lang = ?",
            [(int)$treeId, $fieldName, $lang]
        );
    } else {
        Cot::$db->query(
            "INSERT INTO " . Cot::$db->treecatspage_id_i18n .
            " (tree_id, field_name, lang, value)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE value = VALUES(value)",
            [(int)$treeId, $fieldName, $lang, $value]
        );
    }
}

/**
 * Возвращает значение поля дерева с учётом i18n
 *
 * @param int    $treeId
 * @param string $fieldName
 * @param mixed  $originalValue
 * @return mixed
 */
function treecatspage_id_i18n_get_value($treeId, $fieldName, $originalValue)
{
    if (!treecatspage_id_i18n_enabled()) {
        return $originalValue;
    }

    $defaultLang = treecatspage_id_get_default_lang();
    $currentLang = Cot::$usr['lang'] ?? $defaultLang;

    if ($currentLang === $defaultLang) {
        return $originalValue;
    }

    $translated = treecatspage_id_i18n_load($treeId, $fieldName, $currentLang);
    return $translated !== null ? $translated : $originalValue;
}

/**
 * Получает все записи whitelist дерева (сырые строки)
 *
 * @param int $treeId
 * @return array
 */
function treecatspage_id_get_cats_raw($treeId)
{
    $treeId = (int)$treeId;
    if ($treeId <= 0) {
        return [];
    }

    return Cot::$db->query(
        "SELECT * FROM " . Cot::$db->treecatspage_id_cats .
        " WHERE tree_id = ? ORDER BY parent_id ASC, cat_sort ASC, cat_code ASC",
        [$treeId]
    )->fetchAll();
}

/**
 * Получает whitelist-категории как плоский список кодов
 *
 * @param int $treeId
 * @return array
 */
function treecatspage_id_get_whitelist($treeId)
{
    $rows = treecatspage_id_get_cats_raw($treeId);
    $codes = [];
    foreach ($rows as $row) {
        $codes[] = $row['cat_code'];
    }
    return $codes;
}

/**
 * Строит вложенный массив иерархии из parent_id
 *
 * @param int $treeId
 * @return array  [ ['id'=>, 'cat_code'=>, 'children'=>[...]], ... ]
 */
function treecatspage_id_get_hierarchy($treeId)
{
    $rows = treecatspage_id_get_cats_raw($treeId);
    if (empty($rows)) {
        return [];
    }

    $byParent = [];
    foreach ($rows as $row) {
        $byParent[(int)$row['parent_id']][] = $row;
    }

    $build = function ($parentId) use (&$build, $byParent) {
        $result = [];
        if (!empty($byParent[$parentId])) {
            foreach ($byParent[$parentId] as $item) {
                $item['children'] = $build((int)$item['id']);
                $result[] = $item;
            }
        }
        return $result;
    };

    return $build(0);
}

/**
 * Возвращает ID всех потомков записи (для защиты от циклов в админке)
 *
 * @param int   $itemId
 * @param array $allRows  все строки дерева
 * @return array
 */
function treecatspage_id_get_descendant_ids($itemId, array $allRows)
{
    $ids = [];
    $queue = [(int)$itemId];
    $byParent = [];
    foreach ($allRows as $row) {
        $byParent[(int)$row['parent_id']][] = (int)$row['id'];
    }
    while (!empty($queue)) {
        $current = array_shift($queue);
        if (!empty($byParent[$current])) {
            foreach ($byParent[$current] as $childId) {
                $ids[] = $childId;
                $queue[] = $childId;
            }
        }
    }
    return $ids;
}

/**
 * Получает данные дерева
 *
 * @param int $treeId
 * @return array|false
 */
function treecatspage_id_get_tree($treeId)
{
    $treeId = (int)$treeId;
    if ($treeId <= 0) {
        return false;
    }

    return Cot::$db->query(
        "SELECT * FROM " . Cot::$db->treecatspage_id_trees . " WHERE tree_id = ?",
        [$treeId]
    )->fetch();
}

/**
 * Формирует иерархическое дерево категорий по собственной иерархии (parent_id)
 * Подсчёт страниц — один SQL, accumulate снизу вверх по custom-иерархии
 *
 * @param int    $treeId
 * @param int    $parentItemId  ID записи в cot_treecatspage_id_cats (0 = корень)
 * @param mixed  $selected
 * @param int    $level
 * @param string $template
 * @return string|false
 */
function cot_treecatspage_id_build($treeId, $parentItemId = 0, $selected = '', $level = 0, $template = '')
{
    global $structure, $cfg, $db, $sys, $cot_extrafields, $db_structure, $db_pages;
    global $i18n_notmain, $i18n_locale, $i18n_write, $i18n_admin, $i18n_read, $db_i18n_pages;
    global $L;

    $treeId = (int)$treeId;
    $parentItemId = (int)$parentItemId;
    if ($treeId <= 0) {
        return false;
    }

    /* ============================================================
     * STATIC-КЭШ на treeId
     * ============================================================ */
    static $cache = [];

    if (!isset($cache[$treeId])) {
        $rows = treecatspage_id_get_cats_raw($treeId);
        if (empty($rows)) {
            $cache[$treeId] = null;
            return false;
        }

        // Настройка подсчёта страниц для этого дерева
        $treeRow = treecatspage_id_get_tree($treeId);
        $doCount = !empty($treeRow['tree_count_pages']);

        // Карта id → row
        $byId = [];
        $byParent = [];
        $codes = [];
        foreach ($rows as $row) {
            $byId[(int)$row['id']] = $row;
            $byParent[(int)$row['parent_id']][] = $row;
            $codes[] = $row['cat_code'];
        }
        $codes = array_unique($codes);

        $counts = array_fill_keys($codes, 0);
        $total = 0;

        // SQL-запрос только если для дерева включён подсчёт
        if ($doCount && $db->tableExists($db_pages) && !empty($codes)) {
            $placeholders = implode(',', array_fill(0, count($codes), '?'));
            $sql = $db->query(
                "SELECT page_cat, COUNT(*) AS cnt
                 FROM $db_pages
                 WHERE page_state = 0 AND page_cat IN ($placeholders)
                 GROUP BY page_cat",
                $codes
            );
            while ($r = $sql->fetch()) {
                if (isset($counts[$r['page_cat']])) {
                    $counts[$r['page_cat']] = (int)$r['cnt'];
                    $total += (int)$r['cnt'];
                }
            }

            // Accumulate снизу вверх по custom-иерархии (parent_id)
            $acc = $counts;
            $accumulate = function ($itemId) use (&$accumulate, &$acc, $byId, $byParent) {
                $code = $byId[$itemId]['cat_code'] ?? null;
                if ($code === null) {
                    return 0;
                }
                $sum = $acc[$code] ?? 0;
                if (!empty($byParent[$itemId])) {
                    foreach ($byParent[$itemId] as $child) {
                        $sum += $accumulate((int)$child['id']);
                    }
                }
                $acc[$code] = $sum;
                return $sum;
            };

            if (!empty($byParent[0])) {
                foreach ($byParent[0] as $root) {
                    $accumulate((int)$root['id']);
                }
            }
            $counts = $acc;
        }

        $cache[$treeId] = [
            'byId'     => $byId,
            'byParent' => $byParent,
            'counts'   => $counts,
            'total'    => $total,
            'doCount'  => $doCount,
        ];
    }

    if ($cache[$treeId] === null) {
        return false;
    }

    $data = $cache[$treeId];
    $children = $data['byParent'][$parentItemId] ?? [];

    if (empty($children)) {
        return false;
    }

    // Шаблон
    $extTplFile = cot_tplfile(
        ['treecatspage_id', 'page', 'tree', $template],
        'plug',
        true
    );
    $t1 = new XTemplate($extTplFile);

    /* === Hook === */
    foreach (cot_getextplugins('treecatspage_id.tree.main') as $pl) {
        include $pl;
    }
    /* ===== */

    $parentCode = '';
    $title = $desc = $icon = '';
    $count = 0;
    if ($parentItemId > 0 && isset($data['byId'][$parentItemId])) {
        $parentCode = $data['byId'][$parentItemId]['cat_code'];
        if (isset($structure['page'][$parentCode])) {
            $title = $structure['page'][$parentCode]['title'];
            $desc  = $structure['page'][$parentCode]['desc'];
            $icon  = $structure['page'][$parentCode]['icon'];
        }
        $count = $data['counts'][$parentCode] ?? 0;
    }

    $t1->assign([
        'TITLE'       => htmlspecialchars($title),
        'DESC'        => $desc,
        'COUNT'       => $count,
        'ICON'        => $icon,
        'HREF'        => $parentCode !== '' ? cot_url('page', ['c' => $parentCode]) : '',
        'LEVEL'       => $level,
        'TOTAL_COUNT' => $data['total'],
        'TREE_ID'     => $treeId,
        'DO_COUNT'    => !empty($data['doCount']) ? 1 : 0,
    ]);

    $jj = 0;
    $extp = cot_getextplugins('treecatspage_id.tree.loop');
    $i18n_enabled = $i18n_read;

    foreach ($children as $row) {
        $jj++;
        $code = $row['cat_code'];
        $itemId = (int)$row['id'];

        $catTitle = isset($structure['page'][$code]) ? $structure['page'][$code]['title'] : $code;
        $catDesc  = isset($structure['page'][$code]) ? $structure['page'][$code]['desc'] : '';
        $catIcon  = isset($structure['page'][$code]) ? $structure['page'][$code]['icon'] : '';
        $catCount = $data['counts'][$code] ?? 0;

        $urlparams = ['c' => $code];

        // Есть ли дочерние в custom-иерархии
        $hasChildren = !empty($data['byParent'][$itemId]);

        $t1->assign([
            'ROW_ID'       => $code,
            'ROW_ITEM_ID'  => $itemId,
            'ROW_TITLE'    => htmlspecialchars($catTitle),
            'ROW_DESC'     => $catDesc,
            'ROW_COUNT'    => $catCount,
            'ROW_ICON'     => $catIcon,
            'ROW_HREF'     => cot_url('page', $urlparams),
            'ROW_SELECTED' => ((is_array($selected) && in_array($code, $selected)) || $code == $selected) ? 1 : 0,
            'ROW_SUBCAT'   => $hasChildren
                ? cot_treecatspage_id_build($treeId, $itemId, $selected, $level + 1, $template)
                : '',
            'ROW_LEVEL'    => $level,
            'ROW_ODDEVEN'  => cot_build_oddeven($jj),
            'ROW_JJ'       => $jj,
            'TREE_ID'      => $treeId,
            'DO_COUNT'     => !empty($data['doCount']) ? 1 : 0,
        ]);

        if (!empty($cot_extrafields[$db_structure]) && isset($structure['page'][$code])) {
            foreach ($cot_extrafields[$db_structure] as $exfld) {
                $uname = strtoupper($exfld['field_name']);
                $t1->assign([
                    'ROW_'.$uname.'_TITLE' =>
                        $L['structure_'.$exfld['field_name'].'_title'] ?? $exfld['field_description'],
                    'ROW_'.$uname =>
                        cot_build_extrafields_data('structure', $exfld, $structure['page'][$code][$exfld['field_name']] ?? ''),
                    'ROW_'.$uname.'_VALUE' =>
                        $structure['page'][$code][$exfld['field_name']] ?? '',
                ]);
            }
        }

        if ($i18n_enabled && !empty($i18n_notmain) && function_exists('cot_i18n_get_cat')) {
            $x_i18n = cot_i18n_get_cat($code, $i18n_locale);
            if ($x_i18n) {
                if (empty($cfg['plugin']['i18n']['omitmain']) || $i18n_locale != $cfg['defaultlang']) {
                    $urlparams['l'] = $i18n_locale;
                }
                $t1->assign([
                    'ROW_URL'   => cot_url('page', $urlparams),
                    'ROW_TITLE' => $x_i18n['title'],
                    'ROW_DESC'  => $x_i18n['desc'],
                ]);
            }
        }

        foreach ($extp as $pl) {
            include $pl;
        }

        $t1->parse('MAIN.CATS');
    }

    $t1->parse('MAIN');
    return $t1->text('MAIN');
}

/**
 * Удобный вызов рендера дерева по ID
 *
 * @param int    $treeId
 * @param string $template
 * @return string
 */
function cot_treecatspage_id_render($treeId, $template = '')
{
    return cot_treecatspage_id_build((int)$treeId, 0, '', 0, $template) ?: '';
}
