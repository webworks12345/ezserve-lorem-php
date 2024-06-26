<?php
if (!defined('ACCESS')) die('DIRECT ACCESS NOT ALLOWED');

$accounts = $DB->query("SELECT bo.* FROM business_owner bo WHERE bo.status = 1 ");
$businesses = null;

if (isset($_GET['ownerID'])) {
    $ownerID = $_GET['ownerID'];
    $businesses = $DB->query("SELECT b.* FROM business b WHERE b.status = 1 AND b.ownerID = $ownerID");
}
$keyword = "";
$results = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['keyword'])) {
        $keyword = $_POST['keyword'];

        $sql = "SELECT bo.*
                FROM business_owner bo
                WHERE bo.status = 1
                AND (
                    fname COLLATE utf8mb4_unicode_ci LIKE '%$keyword%' OR lname COLLATE utf8mb4_unicode_ci LIKE '%$keyword%'
                )";

       
        $results = $DB->query($sql);
    }
    else {
      $results = $accounts;
    }}

?>

<?= element('admin_header') ?>

<?= element('admin-side-nav') ?>

<div id="admin-users" class="admin-users">
    <div class="d-flex justify-content-between ">
        <h1>Business Owner Accounts</h1>

        <form method="post" action="">
        <div id="searchbar" class="d-flex my-3 float-end">
            <input type="search" class="form-control rounded" id="searchKeyword" placeholder="Search" aria-label="Search" aria-describedby="search-addon" name="keyword" value="<?= $keyword ?>" />
            <button type="submit" class="search-btn btn btn-primary input-group-text border-0">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </form>
    </div>
    

    <div class="overflow-auto" style="height:100vh">
        <table class="table table-hover table-responsive table-bordered" style="border-radius: 10px">
            <thead class="table-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Business Owner Name</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach (empty($results) ? $accounts : $results as $index => $account) : ?>
       <tr>
                        <td scope="row" class="bg-transparent border border-white"><?= $index + 1 ?></td>
                        <td class="bg-transparent border border-white ">
                            
                                <?= $account['fname'] . ' ' . $account['lname'] ?>
                           
                             </td>
                        <td class="bg-transparent border border-white " style="width: 200px;">
                        <button class="btn btn-sm btn-primary " data-bs-toggle="offcanvas" data-bs-target="#offcanvasAccount<?= $account['ownerID'] ?>"><i class="bi bi-eye-fill"></i> View</button>
                       
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php foreach ($accounts as $account) : ?>
            <?php
            // Fetch businesses for each owner
            $ownerID = $account['ownerID'];
            $businesses = $DB->query("SELECT b.* FROM business b WHERE b.status = 1 AND b.ownerID = $ownerID");
            ?>
            <div class="offcanvas offcanvas-top overflow-auto p-3 rounded" style="width: 50vw; height: 90vh; margin:40px 0 0 25vw;" tabindex="-1" id="offcanvasAccount<?= $account['ownerID'] ?>" data-bs-backdrop="static">
                  <div class="p-3">
                    <div class="offcanvas-header p-0">
                        <h2 class="offcanvas-title" id="offcanvasExampleLabel">Owner's Account Information</h2>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <hr>
                    <div>
                        <div class="d-flex my-3">
                            <span class="col-sm-3"> Owner's Name: </span>
                            <span class="col mx-5 border-bottom"><?= $account['fname'] . ' ' . $account['lname'] ?></span>
                        </div>
                        <div class="d-flex my-3">
                            <span class="col-sm-3"> Owner's Birthday: </span>
                            <span class="col mx-5 border-bottom"><?= $account['birthday'] ?></span>
                        </div>
                        <div class="d-flex my-3">
                            <span class="col-sm-3"> Owner's Email: </span>
                            <span class="col mx-5 border-bottom"><?= $account['email'] ?></span>
                        </div>
                        <div class="d-flex my-3">
                            <span class="col-sm-3"> Owner's Number: </span>
                            <span class="col mx-5 border-bottom"><?= $account['number'] ?></span>
                        </div>
                        <div class="d-flex my-3">
                            <span class="col-sm-3"> Owner's Address: </span>
                            <span class="col mx-5 border-bottom"><?= $account['ownerAddress'] ?></span>
                        </div>
                       
                        <div class="Business-list">
                        <table class="table table-hover table-responsive table-bordered">
    <thead>
        <tr>
            <th>Business Name</th>
            <th>Business Type</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($businesses !== null && $businesses->num_rows > 0) {
            while ($business = $businesses->fetch_assoc()) {
                ?>
                <tr>
                    <td><?= $business['busName'] ?></td>
                    <td><?= $business['busType'] ?></td>
                    <td>
                    <form action="?page=business-details-form" method="post">
                        <input type="hidden" name="businessCode" value="<?= $business['businessCode'] ?>">
                        <button type="submit" class="btn btn-primary view-package m-2" data-business-code="<?= $business['businessCode'] ?>">
                        <i class="bi bi-eye mx-1"></i><span>View</span> 
                        </button>
                    </form>
                    </td>
                </tr>
                <?php
            }
        } else {
            ?>
            <tr>
                <td colspan="3">No businesses found.</td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>

                </div>
               
                                                                                </div>
                                                                            </div>
                                                                        </div>
         
            <div class="offcanvas offcanvas-top overflow-auto p-3 rounded" style="width: 50vw; height: 60vh; margin: 50px 0 0 25vw;" tabindex="-1" id="offcanvasDelete<?= $account['ownerID'] ?>"  data-bs-backdrop="static" >
                <div class="p-3">
                    <div class="offcanvas-header p-0 mb-3">
                        <h3 class="offcanvas-title" id="offcanvasExampleLabel">Delete Owner's Account</h3>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    
                    <p>You are about to delete the owner's account.</p>
                    <p class="text-justify">Before proceeding with the deletion of the owner's account, kindly specify the reason(s) for this action. The owner will receive an email notification stating the reason for the account deletion.</p>
                    <div class="reasons my-4">
                        <p> Select a reason for the deletion request</p>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                            <label class="form-check-label" for="flexCheckDefault"> Created another account</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                            <label class="form-check-label" for="flexCheckDefault"> Requested for account deletion</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                            <label class="form-check-label" for="flexCheckDefault"> Violation of Terms and Condition</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                            <label class="form-check-label" for="flexCheckDefault"> Account Inactivity</label>
                        </div>
                    </div>
                    <form method="post" action="?action=delete_owner">
                        <input type="hidden" name="ownerID" value="<?= $account['ownerID'] ?>">
                        <button type="submit" class="btn btn-danger float-end" onclick="return confirm('Are you sure you want to delete this owner?');">Confirm Delete</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
                    
    <hr>
   
</div>


<script src="assets/js/user.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
