<?php
$fl = $context->orders_list;
$tp = $context->total_orders;
$cp = $context->current_page;
$active = $context->is_active;

$ug =  explode("/", REQUEST_URI);
$ug = $ug[3];
$req = new stdClass;
$req->fg = $ug;
// myprint($fl);
?>

<section class="section">
    <div class="row">
        <div class="col-lg-12">

            <div class="card">
                <div class="card-body">


                    <!-- Table with stripped rows -->
                    <div class="table-responsive">


                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th scope="col">Id</th>
                                    <th scope="col">Order ID</th>
                                    <th scope="col">Driver</th>
                                    <th scope="col">Buyer</th>
                                    <th>Buyer to Driver</th>
                                    <th scope="col">Buyer To Rest.</th>
                                    <th>Add On Price</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($fl as $key => $pv) :
                                    $pv = obj($pv);

                                ?>

                                    <tr>
                                        <th scope="row"><?php echo $pv->id; ?></th>
                                        <th><?php echo $pv->orderid; ?></th>
                                        <th><?php echo $pv->driver_assigned ? $pv->driver : 'NA'; ?></th>
                                        <th><?php echo $pv->buyer; ?></th>
                                        <th><?php echo $pv->driver_assigned ? $pv->driver_to_user . " " . $pv->distance_unit : 'NA'; ?></th>
                                        <th><?php echo $pv->user_to_rest . " " . $pv->distance_unit; ?></th>
                                        <th>
                                            <input type="text" name="add_on_price" class="form-control">
                                        </th>
                                    </tr>

                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- End Table with stripped rows -->
                </div>

            </div>

        </div>
    </div>
</section>