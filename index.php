<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ATM BSONE</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function toggleAmountInput() {
            var aksi = document.getElementById('aksi').value;
            var jumlahInput = document.getElementById('jumlahInput');
            var tarikTunaiMessage = document.getElementById('tarikTunaiMessage');
            if (aksi == 'tarik_tunai' || aksi == 'topup_saldo') {
                jumlahInput.style.display = 'block';
            } else {
                jumlahInput.style.display = 'none';
            }
            if (aksi == 'tarik_tunai') {
                tarikTunaiMessage.style.display = 'block';
            } else {
                tarikTunaiMessage.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <img src="atm.png" alt="ATM Image">
    <?php
    session_start();
    echo "<h1>ATM BSONE</h1>";
    $isValid = false;
    $saldo = 0;
    if (isset($_POST['submit']) || isset($_POST['execute'])) {
        $nama = $_POST['nama'];
        $pin = $_POST['pin'];
        $dataMaster = [
            ["nama" => "bayus", "pin" => "5", "saldo" => 11000000],
            ["nama" => "danar", "pin" => "4", "saldo" => 2000000],
            ["nama" => "ari", "pin" => "3", "saldo" => 1500000],
            ["nama" => "rafi", "pin" => "4", "saldo" => 3000000],
            ["nama" => "dika", "pin" => "4", "saldo" => 2500000],
            ["nama" => "dwi", "pin" => "3", "saldo" => 3500000],
            ["nama" => "dian", "pin" => "4", "saldo" => 4000000],
            ["nama" => "dina", "pin" => "4", "saldo" => 4500000],
            ["nama" => "dini", "pin" => "4", "saldo" => 5000000],
            ["nama" => "dono", "pin" => "4", "saldo" => 5500000]
        ];
        foreach ($dataMaster as &$data) {
            if ($data['nama'] == $nama && $data['pin'] == $pin) {
                $isValid = true;
                if (isset($_SESSION['saldo'])) {
                    $saldo = $_SESSION['saldo'];
                } else {
                    $saldo = $data['saldo'];
                    $_SESSION['saldo'] = $saldo;
                }
                break;
            }
        }
        if ($isValid && isset($_POST['submit'])) {
            echo "<h2>Hi $nama</h2>";
        } elseif (!$isValid) {
            echo "Nama atau PIN yang anda masukkan salah";
        }
    }

    if (!$isValid) {
    ?>
        <form action="index.php" method="post">
            <label for="nama">Nama</label>
            <input type="text" name="nama" id="nama" required>
            
            <label for="pin">PIN</label>
            <input type="password" name="pin" id="pin" required>
            <br><br>
            <center><button type="submit" name="submit">Submit</button></center>
        </form>
    <?php
    } else {
    ?>
        <form action="index.php" method="post">
            <input type="hidden" name="nama" value="<?php echo $nama; ?>">
            <input type="hidden" name="pin" value="<?php echo $pin; ?>">
            <label for="aksi">Menu </label>
            <select name="aksi" id="aksi" onchange="toggleAmountInput()">
                <option value="cek_saldo">Menu</option>
                <option value="cek_saldo">Cek Saldo</option>
                <option value="tarik_tunai">Tarik Tunai</option>
                <option value="topup_saldo">Topup Saldo</option>
            </select>
            <br>
            <div id="jumlahInput" style="display: none;">
                <label for="jumlah">Kelipatan</label>
                <select name="jumlah" id="jumlah">
                    <option value="50000">50,000</option>
                    <option value="100000">100,000</option>
                    <option value="150000">150,000</option>
                    <option value="200000">200,000</option>
                    <option value="250000">250,000</option>
                    <option value="300000">300,000</option>
                    <option value="350000">350,000</option>
                    <option value="400000">400,000</option>
                    <option value="450000">450,000</option>
                    <option value="500000">500,000</option>
                </select>
                <br>
                <label for="customJumlah">Jumlah Custom</label>
                <input type="number" name="customJumlah" id="customJumlah" min="0" step="50000">
                <br>
            </div>
            <button type="submit" name="execute">Kirim</button>
        </form>
        <div id="tarikTunaiMessage" style="display: none;">
            <strong>Silakan tarik tunai</strong>
        </div>
    <?php
        if (isset($_POST['execute'])) {
            $aksi = $_POST['aksi'];
            $jumlah = $_POST['jumlah'];
            $customJumlah = $_POST['customJumlah'];
            if (!empty($customJumlah)) {
                $jumlah = $customJumlah;
            }
            if ($aksi == 'cek_saldo') {
                echo "<h2>Saldo Anda: Rp. $saldo</h2>";
            } elseif ($aksi == 'tarik_tunai') {
                if (empty($jumlah)) {
                    echo "<h2>Jumlah saldo yang ditarik harus diisi</h2>";
                } elseif ($jumlah <= $saldo) {
                    $saldo -= $jumlah;
                    $_SESSION['saldo'] = $saldo;
                    $numBills = $jumlah / 50000;
                    echo "<strong>Anda telah menarik: Rp. $jumlah</strong> <br>";
                    echo "<strong>Sisa saldo Anda: Rp. $saldo</strong> <br>";
                    echo "<strong>Jumlah lembar yang diterima: $numBills lembar 50,000</strong>";
                } else {
                    echo "<h2>Saldo tidak mencukupi</h2>";
                }
            } elseif ($aksi == 'topup_saldo') {
                if (empty($jumlah)) {
                    echo "<h2>Jumlah saldo yang di-topup harus diisi</h2>";
                } else {
                    $saldo += $jumlah;
                    $_SESSION['saldo'] = $saldo;
                    echo "<strong>Anda telah menambah saldo: Rp. $jumlah</strong> <br>";
                    echo "<strong>Saldo Anda sekarang: Rp. $saldo</strong>";
                }
            }
        }

        if (isset($_POST['logout'])) {
            session_destroy();
            header("Location: index.php");
            exit();
        }
    ?>
        <form action="index.php" method="post">
            <br>
            <button type="submit" name="logout" class="logout-button">Logout</button>
        </form>
    <?php
    }
    ?>
</body>
</html>
