(function ($) {

  $(function () {
    /** change event on dropdown menu selection change */
   $('#category-filter').on('change', function (event)  {

      /** prevent any action */
      event.preventDefault();

      /** get category value from active selected option */
      var optionSelected = $(this).find("option:selected");
      var catID  = optionSelected.val(); 

      /** fade out the current div */
      $('.category-feed').fadeOut();

      data = {
        action: 'category_news_filter',
        category_news_nonce: category_news_vars.category_news_nonce,
        cat : catID, /** pass from selected option **/
        posts_per_page: 4,
      };

      $.post( category_news_vars.category_news_ajax_url, data, function(response) {
     
        if(response){
            /** display the results and fade it in */
            $('.category-feed').html( response ).fadeIn();
        };

      });

    });

  });

})(jQuery);
