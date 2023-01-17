/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/javascript.js to edit this template
 */


jQuery( function ( $ ) {
  
    lightGallery(document.getElementById('main'), {
        plugins: [lgZoom],
        selector: '.custom-lightbox',
        controls: false,
        enableDrag: false,
        pager: false
    });
  
});
