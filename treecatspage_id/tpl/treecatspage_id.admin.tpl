<!-- BEGIN: MAIN -->
<div class="container-fluid">
	<ul class="nav nav-tabs mb-3">
		<li class="nav-item">
			<a class="nav-link {TAB_TREES_ACTIVE}" href="{URL_TREES}">{PHP.L.treecatspage_id_tab_trees}</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {TAB_WHITELIST_ACTIVE}" href="{URL_WHITELIST}">{PHP.L.treecatspage_id_tab_whitelist}</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {TAB_I18N_ACTIVE}" href="{URL_I18N}">{PHP.L.treecatspage_id_tab_i18n}</a>
		</li>
	</ul>

	<!-- IF {PHP.tab} == 'trees' -->

	<!-- BEGIN: EDIT_TREE_FORM -->
	<div class="card mb-4">
		<div class="card-header">{PHP.L.treecatspage_id_rename}</div>
		<div class="card-body">
			<form action="{EDIT_TREE_FORM_URL}" method="post">
				<div class="mb-3">
					<label class="form-label">{PHP.L.treecatspage_id_tree_title}</label>
					{EDIT_TREE_TITLE}
				</div>
				<div class="mb-3">
					<label class="form-label">{PHP.L.treecatspage_id_tree_desc}</label>
					{EDIT_TREE_DESC}
				</div>
				<div class="mb-3">
					<label class="form-label d-block">{PHP.L.treecatspage_id_count_pages}</label>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="edit_tree_count_pages" id="edit_count_yes" value="1" {EDIT_TREE_COUNT_YES}>
						<label class="form-check-label" for="edit_count_yes">{PHP.L.treecatspage_id_count_yes}</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="edit_tree_count_pages" id="edit_count_no" value="0" {EDIT_TREE_COUNT_NO}>
						<label class="form-check-label" for="edit_count_no">{PHP.L.treecatspage_id_count_no}</label>
					</div>
					<div class="form-text">{PHP.L.treecatspage_id_count_hint}</div>
				</div>
				<button type="submit" class="btn btn-primary">{PHP.L.treecatspage_id_save}</button>
			</form>
		</div>
	</div>
	<!-- END: EDIT_TREE_FORM -->

	<div class="card mb-4">
		<div class="card-header">{PHP.L.treecatspage_id_add_tree}</div>
		<div class="card-body">
			<form action="{ADD_TREE_FORM_URL}" method="post">
				<div class="mb-3">
					<label class="form-label">{PHP.L.treecatspage_id_tree_title}</label>
					{ADD_TREE_TITLE}
				</div>
				<div class="mb-3">
					<label class="form-label">{PHP.L.treecatspage_id_tree_desc}</label>
					{ADD_TREE_DESC}
				</div>
				<div class="mb-3">
					<label class="form-label d-block">{PHP.L.treecatspage_id_count_pages}</label>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="tree_count_pages" id="add_count_yes" value="1" checked>
						<label class="form-check-label" for="add_count_yes">{PHP.L.treecatspage_id_count_yes}</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="tree_count_pages" id="add_count_no" value="0">
						<label class="form-check-label" for="add_count_no">{PHP.L.treecatspage_id_count_no}</label>
					</div>
					<div class="form-text">{PHP.L.treecatspage_id_count_hint}</div>
				</div>
				<button type="submit" class="btn btn-success">{PHP.L.treecatspage_id_add}</button>
			</form>
		</div>
	</div>

	<div class="card">
		<div class="card-header">{PHP.L.treecatspage_id_trees_list}</div>
		<div class="card-body p-0">
			<table class="table table-striped table-hover mb-0">
				<thead>
					<tr>
						<th>{PHP.L.treecatspage_id_tree_id}</th>
						<th>{PHP.L.treecatspage_id_tree_title}</th>
						<th>{PHP.L.treecatspage_id_tree_desc}</th>
						<th>{PHP.L.treecatspage_id_tree_whitelist}</th>
						<th>{PHP.L.treecatspage_id_count_pages}</th>
						<th>{PHP.L.treecatspage_id_tree_code}</th>
						<th>{PHP.L.treecatspage_id_tree_actions}</th>
					</tr>
				</thead>
				<tbody>
					<!-- BEGIN: TREES_ROW -->
					<tr>
						<td>{TREE_ID}</td>
						<td>{TREE_TITLE}</td>
						<td>{TREE_DESC}</td>
						<td>{TREE_WHITELIST_COUNT}</td>
						<td>
							<!-- IF {TREE_COUNT_PAGES} -->
							<span class="badge bg-success">{TREE_COUNT_LABEL}</span>
							<!-- ELSE -->
							<span class="badge bg-secondary">{TREE_COUNT_LABEL}</span>
							<!-- ENDIF -->
						</td>
						<td>
							<code class="small">{TREE_CODE}</code>
							<br>
							<code class="small text-muted">{TREE_CODE_SIDEBAR}</code>
						</td>
						<td>
							<a href="{TREE_EDIT_URL}" class="btn btn-sm btn-outline-primary">{PHP.L.treecatspage_id_edit}</a>
							<a href="{TREE_RENAME_URL}" class="btn btn-sm btn-outline-secondary">{PHP.L.treecatspage_id_rename}</a>
							<a href="{TREE_DELETE_URL}" class="btn btn-sm btn-outline-danger">{PHP.L.treecatspage_id_delete}</a>
						</td>
					</tr>
					<!-- END: TREES_ROW -->
				</tbody>
			</table>
		</div>
	</div>

	<!-- ENDIF -->

	<!-- IF {PHP.tab} == 'whitelist' -->

	<div class="mb-3">
		<a href="{URL_TREES}" class="btn btn-outline-secondary btn-sm">&larr; {PHP.L.treecatspage_id_back}</a>
	</div>

	<div class="card mb-4">
		<div class="card-header">{PHP.L.treecatspage_id_count_pages}</div>
		<div class="card-body">
			<form action="{SAVE_TREE_SETTINGS_URL}" method="post" class="d-flex flex-wrap align-items-center gap-3">
				<div class="form-check form-check-inline mb-0">
					<input class="form-check-input" type="radio" name="tree_count_pages" id="wl_count_yes" value="1" {TREE_COUNT_YES}>
					<label class="form-check-label" for="wl_count_yes">{PHP.L.treecatspage_id_count_yes}</label>
				</div>
				<div class="form-check form-check-inline mb-0">
					<input class="form-check-input" type="radio" name="tree_count_pages" id="wl_count_no" value="0" {TREE_COUNT_NO}>
					<label class="form-check-label" for="wl_count_no">{PHP.L.treecatspage_id_count_no}</label>
				</div>
				<button type="submit" class="btn btn-sm btn-primary">{PHP.L.treecatspage_id_save}</button>
				<span class="form-text mb-0">{PHP.L.treecatspage_id_count_hint}</span>
			</form>
		</div>
	</div>

	<div class="card mb-4">
		<div class="card-header">
			{PHP.L.treecatspage_id_whitelist_title}: <strong>{TREE_TITLE}</strong> (ID {TREE_ID})
		</div>
		<div class="card-body">
			<!-- IF {HAS_WHITELIST} -->
			<form action="{SAVE_HIERARCHY_URL}" method="post">
				<table class="table table-sm table-bordered align-middle">
					<thead>
						<tr>
							<th style="width:60px">{PHP.L.treecatspage_id_cat_sort}</th>
							<th>{PHP.L.treecatspage_id_cat_title}</th>
							<th>{PHP.L.treecatspage_id_cat_code}</th>
							<th style="min-width:220px">{PHP.L.treecatspage_id_parent}</th>
							<th style="width:90px"></th>
						</tr>
					</thead>
					<tbody>
						<!-- BEGIN: WHITELIST_ROW -->
						<tr>
							<td>
								<input type="number" name="sort[{CAT_ID}]" value="{CAT_SORT}" class="form-control form-control-sm" style="width:70px">
							</td>
							<td>
								<span class="text-muted">{CAT_INDENT}</span>{CAT_TITLE}
								<!-- IF {CAT_LEVEL} > 0 -->
								<span class="badge bg-secondary ms-1">L{CAT_LEVEL}</span>
								<!-- ENDIF -->
							</td>
							<td><code>{CAT_CODE}</code></td>
							<td>
								<select name="parent[{CAT_ID}]" class="form-select form-select-sm">
									{CAT_PARENT_SELECT}
								</select>
							</td>
							<td>
								<a href="{CAT_DELETE_URL}" class="btn btn-sm btn-outline-danger">{PHP.L.treecatspage_id_delete}</a>
							</td>
						</tr>
						<!-- END: WHITELIST_ROW -->
					</tbody>
				</table>
				<button type="submit" class="btn btn-primary">{PHP.L.treecatspage_id_save_hierarchy}</button>
			</form>
			<!-- ELSE -->
			<p class="text-muted mb-0">{PHP.L.treecatspage_id_whitelist_empty}</p>
			<!-- ENDIF -->
		</div>
	</div>

	<div class="card">
		<div class="card-header">{PHP.L.treecatspage_id_available_cats}</div>
		<div class="card-body">
			<!-- IF {HAS_AVAILABLE} -->
			<form action="{ADD_CATS_FORM_URL}" method="post">
				<div class="mb-3">
					<label class="form-label">{PHP.L.treecatspage_id_add_as_child}</label>
					{ADD_PARENT_SELECT}
				</div>
				<div class="mb-3">
					<label class="form-label">{PHP.L.treecatspage_id_select_cats}</label>
					{ADD_CATS_SELECT}
				</div>
				<button type="submit" class="btn btn-success">{PHP.L.treecatspage_id_add_selected}</button>
			</form>
			<!-- ELSE -->
			<p class="text-muted mb-0">{PHP.L.treecatspage_id_no_available}</p>
			<!-- ENDIF -->
		</div>
	</div>

	<!-- ENDIF -->

	<!-- IF {PHP.tab} == 'i18n' -->

	<div class="mb-3">
		<a href="{URL_TREES}" class="btn btn-outline-secondary btn-sm">&larr; {PHP.L.treecatspage_id_back}</a>
	</div>

	<div class="card">
		<div class="card-header">
			{PHP.L.treecatspage_id_i18n_title}: <strong>{TREE_TITLE}</strong> (ID {TREE_ID})
		</div>
		<div class="card-body">
			<!-- IF {I18N_ACTIVE} -->
			<form action="{I18N_FORM_URL}" method="post">
				<div class="mb-3">
					<label class="form-label">{PHP.L.treecatspage_id_i18n_default}</label>
					<input type="text" class="form-control" value="{TREE_TITLE}" disabled>
					<textarea class="form-control mt-2" rows="2" disabled>{TREE_DESC}</textarea>
				</div>

				<!-- BEGIN: I18N_LANG_FIELDS -->
				<div class="border rounded p-3 mb-3">
					<h6 class="mb-3">Lang: {LANG_CODE}</h6>
					<div class="mb-2">
						<label class="form-label">{PHP.L.treecatspage_id_i18n_title_field}</label>
						<input type="text" name="title_{LANG_CODE}" class="form-control" value="{TITLE_VALUE}">
					</div>
					<div>
						<label class="form-label">{PHP.L.treecatspage_id_i18n_desc_field}</label>
						<textarea name="desc_{LANG_CODE}" class="form-control" rows="2">{DESC_VALUE}</textarea>
					</div>
				</div>
				<!-- END: I18N_LANG_FIELDS -->

				<button type="submit" class="btn btn-primary">{PHP.L.treecatspage_id_save}</button>
			</form>
			<!-- ELSE -->
			<p class="text-muted mb-0">{PHP.L.treecatspage_id_i18n_disabled}</p>
			<!-- ENDIF -->
		</div>
	</div>

	<!-- ENDIF -->
</div>
<!-- END: MAIN -->
