define(function(require) {
  'use strict'

  var FundboxCheckoutPaymentMethodComponent;
  var $ = require('jquery');
  var BaseComponent = require('oroui/js/app/components/base/component');
  var FbxChexckout = require('fundboxcheckout/js/app/components/fundbox-checkout');

  FundboxCheckoutPaymentMethodComponent = BaseComponent.extend({
    /**
     * @property {Object}
     */
    options: {
      publicKey: null,
      envUrl: null
    },

    /**
     * @property {jQuery}
     */
    $el: null,

    /**
     * @property {}
     */
    fbxCheckout: null,

    /**
     * @inheritDoc
     */
    constructor: function FundboxCheckoutPaymentMethodComponent() {
      FundboxCheckoutPaymentMethodComponent.__super__.constructor.apply(
        this,
        arguments
      );
    },

    /**
     * @inheritDoc
     */
    initialize: function(options) {
      this.options = _.extend({}, this.options, options);
      this.fbxCheckout = FbxChexckout.configure({ 
        fbxKey: this.options.publicKey,
        env: this.options.envUrl,
        onComplete: function() {}
      });
      this.$el = $('#fbx-learn-more-link');
      this.$el.on('click', this.handleLearnMoreClick.bind(this));
    },

    /**
     * @param {Object} event
     */
    handleLearnMoreClick: function(event) {
      event.preventDefault();
      this.fbxCheckout.learnMore();
    },

    dispose: function() {
      if (this.disposed || !this.disposable) {
        return;
      }

      this.$el.off();

      FundboxCheckoutPaymentMethodComponent.__super__.dispose.call(this);
    }
  })

  return FundboxCheckoutPaymentMethodComponent;
})
