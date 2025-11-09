<?php
session_start();
include("config.php");

// ✅ Cek login
if (!isset($_SESSION['user_email'])) {
    header("Location: ../html/login.html");
    exit();
}

$email = $_SESSION['user_email'];

// ✅ Ambil data user dari database menggunakan prepared statement
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $data = $result->fetch_assoc();
    $user_id = $data['id'];
    
    // Pastikan semua field yang mungkin null punya default value
    $data['overview'] = $data['overview'] ?? '';
    $data['team_name'] = $data['team_name'] ?? '';
    $data['sport'] = $data['sport'] ?? '';
    $data['team_desc'] = $data['team_desc'] ?? '';
    $data['active_tournaments'] = $data['active_tournaments'] ?? 0;
} else {
    // Jika user tidak ditemukan, redirect ke login
    session_destroy();
    header("Location: ../html/login.html");
    exit();
}

// ✅ Ambil foto dari tabel user_photos (jika ada)
$foto = '../assets/profil.png'; // default
if ($user_id !== null) {
    $stmt_foto = $conn->prepare("SELECT photo_path FROM user_photos WHERE user_id = ? LIMIT 1");
    $stmt_foto->bind_param("i", $user_id);
    $stmt_foto->execute();
    $result_foto = $stmt_foto->get_result();
    
    if ($result_foto && $result_foto->num_rows > 0) {
        $foto_row = $result_foto->fetch_assoc();
        // Pastikan path benar: jika sudah ada ../ di DB, jangan tambah lagi
        if (strpos($foto_row['photo_path'], '../') === 0) {
            $foto = $foto_row['photo_path'];
        } else {
            $foto = "../" . $foto_row['photo_path'];
        }
    }
}


