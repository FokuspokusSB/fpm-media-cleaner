!function($){"use strict";function e(e,n){return $.post(ajaxurl,{action:e,...n}).pipe((function(e){try{return JSON.parse(e)}catch(n){return function(e,n="log"){console[n]("----------------------------------"),console[n]("[FPM Media Cleaner]"),console[n](`${e}`),console[n]("----------------------------------")}(n.message,"error"),e}}))}function n(e){const n=document.createElement("td");return n.innerHTML=e,n}function t(){if(!document.querySelector("[data-fpm-media-cleaner]"))return;const t={status:document.querySelector("[data-options-status]"),lastUpdate:document.querySelector("[data-options-last-update]")};e("media-clean-get-cache",{}).done((function(e){const t=document.querySelector("[data-clean-media]");if(!t)return alert("Fehler");const o=t.querySelector("tbody");for(const t of e){const e=document.createElement("tr");e.appendChild(n(t.id));t.guid.indexOf("/uploads");e.appendChild(n(t.guid)),e.appendChild(n(t.post_modified)),e.appendChild(n(t.post_status)),e.appendChild(n(t.post_title)),e.appendChild(n(t.post_type)),o.appendChild(e)}})).fail((function(){console.error("error")})),e("media-clean-get-options",{}).done((function(e){for(const n of e)switch(n.option_key){case"status":t.status.innerHTML=n.option_value;break;case"last_update":t.lastUpdate.innerHTML=n.option_value}})).fail((function(){console.error("error")})),document.querySelector("[data-fpm-media-cleaner-refresh]").addEventListener("click",(function(){e("media-clean-fill-cache",{}).done((function(e){console.log("safty",e)})).fail((function(){console.error("error")}))})),document.querySelector("[data-fpm-media-cleaner-remove]").addEventListener("click",(function(){e("media-clean-remove",{}).done((function(e){})).fail((function(){console.error("error")}))}))}$((function(){t()})),console.log(ajaxurl)}(jQuery);