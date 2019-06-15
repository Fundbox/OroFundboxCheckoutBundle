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

      this.$form = $('[data-role=checkout-content]').find('form');
      this.handleSubmit =  this.handleSubmit.bind(this);
      this.$form.on('submit', this.handleSubmit);
      this._moveLastEventToFirst(this.$form, 'submit');
      mediator.on('frontend:coupons:changed', this.updateOrderDetailsAndPM, this);
      mediator.on('checkout-content:updated', this.updateOrderDetailsAndPM, this);
      mediator.on('checkout:place-order:response', this.handleResponseRedirect, this);
      mediator.on('checkout:payment:method:changed', this.handlePaymentMethodChanged, this);
      mediator.on('checkout-content:initialized', this.handleCheckoutContentInit, this);
    },

    _moveLastEventToFirst: function _moveEventToFirst($element, event) {
      var eventsList = jQuery._data($element.get(0), 'events')[event];
      var lastEvent = eventsList.pop();
      eventsList.splice(0, 0, lastEvent);
    },

    handleCheckoutContentInit: function() {
      mediator.trigger('checkout:payment:method:refresh');
    },

    handlePaymentMethodChanged: function handlePaymentMethodChanged(eventData) {
      this.paymentMethod = eventData.paymentMethod;
    },

    handleResponseRedirect: function handleResponseRedirect(event) {
      if (this.transactionToken && event.responseData.paymentMethod === this.options.paymentMethod) {
        event.stopped = true;
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
      }
    },

    getOrderId: function getOrderId() {
      return window.location.pathname.split('/').pop() || 0;
    },

    setOrderDetailsAndPM: function setOrderDetailsAndPM(data) {
      this.orderDetails = data.orderDetails;
      this.paymentMethod = data.paymentMethod || this.paymentMethod;
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
      if (
        this.paymentMethod &&
        this.paymentMethod === this.options.paymentMethod && 
        !this.transactionToken
      ) {
        e.preventDefault();
        e.stopImmediatePropagation();
        var fbxCheckout = FbxChexckout.configure({ 
          fbxKey: this.options.publicKey,
          env: this.options.envUrl,
          onComplete: this.handleFbxCheckoutComplete.bind(this)
        });
        fbxCheckout.open({
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
      this.transactionToken = null;
      this.$form.off('submit', this.handleSubmit);
      mediator.off('frontend:coupons:changed', this.updateOrderDetailsAndPM, this);
      mediator.off('checkout-content:updated', this.updateOrderDetailsAndPM, this);
      mediator.off('checkout:place-order:response', this.handleSubmit, this);
      mediator.off('checkout:payment:method:changed', this.handlePaymentMethodChanged, this);
      mediator.off('checkout-content:initialized', this.handleCheckoutContentInit, this);

      FundboxCheckoutComponent.__super__.dispose.call(this);
    }
  });

  return FundboxCheckoutComponent;
});
