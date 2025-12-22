jQuery(function($){
  const btn = $('#sbpr-import');
  const status = $('#sbpr-import-status');
  if (!btn.length) return;

  const CFG = window.SBPR_ADMIN || {};

  function hexPad(n, bytes){
    let hex = BigInt(n).toString(16);
    while (hex.length < bytes*2) hex = '0' + hex;
    return hex;
  }

  async function rpcCall(method, params){
    const urls = Array.isArray(CFG.rpc_urls) ? CFG.rpc_urls : [];
    let lastErr = null;
    for (const url of urls){
      try {
        const res = await fetch(url, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ jsonrpc: '2.0', id: 1, method, params })
        });
        const json = await res.json();
        if (json && json.result !== undefined) return json.result;
        lastErr = json && json.error ? (json.error.message || JSON.stringify(json.error)) : 'Unknown RPC error';
      } catch (e){
        lastErr = e && e.message ? e.message : String(e);
      }
    }
    throw new Error(lastErr || 'RPC failed');
  }

  async function ethCall(to, data){
    return await rpcCall('eth_call', [{ to, data }, 'latest']);
  }

  function decodeAbiString(resultHex){
    if (!resultHex || typeof resultHex !== 'string' || !resultHex.startsWith('0x')) return '';
    const hex = resultHex.slice(2);
    if (hex.length < 128) return '';
    const offset = parseInt(hex.slice(0, 64), 16) * 2;
    const len = parseInt(hex.slice(offset, offset + 64), 16) * 2;
    const start = offset + 64;
    const strHex = hex.slice(start, start + len);
    let out = '';
    for (let i = 0; i < strHex.length; i += 2){
      const code = parseInt(strHex.slice(i, i + 2), 16);
      if (!Number.isFinite(code)) break;
      out += String.fromCharCode(code);
    }
    return out;
  }

  function decodeAddress(resultHex){
    if (!resultHex || typeof resultHex !== 'string' || !resultHex.startsWith('0x')) return '';
    const hex = resultHex.slice(2);
    if (hex.length < 64) return '';
    return '0x' + hex.slice(24, 64);
  }

  async function fetchSvg(punkId){
    // punkImageSvg(uint16) selector = 0x74beb047
    const selector = '74beb047';
    const data = '0x' + selector + hexPad(punkId, 32);
    const res = await ethCall(CFG.contract_data, data);
    return decodeAbiString(res);
  }

  async function fetchV1WrappedOwner(punkId){
    // ownerOf(uint256) selector = 0x6352211e
    const selector = '6352211e';
    const data = '0x' + selector + hexPad(punkId, 32);
    const res = await ethCall(CFG.contract_v1_wrapper, data);
    return decodeAddress(res);
  }

  btn.on('click', async function(){
    const postId = btn.data('post-id');
    const punkId = parseInt($('input[name="sbpr_punk_id"]').val(), 10);
    if (!Number.isFinite(punkId)) {
      status.text('Set a Punk ID first.');
      return;
    }

    status.text('Importing…');
    btn.prop('disabled', true);

    try {
      const payload = {};

      // Always try to fetch SVG (this is what powers the homepage).
      try {
        const svg = await fetchSvg(punkId);
        if (svg) payload.svg = svg;
      } catch (e){
        // ignore
      }

      // Wrapped status (cheap call). If it reverts, punk is not wrapped.
      try {
        const owner = await fetchV1WrappedOwner(punkId);
        if (owner) {
          payload.v1_wrapped = 1;
          payload.v1_wrapped_owner = owner;
        } else {
          payload.v1_wrapped = 0;
        }
      } catch (e){
        payload.v1_wrapped = 0;
      }

      const resp = await $.post(CFG.ajax_url, {
        action: 'sbpr_import',
        nonce: CFG.nonce,
        post_id: postId,
        payload: JSON.stringify(payload)
      });

      if (!resp || !resp.success) {
        status.text('Failed.');
        return;
      }

      const updated = (resp.data && resp.data.updated) ? resp.data.updated : [];
      status.text(updated.length ? ('Saved: ' + updated.join(', ')) : 'Nothing new saved.');
      window.location.reload();
    } catch (e){
      status.text('Failed.');
    } finally {
      btn.prop('disabled', false);
    }
  });
});
