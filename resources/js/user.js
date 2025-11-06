import './app';
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$(document).ready(function () {
    let variantIndex = 1;

    $("#addProductBtn").on("click", function() {

        $("#tableBody").append(`
            <tr class="table-row">
                <td class="align-middle product-data">
                    <input type="text" name="products[${variantIndex}][name]" class="form-control product-search-input" placeholder="Enter Product Name" autocomplete="off">
                    <div>
                        <ul class="list-group" id="suggestedProducts" style="max-height: 200px; overflow-y: auto;">

                        </ul>
                    </div>
                </td>
                <td class="align-middle">
                    <select name="products[${variantIndex}][variant]" class="form-select variant-select">
                        <option value="" disabled >Choose variant</option>
                    </select>
                </td>
                <td class="align-middle">
                    <input type="number" name="products[${variantIndex}][price]" class="form-control variant-price" step="0.01" placeholder="0.00">
                </td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-variant" >Remove</button>
                </td>
            </tr>

        `);
        variantIndex ++;
    });

    $(document).on("input", ".product-search-input", function() {
       let searchValue = $(this).val().trim();
       let row = $(this).parents(".table-row");
       $.ajax({
            url:'/suggest-products',
           type: 'POST',
           data: {search: searchValue},
           success: function (response) {
                let suggestedProducts = row.find("#suggestedProducts");
               suggestedProducts.empty();
               if(response.success) {
                   response.products.forEach(function (product) {
                       suggestedProducts.append(`
                          <li class="list-group-item suggested-products" data-product="${product['id']}">${product['title']}</li>
                    `);
                   });
               }
           }
       });
    });

    $(document).on("click", ".suggested-products", function() {
        let row = $(this).parents(".table-row");
        let productInput = row.find(".product-search-input");
        productInput.val($(this).text());
        productInput.attr('readonly', true);
        $(this).parents(".list-group").remove();
        let productId = $(this).data('product');
        $.ajax({
            url: '/suggest-variants',
            type: 'POST',
            data: {id: productId},
            success: function(response) {
                let variantSelect = row.find(".variant-select");

                response.variants.forEach(function (variant) {
                    variantSelect.append(`
                        <option value='${JSON.stringify({id: variant["id"], title: variant["title"]})}'>
                            ${variant['title']}
                        </option>
                    `);
                });
            }
        });

    });

    $(document).on("click", ".remove-variant", function () {
        $(this).parents(".table-row").remove();
    });
});
