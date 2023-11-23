<?php
session_start();
// koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "arsip");


function uruttahun($selectedYear)
{
	global $koneksi;

	// Query data berdasarkan tahun yang dipilih
	$query = "SELECT * FROM arsip_dokumen JOIN kategori ON arsip_dokumen.id_kategori = kategori.id_kategori WHERE arsip_dokumen.tahun = '$selectedYear'";
	return query($query);
}


function query($query)
{
	global $koneksi;
	$result = mysqli_query($koneksi, $query);
	$rows = [];
	while ($row = mysqli_fetch_assoc($result)) {
		$rows[] = $row;
	}
	return $rows;
}

function tambah($data)
{
	global $koneksi;
	$judul_buku = htmlspecialchars($data['judul_buku']);
	$kategori = htmlspecialchars($data['kategori']);
	$tahun = htmlspecialchars($data['tahun']);
	$keterangan = htmlspecialchars($data['keterangan']);
	if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
		$target_dir = "uploads/"; // Change this to the desired directory for uploaded files
		$target_file = $target_dir . basename($_FILES["file"]["name"]);
		$file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

		// Check if the file is allowed (you can modify this to allow specific file types)
		$allowed_types = array("pdf");
		if (!in_array($file_type, $allowed_types)) {
			echo "PDF files are allowed.";
		} else {
			// Move the uploaded file to the specified directory
			if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
				// File upload success, now store information in the database
				$filename = $_FILES["file"]["name"];
				$filesize = $_FILES["file"]["size"];
				$filetype = $_FILES["file"]["type"];
				// query insert data
				$query = "INSERT INTO arsip_dokumen (judul_buku, id_kategori, tahun, keterangan, filename, filesize, filetype) VALUES ('$judul_buku','$kategori', '$tahun', '$keterangan', '$filename', $filesize, '$filetype');";

				if (mysqli_query($koneksi, $query)) {
					echo "The file " . basename($_FILES["file"]["name"]) . " has been uploaded and the information has been stored in the database.";
				} else {
					echo "Sorry, there was an error uploading your file and storing information in the database: " . $koneksi->error;
				}
			} else {
				echo "Sorry, there was an error uploading your file.";
			}
		}
	} else {
		echo "No file was uploaded.";
	}
	return mysqli_affected_rows($koneksi);
}

function tambahAkun($data)
{
	global $koneksi;
	$username = strtolower(stripcslashes($data['username']));
	$password = mysqli_real_escape_string($koneksi, $data['password']);
	$password2 = mysqli_real_escape_string($koneksi, $data['password2']);
	$nama = htmlspecialchars($data['nama']);
	$nip = htmlspecialchars($data['nip']);
	$akses = htmlspecialchars($data['akses']);

	// cek username sudah ada atau belum
	$result = mysqli_query($koneksi, "SELECT username FROM user WHERE username = '$username'");

	if (mysqli_fetch_assoc($result)) {
		echo "<script>
				alert('username sudah terdaftar !');
			</script>";
		return false;
	}

	// cek konfirmasi password
	if ($password !== $password2) {
		echo "<script>
				alert('konfirmasi password tidak sesuai !');
			</script>";
		return false;
	}

	// enkripsi password
	$password = password_hash($password, PASSWORD_DEFAULT);


	// query insert data
	$query = "INSERT INTO user (username, password, nama, nip, akses) 
		VALUES ('$username', '$password', '$nama', '$nip', '$akses');
			";
	mysqli_query($koneksi, $query);

	return mysqli_affected_rows($koneksi);
}

function tambahKategori($data)
{
	global $koneksi;
	$kategori = htmlspecialchars($data['nama_kategori']);

	// cek data sudah ada atau belum
	$result = mysqli_query($koneksi, "SELECT nama_kategori FROM kategori WHERE nama_kategori = '$kategori'");

	if (mysqli_fetch_assoc($result)) {
		echo "<script>
				alert('kategori sudah ada !');
			</script>";
		return false;
	}

	// query insert data
	$query = "INSERT INTO kategori (nama_kategori) VALUES ('$kategori');
			";
	mysqli_query($koneksi, $query);

	return mysqli_affected_rows($koneksi);
}
function ubahKategori($data)
{
	global $koneksi;
	$id = $data['id_kategori'];
	$kategori = htmlspecialchars($data['nama_kategori']);

	// query insert data
	$query = "UPDATE kategori SET nama_kategori = '$kategori' WHERE id_kategori = '$id' ";
	mysqli_query($koneksi, $query);

	return mysqli_affected_rows($koneksi);
}
function hapusKategori($id)
{
	global $koneksi;
	mysqli_query($koneksi, "DELETE FROM kategori WHERE id_kategori = $id");

	return mysqli_affected_rows($koneksi);
}
function hapus($id)
{
	global $koneksi;
	mysqli_query($koneksi, "DELETE FROM arsip_dokumen WHERE id_arsip_dokumen = $id");

	return mysqli_affected_rows($koneksi);
}

