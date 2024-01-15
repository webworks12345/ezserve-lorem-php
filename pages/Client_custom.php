<?php
if (!defined('ACCESS')) die('DIRECT ACCESS NOT ALLOWED');

$businessCode = $_GET['businessCode'];
$branchCode = $_GET['branchCode'];

$servicesQ = $DB->query("SELECT br.*, c.*, i.*
FROM branches br
JOIN custom_category c ON br.branchCode = c.branchCode
JOIN custom_items i ON c.customCategoryCode = i.customCategoryCode
WHERE br.branchCode = '$branchCode'");
?>
<?= element('header') ?>

<div id="client-custom" class="client-custom">
    <div class="container pack-head" style="top: 100px;">
        <div class="row">
            <a href="?page=client_package&businessCode=<?= $businessCode?>&branchCode=<?=$branchCode?>" class="col-xl-1 btn-back btn-lg float-end">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h1 class="col-xl-7 d-flex justify-content-start text-dark">Package Customization</h1>
        </div>
    </div>
</div>

<div class="row d-flex p-5 g-5">
    <div id="package-info" class="package-info col-4" style="width: 50vw; height: 65vh; margin: 100px 0 0 80px;">
        <div class="accords">
            <?php while ($row = $servicesQ->fetch_assoc()): ?>
                <div class="accordion" id="accordion<?= $row['customCategoryCode'] ?>">
                    <div class="card">
                        <div class="card-header" id="heading<?= $row['customCategoryCode'] ?>">
                            <h2 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse<?= $row['customCategoryCode'] ?>" aria-expanded="true" aria-controls="collapse<?= $row['customCategoryCode'] ?>">
                                    <?= $row['categoryName'] ?>
                                </button>
                            </h2>
                        </div>

                        <div id="collapse<?= $row['customCategoryCode'] ?>" class="collapse" aria-labelledby="heading<?= $row['customCategoryCode'] ?>" data-parent="#accordion<?= $row['customCategoryCode'] ?>">
                            <div class="card-body">
                                <ul class="list-group">
                                    <?php
                                    $categoryCode = $row['customCategoryCode'];
                                    $itemsQ = $DB->query("SELECT * FROM custom_items WHERE customCategoryCode = '$categoryCode'");
                                    while ($item = $itemsQ->fetch_assoc()):
                                    ?>
                                        <li class="list-group-item">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="itemCheckbox<?= $item['itemCode'] ?>" name="itemCheckbox[]" value="<?= $item['itemCode'] ?>">
                                                <label class="form-check-label" for="itemCheckbox<?= $item['itemCode'] ?>">
                                                    <?= $item['itemName'] ?>
                                                </label>
                                            </div>
                                            <input type="number" class="form-control" id="quantity<?= $item['itemCode'] ?>" name="quantity[]" placeholder="Quantity" value ="1" min="1">
                                         </li>
                                    <?php endwhile; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <div class="order-list col-4 card border-0 rounded-3 shadow p-3 mb-5 bg-white rounded overflow-auto" style="height: 80vh">
        <h3 class="order-header sticky-top p-3">Order List</h3>
        <hr class="m-0">
        <div class="order justify-content-center px-4 overflow-scroll">
            <hr>
            <table class="table" style="height:80rem">
                <thead>
                    <tr class="sticky-top">
                        <th scope="col">Quantity</th>
                        <th scope="col">Description</th>
                        <th scope="col">Price</th>
                    </tr>
                </thead>
                <tbody class="order-body">
                </tbody>
            </table>
            <div class="total row d-flex sticky-bottom">
                <h3 class="col-7">Total</h3>
                <h4 class="col-5 total-price">0.00</h4>
                <div class="border-top border-bottom voucher-btn row justify-content-center align-items-center" style="height: 60px" href="?page=client_voucher">
                    <h6 class="col-10"><i class="bi bi-tags"></i>Apply Voucher</h6>
                    <i class="bi bi-chevron-right float-end col-2"></i>
                </div>
                <form action="?page=custom_checkout" method="post">
                    <input type="hidden" name="orderDetails" id="orderDetails" value="">
                    <button type="submit" class="btn btn-primary" style="width:100%" data-bs-business-code="">
                        Check Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add this script to the end of your HTML file, after including jQuery and Bootstrap scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Add the following script at the end of your HTML file, after including jQuery and Bootstrap JS -->
<!-- Add the following script at the end of your HTML file, after including jQuery and Bootstrap JS -->
<script>
$(document).ready(function() {
    // Object to store selected items with quantity and price
    var selectedItems = {};

    // Function to update the order list
    function updateOrderList() {
        var orderBody = $('.order-body');
        var totalPrice = 0;

        // Clear existing items in the order list
        orderBody.empty();

        // Iterate over selected items and update the order list
        Object.keys(selectedItems).forEach(function(itemCode) {
            var item = selectedItems[itemCode];
            var itemPrice = parseFloat(item.price) * parseInt(item.quantity);
            totalPrice += itemPrice;

            // Append item to the order list
            orderBody.append(
                '<tr>' +
                    '<td>' + item.quantity + '</td>' +
                    '<td>' + item.itemName + '</td>' +
                    '<td>$' + itemPrice.toFixed(2) + '</td>' +
                '</tr>'
            );
        });

        // Update the total price in the order list
        $('.total-price').text('$' + totalPrice.toFixed(2));

        // Update the hidden input field with order details for form submission
        $('#orderDetails').val(JSON.stringify(selectedItems));
    }

    // Event handler for item checkbox change
    $('input[name="itemCheckbox[]"]').on('change', function() {
        var itemCode = $(this).val();
        var itemName = $(this).closest('li').find('.form-check-label').text();
        var quantity = parseInt($(this).closest('li').find('.form-control').val());

        // Fetch item details (including price) from the custom_items table
        $.ajax({
            type: 'GET',
            url: '?action=fetch_item_details', // Replace with the actual path to your PHP script
            data: { itemCode: itemCode },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update selectedItems object based on checkbox state
                    if ($(this).prop('checked')) {
                        // Add the item to the selectedItems object
                        selectedItems[itemCode] = {
                            itemName: itemName,
                            price: response.item.price,
                            quantity: quantity
                        };
                    } else {
                        // Remove the item from the selectedItems object
                        delete selectedItems[itemCode];
                    }

                    // Update the order list
                    updateOrderList();
                } else {
                    console.error('Error fetching item details.');
                }
            },
            error: function() {
                console.error('Error fetching item details.');
            }
        });
    });
});
</script>
