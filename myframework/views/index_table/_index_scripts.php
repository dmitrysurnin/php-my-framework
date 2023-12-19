<?php
/** @var $this \myframework\CAdminController */

if (! $this->request->isAjax) :
?>

<script type="text/javascript">
window.p = window.p || {};

(function(p) {

	p.container = $('body div.container-fluid');

	p.page = parseInt(<?php echo $_GET['page'] ?? 1 ?>);
	p.sort = '<?php echo $_GET['sort'] ?? '' ?>';
	p.order = parseInt(<?php echo $_GET['order'] ?? 0 ?>);
	p.limit = parseInt(<?php echo $_GET['limit'] ?? $this->_itemsPerPage ?>);

	let ajax, last_datepicker;

	p.get_filters = function() {
		return $('table.table-main thead :input');
	};

	p.get_params = function() {
		return 'page=' + p.page + '&sort=' + p.sort + '&order=' + p.order + '&limit=' + p.limit + '&' + p.get_filters().serialize();
	};

	p.update_page = function() {
		var url = location.pathname + '?' + p.get_params();
		$('.summary .ajax-loading-spinner').show();
		p.send_ajax(url, true);

		if (history.pushState) {
			history.pushState({
				path: url
			}, '', url);
		}
		p.after_update();
	};

	p.set_datepickers = function() {
		$('input.bootstrap-datepicker').each(function() {
			$(this)
				.datepicker({
					format: $(this).data('format'),
					weekStart: 1,
					language: 'ru'
				})
				.on('show', function(event) {
					last_datepicker && last_datepicker !== this && $(last_datepicker).datepicker('hide'); /// чтобы не перекрывались
					last_datepicker = this;
				})
				.on('changeDate', function(event) {
					$(this).datepicker('hide');
					p.update_page();
				})
				.on('mousedown', function(ev) {
					ev.stopPropagation(); /// чтобы не закрывался при втором клике
				});
		});
	};

	p.after_update = function() {
	};

	p.set_filters = function() {
		p.set_datepickers(); /// иначе datepickers потеряются при ajax-перезагрузке страницы
	};

	p.send_ajax = function(url, without_params) {
		let params = typeof(without_params) !== 'undefined' ? '' : p.get_params();
		url = url + (url.indexOf('?') === -1 ? '?' : (params ? '&' : '')) + params + '&ajax=' + new Date().getTime();

		ajax && ajax.abort();
		typeof(console) !== 'undefined' && console.log('ajax: ' + url);

		ajax = $.ajax({
			url: url,
			success: function(resp) {
				if (resp) {
					p.container.html(resp);
					p.set_filters();
				}
			}
		});
	};

	$(document)
		.on('change', 'thead th input', function() {
			setTimeout(function() {
				if (! $('thead th input:focus').length) {
					p.update_page();
				}
			}, 0);
		})
		.on('change', 'thead th select', function() {
			this.blur();
			p.update_page();
		})
		.on('click', 'thead th input[type="checkbox"]', function() {
			p.update_page();
		})
		.on('keydown', 'thead th input', function(e) {
			e.keyCode === 13 && p.update_page();
		})
		.on('keydown', 'body', function(e) {
			if ($(e.target).is('body')) {
				e.keyCode === 39 && p.page ++ && p.update_page(); /// arrow right
				e.keyCode === 37 && p.page > 1 && p.page -- && p.update_page(); /// arrow left
			}
		})
		.on('mousedown', 'div.pagination li a', function(e) {
			(p.page = $(this).data('page')) && p.update_page();
			return false;
		})
		.on('click', 'div.pagination li a', function(e) {
			return false;
		})
		.on('mousedown', 'table thead a.sort', function(e) {
			(p.sort = $(this).data('sort')) && (p.order = $(this).data('order')) && p.update_page();
			return false;
		})
		.on('click', 'table thead a.sort', function(e) {
			return false;
		})
		.on('mousedown', 'thead button.filter_ok', function(e) {
			p.update_page();
		})
		.on('mousedown', 'thead button.filter_clear', function(e) {
			$(this).closest('thead').find('input[name^="filter"]:not(:hidden),select[name^="filter"]:not(:hidden)').val('');
			p.update_page();
		})
		.on('mousedown', 'div.summary button.refresh_table', function(e) {
			p.limit = $(this).prev('input.items_per_page').val();
			p.update_page();
		})
		.on('mousedown', 'div.summary button.clear_filters', function(e) {
			$(this).closest('div.summary').next('table.table.table-main').find('thead').find('input[name^="filter"]:not(:hidden),select[name^="filter"]:not(:hidden)').val('');
			p.page = p.sort = p.order = p.limit = '';
			p.update_page();
		})
		.on('mousedown', 'a.icon-trash, a.fa.fa-trash-o, a.fa.fa-trash', function(e) {
			var t = $(this);
			var tr = $(this).closest('tr');
			tr.addClass('info');
			setTimeout(function() {
				if (confirm('Вы действительно хотите удалить эту строку?')) {
					var url = t.attr('href');
					$.ajax({
						url: url + (url.indexOf('?') === -1 ? '?' : '&') + p.get_params(),
						success: function(resp) {
							if (resp) {
								p.container.html(resp);
								p.set_filters();
							}
						}
					});
				}
				else {
					tr.removeClass('info');
				}
			}, 1);
			return false;
		})
		.on('mousedown', 'a.ajax', function(e) {
			var t = $(this);
			var tr = $(this).closest('tr');
			tr.addClass('info');
			setTimeout(function() {
				if (! t.hasClass('confirm') || confirm('Вы уверены?')) {
					var url = t.attr('href');
					t.replaceWith('<i class="fa fa-spinner fa-spin"></i>');
					$.ajax({
						url: url + (url.indexOf('?') === -1 ? '?' : '&') + p.get_params(),
						type: t.hasClass('post') ? 'post' : 'get',
						success: function(resp) {
							if (resp) {
								p.container.html(resp);
								p.set_filters();
							}
						}
					});
				}
				else {
					tr.removeClass('info');
				}
			}, 1);
			return false;
		})
		.on('click', 'a.icon-trash, a.fa.fa-trash-o, a.fa.fa-trash, a.ajax', function(e) {
			return false;
		})
		.on('click', 'table.table-main tbody tr:has(td input[type="checkbox"].select-row)', function(e) {
			var box = $(this).find('td input[type="checkbox"].select-row');
			box[0].checked = ! box[0].checked;
			$(this).toggleClass('info', box[0].checked);
			return false;
		})
		.on('click', 'table.table-main thead tr th input[type="checkbox"].select-all', function(e) {
			var checked = this.checked;
			$('table.table-main tbody tr td input[type="checkbox"].select-row').each(function() {
				this.checked = checked;
				$(this).closest('tr').toggleClass('info', checked);
			});
		})
		.ready(function() {
			p.set_filters();
		});

	if (history.pushState) {
		$(window)
			.on('popstate', function (event) {
				let state = event.originalEvent.state;
				if (state) {
					p.send_ajax(state.path, true);
				}
			});

		history.replaceState({
			path: location.href
		}, '');
	}

	$(function() {
		$('table.table-main tbody tr:has(td input[type="checkbox"].select-row)').addClass('pointer');
	});

})(window.p);
</script>

<?php
endif;
