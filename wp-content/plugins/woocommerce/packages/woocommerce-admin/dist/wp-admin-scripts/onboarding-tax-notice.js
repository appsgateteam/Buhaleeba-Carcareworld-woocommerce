this.wc=this.wc||{},this.wc.onboardingTaxNotice=function(t){var e={};function n(r){if(e[r])return e[r].exports;var o=e[r]={i:r,l:!1,exports:{}};return t[r].call(o.exports,o,o.exports,n),o.l=!0,o.exports}return n.m=t,n.c=e,n.d=function(t,e,r){n.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:r})},n.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},n.t=function(t,e){if(1&e&&(t=n(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var r=Object.create(null);if(n.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var o in t)n.d(r,o,function(e){return t[e]}.bind(null,o));return r},n.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return n.d(e,"a",e),e},n.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},n.p="",n(n.s=681)}({24:function(t,e){!function(){t.exports=this.wp.data}()},3:function(t,e){!function(){t.exports=this.wp.i18n}()},33:function(t,e,n){"use strict";n.d(e,"a",(function(){return a})),n.d(e,"b",(function(){return d})),n.d(e,"c",(function(){return f})),n.d(e,"d",(function(){return l})),n.d(e,"e",(function(){return b})),n.d(e,"g",(function(){return m})),n.d(e,"h",(function(){return p})),n.d(e,"f",(function(){return y}));var r=n(43),o=n.n(r),c=n(3),u=["wcAdminSettings","preloadSettings"],i="object"===("undefined"==typeof wcSettings?"undefined":o()(wcSettings))?wcSettings:{},s=Object.keys(i).reduce((function(t,e){return u.includes(e)||(t[e]=i[e]),t}),{}),a=s.adminUrl,d=(s.countries,s.currency),f=s.locale,l=s.orderStatuses,b=(s.siteTitle,s.wcAssetUrl);function m(t){var e=arguments.length>1&&void 0!==arguments[1]&&arguments[1],n=arguments.length>2&&void 0!==arguments[2]?arguments[2]:function(t){return t};if(u.includes(t))throw new Error(Object(c.__)("Mutable settings should be accessed via data store."));var r=s.hasOwnProperty(t)?s[t]:e;return n(r,e)}function p(t,e){var n=arguments.length>2&&void 0!==arguments[2]?arguments[2]:function(t){return t};if(u.includes(t))throw new Error(Object(c.__)("Mutable settings should be mutated via data store."));s[t]=n(e)}function y(t){return(a||"")+t}},43:function(t,e){function n(e){return"function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?t.exports=n=function(t){return typeof t}:t.exports=n=function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},n(e)}t.exports=n},681:function(t,e,n){"use strict";n.r(e);var r=n(3),o=n(24),c=n(77),u=n(33),i=function(){var t=document.querySelector(".woocommerce-save-button");t.classList.contains("is-clicked")||(t.classList.add("is-clicked"),function t(){return null!==document.querySelector(".blockUI.blockOverlay")?new Promise((function(t){window.requestAnimationFrame(t)})).then((function(){return t()})):Promise.resolve(!0)}().then((function(){return Object(o.dispatch)("core/notices").createSuccessNotice(Object(r.__)("You've added your first tax rate!",'woocommerce'),{id:"WOOCOMMERCE_ONBOARDING_TAX_NOTICE",actions:[{url:Object(u.f)("admin.php?page=wc-admin"),label:Object(r.__)("Continue setup.",'woocommerce')}]})})))};Object(c.a)((function(){var t=document.querySelector(".woocommerce-save-button");t&&t.addEventListener("click",i)}))},77:function(t,e,n){"use strict";function r(t){"complete"!==document.readyState&&"interactive"!==document.readyState?document.addEventListener("DOMContentLoaded",t):t()}n.d(e,"a",(function(){return r}))}});