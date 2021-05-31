<?php
    // This file contains all the functions needed
    require_once 'connection.php';

    // Use of vendor libraries
    require_once __DIR__ . '/vendor/autoload.php';

    // Check get requests and routing
    if (isset($_GET['type'])) {
        switch ($_GET['type']) {
            case 'activation' :
                activateUser($conn, $_GET['user_id']);
                break;
            case 'export' :
                exportPdf($conn, $_GET['export'], $_GET['user_id']);
                break;
        }
    }

    // Check post request and routing
    if (isset($_POST['type'])) {
        switch ($_POST['type']) {
            case 'signUp' :
                createUser($conn, $_POST);
                break;
            case 'login' :
                loginUser($conn, $_POST);
                break;
            case 'registrasiSiswa' :
                registrasiSiswa($conn, $_POST);
                break;
            case 'pendaftaranSiswa' :
                pendaftaranSiswa($conn, $_POST);
                break;
            case 'updateStatusPendaftaran' :
                updateStatusPendaftaran($conn, $_POST);
                break;
        }
    }

    function createUser($conn, $data)
    {
        // Define variables
        $nama = htmlspecialchars($data['nama']);
        $nik = htmlspecialchars($data['nik']);
        $no_telepon = htmlspecialchars($data['no_telepon']);
        $email = htmlspecialchars($data['email']);
        $password = htmlspecialchars($data['password']);

        // Using prepared statement
        $query = "INSERT INTO users_tb (nama, nik, no_telepon, email, password) VALUES (?, ?, ?, ?, ?);";
        $stmt = mysqli_stmt_init($conn);

        // Error handling
        if (!mysqli_stmt_prepare($stmt, $query)) {
            echo "<script type='text/javascript'> alert('Terjadi kesalahan pada server!'); document.location.href = 'signup.php'; </script>";
            exit();
        }

        // Password hashing
        $hashedPwd = password_hash($password, PASSWORD_DEFAULT);

        // Bind params to prepared statement
        mysqli_stmt_bind_param($stmt, 'sssss', $nama, $nik, $no_telepon, $email, $hashedPwd);
        // Exec
        mysqli_stmt_execute($stmt);
        // Close prepared statement
        mysqli_stmt_close($stmt);

        // Redirect without errors
        echo "<script type='text/javascript'> alert('Registrasi berhasil!'); document.location.href = 'index.php'; </script>";
        exit();
    }

    function loginUser($conn, $data) {
        // Define variables
        $email = htmlspecialchars($data['email']);
        $password = htmlspecialchars($data['password']);

        // Using prepared statement
        $query = "SELECT * FROM users_tb WHERE email = ?;";
        $stmt = mysqli_stmt_init($conn);

        // Error handling
        if (!mysqli_stmt_prepare($stmt, $query)) {
            echo "<script type='text/javascript'> alert('Terjadi kesalahan pada server!'); document.location.href = 'index.php'; </script>";
            exit();
        }

        // Bind params to prepared statement
        mysqli_stmt_bind_param($stmt, 's', $email);
        // Exec
        mysqli_stmt_execute($stmt);

        // Check if username already exists
        $resultData = mysqli_stmt_get_result($stmt);

        // Handling data
        if ($rowData = mysqli_fetch_assoc($resultData)) {
            // Check hashed password
            $pwdHashed = $rowData['password'];
            $pwdCheck = password_verify($password, $pwdHashed);

            // Check credentials
            if (!$pwdCheck) {
                echo "<script type='text/javascript'> alert('Email atau Password salah!'); document.location.href = 'index.php'; </script>";
                exit();
            } else if ($pwdCheck) {
                // Check status user
                if ($rowData['status_user'] == 0) {
                    echo "<script type='text/javascript'> alert('Akun anda masih nonaktif! Harap tunggu hingga admin mengaktifkan akun anda!'); document.location.href = 'index.php'; </script>";
                    exit();
                }
                // Credentials matched, start session
                session_start();
                $_SESSION['user_id'] = $rowData['user_id'];
                $_SESSION['email'] = $rowData['email'];
                $_SESSION['role'] = $rowData['role'];
                if ($rowData['role'] == 1) {
                    echo "<script type='text/javascript'> alert('Selamat datang admin!'); document.location.href = 'admin/index.php'; </script>";
                } else {
                    echo "<script type='text/javascript'> alert('Selamat datang calon siswa!'); document.location.href = 'user/index.php'; </script>";
                }
            }
        } else {
            echo "<script type='text/javascript'> alert('User tidak ditemukan! Harap coba lagi!'); document.location.href = 'index.php'; </script>";
        }
        // Close prepared statement
        mysqli_stmt_close($stmt);
    }

    function activateUser($conn, $user_id)
    {
        // Query
        $query = mysqli_query($conn, "UPDATE users_tb SET status_user = 1 WHERE user_id = '$user_id'");

        if ($query) {
            echo "<script type='text/javascript'> alert('Status user berhasil diubah!'); document.location.href = 'admin/informasi_user.php'; </script>";
        } else {
            echo "<script type='text/javascript'> alert('Gagal mengubah status pendaftaran!'); document.location.href = 'admin/informasi_user.php'; </script>";
        }
    }

    function registrasiSiswa($conn, $data)
    {
        // Define variable
        $user_id = $data['user_id'];
        $jenis_kelamin = $data['jenis_kelamin'];
        $agama = $data['agama'];
        $tempat_lahir = $data['tempat_lahir'];
        $tgl_lahir = $data['tgl_lahir'];
        $alamat = $data['alamat'];
        $nama_ortu = $data['nama_ortu'];
        $no_telepon_ortu = $data['no_telepon_ortu'];

        // Query update data user
        $query = "UPDATE users_tb SET jenis_kelamin = '$jenis_kelamin', agama = '$agama', tempat_lahir = '$tempat_lahir', tgl_lahir = '$tgl_lahir', alamat = '$alamat', nama_ortu  = '$nama_ortu', no_telepon_ortu = '$no_telepon_ortu' WHERE user_id = '$user_id'";

        // Exec
        $process = mysqli_query($conn, $query);

        // Error check
        if ($process) {
            echo "<script type='text/javascript'> alert('Registrasi data berhasil!'); document.location.href = 'user/index.php'; </script>";
        } else {
            echo "<script type='text/javascript'> alert('Proses gagal! Silahkan coba lagi!'); document.location.href = 'user/registrasi.php'; </script>";
        }
    }

    function pendaftaranSiswa($conn, $data)
    {
        // Define variable
        $user_id = $data['user_id'];

        // Pengecekan tipe file PDF
        $surat_pernyataan_tipe = $_FILES['surat_pernyataan']['type'];
        $ijazah_tipe = $_FILES['ijazah']['type'];

        if ($surat_pernyataan_tipe && $ijazah_tipe == 'application/pdf') {
            $no_pendaftaran = 'PSB' . date('dmy-s');
            $now = date('Y-m-d H:i:s');
            $init_query = "INSERT INTO data_pendaftaran (user_id, tgl_pendaftaran, no_pendaftaran) VALUES ('$user_id', '$now', '$no_pendaftaran')";
            // Exec init query
            mysqli_query($conn, $init_query);

            // Get id terakhir
            $query = mysqli_query($conn, "SELECT pendaftaran_id FROM data_pendaftaran ORDER BY pendaftaran_id DESC LIMIT 1");
            $data = mysqli_fetch_array($query);

            // Rename file PDF
            $pas_foto_baru = 'pas_foto_id-' . $data['pendaftaran_id'] . '.jpg';
            $pas_foto_tmp = $_FILES['pas_foto']['tmp_name'];

            $surat_pernyataan_baru = 'surat_pernyataan_id-' . $data['pendaftaran_id'] . '.pdf';
            $surat_pernyataan_tmp = $_FILES['surat_pernyataan']['tmp_name'];
            
            $ijazah_baru = 'ijazah_id-' . $data['pendaftaran_id'] . '.pdf'; 
            $ijazah_tmp = $_FILES['ijazah']['tmp_name'];

            $folder = '/berkas';

            // Memindahkan file yang diupload
            define ('SITE_ROOT', realpath(dirname(__FILE__)));
            move_uploaded_file($pas_foto_tmp, SITE_ROOT . "$folder/$pas_foto_baru");
            move_uploaded_file($surat_pernyataan_tmp, SITE_ROOT . "$folder/$surat_pernyataan_baru");
            move_uploaded_file($ijazah_tmp, SITE_ROOT . "$folder/$ijazah_baru");

            // Mengubah path file pada database
            $update = mysqli_query($conn, "UPDATE data_pendaftaran SET pas_foto = '$pas_foto_baru', ijazah = '$ijazah_baru', surat_pernyataan = '$surat_pernyataan_baru' WHERE pendaftaran_id = '$data[pendaftaran_id]'");

            // Error check
            if ($update) {
                echo "<script type='text/javascript'> alert('Upload berkas berhasil! Terima kasih!'); document.location.href = 'user/index.php'; </script>";
            }
        } else {
            echo "<script type='text/javascript'> alert('Gagal upload file! Pastikan file berformat PDF!'); document.location.href = 'user/index.php'; </script>";
        }
    }

    function updateStatusPendaftaran($conn, $data)
    {
        // Define variables
        $pendaftaran_id = $data['pendaftaran_id'];
        $status_pendaftaran = $data['status_pendaftaran'];

        // Query
        $query = mysqli_query($conn, "UPDATE data_pendaftaran SET status_pendaftaran = '$status_pendaftaran' WHERE pendaftaran_id = '$pendaftaran_id'");

        if ($query) {
            echo "<script type='text/javascript'> alert('Status pendaftaran berhasil diubah!'); document.location.href = 'admin/index.php'; </script>";
        } else {
            echo "<script type='text/javascript'> alert('Gagal mengubah status pendaftaran!'); document.location.href = 'admin/index.php'; </script>";
        }
    }

    function exportPdf($conn, $type, $user_id = null)
    {
        $html = '';
        switch ($type) {
            case 'inf-pendaftaran' :
                // Query
                $query = mysqli_query($conn, "SELECT * FROM data_pendaftaran INNER JOIN users_tb ON data_pendaftaran.user_id = users_tb.user_id");
                $html = '
                    <html>
                    <head>
                    <style>
                    body {font-family: sans-serif;
                        font-size: 10pt;
                    }
                    p {	margin: 0pt; }
                    table.items {
                        border: 0.1mm solid #000000;
                    }
                    td { vertical-align: top; }
                    .items td {
                        border-left: 0.1mm solid #000000;
                        border-right: 0.1mm solid #000000;
                    }
                    table thead td { background-color: #EEEEEE;
                        text-align: center;
                        border: 0.1mm solid #000000;
                        font-variant: small-caps;
                    }
                    .items td.blanktotal {
                        background-color: #EEEEEE;
                        border: 0.1mm solid #000000;
                        background-color: #FFFFFF;
                        border: 0mm none #000000;
                        border-top: 0.1mm solid #000000;
                        border-right: 0.1mm solid #000000;
                    }
                    .items td.totals {
                        text-align: right;
                        border: 0.1mm solid #000000;
                    }
                    .items td.cost {
                        text-align: "." center;
                    }
                    </style>
                    </head>
                    <body>
                    <!--mpdf
                    <htmlpagefooter name="myfooter">
                    <div style="border-top: 1px solid #000000; font-size: 9pt; text-align: center; padding-top: 3mm; ">
                    Page {PAGENO} of {nb}
                    </div>
                    </htmlpagefooter>
                    <sethtmlpagefooter name="myfooter" value="on" />
                    mpdf-->
                    <div style="text-align: right">Date: ' . date('d F Y') . '</div>
                    <div style="text-align: center; font-style: italic;"><b>Informasi Pendaftaran PSB</b></div>
                    <hr />
                    <br />
                    <table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; " cellpadding="8">
                    <thead>
                    <tr>
                        <td width="15%" rowspan="2">ID Pendaftaran</td>
                        <td width="15%" rowspan="2">Nama Lengkap</td>
                        <td width="45%" colspan="3">Berkas</td>
                        <td width="15%" rowspan="2">Status Pendaftaran</td>
                    </tr>
                    <tr>
                        <td>Pas Foto</td>
                        <td>Ijazah</td>
                        <td>Surat Pernyataan</td>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- ITEMS HERE -->';
                    foreach ($query as $data) {
                        $html .= "<tr>
                                <td align='center'> $data[pendaftaran_id] </td>
                                <td> $data[nama] </td>
                                <td align='center'>". (!empty($data['pas_foto']) ? "OK" : '-') ."</td>
                                <td align='center'>". (!empty($data['ijazah']) ? "OK" : "-") ."</td>
                                <td align='center'>". (!empty($data['surat_pernyataan']) ? "OK" : "-") ."</td>
                                <td> $data[status_pendaftaran] </td>
                            </tr>";
                    } 
                    $html .= 
                    '<!-- END ITEMS HERE -->
                    </tbody>
                    </table>
                    </body>
                    </html>
                ';
                break;
            case 'status-pendaftaran-siswa' :
                define ('SITE_ROOT', realpath(dirname(__FILE__)));
                // Query
                $query = mysqli_query($conn, "SELECT * FROM users_tb LEFT JOIN data_pendaftaran ON users_tb.user_id = data_pendaftaran.user_id WHERE users_tb.user_id = '$user_id'");
                $data = mysqli_fetch_assoc($query);
                $html = '
                    <html>
                    <head>
                    <style>
                    body {font-family: sans-serif;
                        font-size: 10pt;
                    }
                    p {	margin: 0pt; }
                    td { vertical-align: top; }
                    tr:nth-child(even) {
                        background-color: #f2f2f2;
                    }
                    </style>
                    </head>
                    <body>
                    <!--mpdf
                    <htmlpagefooter name="myfooter">
                    <div style="border-top: 1px solid #000000; font-size: 9pt; text-align: center; padding-top: 3mm; ">
                    Page {PAGENO} of {nb}
                    </div>
                    </htmlpagefooter>
                    <sethtmlpagefooter name="myfooter" value="on" />
                    mpdf-->
                    <div style="text-align: right">Date: ' . date('d F Y') . '</div>';
                    if (!empty($data['pas_foto'])) {
                        $html .= '<div style="text-align: right"><img src="' .  SITE_ROOT . '/berkas/' . $data['pas_foto'] . '" width="120" height="120"/></div>';
                    }
                    $html .= 
                    '<div style="font-style: italic; font-size: 12pt;"><b>Bukti Pendaftaran ' . $data['nama'] . '</b></div>
                    <hr />
                    <br />
                    <table class="items" width="100%" style="font-size: 10pt; border-collapse: collapse; " cellpadding="8">
                    <tr>
                        <th width="20%">No. Pendaftaran</th>
                        <td>' . (!empty($data['no_pendaftaran']) ? $data['no_pendaftaran'] : '-') . '</td>
                    </tr>
                    <tr>
                        <th>Tanggal Pendaftaran</th>
                        <td>' . (!empty($data['tgl_pendaftaran']) ? date('d M Y', strtotime($data['tgl_pendaftaran'])) : '-') . '</td>
                    </tr>
                    <tr>
                        <th width="20%">No. Identitas (NIK)</th>
                        <td>' . (!empty($data['nik']) ? $data['nik'] : '-') . '</td>
                    </tr>
                    <tr>
                        <th width="20%">Nama</th>
                        <td>' . (!empty($data['nama']) ? $data['nama'] : '-') . '</td>
                    </tr>
                    <tr>
                        <th width="20%">TTL</th>
                        <td>' . (!empty($data['tempat_lahir']) && !empty($data['tgl_lahir']) ? $data['tempat_lahir'] . ', ' . date('d M Y', strtotime($data['tgl_lahir'])) : '-') . '</td>
                    </tr>
                    <tr>
                        <th width="20%">No. Handphone</th>
                        <td>' . (!empty($data['no_telepon']) ? $data['no_telepon'] : '-') . '</td>
                    </tr>
                    <tr>
                        <th width="20%">Nama Kontak Orang Tua/Wali</th>
                        <td>' . (!empty($data['nama_ortu']) ? $data['nama_ortu'] : '-') . '</td>
                    </tr>
                    <tr>
                        <th width="20%">Nomor Kontak Orang Tua/Wali</th>
                        <td>' . (!empty($data['no_telepon_ortu']) ? $data['no_telepon_ortu'] : '-') . '</td>
                    </tr>
                    <tr>
                        <th width="20%">Status Pendaftaran</th>
                        <td>' . (!empty($data['status_pendaftaran']) ? $data['status_pendaftaran'] : 'BELUM DAFTAR') . '</td>
                    </tr>
                    </table>
                    </body>
                    </html>
                ';
                break;
        }
        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->debug = true;
        $mpdf->Output();
    }