function hapusAkun($id)
{
	global $koneksi;
	mysqli_query($koneksi, "DELETE FROM user WHERE id_user = $id");

	return mysqli_affected_rows($koneksi);
}

function ubah($data)
{
	global $koneksi;
	$id = $data['id_arsip_dokumen'];
	$judul_buku = htmlspecialchars($data['judul_buku']);
	$kategori = htmlspecialchars($data['id_kategori']);
	$tahun = htmlspecialchars($data['tahun']);
	$keterangan = htmlspecialchars($data['keterangan']);

	// query insert data
	$query = "UPDATE arsip_dokumen SET judul_buku = '$judul_buku', id_kategori = '$kategori',  tahun = '$tahun',keterangan = '$keterangan' WHERE id_arsip_dokumen = '$id' ";
	mysqli_query($koneksi, $query);

	return mysqli_affected_rows($koneksi);
}

function ubahAkun($data)
{
	global $koneksi;
	$id = $data['id'];
	$username = strtolower(stripcslashes($data['username']));
	$password = mysqli_real_escape_string($koneksi, $data['password']);
	$password2 = mysqli_real_escape_string($koneksi, $data['password2']);
	$nama = htmlspecialchars($data['nama']);
	$nip = htmlspecialchars($data['nip']);
	$akses = htmlspecialchars($data['akses']);

	if ($password !== $password2) {
		echo "<script>
			alert('konfirmasi tidak sesuai');
		</script>";
		return false;
	}
	$password = password_hash($password, PASSWORD_DEFAULT);
	// query insert data
	$query = "UPDATE user
				SET
				username = '$username',
				password = '$password',
				nama = '$nama',
				nip = '$nip', 
				akses = '$akses'
				WHERE id_user = '$id' 
			";
	mysqli_query($koneksi, $query);

	return mysqli_affected_rows($koneksi);
}

function cariJudulBuku($keyword)
{
	$query = "SELECT * FROM arsip_dokumen JOIN kategori ON arsip_dokumen.id_kategori = kategori.id_kategori WHERE judul_buku LIKE '%$keyword%'";
	return query($query);
}
function cari_kategori($keyword)
{
	$query = "SELECT * FROM arsip_dokumen  JOIN kategori ON arsip_dokumen.id_kategori = kategori.id_kategori WHERE nama_kategori LIKE '%$keyword%'";
	return query($query);
}
function caritahun($keyword)
{
	$query = "SELECT * FROM arsip_dokumen  JOIN kategori ON arsip_dokumen.id_kategori = kategori.id_kategori WHERE tahun LIKE '%$keyword%'";
	return query($query);
}
function carinamafile($keyword)
{
	$query = "SELECT * FROM arsip_dokumen  JOIN kategori ON arsip_dokumen.id_kategori = kategori.id_kategori WHERE filename LIKE '%$keyword%'";
	return query($query);
}
function urutsku($data)
{
	$query = "SELECT * FROM arsip_dokumen JOIN kategori ON arsip_dokumen.id_kategori = kategori.id_kategori WHERE nama_kategori Like '%$data%'";
	return query($query);
}




function cariAkun($keyword)
{
	$query = "SELECT * FROM user WHERE 
		username LIKE '%$keyword%' OR
		password LIKE '%$keyword%' OR
		nama LIKE '%$keyword%' OR
		nip LIKE '%$keyword%' OR
		akses LIKE '%$keyword%'
		";
	return query($query);
}

function salam()
{
	date_default_timezone_set("Asia/Jakarta");

	$b = time();
	$hour = date("G", $b);

	if ($hour >= 0 && $hour <= 11) {
		echo "Selamat Pagi";
	} elseif ($hour >= 11 && $hour <= 15) {
		echo "Selamat Siang ";
	} elseif ($hour >= 15 && $hour <= 18) {
		echo "Selamat Sore ";
	} elseif ($hour >= 18 && $hour < 24) {
		echo "Selamat Malam ";
	}
}
