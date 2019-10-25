$("#sylius-product-out-of-stock > form").submit(function (e) {
    e.preventDefault();
    const syliusProductOutOfStockValidationError = $('#sylius-product-out-of-stock-validation-error');
    syliusProductOutOfStockValidationError.addClass('hidden');
    const syliusProductOutOfStockEl = $(this).parent();
    const data = $(this).serializeArray();
    $.ajax({
        type: "POST",
        url: $(this).attr("action"),
        dataType: "html",
        data: data,
        success: function (success) {
            syliusProductOutOfStockEl.html(JSON.parse(success).content)
        },
        error: function (error) {
            const {status, responseText} = error;
            if (status === 400) {
                syliusProductOutOfStockValidationError.html(JSON.parse(responseText).errors[0]);
                syliusProductOutOfStockValidationError.removeClass('hidden');
                $("#sylius-product-out-of-stock > form").removeClass('loading');
            }
        }
    });
    return false;
});
