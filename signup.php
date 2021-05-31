<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.83.1">
    <title>Sign Up | PSB Reapz</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/sign-in/">

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
            font-size: 3.5rem;
            }
        }
    </style>

    <!-- Custom styles for this template -->
    <link href="css/auth.css" rel="stylesheet">
</head>
<body class="text-center">
    <main class="form-signup">
        <form action="functions.php" method="POST" onsubmit="return formCheck();">
            <input type="hidden" name="type" value="signUp">
            <h1 class="h3 mb-3 fw-normal">Sign Up</h1>
            <hr>
            <h3 class="h5 mb-3 fw-normal">Harap isi form di bawah ini!</h3>

            <div class="form-floating mb-2">
                <input type="text" name="nama" class="form-control" id="nama" placeholder="Nama Lengkap" required>
                <label for="nama">Nama Lengkap</label>
            </div>
            <div class="form-floating mb-2">
                <input type="number" name="nik" class="form-control" id="nik" placeholder="No. Indentitas (NIK)" required>
                <label for="nik">No. Indentitas (NIK)</label>
            </div>
            <div class="form-floating mb-2">
                <input type="number" name="no_telepon" class="form-control" id="no_telepon" placeholder="No. Telepon" required>
                <label for="no_telepon">No. Telepon</label>
            </div>
            <div class="form-floating mb-2">
                <input type="email" name="email" class="form-control" id="email" placeholder="name@example.com" required>
                <label for="email">Email</label>
            </div>
            <div class="form-floating mb-2">
                <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
                <label for="password">Password</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" name="password_confirmation" class="form-control" id="password_conf" placeholder="Password" required>
                <label for="password_conf">Ulangi Password</label>
            </div>

            <div class="mb-3">
                <label>
                    Sudah memiliki akun? <a href="index.php" class="text-muted">Login disini.</a>
                </label>
            </div>
            <button class="w-100 btn btn-lg btn-primary" type="submit">Daftar</button>
            <p class="mt-5 mb-3 text-muted">&copy; Bootstrap | 2021</p>
        </form>
    </main>

    <!-- Form check -->
    <script>
        function formCheck() {
            var nama = document.getElementById('nama');
            var nik = document.getElementById('nik');
            var pw = document.getElementById('password');
            var conf_pw = document.getElementById('password_conf');

            if (nama.value.length < 4) {
                alert('Nama minimal 4 karakter!');
                nama.focus();
                return false;
            } else if (nik.value.length < 3) {
                alert('NIK minimal 3 karakter!');
                nik.focus();
                return false;
            } else if (pw.value != conf_pw.value) {
                alert('Password tidak cocok!');
                conf_pw.focus();
                return false;
            } else if (pw.value.length < 6) {            
                alert('Password minimal 6 karakter!');
                pw.focus();
                return false;
            } else {
                return true;
            }
        }
    </script>
</body>
</html>
