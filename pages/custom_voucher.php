<?= element('header') ?>

<?php

$businessCode = $_GET['businessCode'];
$branchCode = $_GET['branchCode'];
$grandTotal = $_GET['grandTotal'];
$searchedVoucherCode = isset($_POST['searchedVoucherCode']) ? $_POST['searchedVoucherCode'] : '';


if ($searchedVoucherCode) {
    $voucherQuery = "SELECT * FROM voucher WHERE businessCode = '$businessCode' AND branchCode = '$branchCode' AND voucherType = 'Gift Card' AND voucherCode = '$searchedVoucherCode'";
} else {
    $voucherQuery = "SELECT * FROM voucher WHERE businessCode = '$businessCode' AND branchCode = '$branchCode' AND voucherType <> 'Gift Card' AND voucherType <> 'Specific Package'";
}

$voucherResult = $DB->query($voucherQuery);

$hasVouchers = $voucherResult->num_rows > 0;
?>

<!-- Display vouchers in Bootstrap cards -->
<div class="container " style="min-height: 100vh; margin-top: 150px">
<div class=" mb-4">
             <div class="d-flex">
            <a href="?page=custom_checkout&businessCode=<?= $businessCode?>&branchCode=<?= $branchCode ?>" class="col-1 btn-back btn-lg justify-content-center align-items-center d-flex text-dark">
            <i class="bi bi-arrow-left"></i>
                </a>
                    <h3 style="font-size: 34px; padding: 20px; ">
                     Select voucher
                    </h3> 
                </div>
                    <div class="d-flex justify-content-center align-items-center">
                        <form method="post">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Enter Voucher Code" name="searchedVoucherCode" value="<?= $searchedVoucherCode ?>">
                                <button class="btn btn-primary" type="submit">Search</button>
                            </div>
                        </form>
                   
                </div>
            </div>

        <?php if ($hasVouchers) : ?>
            <div class="">
                <?php while ($voucher = $voucherResult->fetch_assoc()) : ?>
                    <div class="d-flex justify-content-center align-items-center">
            <div class=" mb-4 d-flex">
            <div class="card ">
            <?php if ($voucher['voucherType'] === 'Minimum Spend') : ?>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <img src="assets/images/min-spend.png" alt="" style="width: 150px; height: 150px;">
                </div>
            <?php endif; ?>

            <?php if ($voucher['voucherType'] === 'Specific Package') : ?>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <img src="assets/images/discountpackage.png" alt="" style="width: 150px; height: 150px;">
                </div>
            <?php elseif ($voucher['voucherType'] === 'Gift Card') : ?>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <img src="assets/images/gift-card.png" alt="" style="width: 150px; height: 150px;">
                </div>
            <?php endif; ?>
                    
                    </div>


                        <div class="card" style="width: 40vw">
                            <div class="card-body">
                            <h1>
                                <?php if ($voucher['discountType'] === 'amount') : ?>
                                    &#8369; <?= $voucher['discountValue'] ?> off <!-- Peso sign for amount -->
                                <?php elseif ($voucher['discountType'] === 'percentage') : ?>
                                    <?= $voucher['discountValue'] ?>% off<!-- Percentage symbol for percentage -->
                                <?php endif; ?>
                            </h1>
                                <h5 class="card-title"><?= $voucher['voucherCode'] ?></h5>
                                <p class="card-text">Condition: <?= $voucher['voucherType'] ?></p>
                                <p class="card-text m-0" style="color:  #ff1a1a"><?= $voucher['startDate'] ?> - <?= $voucher['endDate'] ?></p>
                                <a href="#" class="btn btn-primary float-end" onclick="applyVoucher('<?= $voucher['voucherCode'] ?>', '<?= $voucher['packCode'] ?>', '<?= $voucher['voucherType'] ?>', '<?= $voucher['discountType'] ?>', <?= $voucher['discountValue'] ?>, <?= $grandTotal ?>, '<?= $voucher['startDate'] ?>', '<?= $voucher['endDate'] ?>')">Use Voucher</a>
                    
                              
                                </div>
                </div>
                </div>

                </div>
            </div>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <div class="text-center">
                <h3>No vouchers available</h3>
            </div>
        <?php endif; ?>
    </div>
</div>


<!-- ... Previous HTML and PHP code ... -->

<script>
function applyVoucher(voucherCode, voucherType, discountType, discountValue, grandTotal, startDate, endDate) {
    // Get the current date in the format YYYY-MM-DD
    var currentDate = new Date().toISOString().split('T')[0];

    // Check if the current date is within the voucher validity range
    if (currentDate < startDate || currentDate > endDate) {
        alert('This voucher is not currently valid.');
        return;
    }

    // Logic to apply the voucher and send the discounted total back to the checkout page
    var discountedTotal;

    if (voucherType === 'Minimum Spend') {
        // Check if the grand total meets the minimum spend criteria
        if (grandTotal >= parseFloat(discountValue)) {
            if (discountType === 'percentage') {
                // Calculate the discount based on percentage
                discountedTotal = grandTotal * (1 - (parseFloat(discountValue) / 100));
            } else {
                // Subtract the discount value from the grand total
                discountedTotal = grandTotal - parseFloat(discountValue);
            }
        } else {
            // Grand total doesn't meet the minimum spend criteria, no discount applied
            discountedTotal = grandTotal;
        }
    } else if (voucherType === 'Gift Card') {
        // Check if the discount type is percentage or amount
        if (discountType.toLowerCase() === 'percentage') {
            // Calculate the discount based on percentage
            discountedTotal = grandTotal * (1 - (parseFloat(discountValue) / 100));
        } else {
            // Subtract the discount value from the grand total
            discountedTotal = grandTotal - parseFloat(discountValue);
        }
    } else {
        // Handle other voucher conditions here
    }

    // Redirect back to the checkout page with the discounted total and checkout details
    var checkoutData = <?= json_encode($_GET['checkoutData']) ?>;
    window.location.href = "?page=custom_checkout&businessCode=<?= $businessCode ?>&branchCode=<?= $branchCode ?>&discountedTotal=" + discountedTotal + "&orderDetails=" + encodeURIComponent(checkoutData);
}
</script>
