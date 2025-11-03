import './app';

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


$(document).ready(function () {

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

    $("#searchProductForm").on("submit", function (e) {
        e.preventDefault();
        let searchValue = $("#search").val();
        $.ajax({
            url: '/search-products?action=searchProducts',
            type: 'POST',
            data: {search: searchValue},
            success: function (response) {

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
            success: function (response) {
                let html = generateProductItemsHTML(response.variants);
                $('#productResults').html(html);
            }
        });
    });

    $("#addToOrderForm").on("submit", function (e) {
        e.preventDefault();

        let selectedVariants = [];
        $('input[name="variants[]"]:checked').each(function () {
            selectedVariants.push($(this).val());
        });

        if (selectedVariants.length == 0) {
            $(".add-order-error").text("Select at least one product");
        } else {
            $.ajax({
                url: '/add-to-order?action=addToOrder',
                type: 'POST',
                data: {variants: selectedVariants},
                success: function (response) {
                    let variants = response.variants;


                    $('#productModal').addClass('d-none');

                    let itemCards = $(".item-card");
                    let orderVariants = [];

                    itemCards.each(function () {
                        let cardVariant = $(this).data('variant');
                        orderVariants.push(cardVariant);
                    });

                    Object.values(variants).forEach(item => {
                        if (orderVariants.includes(item.id)) {
                            let matchingCard = $('.item-card').filter(function () {
                                return $(this).data('variant') === item.id;
                            });
                            ;
                            let quantityInput = matchingCard.find('.order-quantity');
                            let quantityInputValue = parseInt(quantityInput.val());
                            quantityInputValue += 1;
                            quantityInput.val(quantityInputValue);
                        } else {
                            let product = item.product;
                            $(".item-container").append(`
                                <div class="item-card d-flex align-items-center justify-content-between py-3 border-bottom"
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

                            `);
                        }
                    })
                    calculateSubTotal();
                }
            })
        }
    });

    $(".item-container").on("input", ".order-quantity", function() {
        let quantity = $(this).val();
        let card = $(this).parents(".item-card");
        let price = card.data('price');
        card.find(".order-item-price").text(`$${(quantity * price).toFixed(2)}`);
        if (quantity <= 0) {
            card.remove();
        }
        calculateSubTotal();
    });

    $(".item-container").on("click", ".remove-item-button", function() {
        let card = $(this).parents(".item-card");
        card.remove();
        calculateSubTotal();
    });

    function calculateSubTotal() {
        let cards = $(".item-card");
        let subtotal = 0;
        let discount_amount = parseInt($(".discount").data('discount'));
        let amount_paid = $(".paid-by-customer").data('amount-paid');

        cards.each(function () {
            let quantity = $(this).find(".order-quantity").val();
            let price = $(this).data('price');

            subtotal += (quantity * price);
        });
        $(".subtotal").text(`$${subtotal.toFixed(2)}`);
        $(".total").text(`$${(subtotal + discount_amount).toFixed(2) }`);
        $(".amount-to-collect").text(`${(subtotal + discount_amount - amount_paid).toFixed(2)}`)
    }

    $("#updateOrder").on("click", function() {
        let cards = $(".item-card");
        let subtotal = 0;
        let discount_amount = parseInt($(".discount").data('discount'));
        let amount_paid = $(".paid-by-customer").data('amount-paid');

        cards.each(function () {
            let quantity = $(this).find(".order-quantity").val();
            let price = $(this).data('price');

            subtotal += (quantity * price);
        });

        let amount_to_collect = (subtotal + discount_amount - amount_paid).toFixed(2);
        if(amount_to_collect <= 0.50 ) {
            //stripe error if amount less than 0.50 usd
            alert("Amount to collect must be greater than 0.50 USD");
        } else {

        }
    });



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
});

