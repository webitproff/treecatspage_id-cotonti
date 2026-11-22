# Tree Cats Page ID

**Tree Cats Page ID** is a plugin for Cotonti CMF that allows you to create multiple independent category trees for the **Page** module. For each tree, an administrator selects categories from the site's existing structure, defines a custom hierarchy, and sets the display order.

The plugin does not create new Cotonti categories or modify the standard `page` hierarchy. It creates a separate navigation view of the selected categories.

- Version: **1.2.0**
- Date: **2026-11-23**
- Author: **[Webitproff](https://github.com/webitproff)**
- License: **BSD**
- Required Cotonti module: **page**
- The custom functions file must also be included: **[functions.custom.php](https://github.com/webitproff/functions.custom.php-cotonti)**
- The latest plugin source code is available in the **[GitHub repository](https://github.com/webitproff/treecatspage_id-cotonti)**

---

## Contents

1. [Plugin overview](#overview-en)
   - [What the plugin is for](#overview-purpose-en)
   - [Key concepts](#overview-concepts-en)
   - [What the plugin does and does not do](#overview-scope-en)
2. [User guide](#user-guide-en)
   - [Opening the administration panel](#user-admin-en)
   - [Creating a tree](#user-create-en)
   - [Adding categories to the whitelist](#user-whitelist-en)
   - [Building and changing the hierarchy](#user-hierarchy-en)
   - [Sorting order](#user-sort-en)
   - [Editing and deleting a tree](#user-edit-delete-en)
   - [Translating the tree title and description](#user-i18n-en)
   - [Tree organization examples](#user-examples-en)
   - [Displaying a tree on the website](#user-output-en)
3. [Technical details and implementation](#technical-en)
   - [Plugin files and their purpose](#technical-files-en)
   - [Database tables](#technical-db-en)
   - [Tree-building business logic](#technical-logic-en)
   - [Page counting](#technical-count-en)
   - [Theme template integration](#technical-templates-en)
   - [Template variables](#technical-vars-en)
   - [Multilingual support](#technical-i18n-en)
   - [Dependencies and compatibility](#technical-dependencies-en)
   - [Recommendations and warnings](#technical-notes-en)
   - [Plugin and data removal](#technical-uninstall-en)

---

<a id="overview-en"></a>
# 1. Plugin overview

<a id="overview-purpose-en"></a>
## 1.1. What the plugin is for

In Cotonti, the **Page** module has a category structure. It is commonly used to organize website pages. However, a website may sometimes need several different navigation views based on the same categories.

For example, a site may need all of the following at the same time:

- a complete section tree for a catalog page;
- a compact tree for a sidebar;
- a separate tree for a topical block;
- a custom category grouping for a particular page or area of the site.

**Tree Cats Page ID** provides this functionality: an administrator creates a separate tree, selects the required Page categories, and arranges them in a custom order, independently of the parent-child relationships in Cotonti's standard category structure.

An analogy: imagine that the standard category structure is a large filing cabinet with folders. The plugin lets you create several different tables of contents or navigation routes using selected folders. The folders themselves stay where they are, but each table of contents can arrange them in its own way.

<a id="overview-concepts-en"></a>
## 1.2. Key concepts

| Term | Plain-language explanation |
|---|---|
| Tree | A separate navigation set with its own numeric ID, title, and description |
| Page category | An existing category of Cotonti's Page module |
| Whitelist | The categories an administrator has included in a particular tree |
| Parent | The category under which other categories are placed in this tree |
| Child category | A category one level below its parent |
| Sorting | The numeric order of categories among items at the same hierarchy level |
| Tree ID | The numeric identifier used to call the tree from a template |

A category can be included in more than one tree. The database uniqueness constraint is based on the combination of `tree_id` and `cat_code`, not on `cat_code` alone.

<a id="overview-scope-en"></a>
## 1.3. What the plugin does and does not do

**The plugin can:**

- store multiple independent trees;
- select Page categories for each tree's whitelist;
- define custom parent → child → grandchild relationships;
- set the order of items within each level;
- optionally count published pages in categories, separately for each tree;
- store translations of a tree's title and description for configured additional languages;
- render a tree using `cot_treecatspage_id_render()` and the plugin's templates.

**Important limitations:**

- the plugin does not create, rename, or delete Page module categories themselves;
- it does not move pages between categories;
- the tree's custom hierarchy is separate from Cotonti's standard category hierarchy;
- only codes of existing Page categories can be added to the whitelist;
- counts are based on pages found in the whitelisted categories and are aggregated upward through the tree's custom hierarchy.

---

<a id="user-guide-en"></a>
# 2. User guide

<a id="user-admin-en"></a>
## 2.1. Opening the administration panel

The administrative page is connected through Cotonti's `tools` hook. The path specified in the source-code comments is:

`admin.php?m=other&p=treecatspage_id`

Open your website's administration panel and go to the **Tree Cats Page ID** plugin page. The interface has three tabs:

1. **Trees** — create, rename/edit, and delete trees.
2. **Whitelist & hierarchy** — add categories, assign parents, and set sorting.
3. **Translations** — translate a tree's title and description if multilingual support is enabled in the plugin settings.

If the whitelist or translations tab is opened without a selected tree, the code attempts to redirect to the first tree by ID. If no trees exist, it returns to the tree list tab.

<a id="user-create-en"></a>
## 2.2. Creating a tree

1. Open the **Trees** tab.
2. Find the **Create tree** form.
3. Enter a **Title**. This field is required: a record is created only if the title is not empty.
4. Enter a **Description**, if needed.
5. Choose the **Count items in category** setting:
   - **Yes** — the plugin runs an SQL query to count pages;
   - **No** — the SQL page-count query is skipped for this tree, and the counters in the template are set to zero.
6. Click **Add**.

After saving, the tree appears in the table. The plugin assigns it a numeric `tree_id`. Make a note of this ID: you will need it when inserting the tree into a theme template.

When a tree is created, page counting is enabled by default (the **Yes** radio button is selected in the form).

<a id="user-whitelist-en"></a>
## 2.3. Adding categories to the whitelist

After creating a tree, add categories to it.

1. In the tree list, click **Edit** next to the required tree. This link opens the whitelist and hierarchy tab.
2. Find the **Available categories** block.
3. In the **Add as a child of** field, select where the new categories should be placed:
   - **Root (top level)** — the new categories become top-level items;
   - a specific category — the new categories are added as its children.
4. In **Select categories**, select one or more categories.
5. Click **Add selected**.

The available list contains categories from the `page` structure that have not yet been added to this tree's whitelist. Categories already included in this tree are not offered again.

**Example.** Suppose the site's standard structure contains the categories “News”, “Articles”, “Reviews”, and “Guides”. If you select only “Articles” and “Reviews” for the “Content” tree, the other categories will not be included in that tree. They are not deleted from the website and remain available for other trees.

<a id="user-hierarchy-en"></a>
## 2.4. Building and changing the hierarchy

The upper part of the **Whitelist & hierarchy** tab displays a table of categories already added to the tree.

Each row provides:

- **Sort** — the numeric order value;
- **Title** — the category title from Cotonti's structure;
- **Code** — the category's system code;
- **Parent** — select the root or another category from this tree;
- **Delete** — remove the category from the whitelist.

To change the hierarchy:

1. Find the row for the category you want to move.
2. In the **Parent** column, select:
   - `— Root (top level) —` to make the category a root item;
   - another category to make it a child of that category.
3. Click **Save hierarchy and sorting**.

The levels are built from relationships between whitelist records, not from the category's standard parent in Cotonti's category structure.

**Example of a three-level tree:**

```text
Content
├── Articles
│   ├── Analysis
│   └── Interviews
└── Reviews
    └── Technology
```

In this example, “Content” is the illustrative name of the tree itself; the items inside it are existing Page categories. “Analysis” and “Interviews” are children of “Articles”, while “Technology” is a child of “Reviews”.

When selecting a parent, the interface excludes the category itself and its descendants from the list. The server-side logic also checks for invalid relationships: a category cannot be assigned as the parent of itself or one of its own descendants. This prevents circular hierarchies.

<a id="user-sort-en"></a>
## 2.5. Sorting order

The **Sort** field accepts a numeric value. It determines the order of items among categories that share the same parent.

For example, the root categories could be assigned these values:

| Category | Parent | Sort |
|---|---|---:|
| News | Root | 10 |
| Articles | Root | 20 |
| Reviews | Root | 30 |

Child categories have their own order within each parent. Changes are saved using the **Save hierarchy and sorting** button.

When reading records, the code sorts by `parent_id`, then `cat_sort`, then `cat_code`. For a predictable order, it is therefore recommended to use different sort values for neighboring items at the same level.

<a id="user-edit-delete-en"></a>
## 2.6. Editing and deleting a tree

### Changing the title, description, and counting option

1. Open the **Trees** tab.
2. Click **Rename** in the row of the required tree.
3. Change the title and/or description.
4. Change the counting option if necessary.
5. Click **Save**.

The counting option can also be changed on the whitelist tab. A separate block with **Yes/No** radio buttons is located above the category table.

### Changing the contents of a tree

On the whitelist tab, you can add available categories and change their parents and sorting order. The **Delete** button in a category row removes that category from the current whitelist.

**Warning:** when a category is removed from the whitelist, the code also deletes all of its child and deeper descendant records from this tree. The Cotonti categories themselves and the website pages are not deleted.

### Deleting an entire tree

1. Open the **Trees** tab.
2. Click **Delete** in the row of the required tree.
3. Confirm the action if the browser or interface asks you to do so.

When a tree is deleted, the code removes its related translation and whitelist records, and then deletes the tree record itself. This removes the tree's settings from the plugin tables; Page categories and website pages are not deleted.

<a id="user-i18n-en"></a>
## 2.7. Translating the tree title and description

Translations apply to the **tree title and description**, not to the titles of Page categories.

Multilingual support is enabled in the plugin settings. The code provides the following options:

| Setting | Purpose |
|---|---|
| Enable multilingual support | Enables or disables translation handling |
| Default language code | The language for which separate translation records are not stored |
| Use first additional language | Enables the first additional language |
| First additional language code | For example, `en` |
| Use second additional language | Enables the second additional language |
| Second additional language code | For example, `uk` |

Workflow:

1. Open the plugin settings in the Cotonti administration area.
2. Enable multilingual support.
3. Specify the default language code.
4. If needed, enable the first and/or second additional language and enter their codes.
5. Open the **Translations** tab for the required tree.
6. Enter the title and description for the additional languages shown.
7. Click **Save**.

The tree's regular fields are used for the default language; no separate translation record is created for it. If a translation for the current additional language is empty, the value-retrieval function returns the original value from the tree.

<a id="user-examples-en"></a>
## 2.8. Tree organization examples

One website can have multiple trees that use different combinations of the same categories.

**Example A — navigation for all content**

```text
Content
├── News
├── Articles
│   ├── Analysis
│   └── Interviews
└── Reviews
```

**Example B — compact sidebar navigation**

```text
Website sections
├── News
└── Reviews
```

**Example C — topical collection**

```text
Technology
├── Computers
├── Smartphones
└── Software
```

These examples illustrate the organizational principle. In actual use, select category codes that already exist in your website's Page structure.

<a id="user-output-en"></a>
## 2.9. Displaying a tree on the website

The **Trees** table displays ready-to-use code snippets for inserting a tree into a theme template. Use the ID of the required tree.

Basic call:

```html
{PHP|cot_treecatspage_id_render(1)}
```

Call using the `sidebar` template:

```html
{PHP|cot_treecatspage_id_render(1, 'sidebar')}
```

Replace `1` with the actual tree ID shown in the administration table. You can place the call in an appropriate theme `.tpl` template — for example, a page template or sidebar template, depending on where the navigation should appear.

The administration list also generates a version that checks whether the plugin is active:

```html
<!-- IF {PHP|cot_plugin_active('treecatspage_id')} -->
{PHP|cot_treecatspage_id_render(1)}
<!-- ENDIF -->
```

Insert the snippet into the appropriate theme template on your website. The function is available for global use.

---

<a id="technical-en"></a>
# 3. Technical details and implementation

<a id="technical-files-en"></a>
## 3.1. Plugin files and their purpose

| Component | Purpose |
|---|---|
| `treecatspage_id.setup.php` | Extension metadata and registration of plugin settings in Cotonti format |
| `treecatspage_id.global.php` | Includes the plugin's main include file through `cot_incfile()` |
| `treecatspage_id.admin.php` | Administrative logic for the trees, whitelist, and translations tabs; connected through the `tools` hook |
| `inc/treecatspage_id.functions.php` | Helper functions, data retrieval, tree building, and rendering |
| `treecatspage_id.admin.tpl` | Administrative page template |
| `lang/treecatspage_id.ru.lang.php` | Russian language strings |
| `lang/treecatspage_id.en.lang.php` | English language strings |
| `tpl/treecatspage_id.page.tree.sidebar.tpl` | Template for rendering the tree as vertical sidebar navigation |
| `treecatspage_id.tree.js` | Expands the active branch and remembers expanded items in the browser |
| `setup/treecatspage_id.install.sql` | SQL for creating the plugin tables |
| `setup/treecatspage_id.uninstall.sql` | SQL for deleting the plugin tables |

<a id="technical-db-en"></a>
## 3.2. Database tables

The plugin uses three custom tables. The SQL creates them with `InnoDB`, the `utf8mb4` character set, and the `utf8mb4_unicode_ci` collation.

### `cot_treecatspage_id_trees`

Stores the general data for each tree:

| Field | Purpose |
|---|---|
| `tree_id` | Primary key; auto-incrementing tree ID |
| `tree_title` | Title |
| `tree_description` | Description |
| `tree_count_pages` | Page-counting flag: `1` or `0` |
| `tree_created` | Creation time as a Unix timestamp |
| `tree_updated` | Last update time as a Unix timestamp |

### `cot_treecatspage_id_cats`

Stores the categories included in the whitelist and their custom hierarchy:

| Field | Purpose |
|---|---|
| `id` | Whitelist row ID |
| `tree_id` | ID of the tree to which the row belongs |
| `cat_code` | Page category code |
| `parent_id` | Parent row ID in this same table; `0` means root |
| `cat_sort` | Sorting order |

The `uniq_tree_cat` unique index prevents the same `cat_code` from being added more than once to the same tree. The same category code can be used in another tree because `tree_id` is part of the unique key.

### `cot_treecatspage_id_i18n`

Stores translations of tree fields:

| Field | Purpose |
|---|---|
| `tree_id` | Tree ID |
| `field_name` | Name of the translatable field; in the current logic, `tree_title` or `tree_description` |
| `lang` | Two-character language code |
| `value` | Translated value |

The primary key consists of `tree_id`, `field_name`, and `lang`. A foreign key references the trees table and uses `ON DELETE CASCADE`.

<a id="technical-logic-en"></a>
## 3.3. Tree-building business logic

### Registration and loading

`treecatspage_id.global.php` includes the plugin's include file. The functions file:

- checks that the `COT_CODE` constant exists;
- includes the plugin's language files and the Page module's language files;
- includes the Page module files and its functions;
- includes `extrafields`;
- registers the plugin's three tables through `Cot::$db->registerTable()`.

### Retrieving categories

The `treecatspage_id_get_cats_raw($treeId)` function selects whitelist rows for a specific tree. The query sorts records by `parent_id`, `cat_sort`, and `cat_code`.

`treecatspage_id_get_whitelist($treeId)` returns a flat list of category codes.

`treecatspage_id_get_hierarchy($treeId)` groups rows by `parent_id` and recursively builds a nested array. Root items have `parent_id = 0`.

### Building the HTML

The main function is:

```php
cot_treecatspage_id_build(
    $treeId,
    $parentItemId = 0,
    $selected = '',
    $level = 0,
    $template = ''
)
```

It:

1. validates the tree ID;
2. builds the tree data once per request and stores it in a static cache indexed by `treeId`;
3. retrieves the whitelist rows and tree settings;
4. builds maps of records by ID and parent ID;
5. retrieves page counts by category code if counting is enabled;
6. aggregates counts from lower levels upward through the custom hierarchy;
7. loads the template using `cot_tplfile(['treecatspage_id', 'page', 'tree', $template], 'plug', true)`;
8. assigns shared and per-row template values;
9. recursively builds child branches;
10. returns the HTML block.

### Public rendering function

The function intended to be called from templates is:

```php
cot_treecatspage_id_render($treeId, $template = '')
```

It starts building from the root level and returns an empty string if the tree cannot be built.

### Extension events

The tree-building code provides the following Cotonti extension points:

- `treecatspage_id.tree.main` — before shared template data is assigned;
- `treecatspage_id.tree.loop` — inside the category loop, before the `MAIN.CATS` row is parsed.

Other plugins can use these points to add processing through their corresponding extension files.

<a id="technical-count-en"></a>
## 3.4. Page counting

Counting is configured separately for each tree through `tree_count_pages`.

If counting is enabled and the pages table exists, the code runs a grouped query against the `$db_pages` table:

```sql
SELECT page_cat, COUNT(*) AS cnt
FROM <pages table>
WHERE page_state = 0
  AND page_cat IN (...)
GROUP BY page_cat
```

This counts page records with `page_state = 0` in categories included in this tree's whitelist.

The counts are then aggregated from lower levels upward through the custom `parent_id` relationships. Therefore, a parent item's counter includes pages in its own category and in its child branches. The overall `TOTAL_COUNT` is the sum of counted pages across the whitelist category codes before aggregation.

If counting is disabled, the count SQL query is not executed; `ROW_COUNT` and `TOTAL_COUNT` remain `0`. The `sidebar` template hides the numbers in this case because it checks the `DO_COUNT` flag.

<a id="technical-templates-en"></a>
## 3.5. Theme template integration

The renderer selects a template file based on the name passed as the second argument:

```php
cot_tplfile(['treecatspage_id', 'page', 'tree', $template], 'plug', true);
```

Examples of calls in `.tpl` files:

```html
{PHP|cot_treecatspage_id_render(1)}
```

```html
{PHP|cot_treecatspage_id_render(1, 'sidebar')}
```

For the second call, a template with the `sidebar` suffix is expected. In the supplied code, this is:

`tpl/treecatspage_id.page.tree.sidebar.tpl`

For a call without a second argument, an empty template name is used. The corresponding template must be present in the `tpl` directory in accordance with Cotonti's template lookup rules. Do not assume that `sidebar` is used automatically by default.

### Features of the `sidebar` template

The supplied template uses:

- Bootstrap navigation classes (`nav`, `nav-item`, `nav-link`);
- Bootstrap Collapse for expanding nested branches;
- Font Awesome classes and icons;
- `.treecatspage-id-tree` as the root selector;
- nested `<ul>` elements for child levels;
- conditional counter output when `DO_COUNT` is enabled.

The root-level header also displays the “Pages — All” line and the overall counter when counting is enabled.

<a id="technical-vars-en"></a>
## 3.6. Template variables

### Shared variables

| Variable | Value |
|---|---|
| `{TITLE}` | Title of the parent category at the current level |
| `{DESC}` | Description of the parent category |
| `{COUNT}` | Parent category counter |
| `{ICON}` | Parent category icon |
| `{HREF}` | Parent category URL |
| `{LEVEL}` | Nesting level of the current call |
| `{TOTAL_COUNT}` | Total number of pages in the tree |
| `{TREE_ID}` | Tree ID |
| `{DO_COUNT}` | `1` if counting is enabled; otherwise `0` |

### `CATS` row variables

| Variable | Value |
|---|---|
| `{ROW_ID}` | Page category code |
| `{ROW_ITEM_ID}` | Internal whitelist row ID |
| `{ROW_TITLE}` | Category title |
| `{ROW_DESC}` | Category description |
| `{ROW_COUNT}` | Number of pages in the category, including child branches |
| `{ROW_ICON}` | Category icon |
| `{ROW_HREF}` | Page category URL |
| `{ROW_SELECTED}` | Indicates whether the category is selected |
| `{ROW_SUBCAT}` | Child category HTML, or an empty string |
| `{ROW_LEVEL}` | Nesting level of the row |
| `{ROW_ODDEVEN}` | Odd/even class generated by `cot_build_oddeven()` |
| `{ROW_JJ}` | Row index in the current loop |
| `{TREE_ID}` | Tree ID |
| `{DO_COUNT}` | Counting flag |

Dynamic variables are available for additional category structure fields in the forms `{ROW_<FIELD_NAME>_TITLE}`, `{ROW_<FIELD_NAME>}`, and `{ROW_<FIELD_NAME>_VALUE}`. The field name is converted to uppercase.

If the module's i18n mechanism is included and its corresponding variables/functions are available, the code can replace the category title, description, and URL with localized values.

<a id="technical-i18n-en"></a>
## 3.7. Multilingual support

The plugin's multilingual support applies to the `tree_title` and `tree_description` fields.

- The default language is taken from the plugin setting `treecatspage_id_i18n_lang_code_default`. If it is empty, `Cot::$cfg['defaultlang']` is used.
- Additional languages are activated using separate flags and language codes for the first and second additional languages.
- Translations are stored in `cot_treecatspage_id_i18n`.
- When the current language matches the default language, the original value from the trees table is returned.
- For an additional language, the translation is used if it exists; otherwise, the original value is returned.

The code also includes the Page module's language files and uses the category i18n mechanism if the corresponding variables and the `cot_i18n_get_cat()` function are available. This is a separate integration for localized Page category data; it does not mean that the plugin translates category titles itself.

<a id="technical-dependencies-en"></a>
## 3.8. Dependencies and compatibility

The following requirements and compatibility notes are stated in the supplied code metadata and comments:

- **Cotonti:** the plugin declares compatibility with Cotonti v1+; its metadata requires the `page` module.
- **PHP:** the source-code comments specify PHP 8.4+.
- **Database:** the comments specify MySQL 8.4; the SQL uses InnoDB and `utf8mb4`.
- **Bootstrap:** the `sidebar` template uses Bootstrap 5 classes and Collapse markup. The template comment specifies Bootstrap 5.3+.
- **Font Awesome:** the `sidebar` template uses `fa-solid` and `fa` classes.
- **Cotonti XTemplate:** the administrative interface and tree are rendered using `XTemplate` and `.tpl` templates.

In `treecatspage_id.tree.js`, branch expansion uses `bootstrap.Collapse` when it is available. Otherwise, the script adds the `show` class to the element, but full interactive toggling through Bootstrap buttons generally requires Bootstrap JavaScript to be loaded.

The exact set of external libraries and the way JavaScript/CSS are loaded depend on the theme and the actual plugin package. Do not assume that the plugin installs Bootstrap or Font Awesome by itself.

<a id="technical-notes-en"></a>
## 3.9. Recommendations and warnings

1. **Create categories in the Page module first.** Only categories already present in `Cot::$structure['page']` can be added to the whitelist.
2. **Do not confuse the two hierarchy systems.** The plugin stores its own `parent_id` relationships; these do not change the standard Page structure.
3. **Check the tree ID.** The numeric ID is passed to `cot_treecatspage_id_render()`.
4. **Do not delete table rows manually unless you understand the relationships.** Removing a category from the whitelist also removes its descendants in that tree.
5. **Deleting a tree is irreversible for the plugin's data.** Its whitelist and translations will be deleted.
6. **Check sorting at every level.** `cat_sort` values are compared within groups that share the same `parent_id`.
7. **Consider the cost of counting.** If counters are not needed, disable counting for the relevant tree; the `COUNT` query will then not run.
8. **Check the theme template.** The `sidebar` template requires the relevant Bootstrap styles and Collapse JavaScript, as well as Font Awesome for the icons it uses.
9. **Check JavaScript loading.** The expansion script relies on `.treecatspage-id-tree`, `data-id`, and Bootstrap Collapse events.
10. **Check translations separately.** Tree title/description translations are not category translations. Category translations use the Page i18n mechanism, if it is installed and available.
11. **Back up the database before updating or removing the plugin.** This is especially important if you plan to run SQL manually.

### JavaScript behavior

The `treecatspage_id.tree.js` script:

- runs after `DOMContentLoaded`;
- finds `.treecatspage-id-tree` containers;
- stores expanded categories in the browser's `localStorage` under the key `treecatspage-id-tree-open`;
- attempts to determine the current category from the segments of `window.location.pathname`;
- adds the `active` class to the matching link and expands its parent levels;
- synchronizes `aria-expanded` and the saved list after the `shown.bs.collapse` and `hidden.bs.collapse` events.

The saved state is stored in the website visitor's browser. It is not a user setting stored in the website database.

<a id="technical-uninstall-en"></a>
## 3.10. Plugin and data removal

In the supplied SQL removal fragment, the tables are dropped in this order:

```sql
DROP TABLE IF EXISTS `cot_treecatspage_id_i18n`;
DROP TABLE IF EXISTS `cot_treecatspage_id_cats`;
DROP TABLE IF EXISTS `cot_treecatspage_id_trees`;
```

The translations table is dropped first, then the whitelist table, and finally the trees table. This order takes the foreign key from translations to the trees table into account.

**Warning:** running the uninstall SQL destroys all plugin data — trees, category membership, and translations. It does not delete Page categories or the pages themselves, but it does remove the navigation tree settings. Make a backup before running it.


---  
> РУССКИЙ
---  


**Tree Cats Page ID** — плагин для Cotonti CMF, который позволяет создавать несколько независимых деревьев категорий модуля **Page**. Для каждого дерева администратор выбирает категории из существующей структуры сайта, задаёт собственную вложенность и порядок вывода.

Плагин не создаёт новые категории Cotonti и не меняет штатную иерархию структуры `page`: он формирует отдельное представление выбранных категорий для навигации.

- Версия: **1.2.0**
- Автор: **[Webitproff](https://github.com/webitproff)**
- Лицензия: **BSD**
- Обязательный модуль Cotonti: **page**
- Обязательно подключенный **[файл с пользовательскими функциями](https://github.com/webitproff/functions.custom.php-cotonti)**
- Актуальный исходный код плагина **[всегда в GitHub-репозитории](https://github.com/webitproff/treecatspage_id-cotonti)**

---

## Содержание

1. [Обзор плагина](#overview)
   - [Для чего нужен плагин](#overview-purpose)
   - [Основные понятия](#overview-concepts)
   - [Что плагин делает и чего не делает](#overview-scope)
2. [Руководство пользователя](#user-guide)
   - [Открытие панели управления](#user-admin)
   - [Создание дерева](#user-create)
   - [Наполнение белого списка](#user-whitelist)
   - [Построение и изменение иерархии](#user-hierarchy)
   - [Порядок сортировки](#user-sort)
   - [Редактирование и удаление дерева](#user-edit-delete)
   - [Переводы названия и описания](#user-i18n)
   - [Примеры организации деревьев](#user-examples)
   - [Вывод дерева на сайте](#user-output)
3. [Техническая и материальная часть](#technical)
   - [Состав и назначение компонентов](#technical-files)
   - [Таблицы базы данных](#technical-db)
   - [Бизнес-логика формирования дерева](#technical-logic)
   - [Подсчёт страниц](#technical-count)
   - [Интеграция в шаблоны темы](#technical-templates)
   - [Переменные шаблонов](#technical-vars)
   - [Мультиязычность](#technical-i18n)
   - [Зависимости и совместимость](#technical-dependencies)
   - [Рекомендации и предупреждения](#technical-notes)
   - [Удаление плагина и данных](#technical-uninstall)

---

<a id="overview"></a>
# 1. Обзор плагина

<a id="overview-purpose"></a>
## 1.1. Для чего нужен плагин

В Cotonti у модуля **Page** есть структура категорий. Обычно она используется как основа для распределения страниц сайта. Но иногда на одном сайте нужны разные варианты навигации по одним и тем же категориям.

Например, на сайте могут одновременно понадобиться:

- полное дерево разделов для страницы каталога;
- короткое дерево для боковой панели;
- отдельное дерево для тематического блока;
- специальная группировка категорий для конкретной страницы или области сайта.

**Tree Cats Page ID** решает эту задачу: администратор создаёт отдельное дерево, выбирает в него нужные категории Page и выстраивает их в собственной последовательности — независимо от родительских связей штатной структуры Cotonti.

Удобная аналогия: представьте, что штатная структура категорий — это большой шкаф с папками. Плагин позволяет составить из выбранных папок несколько разных «оглавлений» или «маршрутов». Сами папки остаются на прежних местах, но в каждом оглавлении их можно расположить по-своему.

<a id="overview-concepts"></a>
## 1.2. Основные понятия

| Термин | Простое объяснение |
|---|---|
| Дерево | Отдельный набор навигации со своим числовым ID, названием и описанием |
| Категория Page | Уже существующая категория модуля Page в Cotonti |
| Белый список (whitelist) | Категории, которые администратор включил в конкретное дерево |
| Родитель | Категория, под которой в этом дереве располагаются другие категории |
| Дочерняя категория | Категория следующего уровня вложенности |
| Сортировка | Числовой порядок категорий среди элементов одного уровня |
| ID дерева | Числовой идентификатор, по которому дерево вызывается в шаблоне |

Одна категория может присутствовать в разных деревьях: уникальность в базе обеспечивается сочетанием `tree_id` и `cat_code`, а не одним `cat_code`.

<a id="overview-scope"></a>
## 1.3. Что плагин делает и чего не делает

**Плагин умеет:**

- хранить несколько независимых деревьев;
- выбирать категории Page в белый список каждого дерева;
- задавать собственную вложенность «родитель → дочерняя → внучатая»;
- задавать порядок элементов внутри уровней;
- по отдельной настройке каждого дерева считать опубликованные страницы по категориям;
- хранить переводы названия и описания дерева для настроенных дополнительных языков;
- выводить дерево через функцию `cot_treecatspage_id_render()` и шаблоны плагина.

**Важно понимать ограничения:**

- плагин не создаёт, не переименовывает и не удаляет сами категории модуля Page;
- он не переносит страницы между категориями;
- собственная иерархия дерева не равна иерархии штатной структуры Cotonti;
- в белый список добавляются коды существующих категорий Page;
- подсчёт относится к страницам, найденным в категориях белого списка, и агрегируется вверх по собственной иерархии дерева.

---

<a id="user-guide"></a>
# 2. Руководство пользователя

<a id="user-admin"></a>
## 2.1. Открытие панели управления

В исходниках административная страница подключена через хук Cotonti `tools`. Путь, указанный в комментариях к коду:

`admin.php?m=other&p=treecatspage_id`

Откройте административную панель сайта и перейдите к странице плагина **Tree Cats Page ID**. В интерфейсе предусмотрены три вкладки:

1. **Деревья** — создание, переименование/редактирование и удаление деревьев.
2. **Whitelist и иерархия** — добавление категорий, назначение родителей и сортировка.
3. **Переводы** — переводы названия и описания дерева, если мультиязычность включена в настройках плагина.

Если открыть вкладку белого списка или переводов без выбранного дерева, код пытается перенаправить на первое дерево по ID. Если деревьев ещё нет, он возвращает на вкладку списка деревьев.

<a id="user-create"></a>
## 2.2. Создание дерева

1. Откройте вкладку **Деревья**.
2. Найдите форму **Создать дерево**.
3. Введите **Название**. Это обязательное поле: запись создаётся только при непустом названии.
4. При необходимости заполните **Описание**.
5. Выберите настройку **Вести подсчёт элементов в категории**:
   - **Да** — плагин выполняет SQL-подсчёт страниц;
   - **Нет** — SQL-подсчёт страниц для этого дерева пропускается, счётчики в шаблоне будут равны нулю.
6. Нажмите **Добавить**.

После сохранения дерево появится в таблице. Плагин назначает ему числовой `tree_id`. Запишите этот ID: он понадобится для вставки дерева в шаблон темы.

При создании значение подсчёта по умолчанию — **Да** (радиокнопка `Да` отмечена в форме).

<a id="user-whitelist"></a>
## 2.3. Наполнение белого списка

После создания дерева добавьте в него категории.

1. В списке деревьев нажмите **Редактировать** напротив нужной записи. Эта ссылка открывает вкладку белого списка и иерархии.
2. Найдите блок **Доступные категории**.
3. В поле **Добавить как дочерний к** выберите место, куда добавлять новые категории:
   - **Корень (верхний уровень)** — новые категории станут верхнеуровневыми;
   - конкретную категорию — новые категории будут добавлены как её дочерние.
4. В списке **Выберите категории** отметьте одну или несколько категорий.
5. Нажмите **Добавить выбранные**.

В список доступных попадают категории из структуры `page`, которые ещё не включены в whitelist данного дерева. Категории, уже добавленные в это дерево, повторно не предлагаются.

**Пример.** Допустим, в штатной структуре сайта есть категории «Новости», «Статьи», «Обзоры» и «Инструкции». Если для дерева «Материалы» выбрать только «Статьи» и «Обзоры», остальные категории в это дерево не попадут. Они при этом не удаляются с сайта и остаются доступными для других деревьев.

<a id="user-hierarchy"></a>
## 2.4. Построение и изменение иерархии

В верхней части вкладки **Whitelist и иерархия** отображается таблица уже добавленных категорий.

Для каждой строки доступны:

- **Сорт.** — числовое значение порядка;
- **Название** — название категории из структуры Cotonti;
- **Код** — системный код категории;
- **Родитель** — выбор корня или другой категории из этого же дерева;
- **Удалить** — удаление категории из whitelist.

Чтобы изменить вложенность:

1. Найдите строку категории, которую нужно переместить.
2. В колонке **Родитель** выберите:
   - `— Корень (верхний уровень) —`, чтобы сделать категорию корневой;
   - другую категорию, чтобы сделать её дочерней.
3. Нажмите **Сохранить иерархию и сортировку**.

Уровни строятся по связям между записями whitelist, а не по штатному родителю категории в структуре Cotonti.

**Пример дерева из трёх уровней:**

```text
Материалы
├── Статьи
│   ├── Аналитика
│   └── Интервью
└── Обзоры
    └── Техника
```

В этом примере «Материалы» — условное название самого дерева, а строки внутри него — существующие категории Page. «Аналитика» и «Интервью» являются дочерними для «Статьи», а «Техника» — дочерней для «Обзоры».

При выборе родителя интерфейс исключает из списка саму категорию и её потомков. Серверная логика также проверяет недопустимые связи: нельзя назначить категорию родителем самой себе или её собственного потомка. Это предотвращает циклическую вложенность.

<a id="user-sort"></a>
## 2.5. Порядок сортировки

Поле **Сорт.** принимает числовое значение. Оно определяет порядок элементов внутри одного родительского уровня.

Например, для корневых категорий можно задать:

| Категория | Родитель | Сорт. |
|---|---|---:|
| Новости | Корень | 10 |
| Статьи | Корень | 20 |
| Обзоры | Корень | 30 |

Для дочерних категорий задаётся собственный порядок в пределах их родителя. Изменения сохраняются кнопкой **Сохранить иерархию и сортировку**.

При чтении записей код использует сортировку `parent_id`, затем `cat_sort`, затем `cat_code`. Поэтому для предсказуемого порядка рекомендуется задавать разные значения сортировки соседним элементам одного уровня.

<a id="user-edit-delete"></a>
## 2.6. Редактирование и удаление дерева

### Изменение названия, описания и подсчёта

1. Откройте вкладку **Деревья**.
2. В строке нужного дерева нажмите **Переименовать**.
3. Измените название и/или описание.
4. При необходимости измените настройку подсчёта.
5. Нажмите **Сохранить**.

Настройку подсчёта можно изменить и на вкладке whitelist — отдельный блок с радиокнопками **Да/Нет** находится над таблицей категорий.

### Изменение состава дерева

На вкладке whitelist можно добавлять доступные категории, менять их родителей и сортировку. Кнопка **Удалить** в строке категории удаляет эту категорию из текущего whitelist.

**Внимание:** при удалении категории из whitelist код также удаляет все её дочерние и более глубокие записи этого дерева. Сами категории Cotonti и страницы сайта не удаляются.

### Удаление всего дерева

1. Откройте вкладку **Деревья**.
2. В строке нужного дерева нажмите **Удалить**.
3. Подтвердите действие, если браузер/интерфейс запросит подтверждение.

При удалении дерева код удаляет связанные записи переводов и whitelist, а затем саму запись дерева. Это действие удаляет настройки дерева из таблиц плагина; категории Page и страницы сайта не удаляются.

<a id="user-i18n"></a>
## 2.7. Переводы названия и описания

Переводы относятся к **названию и описанию дерева**, а не к названиям категорий Page.

Мультиязычность включается в настройках плагина. В коде предусмотрены следующие параметры:

| Настройка | Назначение |
|---|---|
| Включить мультиязычность | Включает/отключает обработку переводов |
| Код основного языка | Язык, для которого отдельные переводы не хранятся |
| Использовать первый дополнительный язык | Включает первый дополнительный язык |
| Код первого дополнительного языка | Например, `en` |
| Использовать второй дополнительный язык | Включает второй дополнительный язык |
| Код второго дополнительного языка | Например, `uk` |

Порядок работы:

1. Откройте настройки плагина в административной части Cotonti.
2. Включите мультиязычность.
3. Укажите код основного языка.
4. При необходимости включите первый и/или второй дополнительный язык и задайте их коды.
5. Откройте вкладку **Переводы** у нужного дерева.
6. Заполните название и описание для отображаемых дополнительных языков.
7. Нажмите **Сохранить**.

Для основного языка используются обычные поля дерева — отдельная запись перевода для него не создаётся. Если перевод для текущего дополнительного языка не заполнен, функция получения значения возвращает исходное значение дерева.

<a id="user-examples"></a>
## 2.8. Примеры организации деревьев

Один сайт может иметь несколько деревьев, использующих разные комбинации одних и тех же категорий.

**Пример A — навигация по всем материалам**

```text
Материалы
├── Новости
├── Статьи
│   ├── Аналитика
│   └── Интервью
└── Обзоры
```

**Пример B — компактная боковая навигация**

```text
Разделы сайта
├── Новости
└── Обзоры
```

**Пример C — тематическая подборка**

```text
Технологии
├── Компьютеры
├── Смартфоны
└── Программное обеспечение
```

Это иллюстрации принципа организации. Для реальной работы нужно выбирать коды категорий, которые уже существуют в структуре Page вашего сайта.

<a id="user-output"></a>
## 2.9. Вывод дерева на сайте

В таблице **Деревья** плагин показывает готовые фрагменты кода для вставки в шаблон темы. Используйте ID нужного дерева.

Базовый вызов:

```html
{PHP|cot_treecatspage_id_render(1)}
```

Вызов с шаблоном `sidebar`:

```html
{PHP|cot_treecatspage_id_render(1, 'sidebar')}
```

Замените `1` на фактический ID дерева из административной таблицы. Вызов можно разместить в подходящем `.tpl`-шаблоне темы — например, в шаблоне страницы или боковой панели, в зависимости от того, где должна отображаться навигация.

В административном списке также формируется вариант вызова с проверкой активности плагина:

```html
<!-- IF {PHP|cot_plugin_active('treecatspage_id')} -->
{PHP|cot_treecatspage_id_render(1)}
<!-- ENDIF -->
```

Вставляйте фрагмент в нужный шаблон темы вашего сайта - функция доступна для вызова и работает глобально.

---

<a id="technical"></a>
# 3. Техническая и материальная часть

<a id="technical-files"></a>
## 3.1. Структура файлов плагина и их назначение



| Компонент | Назначение |
|---|---|
| `treecatspage_id.setup.php` | Метаданные расширения и регистрация настроек плагина в формате Cotonti |
| `treecatspage_id.global.php` | Подключает основной include-файл плагина через `cot_incfile()` |
| `treecatspage_id.admin.php` | Административная логика вкладок деревьев, whitelist и переводов; подключена хуком `tools` |
| `inc/treecatspage_id.functions.php` | Вспомогательные функции, чтение данных, построение и вывод дерева |
| `treecatspage_id.admin.tpl` | Шаблон административной страницы |
| `lang/treecatspage_id.ru.lang.php` | Русские языковые строки |
| `lang/treecatspage_id.en.lang.php` | Английские языковые строки |
| `tpl/treecatspage_id.page.tree.sidebar.tpl` | Шаблон вывода дерева в виде вертикальной боковой навигации |
| `treecatspage_id.tree.js` | Раскрытие активной ветки и запоминание раскрытых элементов в браузере |
| `setup/treecatspage_id.install.sql` | SQL создания таблиц плагина |
| `setup/treecatspage_id.uninstall.sql` | SQL удаления таблиц плагина |



<a id="technical-db"></a>
## 3.2. Таблицы базы данных

Плагин использует три собственные таблицы. В SQL они создаются с `InnoDB`, кодировкой `utf8mb4` и сортировкой `utf8mb4_unicode_ci`.

### `cot_treecatspage_id_trees`

Хранит общие данные каждого дерева:

| Поле | Назначение |
|---|---|
| `tree_id` | Первичный ключ, автоинкрементный ID дерева |
| `tree_title` | Название |
| `tree_description` | Описание |
| `tree_count_pages` | Флаг подсчёта страниц: `1` или `0` |
| `tree_created` | Время создания в Unix timestamp |
| `tree_updated` | Время последнего обновления в Unix timestamp |

### `cot_treecatspage_id_cats`

Хранит категории, включённые в whitelist, и пользовательскую иерархию:

| Поле | Назначение |
|---|---|
| `id` | ID строки whitelist |
| `tree_id` | ID дерева, к которому относится строка |
| `cat_code` | Код категории Page |
| `parent_id` | ID родительской строки в этой же таблице; `0` означает корень |
| `cat_sort` | Порядок сортировки |

Уникальный индекс `uniq_tree_cat` запрещает повторное добавление одного `cat_code` в одно и то же дерево. Один и тот же код категории может использоваться в другом дереве, поскольку `tree_id` входит в состав уникального ключа.

### `cot_treecatspage_id_i18n`

Хранит переводы полей дерева:

| Поле | Назначение |
|---|---|
| `tree_id` | ID дерева |
| `field_name` | Имя переводимого поля, в текущей логике — `tree_title` или `tree_description` |
| `lang` | Двухсимвольный код языка |
| `value` | Переведённое значение |

Первичный ключ состоит из `tree_id`, `field_name` и `lang`. Внешний ключ ссылается на таблицу деревьев и использует `ON DELETE CASCADE`.

<a id="technical-logic"></a>
## 3.3. Бизнес-логика формирования дерева

### Регистрация и загрузка

В `treecatspage_id.global.php` подключается include-файл плагина. Файл функций:

- проверяет наличие константы `COT_CODE`;
- подключает языковые файлы плагина и модуля Page;
- подключает файлы модуля Page и его функций;
- подключает `extrafields`;
- регистрирует три таблицы плагина через `Cot::$db->registerTable()`.

### Получение категорий

Функция `treecatspage_id_get_cats_raw($treeId)` выбирает строки whitelist конкретного дерева. Запрос сортирует записи по `parent_id`, `cat_sort`, `cat_code`.

`treecatspage_id_get_whitelist($treeId)` возвращает плоский список кодов категорий.

`treecatspage_id_get_hierarchy($treeId)` группирует строки по `parent_id` и рекурсивно строит вложенный массив. Корневые элементы имеют `parent_id = 0`.

### Построение HTML

Основная функция:

```php
cot_treecatspage_id_build(
    $treeId,
    $parentItemId = 0,
    $selected = '',
    $level = 0,
    $template = ''
)
```

Она:

1. проверяет корректность ID дерева;
2. один раз на запрос формирует данные дерева и сохраняет их в статическом кэше по `treeId`;
3. получает строки whitelist и настройки дерева;
4. строит карты записей по ID и по родительскому ID;
5. при включённом подсчёте получает количество страниц по кодам категорий;
6. накапливает количество снизу вверх по собственной иерархии;
7. загружает шаблон через `cot_tplfile(['treecatspage_id', 'page', 'tree', $template], 'plug', true)`;
8. назначает общие и построчные значения шаблона;
9. рекурсивно формирует дочерние ветки;
10. возвращает HTML-блок.

Публичная функция для вызова из шаблонов:

```php
cot_treecatspage_id_render($treeId, $template = '')
```

Она запускает построение с корневого уровня и возвращает пустую строку, если построить дерево не удалось.

### События расширения

В коде построения предусмотрены точки расширения Cotonti:

- `treecatspage_id.tree.main` — до назначения общих данных шаблона;
- `treecatspage_id.tree.loop` — внутри цикла по категориям, перед разбором строки `MAIN.CATS`.

Они позволяют другим плагинам подключать дополнительную обработку через соответствующие extension-файлы.

<a id="technical-count"></a>
## 3.4. Подсчёт страниц

Подсчёт задаётся отдельно для каждого дерева полем `tree_count_pages`.

Если настройка включена и таблица страниц существует, код выполняет группирующий запрос к таблице `$db_pages`:

```sql
SELECT page_cat, COUNT(*) AS cnt
FROM <таблица страниц>
WHERE page_state = 0
  AND page_cat IN (...)
GROUP BY page_cat
```

Таким образом, считаются записи страниц со значением `page_state = 0` для категорий, включённых в whitelist этого дерева.

Затем количество накапливается снизу вверх по пользовательским связям `parent_id`. Поэтому счётчик родительского пункта включает страницы его собственной категории и дочерних веток. Общий `TOTAL_COUNT` — сумма подсчитанных страниц по кодам категорий whitelist до агрегации.

Если подсчёт выключен, SQL-запрос подсчёта не выполняется; `ROW_COUNT` и `TOTAL_COUNT` остаются равными `0`. Шаблон `sidebar` при этом скрывает вывод чисел, поскольку проверяет флаг `DO_COUNT`.

<a id="technical-templates"></a>
## 3.5. Интеграция в шаблоны темы

Рендерер выбирает файл шаблона по имени, переданному вторым аргументом:

```php
cot_tplfile(['treecatspage_id', 'page', 'tree', $template], 'plug', true);
```

Примеры вызова в `.tpl`:

```html
{PHP|cot_treecatspage_id_render(1)}
```

```html
{PHP|cot_treecatspage_id_render(1, 'sidebar')}
```

Для второго вызова ожидается шаблон с суффиксом `sidebar`, в представленном коде это:

`tpl/treecatspage_id.page.tree.sidebar.tpl`

Для вызова без второго аргумента используется пустое имя шаблона. Соответствующий шаблон должен присутствовать в папке `tpl` в соответствии с правилами поиска шаблонов Cotonti. Не предполагайте, что `sidebar` автоматически используется по умолчанию.

### Особенности шаблона `sidebar`

Предоставленный шаблон использует:

- Bootstrap-классы навигации (`nav`, `nav-item`, `nav-link`);
- Bootstrap Collapse для раскрытия вложенных веток;
- классы и иконки Font Awesome;
- класс `.treecatspage-id-tree` как корневой селектор;
- вложенный `<ul>` для дочерних уровней;
- условный вывод счётчиков при `DO_COUNT`.

В шапке корневого уровня шаблон также выводит строку «Страницы — Все» и общий счётчик, если подсчёт включён.

<a id="technical-vars"></a>
## 3.6. Переменные шаблонов

### Общие переменные

| Переменная | Значение |
|---|---|
| `{TITLE}` | Название родительской категории текущего уровня |
| `{DESC}` | Описание родительской категории |
| `{COUNT}` | Счётчик родительской категории |
| `{ICON}` | Иконка родительской категории |
| `{HREF}` | Ссылка на родительскую категорию |
| `{LEVEL}` | Уровень вложенности текущего вызова |
| `{TOTAL_COUNT}` | Общее количество страниц дерева |
| `{TREE_ID}` | ID дерева |
| `{DO_COUNT}` | `1`, если подсчёт включён, иначе `0` |

### Переменные строки `CATS`

| Переменная | Значение |
|---|---|
| `{ROW_ID}` | Код категории Page |
| `{ROW_ITEM_ID}` | Внутренний ID строки whitelist |
| `{ROW_TITLE}` | Название категории |
| `{ROW_DESC}` | Описание категории |
| `{ROW_COUNT}` | Количество страниц категории с учётом дочерних веток |
| `{ROW_ICON}` | Иконка категории |
| `{ROW_HREF}` | Ссылка на категорию Page |
| `{ROW_SELECTED}` | Признак выбранной категории |
| `{ROW_SUBCAT}` | HTML дочерних категорий либо пустая строка |
| `{ROW_LEVEL}` | Уровень вложенности строки |
| `{ROW_ODDEVEN}` | Класс чётности, формируемый `cot_build_oddeven()` |
| `{ROW_JJ}` | Порядковый номер строки в текущем цикле |
| `{TREE_ID}` | ID дерева |
| `{DO_COUNT}` | Флаг подсчёта |

Для дополнительных полей структуры категории предусмотрены динамические переменные вида `{ROW_<ИМЯ_ПОЛЯ>_TITLE}`, `{ROW_<ИМЯ_ПОЛЯ>}` и `{ROW_<ИМЯ_ПОЛЯ>_VALUE}`. Имя поля приводится к верхнему регистру.

Если подключён механизм i18n модуля и доступны его соответствующие переменные/функции, код может заменить название, описание и URL категории на локализованные значения.

<a id="technical-i18n"></a>
## 3.7. Мультиязычность

Мультиязычность плагина распространяется на поля `tree_title` и `tree_description`.

- Основной язык берётся из настройки плагина `treecatspage_id_i18n_lang_code_default`; если она не заполнена, используется `Cot::$cfg['defaultlang']`.
- Дополнительные языки активируются отдельными флагами и кодами первого и второго языка.
- Переводы хранятся в `cot_treecatspage_id_i18n`.
- При текущем языке, совпадающем с основным, возвращается исходное значение из таблицы деревьев.
- Для дополнительного языка используется перевод, если он существует; иначе возвращается исходное значение.

Код также подключает языковые файлы модуля Page и использует механизм i18n категорий, если соответствующие переменные и функция `cot_i18n_get_cat()` доступны. Это отдельная интеграция для локализованных данных категорий Page; она не означает, что плагин переводит названия самих категорий.

<a id="technical-dependencies"></a>
## 3.8. Зависимости и совместимость

Из метаданных и комментариев предоставленного кода известны следующие требования и ориентиры:

- **Cotonti:** плагин заявляет совместимость с Cotonti v1+; метаданные требуют модуль `page`.
- **PHP:** в комментариях исходников указано PHP 8.4+.
- **База данных:** в комментариях указана MySQL 8.4; SQL использует InnoDB и `utf8mb4`.
- **Bootstrap:** шаблон `sidebar` использует Bootstrap 5-классы и Collapse-разметку. В комментарии к шаблону указана зависимость Bootstrap 5.3+.
- **Font Awesome:** шаблон `sidebar` использует классы `fa-solid` и `fa`.
- **Cotonti XTemplate:** административный интерфейс и дерево формируются через `XTemplate` и `.tpl`-шаблоны.

В `treecatspage_id.tree.js` раскрытие ветки использует `bootstrap.Collapse`, если он доступен. В противном случае скрипт добавляет класс `show` к элементу, но полноценное интерактивное переключение по Bootstrap-кнопкам обычно требует подключённого Bootstrap JavaScript.

Точный состав внешних библиотек и способ подключения JS/CSS зависят от темы и фактической комплектации плагина. Не следует считать, что плагин самостоятельно устанавливает Bootstrap или Font Awesome.

<a id="technical-notes"></a>
## 3.9. Рекомендации и предупреждения

1. **Сначала создайте категории в модуле Page.** В whitelist можно добавлять только категории, уже присутствующие в `Cot::$structure['page']`.
2. **Не путайте два уровня иерархии.** Плагин хранит собственные связи `parent_id`; они не изменяют штатную структуру Page.
3. **Проверяйте ID дерева.** Именно числовой ID передаётся в `cot_treecatspage_id_render()`.
4. **Не удаляйте строки таблиц вручную без понимания связей.** Удаление категории из whitelist удаляет её потомков в этом дереве.
5. **Удаление дерева необратимо для данных плагина.** Будут удалены его whitelist и переводы.
6. **Проверяйте сортировку на каждом уровне.** Значения `cat_sort` сравниваются внутри групп одного `parent_id`.
7. **Учитывайте стоимость подсчёта.** Если счётчики не нужны, отключите подсчёт для соответствующего дерева — тогда запрос `COUNT` не выполняется.
8. **Проверяйте шаблон темы.** Для `sidebar` нужны соответствующие Bootstrap-стили и Collapse JavaScript, а также Font Awesome для используемых иконок.
9. **Проверяйте загрузку JS.** Скрипт раскрытия ориентируется на `.treecatspage-id-tree`, `data-id` и Bootstrap Collapse-события.
10. **Проверяйте переводы отдельно.** Переводы названия/описания дерева не являются переводами категорий; для перевода категорий используется механизм i18n Page, если он установлен и доступен.
11. **Сделайте резервную копию БД перед обновлением или удалением.** Особенно если вы планируете выполнять SQL вручную.

### Поведение JavaScript

Скрипт `treecatspage_id.tree.js`:

- выполняется после `DOMContentLoaded`;
- находит контейнеры `.treecatspage-id-tree`;
- запоминает открытые категории в `localStorage` под ключом `treecatspage-id-tree-open`;
- пытается определить текущую категорию по сегментам `window.location.pathname`;
- добавляет класс `active` найденной ссылке и раскрывает её родительские уровни;
- синхронизирует `aria-expanded` и сохранённый список после событий `shown.bs.collapse` и `hidden.bs.collapse`.

Сохранённое состояние находится в браузере посетителя. Это не настройка пользователя в базе данных сайта.

<a id="technical-uninstall"></a>
## 3.10. Удаление плагина и данных

В предоставленном SQL-фрагменте удаления таблицы удаляются в таком порядке:

```sql
DROP TABLE IF EXISTS `cot_treecatspage_id_i18n`;
DROP TABLE IF EXISTS `cot_treecatspage_id_cats`;
DROP TABLE IF EXISTS `cot_treecatspage_id_trees`;
```

Сначала удаляется таблица переводов, затем whitelist, затем таблица деревьев. Такой порядок учитывает внешний ключ переводов на таблицу деревьев.

**Предупреждение:** выполнение uninstall SQL уничтожает все данные плагина — деревья, состав категорий и переводы. Оно не удаляет категории Page и сами страницы, но удаляет настройки навигационных деревьев. Перед выполнением обязательно сделайте резервную копию.

---


2026-11-23
