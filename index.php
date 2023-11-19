<?php
require 'functions.php';

if (!isset($_SESSION["login"])) {
	header("Location: login.php");
	exit;
}

// pagination
// konfigurasi
$jumlah_data_per_halaman = 10;
$jumlah_data = count(query("SELECT * FROM arsip_dokumen  JOIN kategori ON arsip_dokumen.id_kategori = kategori.id_kategori WHERE arsip_dokumen.id_kategori"));
$jumlah_halaman = ceil($jumlah_data / $jumlah_data_per_halaman);
$halaman_aktif = (isset($_GET['halaman'])) ? $_GET['halaman'] : 1;
$awal_data = ($jumlah_data_per_halaman * $halaman_aktif) - $jumlah_data_per_halaman;

$isi = query("SELECT * FROM arsip_dokumen JOIN kategori ON arsip_dokumen.id_kategori = kategori.id_kategori WHERE arsip_dokumen.id_kategori LIMIT $awal_data, $jumlah_data_per_halaman");

// tombol cari di klik

if (isset($_POST['cari_judul_buku'])) {
	$keyword = $_POST['keyword'];
	$isi = cariJudulBuku($keyword);
}
if (isset($_POST['cari_tahun'])) {
	$keyword = $_POST['keyword'];
	$isi = caritahun($keyword);
}
if (isset($_POST['cari_kategori'])) {
	$keyword = $_POST['keyword'];
	$isi = cari_kategori($keyword);
}
if (isset($_POST['cari_nama_file'])) {
	$keyword = $_POST['keyword'];
	$isi = carinamafile($keyword);
}
if (isset($_REQUEST['btn'])) {
	$data =  $_REQUEST['btn'];
	$isi = urutsku($data);
}
?>

<!doctype html>
<html lang="en">

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="vendor/bootstrap.min.css">

	<!-- CSS -->
	<link rel="stylesheet" href="vendor/style.css">
	<link rel="shortcut icon" type="text/css" href="img/Logo_BPPT.png">

	<!-- font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
	<title>Aplikasi Arsip Sederhana</title>
</head>
<style></style>

<body class="bg">
	<?php include 'include/navbar.php'; ?>
