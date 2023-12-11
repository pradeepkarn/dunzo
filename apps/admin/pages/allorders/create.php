<?php
$createData = $context;
$uri =  explode("/", REQUEST_URI);

?>

<form action="" id="register-new-fuel-form">
    <div class="card">
        <div class="card-body">

            <div id="res"></div>
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title">Import Orders</h5>
                        </div>
                        <div class="col text-end my-3">
                            <a class="btn btn-dark" href="/<?php echo home; ?>">Back</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <input type="file" name="sheet" class="form-control">
                            <input type="hidden" name="action" value="sheet_upload">
                        </div>
                    </div>
                    <div class="d-grid">
                        <button id="register-fuel-btn" type="button" class="btn btn-primary my-3">Import</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

</form>
<?php pkAjax_form("#register-fuel-btn", "#register-new-fuel-form", "#res"); ?>

<!-- Helpers -->

<?php import("apps/admin/helpers/js/user-search.js.php"); ?>