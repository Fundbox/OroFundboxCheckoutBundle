define(function (require) {
  'use strict';

  var FundboxCheckoutComponent;
  var _ = require('underscore');
  var $ = require('jquery');
  var mediator = require('oroui/js/mediator');
  var BaseComponent = require('oroui/js/app/components/base/component');
  var FbxChexckout = require('fundboxcheckout/js/app/components/fundbox-checkout');
  var routing = require('routing');
  var TRANSACTION_TOKEN_KEY = 'fbx_transaction_token';
  
  FundboxCheckoutComponent = BaseComponent.extend({
    /**
     * @property {Object}
     */
    options: {
      paymentMethod: null,
      publicKey: null,
      envUrl: null,
      transactionType: null,
      routeName: 'fundbox_checkout_order_details',
      orderId: null
    },

    /**
     * @property {}
     */
    fbxCheckout: null,

    /**
     * @property {}
     */
    orderDetails: null,

    /**
     * @property {}
     */
    paymentMethod: null,

    /**
     * @property {}
     */
    $form: null,

    /**
     * @property {}
     */
    transactionToken: null,

    /**
     * @inheritDoc
     */
    constructor: function FundboxCheckoutComponent() {
      FundboxCheckoutComponent.__super__.constructor.apply(this, arguments);
    },

    /**
     * @inheritDoc
     */
    initialize: function (options) {
      var orderId = this.getOrderId();
      this.options = _.extend({}, this.options, options, { orderId: orderId });
      this.updateOrderDetailsAndPM();
      this.fbxCheckout = FbxChexckout.configure({ 
        fbxKey: this.options.publicKey,
        env: this.options.envUrl,
        onComplete: this.handleFbxCheckoutComplete.bind(this)
      });

      this.$form = $('[data-role=checkout-content]').find('form');
      this.$form.on('submit', this.handleSubmit.bind(this));
      this._moveLastEventToFirst(this.$form, 'submit');
      mediator.on('frontend:coupons:changed', this.updateOrderDetailsAndPM, this);
      mediator.on('checkout-content:updated', this.updateOrderDetailsAndPM, this);
      mediator.on('checkout:place-order:response', this.handleResponseRedirect, this);
    },

    _moveLastEventToFirst: function _moveEventToFirst($element, event) {
      var eventsList = jQuery._data($element.get(0), 'events')[event];
      var lastEvent = eventsList.pop();
      eventsList.splice(0, 0, lastEvent);
    },

    handleResponseRedirect: function handleResponseRedirect(event) {
      mediator.execute('showLoading');
      mediator.execute(
        'redirectTo',
        { url: event.responseData.returnUrl + 
            '?' + TRANSACTION_TOKEN_KEY + 
            '=' + this.transactionToken
        },
        { redirect: true }
      );
      this.transactionToken = null;
    },

    getOrderId: function getOrderId() {
      return window.location.pathname.split('/').pop() || 0;
    },

    setOrderDetailsAndPM: function setOrderDetailsAndPM(data) {
      this.orderDetails = data.orderDetails;
      this.paymentMethod = data.paymentMethod;
    },

    updateOrderDetailsAndPM: function updateOrderDetailsAndPM() {
      mediator.execute('showLoading');
      this.getOrderDetailsAndPM()
        .done(this.setOrderDetailsAndPM.bind(this))
        .always(function() { mediator.execute('hideLoading'); });
    },

    getOrderDetailsAndPM: function getOrderDetailsAndPM() {
      return $.ajax({
        url: routing.generate(this.options.routeName, { entityId: this.options.orderId })
      });
    },

    /**
     * @param {Object} eventData
     */
    handleSubmit: function handleCheckoutSubmit(e) {
      if (this.paymentMethod === this.options.paymentMethod && !this.transactionToken) {
        e.preventDefault();
        e.stopImmediatePropagation();
        this.fbxCheckout.open({
          orderDetails: _.extend({}, this.orderDetails, { transaction_type: this.options.transactionType })
        });
        mediator.execute('hideLoading');
      }
    },

    /**
     * @param {Object} eventData
     */
    handleFbxCheckoutComplete: function handleFbxCheckoutComplete(transactionToken) {
      this.transactionToken = transactionToken;
      this.$form.trigger('submit');
    },

    dispose: function () {
      if (this.disposed) {
        return;
      }
      this.fbxCheckout = null;
      this.transactionToken = null;
      mediator.off('frontend:coupons:changed', this.updateOrderDetailsAndPM, this);
      mediator.off('checkout-content:updated', this.updateOrderDetailsAndPM, this);
      mediator.off('checkout:place-order:response', this.handleSubmit, this);

      FundboxCheckoutComponent.__super__.dispose.call(this);
    }
  });

  return FundboxCheckoutComponent;
});
