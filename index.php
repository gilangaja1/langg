<?php
require 'connectdb.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['save_data'])) {
        $kode_lembar_kerja = $_POST['kode_lembar_kerja'];
        $tanggal_kirim = $_POST['tanggal_kirim'];
        $jamKirim = $_POST['jam_kirim'];
        $tiket = $_POST['tiket'];
        $service_number = $_POST['service_number'];
        $jenis_pekerjaan = $_POST['jenis_pekerjaan'];
        $hvc = $_POST['hvc'];
        $sto = $_POST['sto'];
        $mapping_team = $_POST['mapping_team'];
        $nama_teknisi = $_POST['nama_teknisi'];
        $status_order = $_POST['status_order'];
        $keluhan = $_POST['keluhan'];
        $letak_gangguan = $_POST['letak_gangguan'];
        $keterangan_perbaikan = $_POST['keterangan_perbaikan'];
        $jenis_gangguan = $_POST['jenis_gangguan'];

        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO lembar_kerja (
            kode_lembar_kerja, tanggal_kirim, jam_kirim, tiket, service_number,
            jenis_pekerjaan, hvc, sto, mapping_team, nama_teknisi, status_order,
            keluhan, letak_gangguan, keterangan_perbaikan, jenis_gangguan
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Bind parameters
        $stmt->bind_param("sssssssssssssss", 
            $kode_lembar_kerja, $tanggal_kirim, $jamKirim, $tiket, $service_number,
            $jenis_pekerjaan, $hvc, $sto, $mapping_team, $nama_teknisi, $status_order,
            $keluhan, $letak_gangguan, $keterangan_perbaikan, $jenis_gangguan
        );

        // Execute the statement
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Lembar kerja berhasil ditambahkan!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
        }

        // Close the statement
        $stmt->close();
        exit; // Stop further execution
    }
}

// Fetch data for display
$sql = "SELECT * FROM lembar_kerja";

// Apply date filter if startDate and endDate are provided in the GET request
if (isset($_GET['startDate']) && isset($_GET['endDate'])) {
    $startDate = $_GET['startDate'];
    $endDate = $_GET['endDate'];
    $sql .= " WHERE tanggal_kirim BETWEEN '$startDate' AND '$endDate'";
}

