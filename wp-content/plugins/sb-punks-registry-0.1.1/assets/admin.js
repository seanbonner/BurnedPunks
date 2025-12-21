jQuery(function($){
  const btn = $('#sbpr-import');
  const status = $('#sbpr-import-status');
  if (!btn.length) return;

  btn.on('click', function(){
    const postId = btn.data('post-id');
    status.text('Importing…');
    btn.prop('disabled', true);

    $.post(SBPR_ADMIN.ajax_url, {
      action: 'sbpr_import',
      nonce: SBPR_ADMIN.nonce,
      post_id: postId
    })
    .done(function(resp){
      if (!resp || !resp.success) {
        status.text('Failed.');
        return;
      }
      const errs = (resp.data && resp.data.errors) ? resp.data.errors : [];
      if (errs.length) {
        status.text('Imported, but missing: ' + errs.join(', '));
      } else {
        status.text('Imported.');
      }
      // Force reload so metabox updates.
      window.location.reload();
    })
    .fail(function(){
      status.text('Failed.');
    })
    .always(function(){
      btn.prop('disabled', false);
    });
  });
});