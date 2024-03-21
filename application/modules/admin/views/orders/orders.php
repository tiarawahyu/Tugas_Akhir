<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	?>
<!-- Header -->
<div class="header bg-primary pb-6">
	<div class="container-fluid">
		<div class="header-body">
			<div class="row align-items-center py-4">
				<div class="col-lg-6 col-7">
					<h6 class="h2 text-white d-inline-block mb-0">Kelola Order Customer</h6>
				</div>
				<div class="col-lg-6 col-5 text-right">
					<nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
						<ol class="breadcrumb breadcrumb-links breadcrumb-dark">
							<li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
							<li class="breadcrumb-item active" aria-current="page">Order</li>
						</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Page content -->
<div class="container-fluid mt--6">
<div class="row">
	<div class="col">
		<div class="card">
			<!-- Card header -->
			<div class="card-header">
				<h3 class="mb-0">Kelola Order</h3>
			</div>
      <div class="row ml-2 mr-2 mt-2 mb-2">
        <div class="col-lg-4 col-md-4">
        </div>
        <div class="col-lg-3 col-md-3">
          <label class="form-control-label" for="filter_periode">Periode</label>
          <select class="select form-control" id="filter_periode">
            <option value="_ALL_">(semua)</option>
            <option value="DAY">Hari ini</option>
            <option value="MONTH">Bulan ini</option>
            <option value="YEAR">Tahun ini</option>
          </select>
        </div>
        <div class="col-lg-3 col-md-3">
          <label class="form-control-label" for="filter_status">Status</label>
          <select class="select form-control" id="filter_status">
            <option value="_ALL_">(semua)</option>
            <option value="4">Selesai</option>
            <option value="5">Dibatalkan</option>
          </select>
        </div>
        <div class="col-lg-2 col-md-2 pt-4">
          <?php if ( count($orders) > 0) : ?>
          <button class="btn btn-success" onClick="javascript:exportData()">Download</button>
          <?php endif; ?>
        </div>
      </div>
			<?php if ( count($orders) > 0) : ?>
			<div class="card-body p-0">
				<div class="table-responsive">
					<!-- Projects table -->
					<table class="table align-items-center table-flush">
						<thead class="thead-light">
							<tr>
								<th scope="col">No.</th>
								<th scope="col">ID</th>
								<th scope="col">Customer</th>
								<th scope="col">Tanggal</th>
								<th scope="col">Jumlah Item</th>
								<th scope="col">Jumlah Harga</th>
								<th scope="col">Status</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($orders as $key => $order) : ?>
							<tr>
								<th scope="col">
									<?php echo $order->M_ID ?>
								</th>
								<th scope="col">
									<?php echo anchor('admin/orders/view/'. $order->id, '#'. $order->order_number); ?>
								</th>
								<td><?php echo $order->customer; ?></td>
								<td>
									<?php echo get_formatted_date($order->order_date); ?>
								</td>
								<td>
									<?php echo $order->total_items; ?>
								</td>
								<td>
									Rp <?php echo format_rupiah($order->total_price); ?>
								</td>
								<td><?php echo get_order_status($order->order_status, $order->payment_method); ?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="card-footer">
				<?php echo $pagination; ?>
			</div>
			<?php else : ?>
			<div class="card-body">
				<div class="row">
					<div class="col-md-8">
						<div class="alert alert-primary">
							Tidak ada ada order.
						</div>
					</div>
					<div class="col-md-4">
						<a href="<?php echo site_url('admin/products/add_new_product'); ?>"><i class="fa fa-plus"></i> Tambah produk baru</a>
						<br>
						<a href="<?php echo site_url('admin/products/category'); ?>"><i class="fa fa-list"></i> Kelola kategori</a>
					</div>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<script type="text/javascript">

  $("#filter_periode").val("<?php echo $filter_periode; ?>")
  $("#filter_status").val("<?php echo $filter_status; ?>")

  $("#filter_periode").on("change", function(){
    var periode = $(this).val()
    console.log(periode)
    filter("filter_periode", periode)
  })
  $("#filter_status").on("change", function(){
    var status = $(this).val()
    console.log(status)
    filter("filter_status", status)
  })

  function filter(k, v){
    const queryParams = new URLSearchParams(window.location.search);
    queryParams.set(k, v);
    const newUrl = `${window.location.pathname}?${queryParams.toString()}`;
    window.location.href = newUrl;
  }

  function exportData(){
    filter("export", "Y")
  }

  // function exportData(){
  //   const queryParams = new URLSearchParams(window.location.search);
  //   queryParams.set("export", "Y");
  //   const newUrl = `${window.location.pathname}?${queryParams.toString()}`;
  //   $.ajax({
  //       url: newUrl,
  //       type: 'POST',
  //       success: function(data) {
  //           // Memicu unduhan file
  //           var blob = new Blob([data]);
  //           var link = document.createElement('a');
  //           link.href = window.URL.createObjectURL(blob);
  //           link.download = 'order.xlsx';
  //           document.body.appendChild(link);
  //           link.click();
  //           document.body.removeChild(link);
  //       },
  //       error: function() {
  //           alert('Gagal mengunduh file.');
  //       }
  //   });
  // }

</script>
