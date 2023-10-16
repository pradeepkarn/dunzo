<?php
$createData = $context;
?>

<form action="/<?php echo home . route('productCatStoreAjax'); ?>" id="save-new-post-form">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <h5 class="card-title">Add category</h5>
                </div>
                <div class="col text-end my-3">
                    <a class="btn btn-dark" href="/<?php echo home . route('productCatList'); ?>">Back</a>
                </div>
            </div>
            <div id="res"></div>
            <div class="row">
                <div class="col-md-8">
                    <h4>Title</h4>
                    <input type="text" name="title" class="form-control my-3" placeholder="Title">
                    <textarea class="tinymce-editor" name="content" id="mce_0" aria-hidden="true"></textarea>
                </div>
                <div class="col-md-4">
                    <h4>Banner</h4>
                    <input accept="image/*" id="image-input" type="file" name="banner" class="form-control my-3">
                    <img style="width:100%; max-height:300px; object-fit:contain;" id="banner" src="" alt="">
                    <!-- <h4>Single Game Link</h4>
                    <input type="text" name="link" class="form-control my-3" placeholder="Game link"> -->
                    <!-- <h4>OR</h4> -->
                    <!-- Game url -->
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Import Bulk URLs (.csv file)</h4>
                            <div id="resupload" style="max-height: 100px; overflow-y:scroll;"></div>
                        </div>
                        <div class="col-md-12">
                            <input type="file" accept=".csv" name="csvfile" class="form-control">
                        </div>
                        <!-- <div class="col-md-12">
                        <button id="uploadcsvbtn" class="btn btn-primary">Import</button>
                    </div> -->
                        <div class="col-md-12">
                            <h5 id="upload-info">Pleaase wait while uploading in database ...</h5>
                        </div>
                        <div class="col-md-12">
                            <a href="<?php echo BASEURI; ?>/data/csv/games.csv" download>Download Sample CSV</a>
                        </div>
                        <?php
                        ajaxActive("#upload-info");
                        // pkAjax_form("#uploadcsvbtn", "#uploadcsvform", "#resupload");
                        ?>
                    </div>
                    <!-- Game url end -->
                    <h4>Price</h4>
                    <input type="number" scope="any" name="price" class="form-control my-3" placeholder="Price">
                    <h4>Live timing:</h4>
                    <label for="fromTime">Opens at</label>
                    <input type="datetime-local" class="form-control" name="opens_at" id="fromTime">
                    <label for="toTime">Closes at</label>
                    <input type="datetime-local" class="form-control" name="closes_at" id="toTime">
                    <div class="d-grid">
                        <button id="save-post-btn" type="button" class="btn btn-primary my-3">Save</button>
                    </div>
                </div>

            </div>

        </div>
    </div>

</form>
<script>
    const imageInputPost = document.getElementById('image-input');
    const imagePost = document.getElementById('banner');

    imageInputPost.addEventListener('change', (event) => {
        const file = event.target.files[0];
        const fileReader = new FileReader();

        fileReader.onload = () => {
            imagePost.src = fileReader.result;
        };

        fileReader.readAsDataURL(file);
    });
</script>
<?php pkAjax_form("#save-post-btn", "#save-new-post-form", "#resupload"); ?>