<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-7">
                    <h1><b>Review Saya</b></h1>
                </div>
                <div class="col-sm-1"> 
                    <?php echo anchor('customer/reviews/write', 'Review Baru'); ?>
                </div>
                <div class="col-sm-4">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><?php echo anchor(base_url(), 'Home'); ?></li>
                        <li class="breadcrumb-item active"><?php echo anchor('customer/reviews/write', 'Dasboard'); ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card card-dark">
            <div class="card-body<?php echo ( count($reviews) > 0) ? ' p-0' : ''; ?>">
            <?php if ( count($reviews) > 0) : ?>
                <div class="table-responsive">
                    <table class="table table-striped m-0">
                        <tr class="bg-dark">
                            <th scope="col">No.</th>
                            <th scope="col">Order</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Review</th>
                        </tr>
                        <?php foreach ($reviews as $key => $review) : ?>
                        <tr>
                            <td><?php echo $key+1 ?></td>
                            <td><?php echo anchor('customer/reviews/view/'. $review->id, '#'. $review->order_number); ?></td>
                            <td><?php echo get_formatted_date($review->review_date); ?></td>
                            <td><?php echo $review->review_text; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php else : ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            Belum ada review yang ditulis. Silahkan tulis baru.
                        </div>

                        <?php echo anchor('customer/reviews/write', 'Tulisan review baru'); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($pagination) : ?>
            <div class="card-footer">
                <?php echo $pagination; ?> 
            </div>
            <?php endif; ?>

        </div>
    </section>

</div>