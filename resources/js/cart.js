import './app';

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


$(document).ready(function () {
    collapseCartPage();
    let productCards = $(".card");
    productCards.each(function () {
        $(this).find(".size-buttons").first().addClass("btn-dark");
    });

    $("#billContainer").on("click", ".remove-giftcard", function() {
        const button = $(this);
        $.ajax({
            url: '/remove-giftcard',
            type: 'POST',
            data: {giftcard: ''},
            success: function (response) {
                if(response.success) {
                    button.closest(".order-giftcard-info").empty();
                    $(".giftcard-error-message").empty();
                    showTotal(response.total);
                    console.log(typeof(response.total));
                }
            }

        });
    });
    $(".order-price-container").on("click", ".remove-giftcard", function() {
        const button = $(this);
        $.ajax({
            url: '/remove-giftcard',
            type: 'POST',
            data: {giftcard: ''},
            success: function (response) {
                if(response.success) {
                    button.closest(".order-giftcard-info").empty();
                    $(".giftcard-error-message").empty();
                    showTotal(response.total);
                    console.log(typeof(response.total));
                }
            }

        });
    });

    $("#applyGiftCardForm").on("submit" , function(e) {
        e.preventDefault();
        let giftCode = $("#giftCode").val();

        $.ajax({
           url: '/validate-code?action=validateGiftCard',
           type: 'POST',
           data: {code: giftCode},
            success: function (response) {
               if(response.success) {
                   showTotal(response.total);
                    $(".giftcard-error-message").text("Giftcard Applied.");
                    $(".order-giftcard-info").html(`
                    <span>Giftcard - ${response.data.code}
                        <a class="remove-giftcard btn btn-danger btn-sm p-1 ms-3" style="font-size: 0.75rem; line-height: 1;">X</a>
                    </span>
                    <span class="discounted-value fw-semibold text-dark">${response.data.balance}</span>
                    `);
               } else {
                   $(".giftcard-error-message").text(response.message);
               }
            }
        });
    })
    //{id: 4, code: "25155122", initial_balance: "60.00", balance: "60.00", status: 1, user_id: "anyone",…}

    $("#cardContainer").on("click", ".size-buttons", function() {
        $(this).addClass("btn-dark");
        $(this).siblings().removeClass("btn-dark");
        let selectedPrice = $(this).data('price');
        let selectedSize = $(this).data('size');
        let card = $(this).parents(".card");
        let selectedVariant = $(this).data('variant-id');

        card.data("selected-size", selectedSize);
        card.data("selected-price", selectedPrice);
        card.data("selected-variant", selectedVariant);
        card.find(".product-price").text(`$${selectedPrice}`);

        alreadyInCart(selectedVariant, card);
    });


    $(".cart-container").on("click", ".remove-item-button", function() {
        console.log("remove button clicked");
        let card = $(this).parents('.cart-card');
        let variant = card.data('variant');
        card.remove();
        removeFromCart(variant);
    });


    $("#cartButton").on("click", function() {
        $.ajax({
            url: '/render-cart-summary?action=renderCartSummary',
            type: 'POST',
            data: {cart: ''},
            success: function (response) {
                let cart = response.cart;
                let subtotal = response.subtotal;

                showQuantity(response.quantity);

                let cartHTML = '';
                Object.values(cart).forEach(item => {
                    cartHTML += `
                    <div class="cart-card"
                     data-variant="${item['id']}"
                     data-size="${item['attributes']['size']}"
                     data-price="${item['price']}"
                     data-name="${item['name']}"
                     data-image="${item['attributes']['image']}"
                    >
                      <div class="cart-image">
                        <img src="${item['attributes']['image']}" class="cart-item-image" alt="food image">
                      </div>
                      <div class="cart-item-details">
                        <p class="cart-item-name">${item["name"]}</p>
                        <div class="size-details" style="display: flex;" data-size="${item['attributes']['size']}">
                        <p>Size: </p>
                        <p class="cart-item-size">${item["attributes"]["size"]}</p>
                        </div>

                        <div class="add-quantity-button"><input type="number" class="form-control cart-quantity" value="${item['quantity']}" min="0" data-id="" required></div>
                      </div>
                      <div class="cart-price-details" data-price="">
                        <button class="btn btn-outline-danger btn-sm d-inline-flex justify-content-center align-items-center p-0 remove-item-button"
                                style="width: 30px; height: 30px;">
                          ×
                        </button>

                        <p class="cart-item-price">$${(item['price']).toFixed(2)}</p>
                      </div>
                    </div>
                    `;
                });

                $(".cart-container").html(cartHTML);
            }
        });
    });


    $(".cart-container").on("input", ".cart-quantity", function () {
        let card = $(this).parents(".cart-card");
        let size = card.data("size");
        let price = card.data("price");
        let variant = card.data("variant");
        let name = card.data("name");
        let quantity = $(this).val();
        let images = card.data("image");

        let menuCards = $(".card");
        menuCards.each(function() {
            let cardVariant = $(this).data("selected-variant");
            if( cardVariant == variant) {
                $(this).find(".cart-quantity").val(quantity);

                let quantityValue = $(this).find(".cart-quantity").val();
                if (quantityValue == 0) {
                    $(this).find(".add-button").html(`
                    <a class="btn btn-primary px-3 py-1 d-flex justify-content-center align-items-center fw-bold add-to-cart-button">
                            Add &gt;
                        </a>
                    `)
                }
            }
        });

        if (quantity == 0) {
            removeFromCart(variant);
            card.remove();
        } else {
            const product = {
                variantId: variant,
                productName: name,
                productSize: size,
                productPrice: price,
                quantity: quantity,
                image: images,
            };
            updateCart(product);
        }
    });


    $("#cardContainer").on("input", ".cart-quantity", function () {
        let card = $(this).parents(".card");
        let size = card.data("selected-size");
        let price = card.data("selected-price");
        let variant = card.data("selected-variant");
        let name = card.data("name");
        let quantity = $(this).val();
        let images = card.data("image");

        if (quantity == 0) {
            removeFromCart(variant);
            $(this).parent().html(`
			    <a class="btn btn-primary px-3 py-1 d-flex justify-content-center align-items-center fw-bold add-to-cart-button">Add ></a>
			`);
        } else {
            const product = {
                variantId: variant,
                productName: name,
                productSize: size,
                productPrice: price,
                quantity: quantity,
                image: images,
            };
            updateCart(product);
        }
    });


    $("#cardContainer").on("click", ".add-to-cart-button",  function() {
        let card = $(this).parents(".card");
        let size = card.data("selected-size");
        let price = card.data("selected-price");
        let variant = card.data("selected-variant");
        let name = card.data("name");
        let quantity = 1;
        let images = card.data("image");

        const product = {
            variantId: variant,
            productName: name,
            productSize: size,
            productPrice: price,
            quantity: quantity,
            image: images,
    };
        $(this).parent().html(`
            <input type="number" class="form-control cart-quantity ms-auto d-flex justify-content-center align-items-center" value="1" min="0" required>
        `);
        addToCart(product);
    });

    $('#closeModal').on('click', function () {
        $('#productModal').addClass('d-none');
    });

    // Close modal when clicking outside content
    $(window).on('click', function (e) {
        const $productModal = $('#productModal');
        if ($(e.target).is($productModal)) {
            $productModal.addClass('d-none');
        }
    });

    $("#searchProductForm").on("submit", function(e) {
        e.preventDefault();
        let searchValue = $("#search").val();
        $.ajax({
            url: '/search-products?action=searchProducts',
            type: 'POST',
            data: {search: searchValue},
            success: function(response) {

                let html = generateProductItemsHTML(response.variants);

                $('#productResults').html(html);
                $('#productModal').removeClass('d-none');
            }
        });
    });

    $("#productModal").on("input", "#searchProductsInput", function () {
        let value = $(this).val();
        $.ajax({
            url: '/search-product-variants?action=searchProducts',
            type: 'POST',
            data: {search: value},
            success: function(response) {
                let html = generateProductItemsHTML(response.variants);
                $('#productResults').html(html);
            }
        });
    });

    $("#addToOrderForm").on("submit", function(e) {
        e.preventDefault();

        let selectedVariants = [];
        $('input[name="variants[]"]:checked').each(function () {
            selectedVariants.push($(this).val());
        });

        if( selectedVariants.length == 0) {
            $(".add-order-error").text("Select at least one product");
        } else {
            $.ajax({
                url: '/add-to-order?action=addToOrder',
                type: 'POST',
                data: {variants: selectedVariants},
                success: function(response) {
                    let variants = response.variants;


                    $('#productModal').addClass('d-none');

                    let itemCards = $(".item-card");
                    let orderVariants = [];

                    itemCards.each(function () {
                        let cardVariant = $(this).data('variant');
                        orderVariants.push(cardVariant);
                    });

                    Object.values(variants).forEach(item => {
                        if(orderVariants.includes(item.id)) {
                            let matchingCard = $('.item-card').filter(function() {
                                return $(this).data('variant') === item.id;
                            });;
                            let quantityInput = matchingCard.find('.order-quantity');
                            let quantityInputValue = parseInt(quantityInput.val());
                            quantityInputValue += 1;
                            quantityInput.val(quantityInputValue);
                        } else {
                            let product = item.product;
                            $(".item-container").append(`
                                <div class="item-card d-flex align-items-center justify-content-between py-3"
                                     data-variant="${item.id}"
                                     data-size="${item.title}"
                                     data-price="${item.price}"
                                     data-name="${product.title}"
                                     data-image="${product.image_urls[0] ?? "no-image.png"}">

                                    <!-- IMAGE -->
                                    <div class="item-image me-3 flex-shrink-0">
                                        <img src="${product.image_urls[0] ?? "no-image.png"}"
                                             class="order-item-image img-fluid rounded"
                                             alt="food image"
                                             style="width: 80px; height: 80px; object-fit: cover;">
                                    </div>

                                    <!-- ITEM DETAILS -->
                                    <div class="order-item-details flex-grow-1">
                                        <p class="order-item-name fw-semibold mb-1">${product.title}</p>

                                        <div class="size-details d-flex align-items-center mb-2">
                                            <p class="mb-0 me-2">Size:</p>
                                            <p class="order-item-size mb-0 fw-medium">${item.title}</p>
                                        </div>

                                        <div class="add-quantity-button" style="max-width: 100px;">
                                            <input type="number"
                                                   class="form-control order-quantity text-center"
                                                   value="1"
                                                   min="0"
                                                   required>
                                        </div>
                                    </div>

                                    <!-- PRICE + REMOVE -->
                                    <div class="order-price-details text-end ms-3">
                                        <button class="btn btn-outline-danger btn-sm d-inline-flex justify-content-center align-items-center p-0 remove-item-button ms-auto"
                                                style="width: 30px; height: 30px; line-height: 0;">
                                            ×
                                        </button>
                                        <p class="order-item-price fw-bold mb-0 mt-2">${item.price}</p>
                                    </div>
                                </div>
                                <hr class="my-2">
                            `);
                        }
                    })
                }
            })
        }
    })

    function generateProductItemsHTML(variants) {
        let html = '';

        if (variants.length > 0) {
            $.each(variants, function (i, variant) {
                const product = variant.product;

                const imageUrl = product.image_urls && product.image_urls.length > 0
                    ? product.image_urls[0]
                    : '/default-image.jpg';

                html += `
                <div class="product-item d-flex align-items-center border rounded p-3 mb-3">
                    <input type="checkbox" name="variants[]" value="${variant.id}" class="form-check-input me-3">

                    <div class="product-image me-3">
                        <img src="${imageUrl}" alt="${product.title}" class="img-fluid rounded" style="width: 60px; height: 60px; object-fit: cover;">
                    </div>

                    <div class="product-info flex-grow-1">
                        <h6 class="mb-1">${product.title} - ${variant.title}</h6>
                        <p class="text-muted small mb-0">$${parseFloat(variant.price).toFixed(2)}</p>
                    </div>
                </div>
            `;
            });
        } else {
            html = `<p class="text-muted">No products found.</p>`;
        }

        return html;
    }


    function updateCart(product) {
        $.ajax({
            url: '/update-cart?action=updateCart',
            type: 'POST',
            data: {product: product},
            success: function(response) {
                showSubTotal(response.subtotal);
                showQuantity(response.quantity);
                showTotal(response.total);
            }
        });
    }

    function addToCart(product) {
        $.ajax({
            url: '/add-to-cart?action=addToCart',
            type: 'POST',
            data: {product: product},
            success: function(response) {
                showSubTotal(response.subtotal);
                showQuantity(response.quantity);
                showTotal(response.total);
            }
        });
    }
    function removeFromCart(variantId) {
        $.ajax({
            url: '/remove-from-cart?action=removeFromCart',
            type: 'POST',
            data: {id: variantId},
            success: function(response) {
                showSubTotal(response.subtotal);
                showQuantity(response.quantity);
                showTotal(response.total);

            }
        });
    }

    function collapseCartPage() {
        const hideButton = $("#hideCartPage");
        const overlay = $(".overlay");
        const cartPage = $("#cartPage");
        const showButton = $("#cartButton");
        function showCartPage() {
            cartPage.addClass("show-cart");
        };
        function hideCartPage() {
            cartPage.removeClass("show-cart");
        };
        showButton.on("click" , function() {
            showCartPage();
        });
        hideButton.on("click" , function() {
            hideCartPage();
        });
        overlay.on("click" , function() {
            hideCartPage();
        });
    }

    function showSubTotal(value) {
        $(".subtotal").text(`$${value.toFixed(2)}`);
    }
    function showTotal(value) {
        $(".total").text(`$${value.toFixed(2)}`);
    }

    function showQuantity(value) {
        $("#cartButton").text(`${value}`);
    }

    function alreadyInCart(variantId, card) {
        $.ajax({
            url: '/already-in-cart?action=alreadyInCart',
            type: 'POST',
            data: {id: variantId},
            success: function (response) {
                let addButtonContainer = card.find(".add-button");
                if(response) {
                    if(response.inCart) {
                        addButtonContainer.html(`
                            <input type="number" class="form-control cart-quantity ms-auto d-flex justify-content-center align-items-center" value="${response.quantity}" min="0" required>
                        `);
                    } else {
                        addButtonContainer.html(`
                            <a class="btn btn-primary px-3 py-1 d-flex justify-content-center align-items-center fw-bold add-to-cart-button">Add ></a>
                        `);
                    }
                }
            }
        })
    }
});