// ==========================================================
// 🔧 LOGIC UPDATE PROFIL & SUMMARY (Gabung di sini)
// ==========================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update Profil
    if (isset($_POST['update_profile'])) {
        $nama = $_POST['nama'];
        $overview = $_POST['overview'];
        $foto_lama = $_POST['foto_lama'];
        $target_file = $foto_lama; // default pakai foto lama

        // ✅ Upload foto jika ada
        if (!empty($_FILES['foto']['name'])) {
            $foto_name = time() . '_' . basename($_FILES['foto']['name']);
            $target_dir = "../assets/foto_profil/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $target_file = $target_dir . $foto_name;
            $db_path = "assets/foto_profil/" . $foto_name; // path disimpan ke DB (tanpa ../)

            // Validasi tipe file
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $file_type = $_FILES['foto']['type'];

            if (in_array($file_type, $allowed_types)) {
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
                    // Cek apakah user sudah punya foto
                    $check_photo = $conn->prepare("SELECT id FROM user_photos WHERE user_id = ?");
                    $check_photo->bind_param("i", $user_id);
                    $check_photo->execute();
                    $result_photo = $check_photo->get_result();

                    if ($result_photo->num_rows > 0) {
                        // Update foto lama
                        $update_photo = $conn->prepare("UPDATE user_photos SET photo_path = ?, uploaded_at = NOW() WHERE user_id = ?");
                        $update_photo->bind_param("si", $db_path, $user_id);
                        $update_photo->execute();
                    } else {
                        // Simpan foto baru
                        $insert_photo = $conn->prepare("INSERT INTO user_photos (user_id, photo_path, uploaded_at) VALUES (?, ?, NOW())");
                        $insert_photo->bind_param("is", $user_id, $db_path);
                        $insert_photo->execute();
                    }
                    
                    // Update variabel $foto untuk langsung tampil
                    $foto = $target_file;
                } else {
                    echo "<script>alert('Gagal mengupload foto!');</script>";
                }
            } else {
                echo "<script>alert('Format file tidak didukung! Gunakan JPG, PNG, atau GIF.');</script>";
            }
        }

        // Update nama & overview di tabel users menggunakan prepared statement
        $stmt_update = $conn->prepare("UPDATE users SET nama = ?, overview = ? WHERE email = ?");
        $stmt_update->bind_param("sss", $nama, $overview, $email);
        
        if ($stmt_update->execute()) {
            // ✅ Ambil ulang foto terbaru supaya langsung tampil tanpa reload manual
            $stmt_foto = $conn->prepare("SELECT photo_path FROM user_photos WHERE user_id = ? LIMIT 1");
            $stmt_foto->bind_param("i", $user_id);
            $stmt_foto->execute();
            $result_foto = $stmt_foto->get_result();
            
            if ($result_foto && $result_foto->num_rows > 0) {
                $foto_row = $result_foto->fetch_assoc();
                // Pastikan path benar
                if (strpos($foto_row['photo_path'], '../') === 0) {
                    $foto = $foto_row['photo_path'];
                } else {
                    $foto = "../" . $foto_row['photo_path'];
                }
            }

            // Refresh data user
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();

            echo "<script>alert('Profil berhasil diperbarui!'); window.location='editprofil.php';</script>";
            exit;
        } else {
            echo "<script>alert('Gagal memperbarui profil: " . $conn->error . "');</script>";
        }
    }

    // Update Summary
    if (isset($_POST['update_summary'])) {
        $team_name = $_POST['team_name'];
        $sport = $_POST['sport'];
        $active_tournaments = (int)$_POST['active_tournaments'];
        $team_desc = $_POST['team_desc'];

        $stmt_summary = $conn->prepare("UPDATE users SET team_name = ?, sport = ?, active_tournaments = ?, team_desc = ? WHERE email = ?");
        $stmt_summary->bind_param("ssiss", $team_name, $sport, $active_tournaments, $team_desc, $email);
        
        if ($stmt_summary->execute()) {
            // Refresh data user
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            
            echo "<script>alert('Summary berhasil diperbarui!'); window.location='editprofil.php';</script>";
            exit;
        } else {
            echo "<script>alert('Gagal memperbarui summary: " . $conn->error . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil | CaborNation</title>
  <link rel="stylesheet" href="../css/editprofil.css">
</head>
<body>
  <!-- 🧭 SIDEBAR -->
  <aside class="sidebar">
    <div class="logo">CaborNation</div>
    <ul class="menu">
      <li><a href="dashboard.php">Beranda</a></li>
      <li><a href="#">Tim Saya</a></li>
      <li><a href="#">Turnamen</a></li>
      <li class="active"><a href="editprofil.php">Profil</a></li>
      <li><a href="logout.php">Keluar</a></li>
    </ul>
  </aside>

  <!-- 💻 MAIN CONTENT -->
  <main class="container">
    <!-- 🔹 Card Profil Atas -->
    <div class="profile-card">
      <div class="profile-header">
        <form id="fotoForm" action="editprofil.php" method="POST" enctype="multipart/form-data" style="display:inline;">
          <div class="profile-pic">
            <label for="foto">
              <img id="preview" src="<?php echo htmlspecialchars($foto); ?>" alt="Foto Profil" title="Klik untuk ubah foto">
              <div class="overlay">Ubah Foto</div>
            </label>
            <input type="file" name="foto" id="foto" accept="image/*" onchange="autoSubmitFoto(event)" style="display:none;">
            <input type="hidden" name="update_profile" value="1">
            <input type="hidden" name="nama" value="<?php echo htmlspecialchars($data['nama']); ?>">
            <input type="hidden" name="overview" value="<?php echo htmlspecialchars($data['overview'] ?? ''); ?>">
            <input type="hidden" name="foto_lama" value="<?php echo htmlspecialchars($foto); ?>">
          </div>
        </form>

        <div class="profile-info">
          <p class="official"><?php echo ucfirst($data['role']); ?></p>
          <h1><?php echo htmlspecialchars($data['nama']); ?></h1>
          <p class="email"><?php echo htmlspecialchars($data['email']); ?></p>
        </div>
      </div>
    </div>

    <!-- 🔹 Edit Form -->
    <form action="editprofil.php" method="POST" enctype="multipart/form-data" class="edit-form">
      <h3>Edit Profil</h3>
      <div class="form-group">
        <label>Nama</label>
        <input type="text" name="nama" value="<?php echo htmlspecialchars($data['nama']); ?>" required>
      </div>

      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($data['email']); ?>" readonly>
      </div>

      <div class="form-group">
        <label>Bio</label>
        <textarea name="overview" rows="5" placeholder="Tulis bio singkat kamu..."><?php 
          // Pastikan nilai tidak null sebelum di-output
          $overview_value = isset($data['overview']) && $data['overview'] !== null ? $data['overview'] : '';
          echo htmlspecialchars($overview_value); 
        ?></textarea>
      </div>

      <input type="hidden" name="foto_lama" value="<?php echo htmlspecialchars($foto); ?>">

      <button type="submit" name="update_profile" class="btn-main">Simpan Perubahan</button>
    </form>

    <!-- 🔹 Summary Card -->
    <form action="editprofil.php" method="POST" class="summary-card">
      <h3>Official Summary</h3>

      <div class="form-group">
        <label>Nama Tim</label>
        <input type="text" name="team_name" value="<?php echo htmlspecialchars($data['team_name'] ?? ''); ?>" placeholder="Contoh: UNESA Volleyball Team">
      </div>

      <div class="form-group">
        <label>Cabang Olahraga</label>
        <input type="text" name="sport" value="<?php echo htmlspecialchars($data['sport'] ?? ''); ?>" placeholder="Contoh: Voli, Basket, Futsal">
      </div>

      <div class="form-group">
        <label>Turnamen Aktif</label>
        <input type="number" name="active_tournaments" value="<?php echo htmlspecialchars($data['active_tournaments'] ?? 0); ?>" min="0">
      </div>

      <div class="form-group">
        <label>Deskripsi Tim</label>
        <textarea name="team_desc" rows="5" placeholder="Ceritakan sedikit tentang timmu..."><?php echo htmlspecialchars($data['team_desc'] ?? ''); ?></textarea>
      </div>

      <button type="submit" name="update_summary" class="btn-main">Simpan Summary</button>
    </form>
  </main>

  <script>
    // ✅ Preview Foto dan Auto Submit
    function autoSubmitFoto(event) {
      const file = event.target.files[0];
      if (file) {
        // Preview gambar
        const reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('preview').src = e.target.result;
        };
        reader.readAsDataURL(file);
        
        // Auto submit form
        setTimeout(function() {
          if (confirm('Upload foto ini?')) {
            document.getElementById('fotoForm').submit();
          }
        }, 100);
      }
    }
  </script>
</body>
</html>