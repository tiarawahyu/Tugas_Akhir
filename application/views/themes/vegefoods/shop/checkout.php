<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="hero-wrap hero-bread" style="background-image: url('<?php echo get_theme_uri('images/bg_1.jpg'); ?>');">
    <div class="container">
      <div class="row no-gutters slider-text align-items-center justify-content-center">
        <div class="col-md-9 ftco-animate text-center">
            <p class="breadcrumbs"><span class="mr-2"><?php echo anchor(base_url(), 'Home'); ?></span> <span>Checkout</span></p>
          <h1 class="mb-0 bread">Checkout</h1>
        </div>
      </div>
    </div>
  </div>

  <section class="ftco-section">
    <div class="container">
    <form id="form-co" action="<?php echo site_url('shop/checkout/order'); ?>" method="POST">

      <div class="row justify-content-center">
        <div class="col-xl-7 ftco-animate">
                <h3 class="mb-4 billing-heading">Alamat Pengiriman</h3>

                <div class="form-group">
                    <label for="name" class="form-control-label">Pengiriman untuk (nama):</label>
                    <input type="text" name="name" value="<?php echo $customer->name; ?>" class="form-control" id="name" required>
                </div>

                <div class="form-group">
                    <label for="hp" class="form-control-label">No. HP:</label>
                    <input type="text" name="phone_number" value="<?php echo $customer->phone_number; ?>" class="form-control" id="hp" required>
                </div>

                <div class="form-group">
                    <label for="hp" class="form-control-label">Email:</label>
                    <input type="text" name="email" value="<?php echo $as_user; ?>" class="form-control" id="email" required>
                </div>

                <div class="form-group">
                    <label for="hp" class="form-control-label">Kota:</label>
                    <select class="form-control select" id="kota" name="kota" onchange="reload()">
                        <option value="--pilih--" <?php echo ($kota == "--pilih--")? 'selected' : ''; ?> >-- pilih kota --</option>
                        <?php foreach($kota as $k => $v){ ?>
                            <option value="<?php echo ($v->city_name . "<>" . $v->postal_code . "<>" . $v->city_id) ?>" <?php echo ($selected_kota == $v->city_id)? 'selected' : '' ?> ><?php echo ($v->type . " " . $v->city_name . " - " . $v->province) ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="hp" class="form-control-label">Pengiriman:</label>
                    <h5 class="">JNE</h5>
                    <select class="form-control select" id="kurir" onchange="gantiKurir()">
                        <option value="0">-- pilih jenis pengiriman --</option>
                        <?php foreach($ongkir["costs"] as $k => $v){
                            $harga = format_rupiah($v["cost"][0]["value"]);
                            ?>
                            <option value="<?php echo $v["cost"][0]["value"] ?>">
                                <?php echo "Rp " . $harga . " - " . $v["service"] . " - " . $v["description"] . " - estimasi " . $v["cost"][0]["etd"] . " hari" ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="address" class="form-control-label">Alamat:</label>
                    <textarea name="address" class="form-control" id="address" required><?php echo $customer->address; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="note" class="form-control-label">Catatan:</label>
                    <textarea name="note" class="form-control" id="note"></textarea>
                </div>

        </div>
        <div class="col-xl-5">
            <div class="row mt-5 pt-3">
                <div class="col-md-12 d-flex mb-5">
                    <div class="cart-detail cart-total p-3 p-md-4">
                        <h3 class="billing-heading mb-4">Rincian Belanja</h3>
                        <p class="d-flex">
                            <span>Subtotal</span>
                            <span>Rp <?php echo format_rupiah($subtotal); ?></span>
                        </p>
                        <p class="d-flex">
                            <span>Ongkos kirim</span>
                            <span id="display-ongkir">Rp 0</span>
                        </p>
                        <hr>
                        <p class="d-flex total-price">
                            <span>Total</span>
                            <span id="display-total">Rp <?php echo format_rupiah($total); ?></span>
                        </p>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="cart-detail p-3 p-md-4" style="display:none;">
                            <h3 class="billing-heading mb-4">Metode Pembayaran</h3>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="radio">
                                        <label><input type="radio" name="payment" class="mr-2" value="1" checked> Transfer bank</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="radio">
                                        <label><input type="radio" name="payment" class="mr-2" value="2" > Bayar ditempat</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <div class="form-group text-right" style="margin-top: 10px;">
                    <input id="pengiriman_addon" type="hidden" name="pengiriman_addon" value="">
                <input class="btn btn-primary py-2 px-2" value="Buat Pesanan" onclick="co()">
            </div>
                </div>

                
            </div>
        </div> <!-- .col-md-8 -->
      </div>

    </form>
    </div>

    <form id="form-reload" action="<?php echo site_url('shop/checkout'); ?>" method="POST">
        <?php
            foreach($_SESSION["QTY_CART"] as $v){
                $arr = explode("<>", $v);
        ?>
            <input type="hidden" name="<?php echo $arr[0]; ?>" value="<?php echo $arr[1]; ?>">
        <?php
            }
        ?>
        <input id="form-reload-kota" type="hidden" name="selected_kota" value="-">
    </form>

  </section> <!-- .section -->

  <script>
    var kota = document.getElementById("kota").value
    document.getElementById("form-reload-kota").value = kota
    function reload(){
        var kota = document.getElementById("kota").value
        // if(kota == "--pilih--"){
        //     return
        // }
        var kota_arr = kota.split("<>")
        document.getElementById("form-reload-kota").value = kota_arr[2]
        var formRelaod = document.getElementById("form-reload")

        formRelaod.submit()
    }

    
    function gantiKurir(){
        var el_kurir = document.getElementById("kurir")
        // if(kurir.value == "0"){
        //     return
        // }
        var ongkir = parseInt(el_kurir.value)
        var display = ongkir.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })
        document.getElementById("display-ongkir").innerHTML = display

        var prevSubtotal = parseInt("<?php echo $subtotal ?>")
        var total = prevSubtotal + ongkir
        document.getElementById("display-total").innerHTML = total.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })

        var pengiriman = (el_kurir.options[el_kurir.selectedIndex].text).split(" - ")
        var pengirimanAddon = {
            value : ongkir,
            service : pengiriman[1],
            description : pengiriman[2],
            etd : pengiriman[3],
        }
        pengirimanAddon = JSON.stringify(pengirimanAddon)
        console.log(pengirimanAddon)
        document.getElementById("pengiriman_addon").value = pengirimanAddon
    }


    function co(){
        var kota = document.getElementById("kota").value
        var kurir = document.getElementById("kurir").value

        if(kota == "--pilih--" || kurir == "0"){
            alert("Mohon pilih kota dan jenis pengiriman")
            return
        }

        document.getElementById("form-co").submit()
    }


  </script>