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


                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Order ID</th>
                                    <th scope="col">Driver</th>
                                    <th scope="col">Buyer</th>
                                    <th>Buyer to Driver</th>
                                    <th scope="col">Buyer To Rest.</th>
                                    <th>Add On Price</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $db = new Dbobjects;
                                foreach ($fl as $key => $pv) :
                                    $pv = obj($pv);
                                    $btnid = uniqid("btn{$pv->orderid}");
                                    $formid = uniqid("form{$pv->orderid}");
                                    $add_on_price = $db->showOne("select add_on_price from orders where orders.unique_id = '$pv->orderid'")['add_on_price'];
                                ?>

                                    <tr>
                                        <th><?php echo $pv->orderid; ?></th>
                                        <th><?php echo $pv->driver_assigned ? $pv->driver : 'NA'; ?></th>
                                        <th><?php echo $pv->buyer; ?></th>
                                        <th><?php echo $pv->driver_assigned ? $pv->driver_to_user . " " . $pv->distance_unit : 'NA'; ?></th>
                                        <th><?php echo $pv->user_to_rest . " " . $pv->distance_unit; ?></th>
                                        <th>
                                            <form id="<?php echo $formid; ?>" method="post" action="<?php echo BASEURI . route('updateAddOnPrice'); ?>">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div>
                                                        <input type="hidden" name="orderid" value="<?php echo $pv->orderid; ?>">
                                                        <input type="text" name="add_on_price" value="<?php echo $add_on_price; ?>" class="form-control">
                                                    </div>
                                                    <div>
                                                        <button type="button" id="<?php echo $btnid; ?>" class="btn btn-sm btn-primary">Set Price</button>
                                                    </div>
                                                </div>
                                            </form>
                                            <?php send_to_server("#$btnid","#$formid"); ?>
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
<script>
    const testCode = (res)=>{
        console.log(res);
    }
</script>
