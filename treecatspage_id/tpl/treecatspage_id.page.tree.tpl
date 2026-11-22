<!--
/********************************************************************************
 * File: treecatspage_id.page.tree.tpl
 * Extension: plugin 'treecatspage_id'
 * Description: Default HTML template for treecatspage_id plugin tree
 * Compatibility: CMF/CMS Cotonti v.1.0+
 * Dependencies: Bootstrap 5.3+ ; Font Awesome
 * Version=1.0.0
 * Created: 22 Nov 2026
 * Copyright (c) 2026 webitproff
 * License: BSD
 ********************************************************************************/
-->
<!-- BEGIN: MAIN -->
<ul class="nav flex-column treecatspage-id-tree">
	<!-- IF {LEVEL} == 0 -->
	<li class="nav-item">
		<div class="d-flex align-items-center px-3 my-2">
			<span>{PHP.L.Pages}</span>
			<!-- IF {DO_COUNT} --><span class="ms-auto">({TOTAL_COUNT})</span><!-- ENDIF -->
		</div>
	</li>
	<!-- ENDIF -->
	<!-- BEGIN: CATS -->
	<li class="nav-item" data-id="{ROW_ID}">
		<!-- IF {ROW_SUBCAT} -->
		<div class="d-flex align-items-center">
			<a href="{ROW_HREF}" class="nav-link flex-grow-1 d-flex align-items-center text-decoration-none text-reset<!-- IF {ROW_SELECTED} --> active<!-- ENDIF -->">
				<span>{ROW_TITLE}</span>
				<!-- IF {DO_COUNT} --><span class="ms-auto">({ROW_COUNT})</span><!-- ENDIF -->
			</a>
			<button type="button"
			        class="toggle-subcats btn btn-link px-2 py-0"
			        data-bs-toggle="collapse"
			        data-bs-target="#collapse-{TREE_ID}-{ROW_ID}"
			        aria-expanded="false"
			        aria-controls="collapse-{TREE_ID}-{ROW_ID}"
			        aria-label="{ROW_TITLE}">
				<i class="fa fa-angle-down" aria-hidden="true"></i>
			</button>
		</div>
		<div class="collapse" id="collapse-{TREE_ID}-{ROW_ID}">
			{ROW_SUBCAT}
		</div>
		<!-- ELSE -->
		<a href="{ROW_HREF}" class="nav-link d-flex align-items-center<!-- IF {ROW_SELECTED} --> active<!-- ENDIF -->">
			<span>{ROW_TITLE}</span>
			<!-- IF {DO_COUNT} --><span class="ms-auto">({ROW_COUNT})</span><!-- ENDIF -->
		</a>
		<!-- ENDIF -->
	</li>
	<!-- END: CATS -->
</ul>
<style>
.treecatspage-id-tree .treecatspage-id-tree {
  border-left: 1px solid var(--border-color, #dee2e6);
  padding-left: 0.75rem;
  margin-left: 0.25rem;
}
</style>
<!-- END: MAIN -->
