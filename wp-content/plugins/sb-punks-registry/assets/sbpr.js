
(function(){
  function ready(fn){ if(document.readyState !== 'loading'){ fn(); } else { document.addEventListener('DOMContentLoaded', fn); } }
  ready(function(){
    const mag = document.querySelector('.sbpr-mag');
    const magInner = mag ? mag.querySelector('.sbpr-mag__inner') : null;
    if(!mag || !magInner) return;

    function clearMag(){
      mag.classList.remove('is-on');
      magInner.innerHTML = '';
    }
    document.addEventListener('mouseover', function(e){
      const a = e.target.closest && e.target.closest('.sbpr-tile');
      if(!a) return;

      const img = a.querySelector('img');
      const svgWrap = a.querySelector('.sbpr-tile__svg');

      magInner.innerHTML = '';
      if(img){
        const clone = img.cloneNode(true);
        clone.removeAttribute('loading');
        magInner.appendChild(clone);
        mag.classList.add('is-on');
      } else if(svgWrap){
        // clone inner SVG wrapper
        const clone = svgWrap.cloneNode(true);
        magInner.appendChild(clone);
        mag.classList.add('is-on');
      } else {
        clearMag();
      }
    });
    document.addEventListener('mouseout', function(e){
      const leaving = e.target.closest && e.target.closest('.sbpr-tile');
      if(leaving) clearMag();
    });
  });
})();
