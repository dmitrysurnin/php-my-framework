
function bindSend(modal, url, table) {
  var save = modal.find('div.modal-footer button.btn-primary');
  save.removeAttr('disabled');
  save.on('click', function() {
    save.attr('disabled', 'disabled');
    if (url) {
      $.ajax({
        url: url,
        async: false,
        type: 'POST',
        data: save.closest('form').serialize(),
        success: function(resp) {
          resp = $.trim(resp);
          if (resp.indexOf('<form') == 0) {
            modal.html(resp);
            bindSend(modal, url, table);
          }
          else {
            modal.modal('hide');
            table.parent().html(resp);
          }
        },
        complete: function(xhr) {
        },
        error: function(xhr) {
          alert('error');
        }
      });
    }
    return false;
  });
}

$('a.grid-update').on('click', function() {
  var button = $(this);
  var modal = $('#' + $(this).attr('modal'));
  var url = button.attr('href');
  var table = button.closest('table');
  modal.find('.modal-body').html('<img src=\"/images/loading4.gif\">');
  modal.modal('show');
  $.ajax({
    url: url,
    success: function(resp) {
      modal.html(resp);
      bindSend(modal, url, table);
    },
    complete: function(xhr) {
    },
    error: function(xhr) {
    }
  });
  return false;
});

$('a.grid-delete').on('click', function() {
  var button = $(this);
  var url = button.attr('href');
  var table = button.closest('table');
  $.ajax({
    url: url,
    success: function(resp) {
      table.parent().html(resp);
    },
    complete: function(xhr) {
    },
    error: function(xhr) {
    }
  });
  return false;
});

$('div.modal').on('shown', function() {
  $('body').css('overflow', 'hidden');
}).on('hidden', function() {
  $('body').css('overflow', 'auto');
});
