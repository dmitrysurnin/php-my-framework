<?php
/** @var $this \myframework\CAdminController */

if (! $this->request->isAjax) :
?>

<script type="text/javascript">
window.p = {};

(function(p) {

	p.container = $('body div.container-fluid');
	let last_datepicker;

	$(document)
		.on('click', 'button[type="submit"]', function(e) {
			let t = $(this);
			setTimeout(function() {
				t.attr('disabled', 'disabled');
			}, 1);
		})
		.on('click', 'a.file-upload', function() {
			$(this).parent().find('input[type="file"]').trigger('click');
			return false;
		});

	$(window).on('load', function() {

		$('button[type="submit"]').removeAttr('disabled');

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

	});

})(window.p);
</script>

<?php
endif;