$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Error executing query: " . mysqli_error($conn));
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        .table-blue {
            background-color: #32CD32; /* Warna hijau muda */
            color: #000; /* Teks hitam */
        }

        .table-blue th, .table-blue td {
            background-color: #32CD32; /* Warna hijau muda */
            color: #000; /* Teks hitam */
        }

        /* Styling for the search input */
        .input-group-sm {
            max-width: 400px;
            margin-bottom: 10px;
        }

        .input-group-sm input.form-control {
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        .input-group-sm .btn {
            border-radius: 4px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <?= $top_title; ?>
            <small><?= $mid_title; ?></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active"><?= $title; ?></li>
        </ol>
    </section>

    <div class="input-group input-group-sm">
        <input type="text" id="searchInput" class="form-control" placeholder="Search Order Lembar Kerja...">
        <span class="input-group-btn">
            <button type="button" class="btn btn-info btn-flat"><i class="fa fa-search"></i></button>
        </span>
    </div>

    <div style="margin-top: 10px;">
        <a href="#" class="btn btn-success btn-sm" data-toggle="modal" data-target="#tambahOrderModal"><i class="fa fa-plus-circle"></i> Tambah Order Lembar Kerja</a>
    </div>

    <div class="input-group input-group-sm" style="margin-top: 20px;">
        <input type="date" id="startDate" class="form-control" placeholder="Start Date">
        <input type="date" id="endDate" class="form-control" placeholder="End Date">
        <span class="input-group-btn">
            <button type="button" id="filterDateBtn" class="btn btn-info btn-flat"><i class="fa fa-filter"></i> Filter by Date</button>
            <a href="#" class="btn btn-primary btn-sm">Download</a> 
        </span>
    </div>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-danger">
                    <div class="box-header">
                    <h3 class="box-title" style="color: black; background-color: #90EE90; padding: 5px; border-radius: 5px;">Data Lembar Kerja</h3>
                    </div>
                    <div class="box-body table-responsive">
                        <table id="example1" class="table table-bordered table-striped table-hover table-blue">
                            <thead>
                                <tr>
                                    <th>Kode Lembar Kerja</th>
                                    <th>Tanggal Kirim</th>
                                    <th>Jam</th>
                                    <th>Nomor Tiket</th>
                                    <th>Service Nomor</th>
                                    <th>Kategori Pekerjaan</th>
                                    <th>HVC</th>
                                    <th>STO</th>
                                    <th>Mapping Team</th>
                                    <th>Nama Teknisi</th>
                                    <th>Status Order</th>
                                    <th>Keluhan (Real)</th>
                                    <th>Letak Gangguan (Real)</th>
                                    <th>Keterangan Perbaikan/Kendala (Real)</th>
                                    <th>Jenis Gangguan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if (mysqli_num_rows($result) > 0) {
                                    while($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . $row["kode_lembar_kerja"] . "</td>";
                                        echo "<td>" . $row["tanggal_kirim"] . "</td>";
                                        echo "<td>" . $row["jam_kirim"] . "</td>";
                                        echo "<td>" . $row["tiket"] . "</td>";
                                        echo "<td>" . $row["service_number"] . "</td>";
                                        echo "<td>" . $row["jenis_pekerjaan"] . "</td>";
                                        echo "<td>" . $row["hvc"] . "</td>";
                                        echo "<td>" . $row["sto"] . "</td>";
                                        echo "<td>" . $row["mapping_team"] . "</td>";
                                        echo "<td>" . $row["nama_teknisi"] . "</td>";
                                        echo "<td>" . $row["status_order"] . "</td>";
                                        echo "<td>" . $row["keluhan"] . "</td>";
                                        echo "<td>" . $row["letak_gangguan"] . "</td>";
                                        echo "<td>" . $row["keterangan_perbaikan"] . "</td>";
                                        echo "<td>" . $row["jenis_gangguan"] . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='15'>Tidak ada data ditemukan.</td></tr>";
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Kode Lembar Kerja</th>
                                    <th>Tanggal Kirim</th>
                                    <th>Jam</th>
                                    <th>Nomor Tiket</th>
                                    <th>Service Nomor</th>
                                    <th>Kategori Pekerjaan</th>
                                    <th>HVC</th>
                                    <th>STO</th>
                                    <th>Mapping Team</th>
                                    <th>Nama Teknisi</th>
                                    <th>Status Order</th>
                                    <th>Keluhan (Real)</th>
                                    <th>Letak Gangguan (Real)</th>
                                    <th>Keterangan Perbaikan/Kendala (Real)</th>
                                    <th>Jenis Gangguan</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php foreach ($result as $jp) : ?>
    
    <?php endforeach; ?>

    <!-- Modal for adding data -->
    <div class="modal fade" id="tambahOrderModal" tabindex="-1" role="dialog" aria-labelledby="tambahOrderModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="tambahOrderModalLabel">Tambah Data Lembar Kerja</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="tambahOrderForm" action"index.php" method"POST">
              <!-- Existing form fields -->
              <div class="form-group">
                <label for="kodeLembarKerja">Kode Lembar Kerja</label>
                <input type="text" class="form-control" id="kodeLembarKerja" name="kode_lembar_kerja">
              </div>
              <div class="form-group">
                <label for="tanggalKirim">Tanggal Kirim</label>
                <input type="date" class="form-control" id="tanggalKirim" name="tanggal_kirim" value="<?= date('Y-m-d'); ?>">
              </div>
              <div class="form-group">
                <label for="jamKirim">Jam Kirim / Assign</label>
                <input type="time" class="form-control" id="jamKirim" name="jam_kirim">
              </div>
              <div class="form-group">
                <label for="tiket">Tiket</label>
                <input type="text" class="form-control" id="tiket" name="tiket" placeholder="Inputkan nomor tiket contoh: INC12345">
              </div>
              <div class="form-group">
                <label for="serviceNumberNode">Service Number / Node</label>
                <input type="text" class="form-control" id="serviceNumberNode" name="service_number" placeholder="Inputkan Service No atau Node">
              </div>

               <!-- New form fields based on the uploaded image -->
               <div class="form-group">
                <label for="jenisPekerjaan">Jenis Pekerjaan</label>
                <select class="form-control" id="jenisPekerjaan" name="jenis_pekerjaan">
                  <option value="">--- Pilih Jenis Pekerjaan ---</option>                
                  <!-- Populate options dynamically -->
                  <option value="SALAM PERDANA">SALAM PERDANA</option>
                  <option value="PENGUKURAN (MONET)">PENGUKURAN (MONET)</option>
                  <option value="UNSPEC">UNSPEC</option>
                  <option value="VALINS">VALINS</option>
                  <option value="DISMANTLE NTE / CABLE">DISMANTLE NTE / CABLE</option>
                  <option value="EVENT">EVENT</option>
                  <option value="SQM">SQM</option>
                  <option value="GANGGUAN REGULER (INDIHOME)">GANGGUAN REGULER (INDIHOME)</option>
                  <option value="CCAN DATIN">CCAN DATIN</option>
                  <option value="INFRACARE">INFRACARE</option>
                  <option value="TANGIBLE ODP">TANGIBLE ODP</option>
                  <option value="TANGIBLE ODC">TANGIBLE ODC</option>
                  <option value="BANTEK PSB">BANTEK PSB</option>
                  <option value="WIFI ID REGULER">WIFI ID REGULER</option>
                  <option value="PREVENTIVE NE">PREVENTIVE NE</option>
                  <option value="PREVENTIVE FTM">PREVENTIVE FTM</option>
                  <option value="GAMAS ODP (LOST/UNSPEC)">GAMAS ODP (LOST/UNSPEC)</option>
                  <option value="GAMAS DISTRIBUSI">GAMAS DISTRIBUSI</optio>
                  <option value="GAMAS FEEDER">GAMAS FEEDER</option>
                  <option value="VALIDASI PORT FTM ODC">VALIDASI PORT FTM ODC</option>
                  <option value="SQUAD/MTEL">SQUAD/MTEL</option>
                  <option value="PREVENTIVE SQUAD/MTEL">PREVENTIVE SQUAD/MTEL</option>
                  <option value="GAMAS CORE FTM">GAMAS CORE FTM</option>
                  <option value="GANGGUAN SPBU">GANGGUAN SPBU</option>
                  <option value="BANTEK PSB">BANTEK PSB</option>
                  <option value="PREVENTIVE GCU">PREVENTIVE GCU</option>
                  <option value="CCAN HSI/VOICE">CCAN HSI/VOICE</option>
                  <option value="KENDALA PSB CLUSTER (TOS)">KENDALA PSB CLUSTER (TOS)</option>
                  <option value="IJIN TANAM TIANG (TOS)">IJIN TANAM TIANG (TOS)</option>
                  <option value="GANGGUAN CLUSTER (TOS)">GANGGUAN CLUSTER (TOS)</option>

                  <!-- Populate options dynamically -->
                </select>
              </div>
              <div class="form-group">
                <label for="hvc">HVC</label>
                <select class="form-control" id="hvc" name="hvc">
                  <option value="">--- Pilih Flag HVC ---</option>
                  <!-- Populate options dynamically -->
                  <option value="HVC_DIAMOND">HVC_DIAMOND</option>
                  <option value="HVC_PLATINUM">HVC_PLATINUM</option>
                  <option value="HVC_GOLD">HVC_GOLD</option>
                  <option value="HVC_SILVER">HVC_SILVER</option>
                  <option value="REGULER">REGULER</option>
                  <option value="NON_HVC">NON_HVC</option>
                  <option value="VIP">VIP</option>

                  <!-- Populate options dynamically -->
                </select>
              </div>
              <div class="form-group">
                <label for="sto">STO</label>
                <select class="form-control" id="sto" name="sto">
                  <option value="">--- Pilih STO ---</option>
                  <option value="UBN">UBN</option>
                  <option value="MMN">MMN</option>
                  <option value="SMY">SMY</option>
                  <option value="TOP">TOP</option>
                  <option value="SWI">SWI</option>
                  <option value="SAU">SAU</option>
                  <option value="KLM">KLM</option>
                  <option value="BNO">BNO</option>
                  <option value="KUT">KUT</option>
                  <option value="JBR">JBR</option>
                  <option value="NDA">NDA</option>
                  <option value="ALL">ALL</option>

                  <!-- Populate options dynamically -->
                </select>
              </div>
              <div class="form-group">
              <label for="mappingTeam">Mapping Team</label>
              <select class="form-control" id="mappingTeam" name="mapping_team">
              <option value="">--- Pilih Mapping Team ---</option>
              <option value="IOAN SEKTOR">IOAN SEKTOR</option>
              <option value="CCAN">CCAN</option>
             <option value="WIFI ID">WIFI ID</option>
              <option value="SQUAT">SQUAT</option>
             <option value="SPBU">SPBU</option>
              </select>
                </div>

              <div class="form-group">
                <label for="namaTeknisi">Nama Teknisi</label>
                <input type="text" class="form-control" id="namaTeknisi" name="nama_teknisi" placeholder="Inputkan nama teknisi">
              </div>
              <div class="form-group">
                <label for="statusOrder">Status Order</label>
                <select class="form-control" id="statusOrder" name="status_order">
                  <option value="">--- Pilih Status Order ---</option>
                  <option value="OPEN">OPEN</option>
                  <option value="KENDALA">KENDALA</option>
                  <option value="MANJA/REMANJA">MANJA/REMANJA</option>
                  <option value="CLOSED">CLOSED</option>
                  <!-- Populate options dynamically -->
                </select>
              </div>
              <div class="form-group">
                <label for="keluhan">Keluhan (Real)</label>
                <textarea class="form-control" id="keluhan" name="keluhan" placeholder="Inputkan keluhan"></textarea>
              </div>
              <div class="form-group">
              <label for="letakGangguan">Letak Gangguan (Real)</label>
             <select class="form-control" id="letakGangguan" name="letak_gangguan">
             <option value="">--- Pilih Letak Gangguan ---</option>
             <option value="OLT">OLT</option>
            <option value="FTM">FTM</option>
            <option value="FEEDER">FEEDER</option>
            <option value="ODC">ODC</option>
            <option value="DISTRIBUSI">DISTRIBUSI</option>
            <option value="ODP">ODP</option>
            <option value="PASSIVE SPLITER">PASSIVE SPLITER</option>
            <option value="ADAPTER/ I-CONNECTOR">ADAPTER/ I-CONNECTOR</option>
            <option value="DROPCORE / DROP CABLE">DROPCORE / DROP CABLE</option>
            <option value="OTP">OTP</option>
            <option value="PREKSO">PREKSO</option>
            <option value="ROSET">ROSET</option>
            <option value="PATCHCORD">PATCHCORD</option>
            <option value="ONT">ONT</option>
            <option value="STB">STB</option>
            <option value="KABEL LAN (RJ45)">KABEL LAN (RJ45)</option>
            <option value="KABEL TELEPON (RJ11)">KABEL TELEPON (RJ11)</option>
            <option value="IKG/IKR">IKG/IKR</option>
            <option value="ACCES POINT">ACCES POINT</option>
            <option value="PPOE">PPOE</option>
            <option value="SWITCH HUB">SWITCH HUB</option>
            <option value="LAM JUMPER">LAM JUMPER</option>
            <option value="STOP KONTAK LISTRIK">STOP KONTAK LISTRIK</option>
            <option value="BRAS">BRAS</option>
            <option value="METRO">METRO</option>
            <option value="PESAWAT TELEPON (CUSTOMER)">PESAWAT TELEPON (CUSTOMER)</option>
            <option value="LOGIC">LOGIC</option>
            <option value="INDIBOX">INDIBOX</option>
            <option value="CCTV INDIHOME / SMART CAMERA">CCTV INDIHOME / SMART CAMERA</option>
            <option value="EXTENDER">EXTENDER</option>
            <option value="PLC">PLC</option>
            <option value="BAIK SENDIRI / NORMAL KEMBALI">BAIK SENDIRI / NORMAL KEMBALI</option>
            <option value="REMOTE STB / TV RUSAK">REMOTE STB / TV RUSAK</option>

                </select>
                </div>
              <div class="form-group">
                <label for="keteranganPerbaikan">Keterangan Perbaikan / Kendala (Real)</label>
                <textarea class="form-control" id="keteranganPerbaikan" name="keterangan_perbaikan" placeholder="Inputkan keterangan perbaikan atau kendala"></textarea>
              </div>
              <div class="form-group">
             <label for="jenisGangguan">Jenis Gangguan</label>
             <select class="form-control" id="jenisGangguan" name="jenis_gangguan">
             <option value="">Pilih jenis gangguan</option>
             <option value="FISIK">FISIK</option>
            <option value="LOGIC">LOGIC</option>
            <option value="BILLING / PAYMENT">BILLING / PAYMENT</option>
             </select>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" name="save_data" class="btn btn-primary">Save data</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

 <!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.6.0/js/bootstrap.min.js"></script>

  <script>
    $(document).ready(function() {
    // Generate random "Kode Lembar Kerja" and current time when modal is opened
    $('#tambahOrderModal').on('show.bs.modal'), function () {
        var kode_lembar_kerja = 'INC' + Math.floor(Math.random() * 1000000).toString().padStart(6, '0');
        $('#kode_lembar_kerja').val(kode_lembar_kerja);
    }

        // Set current time for "Jam Kirim / Assign" field
        var now = new Date();
        var hours = now.getHours().toString().padStart(2, '0');
        var minutes = now.getMinutes().toString().padStart(2, '0');
        var currentTime = hours + ':' + minutes;
        $('#jam_Kirim').val(currentTime);
      });

      // Filter by date functionality
      $('#filterDateBtn').on('click', function() {
        var startDate = $('#startDate').val();
        var endDate = $('#endDate').val();
        if (startDate && endDate) {
          // Implement your date filter logic here
          console.log('Filtering data from ' + startDate + ' to ' + endDate);
        } else {
          alert('Please select both start and end dates.');
        }
      });

      // Form submission handling (menggunakan AJAX)
    $('#tambahOrderForm').on('save_data', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        console.log('Form submitted:', formData);

        $.ajax({
       url: 'http://localhost/phpmyadmin/index.php?route=/&route=%2F&db=miksu&table=lembar_kerja', // URL localhost Anda
      type: 'POST',
     data: formData,
     dataType: 'json',
      success: function (response) {
        if (response.success) {
            // Tampilkan pesan sukses
            alert(response.message);

            var newRowData = {
            kode_lembar_kerja: $('#kode_lembar_kerja').val(),
             tanggal_kirim: $('#tanggal_kirim').val(),
            jam_kirim: $('#jam_kirim').val(),
             tiket: $('#tiket').val(),
             service_number: $('#service_number').val(),
            jenis_pekerjaan: $('#jenis_pekerjaan').val(),
             hvc: $('#hvc').val(),
             sto: $('#sto').val(),
             mapping_team: $('#mapping_team').val(),
             nama_teknisi: $('#nama_teknisi').val(),
             status_order: $('#status_order').val(),
            keluhan: $('#keluhan').val(),
             letak_gangguan: $('#letak_gangguan').val(),
            keterangan_perbaikan: $('#keterangan_perbaikan').val(),
            jenis_gangguan: $('#jenis_gangguan').val()
};


                var newRow = '<tr>' +
                        '<td>' + newRowData.kode_lembar_kerja + '</td>' +
                        '<td>' + newRowData.tanggal_kirim + '</td>' +
                        '<td>' + newRowData.jam_kirim + '</td>' +
                        '<td>' + newRowData.tiket + '</td>' +
                        '<td>' + newRowData.service_number + '</td>' +
                        '<td>' + newRowData.jenis_pekerjaan + '</td>' +
                        '<td>' + newRowData.hvc + '</td>' +
                        '<td>' + newRowData.sto + '</td>' +
                        '<td>' + newRowData.mapping_team + '</td>' +
                        '<td>' + newRowData.nama_teknisi + '</td>' +
                        '<td>' + newRowData.status_order + '</td>' +
                        '<td>' + newRowData.keluhan + '</td>' +
                        '<td>' + newRowData.letak_gangguan + '</td>' +
                        '<td>' + newRowData.keterangan_perbaikan + '</td>' +
                        '<td>' + newRowData.jenis_gangguan + '</td>' + 
                        '</tr>';

                // Tambahkan baris baru ke tabel
                $('#example1 tbody').append(newRow);

                // Reset formulir setelah data berhasil disimpan
                $('#tambahOrderForm')[0].reset();

                // Tutup modal
                $('#tambahOrderModal').modal('hide');
            } else {
                // Tampilkan pesan error
                alert(response.message);
            }
        }, 
        error: function (error) {
            console.log('Form submission error:', error);
            alert('Terjadi kesalahan saat menyimpan data.');
        }
    });
});

      // Filter by date functionality (modified to send GET request)
      $('#filterDateBtn').on('click', function() {
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();
            if (startDate && endDate) {
                window.location.href = '?startDate=' + startDate + '&endDate=' + endDate; 
            } else {
                alert('Please select both start and end dates.');
            }
        });
    </script>
</body>
</html>