<h1>men</h1>
<h1>men</h1>
	<section id="bidang" class="bidang">
		<div class="container-fluid">
			<div class="row marquee justify-content-center">
				<div class="col-md-6 mt-2 mb-3">
					<marquee scrollamount="10" loop="infinite" direction="left" class="nav-link text-white p-2 bg-info rounded text-center">
						<h4><?= salam(); ?>, <?= ucfirst($_SESSION['nama']); ?></h4>
					</marquee>
				</div>
			</div>
			<div class="row h3">
				<div class="col">
					<h3 class="bg-supergraphicss p-2 text-white text-center rounded">Dashboard</h3>
				</div>
			</div>
			<div class="row justify-content-center menu">

				<div class="col-md-10 mt-3 mb-2">
					<form class="form-inline" method="post">
						<input for="colFormLabel" class="form-control mr-sm-2" type="text" autocomplete="off" placeholder="Cari" aria-label="Search" name="keyword">
						<div class="dropdown">
							<button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								Cari berdasarkan
								<img src="img/baseline_search_white_18dp.png">
							</button>
							<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
								<button class="dropdown-item" name="cari_judul_buku" type="submit">Judul Buku</button>
								<button class="dropdown-item" name="cari_kategori" type="submit">Kategori</button>
								<button class="dropdown-item" name="cari_tahun" type="submit">Tahun</button>
								<button class="dropdown-item" name="cari_nama_file" type="submit">Nama File</button>
							</div>
						</div>
					</form>
				</div>
				<?php if ($_SESSION['akses'] == 'administrator') : ?>
					<div class="col-md-2 mt-3 mb-2">
						<a href="input.php" class="btn btn-primary" style="border-radius: 5px;">
							Tambah Data Arsip</a>
					</div>
				<?php endif ?>
			</div>
			<div class="row pagination">
				<!-- pagination -->
				<div class="col-md-12">
					<nav aria-label="Page navigation example">
						<ul class="pagination pagination-md">
							<li class="page-item">
								<?php if ($halaman_aktif > 1) : ?>
									<a class="page-link" href="?halaman=<?= $halaman_aktif - 1; ?>" aria-label="Previous">
										<span aria-hidden="true">&laquo;</span>
									</a>
								<?php endif; ?>
							</li>
							<?php for ($i = 1; $i <= $jumlah_halaman; $i++) : ?>
								<?php if ($i == $halaman_aktif) : ?>
									<li class="page-item"><a class="rounded page-link bg-primary text-white" href="?halaman=<?= $i; ?>"><?= $i; ?></a></li>
								<?php else : ?>
									<li class="page-item"><a class="page-link" href="?halaman=<?= $i; ?>"><?= $i; ?></a></li>
								<?php endif; ?>
							<?php endfor; ?>
							<li class="page-item">
								<?php if ($halaman_aktif < $jumlah_halaman) : ?>
									<a class="page-link" href="?halaman=<?= $halaman_aktif + 1; ?>" aria-label="Next">
										<span aria-hidden="true">&raquo;</span>
									</a>
								<?php endif; ?>
							</li>
						</ul>
					</nav>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<table rules="all" border="2" cellpadding="10" cellspacing="0" class="table bg-white table-bordered table-hover table-primary shadow-box">
						<tr class="bg-primary text-white text-center">
							<th>No </th>
							<th>Judul Buku</th>
							<th>Tahun</th>
							<th>
								<div class="dropdown">
									<form action="" method="post">
									Kategori
									<button class="btn dropdown-toggle text-light" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									</button>
										<div class="dropdown-menu" name="drop" aria-labelledby="dropdownMenuButton">
											<?php $kategori = query("SELECT * FROM kategori"); ?>
											<?php foreach ($kategori as $row) : ?>
												<button class="dropdown-item" name="btn" value="<?= $row['nama_kategori'] ?>" type="submit"><?= $row['nama_kategori'] ?></button>
												<?php endforeach; ?>
										</div>
									</form>
								</div>
							</th>
							<th>FIle</th>
							<th class="aksi">Aksi</th>
							<th>Keterangan</th>
						</tr>
						<?php $i = 1; ?>
						<?php foreach ($isi as $row) :
							$file_path = "uploads/" . $row['filename']; ?>
							<tr class="text-center">
								<td><?= $i; ?></td>
								<td><?= $row["judul_buku"]; ?></td>
								<td><?= $row["tahun"]; ?></td>
								<td><?= $row["nama_kategori"]; ?></td>
								<td><?= $row["filename"]; ?></td>
								<td class="aksi">
									<?php if ($_SESSION['akses'] == 'administrator') : ?>
										<a href="ubah.php?id=<?= $row['id_arsip_dokumen']; ?>" onclick="return confirm('Apakah Anda Ingin Mengubah Data ?');" class="m-1 btn btn-warning text-center text-white">
											<img src="img/baseline_edit_white_18dp.png">
										</a>
										<a href="hapus.php?id=<?= $row['id_arsip_dokumen']; ?>" onclick="return confirm('Apakah Anda Ingin Menghapus Data ?');" class="m-1 btn btn-danger text-center">
											<img src="img/baseline_delete_white_18dp.png">
										</a>
									<?php endif ?>
									<a href="<?php echo $file_path; ?>" class="btn btn-warning text-light mb-2" target="_blank" style="border-radius: 5px;">Lihat</a>
									<a href="<?php echo $file_path; ?>" class="btn btn-danger" style="border-radius: 5px;" download>Unduh</a>
								</td>
								<td>
									<div class="data-keterangan" data-keterangan="<?= htmlspecialchars($row['keterangan'], ENT_QUOTES, 'UTF-8'); ?>"></div><button class="btn btn-success" onclick="showmsg(this);">Riwayat</button>
								</td>
							</tr>
							<?php $i++; ?>
						<?php endforeach; ?>
					</table>
				</div>
			</div>
			<div class="row pagination">
				<!-- pagination -->
				<div class="col-md-12">
					<nav aria-label="Page navigation example">
						<ul class="pagination pagination-md">
							<li class="page-item">
								<?php if ($halaman_aktif > 1) : ?>
									<a class="page-link" href="?halaman=<?= $halaman_aktif - 1; ?>" aria-label="Previous">
										<span aria-hidden="true">&laquo;</span>
									</a>
								<?php endif; ?>
							</li>
							<?php for ($i = 1; $i <= $jumlah_halaman; $i++) : ?>
								<?php if ($i == $halaman_aktif) : ?>
									<li class="page-item"><a class="rounded page-link bg-primary text-white" href="?halaman=<?= $i; ?>"><?= $i; ?></a></li>
								<?php else : ?>
									<li class="page-item"><a class="page-link" href="?halaman=<?= $i; ?>"><?= $i; ?></a></li>
								<?php endif; ?>
							<?php endfor; ?>
							<li class="page-item">
								<?php if ($halaman_aktif < $jumlah_halaman) : ?>
									<a class="page-link" href="?halaman=<?= $halaman_aktif + 1; ?>" aria-label="Next">
										<span aria-hidden="true">&raquo;</span>
									</a>
								<?php endif; ?>
							</li>
						</ul>
					</nav>
				</div>
			</div>
		</div>
	</section>
	<div class="footer">
		<p>COPYRIGHT &copy; <?= date('Y'); ?> PDAM></a></a></p>
	</div>

	<!-- Optional JavaScript -->
	<!-- jQuery first, then Popper.js, then Bootstrap JS -->
	<script src="vendor/jquery-3.3.1.slim.min.js"></script>
	<script src="vendor/popper.min.js"></script>
	<script src="vendor/bootstrap.min.js"></script>
	<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
	<script>
		var currentDataKeterangan = null;

		function showmsg(button) {
			var dataKeterangan = button.previousElementSibling.getAttribute('data-keterangan');

			if (dataKeterangan !== currentDataKeterangan) {  
				swal({
					text: dataKeterangan,
				});
				currentDataKeterangan = dataKeterangan;
			}
		}
	</script>
	<script>
		$(function() {
			$('[data-toggle="popover"]').popover()
		})
	</script>
</body>

</html>