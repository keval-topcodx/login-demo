import './app';
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


$(document).ready(function () {
    let $variantIndex = 1;

    $("#addRow").click(function () {
        $("#tableBody").append(`
        <tr>
            <td>
                <input type="text" class="form-control" name="variants[${$variantIndex}][title]" placeholder="Enter Title">
            </td>
            <td>
                <input type="number" class="form-control" name="variants[${$variantIndex}][price]" step="0.01" placeholder="0.00">
            </td>
            <td>
                <input type="text" class="form-control" name="variants[${$variantIndex}][sku]" placeholder="SKU code">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger remove-size" >Remove</button>
            </td>
        </tr>
        `);
        $variantIndex++;
    });

    $("#sizeTable").on("click" , ".remove-size" , function () {
        $(this).parents("tr").remove();
    });
    let productTags = [];
    let tags = [];
    $("#tags").on("input", function () {
        let searchValue = $(this).val();
        if (searchValue.length > 1) {
            $.ajax({
                url:'get-tags?action=getTags',
                type: 'POST',
                data: { search: searchValue },
                success: function(data) {
                    $("#suggestedIds").hide();
                    if(data['success'] === true) {
                        $("#suggestedIds").show();
                        $("#suggestedIds").empty();
                        let tags = data['tags'];
                        tags.forEach(tag => {
                            $("#suggestedIds").append(`
                                  <li class="list-group-item suggested-ids" data-id="${tag['id']}" data-name="${tag['name']}">${tag['name']}</li>
                            `)
                        });
                    }
                }
            });
        } else {
            $("#suggestedIds").hide();
        }
    });

    $("#productForm").on("keydown", "#tags", function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            let tag = $(this).val();
            if(!tags.includes(tag)) {
                tags.push(tag);
                productTags.push(tag);
                $("#productTags").val(JSON.stringify(productTags));
                let selectedIdDiv = $(".selectedIds");

                selectedIdDiv.append(`
                    <span class="tag-tablet ms-3 bg-primary text-white rounded-pill px-3 py-1 d-inline-flex align-items-center mt-3" data-name="${tag}">
                        ${tag}
                        <button type="button" class="tag-remove-btn btn-close btn-close-white ms-2" aria-label="Remove" style="font-size: 0.6rem;"></button>
                    </span>
                `);
            }
            $(this).val("");
        }
    });

    $("#productForm").on('click', '.suggested-ids', function() {
        let tagId = $(this).data('id');
        let tagName = $(this).data('name');
        $(".suggested-ids").hide();
        let selectedIdDiv = $(".selectedIds");

        if(!tags.includes(tagName)) {
            tags.push(tagName);
            productTags.push(tagId);
            $("#productTags").val(JSON.stringify(productTags));
            selectedIdDiv.append(`<span class="tag-tablet ms-3 bg-primary text-white rounded-pill px-3 py-1 d-inline-flex align-items-center" data-name="${tagName}" data-id="${tagId}">
                ${tagName}
                    <button type="button" class="tag-remove-btn btn-close btn-close-white ms-2" aria-label="Remove" style="font-size: 0.6rem;"></button>
                </span>
                `);
        }
        $("#tags").val("");
    });

    $("#productEditForm").on('click', '.suggested-ids', function() {
        let tagId = $(this).data('id');
        let tagName = $(this).data('name');
        let editProductTags = JSON.parse($("#productTags").val());
        $(".suggested-ids").hide();
        let selectedIdDiv = $(".selectedIds");

        if(!tags.includes(tagName)) {
            tags.push(tagName);
            editProductTags.push(tagId);
            $("#productTags").val(JSON.stringify(editProductTags));
            selectedIdDiv.append(`<span class="tag-tablet ms-3 bg-primary text-white rounded-pill px-3 py-1 d-inline-flex align-items-center" data-name="${tagName}" data-id="${tagId}">
                ${tagName}
                    <button type="button" class="tag-remove-btn btn-close btn-close-white ms-2" aria-label="Remove" style="font-size: 0.6rem;"></button>
                </span>
                `);
        }
        $("#tags").val("");
    });

    $("#productForm").on("click", ".tag-remove-btn", function() {
        let span = $(this).parents('.tag-tablet');
        if(span.data('id')){
            let id = span.data('id');
            productTags.splice( $.inArray(id, productTags), 1 );
            $("#productTags").val(JSON.stringify(productTags));
        } else if (span.data('name')) {
            let name = span.data('name');
            productTags.splice( $.inArray(name, productTags), 1 );
            $("#productTags").val(JSON.stringify(productTags));
        }

       $(this).parents('.tag-tablet').remove();
    });

    $("#productEditForm").on("click", ".tag-remove-btn", function() {
        let editProductTags = JSON.parse($("#productTags").val());
        let span = $(this).parents('.tag-tablet');
        if(span.data('id')){;
            let id = span.data('id');
            editProductTags.splice( $.inArray(id, editProductTags), 1 );
            $("#productTags").val(JSON.stringify(editProductTags));
        } else if (span.data('name')) {
            let name = span.data('name');
            editProductTags.splice( $.inArray(name, productTags), 1 );
            $("#productTags").val(JSON.stringify(editProductTags));
        }

        $(this).parents('.tag-tablet').remove();
    });
    $("#productEditForm").on("keydown", "#tags", function(event) {
        let editProductTags = JSON.parse($("#productTags").val());
        if (event.key === 'Enter') {
            event.preventDefault();
            let tag = $(this).val();
            if(!tags.includes(tag)) {
                tags.push(tag);
                editProductTags.push(tag);
                $("#productTags").val(JSON.stringify(editProductTags));
                let selectedIdDiv = $(".selectedIds");

                selectedIdDiv.append(`
                    <span class="tag-tablet ms-3 bg-primary text-white rounded-pill px-3 py-1 d-inline-flex align-items-center mt-3" data-name="${tag}">
                        ${tag}
                        <button type="button" class="tag-remove-btn btn-close btn-close-white ms-2" aria-label="Remove" style="font-size: 0.6rem;"></button>
                    </span>
                `);
            }
            $(this).val("");
        }
    });

});
