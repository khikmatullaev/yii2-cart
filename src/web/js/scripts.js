if (typeof dvizh == "undefined" || !dvizh) {
    var dvizh = {};
}

dvizh.cart = {
    init: function () {

        productElementCount = '[data-role=product-element-count]';
        cartElementsCount = '[data-role=cart-element-count]';
        buyElementButton = '[data-role=cart-buy-button]';
        deleteElementButton = '[data-role=cart-delete-button]';
        truncateCartButton = '[data-role=truncate-cart-button]';

        dvizh.cart.csrf = jQuery('meta[name=csrf-token]').attr("content");
        dvizh.cart.csrf_param = jQuery('meta[name=csrf-param]').attr("content");




        jQuery(document).on('change', cartElementsCount, function () {

            var self = this,
                url = jQuery(self).data('href');

            if (jQuery(self).val() < 0) {
              jQuery(self).val('0');
                return false;
            }

            cartElementId = jQuery(self).data('id');
            cartElementCount = jQuery(self).val();
            cartQuantityUrl = jQuery(self).data('cart-quantity-url');

            jQuery.ajax({
                url: cartQuantityUrl,
                type: "post",
                data: { id: cartElementId, quantity: cartElementCount },
                success: function (answer) {
                  json = JSON.parse(answer);

                  if( json.bool == true ) {
                        dvizh.cart.changeElementCount(cartElementId, cartElementCount, url);
                        dvizh.cart.changeElementCost(cartElementId, cartElementCount, url);
                    }else{
                      //alert(json.error);
                      Swal({
                          type: 'error',
                          title: 'Upozornění',
                          text: json.error,
                      });
                      jQuery(self).val(json.quantity);
                    }
                }
            });
        });


        jQuery(document).on('change', productElementCount, function () {

            var self = jQuery(".dvizh-cart-element-before-count");
                url = jQuery(self).data('href'),
                count_product = jQuery(self).val();

            if (jQuery(self).val() < 0) {
              jQuery(self).val('0');
                return false;
            }



            setTimeout(function(){
                cartElementId = jQuery(self).data('id');
                cartElementCount = jQuery(self).val();
                cartQuantityUrl = jQuery(self).data('cart-quantity-url');
                var piece_price = jQuery(".dvizh-shop-price").html();

                console.log(piece_price + " " + count_product);
                console.log("sum " + piece_price * count_product);
                jQuery(".dvizh-shop-price").hide();
                jQuery(".price-product-sum").show();
                jQuery(".price-product-sum").html(piece_price*count_product);
            },500);

        });

        jQuery(document).on('click', buyElementButton, function () {

            if( jQuery('#options-exist-flag').val() && !jQuery(".dvizh-option-values-before").val() ) {
                alert('Choose the options');
                return false;
            }

            var self = this,
                url = jQuery(self).data('url'),
                productQuantityCheckUrl = jQuery(self).data('product-url'),
                productOptionQuantityCheckUrl = jQuery(self).data('product-option-url'),
                itemModelName = jQuery(self).data('model'),
                itemId = jQuery(self).data('id'),
                itemCount = jQuery(self).data('count'),
                itemPrice = jQuery(self).data('price'),
                itemOptions = jQuery(self).data('options');

            var checkQuantityUrl = jQuery('#options-exist-flag').val() ? productOptionQuantityCheckUrl : productQuantityCheckUrl;

            jQuery.ajax({
                url: checkQuantityUrl,
                type: "post",
                data: { productId: itemId, productOptionId: itemOptions, quantity: itemCount },
                success: function (answer) {
                    json = JSON.parse(answer);

                    if( json.bool == true )
                        dvizh.cart.addElement(itemModelName, itemId, itemCount, itemPrice, itemOptions, url);
                    else
                        //alert(json.error);

                        Swal({
                            type: 'error',
                            title: 'Upozornění',
                            text: json.error,
                        });
                }
            });

            return false;
        });

        jQuery(document).on('click', truncateCartButton, function () {

            var self = this,
                url = jQuery(self).data('url');

            dvizh.cart.truncate(url);

            return false;
        });

        jQuery(document).on('click', deleteElementButton, function (e) {

            e.preventDefault();

            var self = this,
                url = jQuery(self).data('url'),
                elementId = jQuery(self).data('id');

            dvizh.cart.deleteElement(elementId, url);

            if (lineSelector = jQuery(self).data('line-selector')) {
                jQuery(self).parents(lineSelector).last().hide('slow');
            }

            return false;
        });

        jQuery(document).on('click', '.dvizh-arr', this.changeInputValue);
        jQuery(document).on('change', '.dvizh-cart-element-before-count', this.changeBeforeElementCount);
        jQuery(document).on('change', '.dvizh-option-values-before', this.changeBeforeElementOptions);
        jQuery(document).on('change', '.dvizh-option-values', this.changeElementOptions);

        return true;
    },
    elementsListWidgetParams: [],
    jsonResult: null,
    csrf: null,
    csrf_param: null,
    changeElementOptions: function () {
        jQuery(document).trigger("changeCartElementOptions", this);

        var id = jQuery(this).data('id');

        var options = {};

        if (jQuery(this).is('select')) {
            var els = jQuery('.dvizh-cart-option' + id);
        }
        else {
            var els = jQuery('.dvizh-cart-option' + id + ':checked');
            console.log('radio');
        }

        jQuery(els).each(function () {
            var name = jQuery(this).data('id');

            options[id] = jQuery(this).val();
        });

        var data = {};
        data.CartElement = {};
        data.CartElement.id = id;
        data.CartElement.options = JSON.stringify(options);

        dvizh.cart.sendData(data, jQuery(this).data('href'));

        return false;
    },
    changeBeforeElementOptions: function () {
      var id = jQuery(this).data('id');
        var filter_id = jQuery(this).data('filter-id');
        var buyButton = jQuery('.dvizh-cart-buy-button' + id);

        var options = jQuery(buyButton).data('options');
        if (!options) {
            options = {};
        }

        options[filter_id] = jQuery(this).val();

        jQuery(buyButton).data('options', options);
        jQuery(buyButton).attr('data-options', options);

        jQuery(document).trigger("beforeChangeCartElementOptions", id);

        return true;
    },
    deleteElement: function (elementId, url) {

        dvizh.cart.sendData({elementId: elementId}, url);

        return false;
    },
    changeInputValue: function () {

      var val = parseInt(jQuery(this).siblings('input').val());
        var input = jQuery(this).siblings('input');

        if (jQuery(this).hasClass('dvizh-downArr')) {
            if (val <= 0) {
                return false;
            }
            jQuery(input).val(val - 1);
        }
        else {
            jQuery(input).val(val + 1);
        }

        jQuery(input).change();

        return false;
    },
    changeBeforeElementCount: function () {
      if (jQuery(this).val() <= 0) {
            jQuery(this).val('0');
        }

        var id = jQuery(this).data('id');
        var buyButton = jQuery('.dvizh-cart-buy-button' + id);
        jQuery(buyButton).data('count', jQuery(this).val());
        jQuery(buyButton).attr('data-count', jQuery(this).val());

        return true;
    },
    changeElementCost: function(cartElementId, cartElementCount) {
        var newCost = jQuery('.dvizh-cart-element-price'+cartElementId).html() * cartElementCount;
        jQuery('.dvizh-cart-element-cost'+cartElementId).html(newCost);
    },
    changeElementCount: function (cartElementId, cartElementCount, url) {
        var data = {};

        data.CartElement = {};
        data.CartElement.id = cartElementId;
        data.CartElement.count = cartElementCount;

        dvizh.cart.sendData(data, url);

        return false;
    },
    addElement: function (itemModelName, itemId, itemCount, itemPrice, itemOptions, url) {

        var data = {};
        data.CartElement = {};
        data.CartElement.model = itemModelName;
        data.CartElement.item_id = itemId;
        data.CartElement.count = itemCount;
        data.CartElement.price = itemPrice;
        data.CartElement.options = itemOptions;

        dvizh.cart.sendData(data, url);
        const swalWithBootstrapButtons = Swal.mixin({
            confirmButtonClass: 'btn btn-success modal-cart-redirect',
            cancelButtonClass: 'btn btn-default',
            buttonsStyling: false,
        })

        swalWithBootstrapButtons({
                title: 'Přidáno do košíku',
                text: "Produkt byl přidán do košíku.",
                type: 'success',
                showCancelButton: true,
                confirmButtonText: 'Přejít do košíku',
                cancelButtonText: 'Pokračovat v nákupu',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                window.location.href = baseurl +"order/cart/index";
            } else if (
                // Read more about handling dismissals
                result.dismiss === Swal.DismissReason.cancel
            ) {
               // close window
            }
        });
        return false;
    },
    truncate: function (url) {
        dvizh.cart.sendData({}, url);
        return false;
    },
    sendData: function (data, link) {
        if (!link) {
            link = '/cart/element/create';
        }

        jQuery(document).trigger("sendDataToCart", data);

        data.elementsListWidgetParams = dvizh.cart.elementsListWidgetParams;
        data[dvizh.cart.csrf_param] = dvizh.cart.csrf;

        jQuery('.dvizh-cart-block').css({'opacity': '0.3'});
        jQuery('.dvizh-cart-count').css({'opacity': '0.3'});
        jQuery('.dvizh-cart-price').css({'opacity': '0.3'});

        jQuery.post(link, data,
            function (json) {
                jQuery('.dvizh-cart-block').css({'opacity': '1'});
                jQuery('.dvizh-cart-count').css({'opacity': '1'});
                jQuery('.dvizh-cart-price').css({'opacity': '1'});

                if (json.result == 'fail') {
                    console.log(json.error);
                }
                else {
                    dvizh.cart.renderCart(json);
                    $(document).trigger('dvizhCartChanged');
                }

            }, "json");

        return false;
    },
    renderCart: function (json) {
        if (!json) {
            var json = {};
            jQuery.post('/cart/default/info', {},
                function (answer) {
                    json = answer;
                }, "json");
        }

        jQuery('.dvizh-cart-block').replaceWith(json.elementsHTML);
        jQuery('.dvizh-cart-count').html(json.count);
        jQuery('.dvizh-cart-price').html(json.price);

        jQuery(document).trigger("renderCart", json);

        return true;
    },
};

$(function() {
    dvizh.cart.init();
});

