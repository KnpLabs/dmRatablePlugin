dmRatablePlugin provides a Doctrine behavior, controllers, templates and assets to set up a rating system quickly.

## Doctrine behavior

To declare that a model is ratable, add the behavior in your config/doctrine/schema.yml file:

    Article:
      actAs:
        DmRatable:

Run migrations and update the model:

    php symfony doctrine:generate-migrations-diff
    php symfony doctrine:migrate
    php symfony dm:setup

A new table, article_rate, is created to store the article rates.

## Display rating stars

Include the plugin assets in your apps/front/config/view.yml file:

    stylesheets:
      - dmRatablePlugin.stars
      - ...

    javascripts:
      - lib.ui-core
      - lib.ui-widget
      - dmRatablePlugin.stars
      - ...

Include the dmRatable/rating component from your record template.

    include_component('dmRatable', 'rating', array('record' => $article));

## JavaScript

To enable the jquery.stars plugin, paste the following code to your web/js/front.js file:

    $('.dm_ratable_rating').each(function() {
      var $this = $(this).stars({
        inputType: "select",
        callback: function(ui, type, value){
          $this.find('.message').html(value==ui.options.cancelValue ? "Rating removed" : "Rating saved! ("+value+")").stop().css("opacity", 1).fadeIn(30);
          setTimeout(function(){ $this.find('.message').fadeOut(1000) }, 2000);
          $.ajax({
            type:     'POST',
            url:      $this.find('form').attr('action'),
            data:     { hash: $this.attr('data-hash'), value: value, type: type },
            success:  function(data) {
              if(data.new_value) {
                  $this.stars('select', newValue);
              }
            }
          });
        }
      });
    });

Then customize it to fit your needs.

## Admin

To show the rating of each record in its admin module, add a _rating partial:

    # apps/admin/modules/article/config/generator.yml
    
      list:
        display:
          - _rating

    # apps/admin/modules/article/templates/_rating.php

    <?php
    echo sprintf('%d/%d',
      $article->getRating(),
      $article->getTable()->getTemplate('DmRatable')->getOption('max_rate')
    );

