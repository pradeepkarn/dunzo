<?php
$createData = $context;
$fg =  explode("/", REQUEST_URI);
$fg = $fg[3];
$req = new stdClass;
$req->fg = $fg;
?>

<form action="/<?php echo home . route('fuelStoreAjax', ['fg' => $req->fg]); ?>" id="register-new-fuel-form">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <h5 class="card-title">Add/Deduct <?php echo $req->fg; ?></h5>
                </div>
                <div class="position-relative">
                    <div>
                        <input id="userSearchInput" type="seach" name="searchuser" class="form-control" placeholder="search user...">
                    </div>
                    <div>
                        <ul class="position-absolute w-100 bg-white border-dark" id="suggestionList"></ul>
                    </div>
                </div>
                <div class="col text-end my-3">
                    <a class="btn btn-dark" href="/<?php echo home . route('fuelList', ['fg' => $req->fg]); ?>">Back</a>
                </div>
            </div>
            <div id="res"></div>
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>Email</h4>
                            <input id="email" type="email" name="email" class="form-control my-3" placeholder="Email">
                        </div>
                        <div class="col-md-4">
                            <h4>Username</h4>
                            <input id="username" type="text" name="username" class="form-control my-3" placeholder="username">
                        </div>
                        <div class="col-md-3">
                            <h4>ISD Code</h4>
                            <input id="isd_code" type="number" name="isd_code" class="form-control my-3" placeholder="Ex: 96">
                        </div>
                        <div class="col-md-9">
                            <h4>Mobile</h4>
                            <input id="mobile" type="number" name="mobile" class="form-control my-3" placeholder="mobile">
                        </div>
                        <div class="col-md-6">
                            <h4>First name</h4>
                            <input type="text" id="first_name" name="first_name" class="form-control my-3" placeholder="First name">
                        </div>
                        <div class="col-md-6">
                            <h4>Lats name</h4>
                            <input type="text" id="last_name" name="last_name" class="form-control my-3" placeholder="Last name">
                        </div>
                       
                       
                    </div>
                </div>

                <div class="col-md-4">
                    <h4 class="text-capitalize"><?php echo $req->fg; ?></h4>
                    <div class="d-flex gap-2 align-items-center">
                        <div>
                            <select class="form-control" name="balance">
                                <option value="1">Add</option>
                                <option value="0">Deduct</option>
                            </select>
                        </div>
                        <div><input type="number" name="volume" class="form-control my-3" placeholder="<?php echo ucfirst($req->fg); ?> volume"></div>
                        <div><span>Litre</span></div>
                    </div>

                    <div class="d-grid">
                        <input type="hidden" name="user_id" id="user_id">
                        <input type="hidden" name="fg" value="<?php echo $req->fg; ?>">
                        <button id="register-fuel-btn" type="button" class="btn btn-primary my-3">Save</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

</form>
<?php pkAjax_form("#register-fuel-btn", "#register-new-fuel-form", "#res"); ?>

<!-- Helpers -->

<?php import("apps/admin/helpers/js/user-search.js.php"); ?>