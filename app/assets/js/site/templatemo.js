/*

TemplateMo 559 Zay Shop

https://templatemo.com/tm-559-zay-shop

*/

'use strict';
$(document).ready(function() {
    // Product detail
    $('.product-links-wap a').click(function(){
      var this_src = $(this).children('img').attr('src');
      $('#product-detail').attr('src',this_src);
      return false;
    });

    $(document).on('click', '.btn-minus', function() {
        let wrapper = $(this).closest('.quantity-input-wrapper');
        let val = parseInt(wrapper.find('.quantity-input').val());
        val = isNaN(val) ? 1 : val;
        val = val > 0 ? val - 1 : 0;

        wrapper.find('.quantity-input').val(val).trigger('change')

        return false;
    });

    $(document).on('click', '.btn-plus', function() {
        let wrapper = $(this).closest('.quantity-input-wrapper');
        let val = parseInt(wrapper.find('.quantity-input').val());
        val = isNaN(val) ? 1 : val;
        val++;

        wrapper.find('.quantity-input').val(val).trigger('change')

        return false;
    });

    $('.btn-size').click(function(){
      var this_val = $(this).html();
      $("#product-size").val(this_val);
      $(".btn-size").removeClass('btn-secondary');
      $(".btn-size").addClass('btn-success');
      $(this).removeClass('btn-success');
      $(this).addClass('btn-secondary');
      return false;
    });
    // End roduct detail

});
