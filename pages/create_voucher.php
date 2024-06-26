<?= element('owner_header') ?>

<?php
global $DB;
$ownerID = isset($_SESSION['userID']) ? $_SESSION['userID'] : null;
$businesses = $DB->query("SELECT * FROM business WHERE ownerID = '$ownerID' AND status ='1'");
$branches = $DB->query("SELECT * FROM branches");
$packages = $DB->query("SELECT * FROM package");
?>

<div class="voucher-form">
    <div class="card p-5 bg-opacity-25 bg-white">
        <div class="d-flex">
            <a href="?page=owner_voucher" class="mx-3 btn-back btn-lg justify-content-center align-items-center d-flex text-dark">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h2 >Create Voucher</h2>
            <hr>
        </div>

        <form method="post" action="?action=save_voucher" id="voucherForm">
            <label for="businessCode">Select Business:</label>
            <select name="businessCode" class="form-select " id="businessCode" onchange="updateBranches()">
                <option disabled selected >--Select Business--</option>
                <option value="0">All Businesses</option>
                <?php while ($row = $businesses->fetch_assoc()) {
                    echo "<option value='" . $row["businessCode"] . "'>" . $row["busName"] . "</option>";
                }
                ?>
            </select>                             

            <input type="hidden" name="packCode" id="hiddenPackageCode" value="">
            <input type="hidden" name="ownerID" id="ownerID" value="<?=$ownerID?>">
            <br>

            <label for="branchCode">Select Branch:</label>
            <select name="branchCode" class="form-select" id="branchCode" onchange="updatePackages()">
            </select><br>
            
            <label for="voucherCode">Voucher Code:</label>
            <div class="d-flex">  
            <input type="text" class="form-control" id="voucherCode" name="voucherCode" placeholder="Enter voucher code" required >
            <button type="button" class= "btn btn-primary" onclick="generateRandomCode()"> 
            <i class="bi bi-shuffle"></i>
         </button></div>
         <br>

        <label for="condition">Voucher Type:</label>
        <select name="condition" class="form-select" id="condition" onchange="updateDiscountFields()">
            <option disabled selected >--Select voucher type--</option>
            <option value="Gift Card">Gift Card</option>
            <option value="Specific Package">Specific Package</option>
            <option value="Minimum Spend">Minimum Spend</option>
        </select><br>
            <div id="specificPackageField" style="display: none;">
                <label for="selectedPackage">Select Package:</label>
                <select name="selectedPackage" class="form-control" id="selectedPackage">
                    <?php
                    while ($row = $packages->fetch_assoc()) {
                        echo "<option value='" . $row["packageCode"] . "' data-min-spend='" . $row["minSpend"] . "'>" . $row["packageName"] . "</option>";
                    }
                    ?>
                       <input type="hidden" name="packageCode" id="hiddenPackageCode" value="">

                </select><br>
            </div>

            <div id="minimumSpendField" style="display: none;">
                <label for="minSpend">Minimum Spend:</label>
                <div class="d-flex">
                <span class="input-group-text" id="basic-addon2">₱</span>
                <input type="number" class="form-control" name="minSpend" min="1"><br>
                </div>
                <br>
            </div>
           
            <label for="discountType">Discount Type:</label>
            <select name="discountType" class="form-select" id="discountTypeSelect">
                <option disabled selected >--Select discount type--</option>
                <option value="percentage">Percentage</option>
                <option value="amount">Amount</option>
            </select><br>
          
            <p id="defaultMessage"></p>
            <div class="input-group mb-3" id="percentageInput"  style="display: none;">
                <input type="number" class="form-control" placeholder="Percentage Value" aria-label="Percentage Value" name="percentageValue">
                <div class="input-group-append">
                    <span class="input-group-text" id="basic-addon2">%</span>
                </div>
                </div>

            <div class="input-group mb-3" id="amountInput" style="display: none;">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="">₱</span>
                </div>
                <input type="number" class="form-control" placeholder="Amount Value" aria-label="Amount Value" aria-describedby="basic-addon1" name="amountValue">
            </div>


            <label for="startDate">Start Date:</label>
            <input type="date"class="form-control" name="startDate" required><br>

            <label for="endDate">End Date:</label>
            <input type="date" class="form-control" name="endDate" required><br>

            <input type="submit" class="btn btn-primary" style="float: right;" value="Create Voucher">
        </form>
    </div>
</div>
                
<script>
function updateBranches() {
    var businessCode = document.getElementById("businessCode").value;
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "?action=getBranches&businessCode=" + businessCode, true);

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var branchLabel = document.querySelector("label[for='branchCode']");
            var branchCodeDropdown = document.getElementById("branchCode");
            if (businessCode === "0") {
                branchLabel.style.display = "none";
                branchCodeDropdown.style.display = "none";
                // Set branchCode to "0"
                document.getElementById("branchCode").innerHTML = '<option value="0">All Branches</option>';
                document.getElementById("hiddenPackageCode").value = ""; 
            } else {
                branchLabel.style.display = "block";
                branchCodeDropdown.style.display = "block";
                document.getElementById("branchCode").innerHTML = xhr.responseText;
                updatePackages();
            }
        }
    };
    xhr.send();
}


    function updatePackages() {
        var branchCode = document.getElementById("branchCode").value;
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "?action=getPackages&branchCode=" + branchCode, true);

        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                document.getElementById("selectedPackage").innerHTML = xhr.responseText;
                var firstPackageCode = document.querySelector("#selectedPackage option:first-child").value;
                document.getElementById("hiddenPackageCode").value = firstPackageCode;
            }
        };
        xhr.send();
    }

    function updateDiscountFields() {
        var condition = document.getElementById("condition").value;
        var specificPackageField = document.getElementById("specificPackageField");
        var minimumSpendField = document.getElementById("minimumSpendField");
        var minSpendInput = document.querySelector("input[name='minSpend']");

        if (condition === "Specific Package") {
            specificPackageField.style.display = "block";
            minimumSpendField.style.display = "none";
            minSpendInput.removeAttribute("required"); 
        } else if (condition === "Minimum Spend") {
            specificPackageField.style.display = "none";
            minimumSpendField.style.display = "block";
            minSpendInput.setAttribute("required", "required"); 
        } else {
            specificPackageField.style.display = "none";
            minimumSpendField.style.display = "none";
            minSpendInput.removeAttribute("required"); 
        }
    }

    updateBranches();
    updatePackages();

    document.getElementById("discountTypeSelect").addEventListener("change", function() {
        var discountType = this.value;
        
        document.getElementById("percentageInput").style.display = "none";
        document.getElementById("amountInput").style.display = "none";
        document.getElementById("defaultMessage").style.display = "none";

        if (discountType === 'percentage') {
            document.getElementById("percentageInput").style.display = "flex";
        } else if (discountType === 'amount') {
            document.getElementById("amountInput").style.display = "flex";
        } else {
            document.getElementById("defaultMessage").style.display = "block";
        }
    });

    function generateRandomCode() {
            var length = 10; 
            var charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            var randomCode = "";
            for (var i = 0; i < length; i++) {
                randomCode += charset.charAt(Math.floor(Math.random() * charset.length));
            }
            document.getElementById("voucherCode").value = randomCode;
        }
</script>

<style>
     @media (max-width:2000px) {
        .voucher-form{
    margin-left:28%;
    margin-top:10%;
    width: 50vw;

        }
        .back-arrow{
            margin: 0 0 5% 0;
        }
    }
    @media (max-width:700px) {
        .voucher-form{
width: 100vw;
margin: 0;
        }
        .back-arrow{
            margin: 70px 0 0 10% 
        }
    }
</style>