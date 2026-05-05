<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kategori - UTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
    require_once 'config/database.php';
    
        $errors = [];
        $id = $_GET['id_kategori'] ?? null;

        if (!$id || !is_numeric($id)) {
            header("Location: index.php?error=" . urlencode("ID kategori tidak valid."));
            exit;
        }
    
        $stmt = $conn->prepare("SELECT * FROM kategori WHERE id_kategori = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $kategori = $result->fetch_assoc();
        $stmt->close();
    
        if (!$kategori) {
            header("Location: index.php?error=" . urlencode("Data kategori tidak ditemukan."));
            exit;
        }

        $kode = $kategori['kode_kategori'];
        $nama = $kategori['nama_kategori'];
        $deskripsi = $kategori['deskripsi'];
        $status = $kategori['status'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $kode = htmlspecialchars(trim($_POST['kode_kategori'] ?? ''));
        $nama = htmlspecialchars(trim($_POST['nama_kategori'] ?? ''));
        $deskripsi = htmlspecialchars(trim($_POST['deskripsi'] ?? ''));
        $status = htmlspecialchars(trim($_POST['status'] ?? 'Aktif'));

        if (empty($kode)) {
            $errors[] = "Kode kategori wajib diisi.";
        } elseif (strlen($kode) < 4 || strlen($kode) > 10) {
            $errors[] = "Kode kategori harus terdiri dari 4-10 karakter.";
        } elseif (substr($kode, 0, 4) !== 'KAT-') {
            $errors[] = "Kode kategori harus diawali dengan 'KAT-'.";
        }

        if (empty($nama)) {
            $errors[] = "Nama kategori wajib diisi.";
        } elseif (strlen($nama) < 3) {
            $errors[] = "Nama kategori minimal 3 karakter.";
        } elseif (strlen($nama) > 50) {
            $errors[] = "Nama kategori maksimal 50 karakter.";
        }

        if (!empty($deskripsi) && strlen($deskripsi) > 200) {
            $errors[] = "Deskripsi maksimal 200 karakter.";
        }

        if (!in_array($status, ['Aktif', 'Nonaktif'])) {
            $errors[] = "Status harus Aktif atau Nonaktif.";
        }

        if (empty($errors)) {
            $check = $conn->prepare("SELECT id_kategori FROM kategori WHERE kode_kategori = ? AND id_kategori != ? LIMIT 1");
            $check->bind_param("si", $kode, $id);
            $check->execute();
            $result = $check->get_result();

            if ($result->num_rows > 0) {
                $errors[] = "Kode kategori sudah digunakan oleh data lain.";
            }

            $check->close();
        }

        if (empty($errors)) {
            $update = $conn->prepare("UPDATE kategori 
            SET kode_kategori = ?, nama_kategori = ?, deskripsi = ?, status = ? WHERE id_kategori = ?");
            $update->bind_param("ssssi", $kode, $nama, $deskripsi, $status, $id);

            if ($update->execute()) {
                header("Location: index.php?success=" . urlencode("Kategori berhasil diubah."));
                exit;
            } else {
                $errors[] = "Gagal mengubah data kategori.";
            }

            $update->close();
        }
    }
    ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Kategori</h4>
                    </div>
                    <div class="card-body">
                        <div class="card-body">
                                    <?php if (!empty($errors)): ?>
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                <?php foreach ($errors as $error): ?>
                                                    <li><?= htmlspecialchars($error) ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>

                                    <form method="POST">
                                        <div class="mb-3">
                                            <label for="kode_kategori" class="form-label">Kode Kategori</label>
                                            <input type="text" class="form-control" id="kode_kategori" name="kode_kategori"
                                                value="<?= htmlspecialchars($kode) ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="nama_kategori" class="form-label">Nama Kategori</label>
                                            <input type="text" class="form-control" id="nama_kategori" name="nama_kategori"
                                                value="<?= htmlspecialchars($nama) ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="deskripsi" class="form-label">Deskripsi</label>
                                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4"><?= htmlspecialchars($deskripsi) ?></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label d-block">Status</label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="status" id="aktif" value="Aktif"
                                                    <?= ($status === 'Aktif') ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="aktif">Aktif</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="status" id="nonaktif" value="Nonaktif"
                                                    <?= ($status === 'Nonaktif') ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="nonaktif">Nonaktif</label>
                                            </div>
                                        </div>

                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            <a href="index.php" class="btn btn-secondary">Kembali</a>
                                        </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>