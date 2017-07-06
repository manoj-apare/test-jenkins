/**
 * @file
 * Cypress Store js.
 */

var $ = jQuery;
$(function () {
    $(document).ready(function () {
        if ($(".user-logged-in").length > 0) {
    }
    else {
         $("body").addClass('not-logged-in');
    }
    $('.user-login-form input').removeAttr("data-original-title");
    $('#edit-coupon-redemption-coupons input#edit-coupon-redemption-coupons-code').removeAttr("data-original-title");
    // A $("body:not(user-logged-in)").addClass('not-logged-in');.
        $(".block-search-form-block").addClass('col-md-4 col-sm-4');
        var width = $(window).width();
        $(window).resize(function () {
            $('.block-search-form-block input[type="search"]').width($(window).width() - 41);
        });
        $('.block-search-form-block input[type="search"]').width($(window).width() - 41);
        $('.block-search-form-block input[type="search"]').parent().append('<input class="input-submit" type="submit" value="">');
        $('.block-search-form-block input[type="search"]').attr("placeholder", "Enter your keywords");
        $("#block-primarymenublock").addClass('col-md-4 hidden-xs');
        $('.language-menu, .account-menu').addClass("dropdown-menu");
        $('.primary-menu li:nth-child(2), .primary-menu li:last-child').addClass('dropdown expanded');
        $('.primary-menu li:nth-child(2) > a').on('click',function () {
            // $(this).toggleClass('open');.
            $('ul.account-menu').hide();
            $('ul.language-menu').toggle();
        });
        $('.user-menu li:first-child').on('click',function () {
            $('ul.language-menu').hide();
            $('ul.user-menu').toggle();
        });
        $('.user-logged-in .user-menu ul.menu.nav li ul.menu ').append('<img alt="Close" class="h1 close-nav" src="/themes/cypress_store/images/main-nav-caret.svg">');
        // $("[role='heading']").addClass('col-md-12 header');
        // $(".form-search").append("<input class="form-submit" type="submit" id="edit-submit">");.
        $('.region-header').prepend('<button type="button" class="navbar-toggle"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>');
            $('button.navbar-toggle').on('click',function () {
                $('.main-menu').toggleClass('hidden-xs');
                $('.menu-drop').hide();
                $('.navbar-toggle span:nth-child(2)').toggleClass('rotate-close-1');
                $('.navbar-toggle span:nth-child(3)').toggleClass('rotate-close-2');
                $('.navbar-toggle span:nth-child(4)').toggleClass('rotate-close-3');
            });
        $('a.user-icon').on('click', function () {
            // alert('ok');.
            // $('.primary-menu').toggleClass('hidden-xs');.
            $('.mobile-menu').toggle();
            $('.language-menu').hide();
            // $('.main-menu').hide();
        });
        $('.english').on('click', function (e) {
            $('.language-menu').toggle();
        });
        $('.mobile-menu .language-menu').removeClass('dropdown-menu');
        $('.main-menu ul.menu.nav ul').addClass('hidden-lg hidden-md hidden-sm');
        // $('.main-menu ul.menu.nav ul').removeClass('dropdown-menu').addClass('nav-xs');.
        $('.menu-drop').addClass('hidden-xs');
        $("#block-mainmenu > ul > li:nth-child(1)").click(function (e) {
            var $content = $('#solutions-menu-content');
            var isVisible = $content.is(":visible");
              $('.menu-drop').hide();
              $('.mobile-menu').hide();
              if (isVisible) {
                return;
            }
            $content.show();
            });
            $("#block-mainmenu > ul > li:nth-child(2)").click(function (e) {
                var $content = $('#products-menu-content');
                var isVisible = $content.is(":visible");
                $('.menu-drop').hide();
                $('.mobile-menu').hide();
                if (isVisible) {
                    return;
            }
            $content.show();
            });
            $("#block-mainmenu > ul > li:nth-child(3)").click(function (e) {
              var $content = $('#design-menu-content');
              var isVisible = $content.is(":visible");
              $('.menu-drop').hide();
              $('.mobile-menu').hide();
              if (isVisible) {
                return;
            }
            $content.show();
            });
            $("#block-mainmenu > ul > li:nth-child(4)").click(function (e) {
              var $content = $('#buy-menu-content');
              var isVisible = $content.is(":visible");
              $('.menu-drop').hide();
              $('.mobile-menu').hide();
              if (isVisible) {
                return;
              }
            $content.show();
            });
        /* close icon*/
          $("img.h1.close-nav").click(function () {
          $('.menu-drop').hide();
          $(".language-menu").hide();
          $(".account-menu").hide();
          $('.language-menu').css("display", "none");
          // $('.main-menu .menu.nav').hide();.
          });
        $(".mobile-menu img.h1.close-nav-user").click(function () {
            $('.mobile-menu').hide();
        });
        $('.main-menu .menu.nav li ul li:last-child').append('<img alt="Close" class="h1 close-nav" src="/themes/cypress_store/images/main-nav-caret.svg" />');
        $('.menu-drop').parent('div').addClass('menu-drop-parent');
        $('.main-menu .menu.nav > li > ul > li > ul').removeClass('dropdown-menu');
        $('.path-addressbook .views-view-grid').addClass('col-sm-12 col-md-12 col-lg-12');
        $('.path-addressbook .views-view-grid .views-row').addClass('col-sm-6 col-md-4 col-lg-3');
        // Review page.
        $('.path-checkout-review .layout-region-checkout-secondary .views-field-cart-product-image .field-content').removeClass('col-md-4 col-sm-4').addClass('col-md-3 col-sm-3');
        $('.path-checkout-review .layout-region-checkout-secondary .views-field-quantity  .field-content').removeClass('col-md-2 col-sm-2').addClass('col-md-1 col-sm-1');
        $('.path-checkout-review .layout-region-checkout-secondary .views-field-purchased-entity .field-content').removeClass('col-md-4 col-sm-4').addClass('col-md-5 col-sm-5');
        $('.path-checkout-review .layout-region-checkout-secondary .views-field-total-price__number  .field-content').removeClass('col-md-2 col-sm-2').addClass('col-md-3 col-sm-3');
        // Review page footer.
        $('.path-checkout-review .view-footer > div').addClass('container-fluid');
        $('.path-checkout-review .view-footer .order-total-line').addClass('col-lg-12 col-md-12 col-sm-12 col-xs-12');
        $('.path-checkout-review .view-footer .order-total-line-label').addClass('col-lg-3 col-lg-offset-7 col-md-3 col-md-offset-7 col-sm-3 col-sm-offset-7 col-xs-9');
        $('.path-checkout-review .view-footer .order-total-line-value').addClass('col-lg-1 col-md-1 col-sm-1 col-xs-3');
        // Orders page(cancel & track page).
        $('.order-information .order-total-line').parent().addClass('container-fluid');
        $('.order-information .order-total-line').addClass('col-lg-12 col-md-12 col-sm-12 col-xs-12');
        $('.order-information .order-total-line-label').addClass('col-lg-3 col-lg-offset-7 col-md-3 col-md-offset-7 col-sm-3 col-sm-offset-7 col-xs-9');
        $('.order-information .order-total-line-value').addClass('col-lg-1 col-md-1 col-sm-1 col-xs-3');
        });
        // Address-select-continue button.
        $('#edit-cypress-shipping-information-submit').wrap("<div class='address-select-btn'></div>");
        // Order-information page.
        /*if ($(".payment-information").length > 0) {
        }
        else {
            $(".parts-information").css("padding", "0 15px 0 0");
        }*/
        /*Fonts for IE.*/
        var ua = window.navigator.userAgent;
        var msie = ua.indexOf("MSIE ");
        if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
        // If Internet Explorer, return version number.
        $('head').append('<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">');
        }
        // Fonts for Windows Safari.
        var is_safari = navigator.userAgent.indexOf("Safari") > -1;
        if (is_safari) {
            $('head').append('<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">');
        }
        // cart separate lines
        $('.cart.cart-form .cypress-cart').last().css("border-bottom","none");
});
