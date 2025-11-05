import './app';
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$(document).ready(function () {
    let $variantIndex = 1;

    $("#addProductBtn").on("click", function() {

        $("#tableBody").append(`
            <tr class="table-row">
                <td>
                    <input type="text" class="form-control product-search-input" placeholder="Enter Product Name">
                </td>
                <td>
                    <select id="variant" name="variant" class="form-select">
                        <option value="" disabled >Choose variant</option>
                        <option value="1"></option>
                        <option value="0"></option>
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control" step="0.01" placeholder="0.00">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-variant" >Remove</button>
                </td>
            </tr>

        `);
    });
    $("#tableBody").on("input", ".product-search-input", function() {
       let searchValue = $(this).val().trim();
       $.ajax({
            url:'/search-products',
           type: 'POST',
           data: {search: searchValue},
           success: function (response) {
                console.log(response.variants);
           }
       });
    });

    $("#tableBody").on("click", ".remove-variant", function () {
        $(this).parents(".table-row").remove();
    })
});
