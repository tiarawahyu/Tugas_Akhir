<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Dashboard</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <?php if ($flash) : ?>
              <div class="text-success font-weight-bold"><?php echo $flash; ?></div>
              <?php else : ?>
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>" class="fas fa-house fa-lg" style="color: blue;"></a></li>
            </ol>
              <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
   
    <section class="content">
      <div class="container-fluid">
        <div class="row">

          <div class="col-lg-3 col-6" style="cursor: pointer;" onclick="javascrip:window.location='customer/orders?status=1'">
            <!-- small box -->
            <div class="small-box" style="background: #D3D3D3;">
              <div class="inner">
                <h3><?php echo $total_unpaid_order; ?>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<i class="fas fa-wallet fa-sm" style="color: blue;"></i></h3>
                <b><center>Belum Dibayar</p></center><p></b>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <!-- <a href="<?php echo site_url('customer/orders'); ?>" class="small-box-footer">Lihat Order <i class="fas fa-arrow-circle-right"></i></a> -->
            </div>
          </div>
          
          <div class="col-lg-3 col-6" style="cursor: pointer;" onclick="javascrip:window.location='customer/orders?status=2'">
            <!-- small box -->
            <div class="small-box" style="background: #B0C4DE;">
              <div class="inner">
                <h3><?php echo $total_process_order; ?>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<i class="fas fa-box fa-sm" style="color: blue;"></i></h3>

                <b><center>Dikemas</p></center><p></b>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <!-- <a href="<?php echo site_url('customer/payments'); ?>" class="small-box-footer">Lihat Pembayaran <i class="fas fa-arrow-circle-right"></i></a> -->
            </div>
          </div>
          
          <div class="col-lg-3 col-6" style="cursor: pointer;" onclick="javascrip:window.location='customer/orders?status=3'">
            <!-- small box -->
            <div class="small-box" style="background: #FFA07A;">
              <div class="inner">
                <h3><?php echo $total_delivery_order; ?>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<i class="fas fa-truck fa-sm" style="color: blue;"></i></h3>

                <b><center>Dikirim</p></center><p></b>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <!-- <a href="<?php echo site_url('customer/orders'); ?>" class="small-box-footer">Lihat Order <i class="fas fa-arrow-circle-right"></i></a> -->
            </div>
          </div>
          
          <div class="col-lg-3 col-6" style="cursor: pointer;" onclick="javascrip:window.location='customer/orders?status=4'">
              <!-- small box -->
              <div class="small-box" style="background: #20B2AA;">
                <div class="inner">
                  <h3><?php echo $total_success_order; ?>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<i class="fas fa-circle-check fa-sm" style="color: blue;"></i></h3>
  
                  <b><center>Selesai</p></center><p></b>
                </div>
                <div class="icon">
                  <i class="ion ion-pie-graph"></i>
                </div>
                <!-- <a href="<?php echo site_url('customer/reviews'); ?>" class="small-box-footer">Lihat Reviews <i class="fas fa-arrow-circle-right"></i></a> -->
              </div>
          </div>
        </div>
      </div>
    </div>
          </div>
        </div>
       
      </div>
    </section>
  </div>