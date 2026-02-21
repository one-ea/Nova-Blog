{const u=()=>{if(document.getElementById("md-ripple-styles"))return;const e=document.createElement("style");e.id="md-ripple-styles",e.textContent=`
      .md-ripple { position: relative; overflow: hidden; -webkit-tap-highlight-color: transparent; }
      .md-ripple__wave {
        position: absolute;
        border-radius: 50%;
        background: var(--md-ripple-color, currentColor);
        opacity: 0.16;
        pointer-events: none;
        transform: scale(0);
        animation: md-ripple-expand 300ms cubic-bezier(0.4, 0, 0.2, 1) forwards;
      }
      .md-ripple__wave--fade {
        animation: md-ripple-fade 300ms cubic-bezier(0.4, 0, 0.2, 1) forwards;
      }
      @keyframes md-ripple-expand {
        to { transform: scale(1); opacity: 0.12; }
      }
      @keyframes md-ripple-fade {
        to { opacity: 0; }
      }
    `,document.head.appendChild(e)},f=(e,i,o)=>{const r=e.getBoundingClientRect(),a=i-r.left,s=o-r.top,d=Math.hypot(Math.max(a,r.width-a),Math.max(s,r.height-s)),m=d*2,n=document.createElement("span");return n.className="md-ripple__wave",n.style.width=`${m}px`,n.style.height=`${m}px`,n.style.left=`${a-d}px`,n.style.top=`${s-d}px`,e.appendChild(n),n},p=e=>{!e||e.classList.contains("md-ripple__wave--fade")||(e.classList.add("md-ripple__wave--fade"),e.addEventListener("animationend",()=>e.remove(),{once:!0}))};let t=null;const v=e=>{if(e.button&&e.button!==0)return;const i=e.target.closest(".md-ripple");i&&(window.matchMedia("(prefers-reduced-motion: reduce)").matches||(t=f(i,e.clientX,e.clientY)))},h=()=>{if(!t)return;const e=t;t=null;const i=e.getAnimations?.()?.[0]?.currentTime??150,o=Math.max(0,150-i);setTimeout(()=>p(e),o)},c=()=>{t&&(p(t),t=null)},l=()=>{u(),document.addEventListener("pointerdown",v,{passive:!0}),document.addEventListener("pointerup",h,{passive:!0}),document.addEventListener("pointerleave",c,{passive:!0}),document.addEventListener("pointercancel",c,{passive:!0})};document.readyState==="loading"?document.addEventListener("DOMContentLoaded",l):l()}
