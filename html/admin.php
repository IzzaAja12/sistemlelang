<?php
include 'config.php';

// Tambah data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
  $nama = $_POST['nama_lengkap'];
  $username = $_POST['username'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $telp = $_POST['telp'];

  try {
    $stmt = $pdo->prepare("INSERT INTO tb_masyarakat (nama_lengkap, username, password, telp) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nama, $username, $password, $telp]);
    echo "<script>alert('Data berhasil ditambahkan!'); window.location='admin.php';</script>";
  } catch (PDOException $e) {
    echo "<script>alert('Gagal menambahkan data: " . $e->getMessage() . "');</script>";
  }
}

// Edit data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
  $id = $_POST['id_user'];
  $nama = $_POST['nama_lengkap'];
  $username = $_POST['username'];
  $telp = $_POST['telp'];

  try {
    $stmt = $pdo->prepare("UPDATE tb_masyarakat SET nama_lengkap=?, username=?, telp=? WHERE id_user=?");
    $stmt->execute([$nama, $username, $telp, $id]);
    echo "<script>alert('Data berhasil diubah!'); window.location='admin.php';</script>";
  } catch (PDOException $e) {
    echo "<script>alert('Gagal mengedit data: " . $e->getMessage() . "');</script>";
  }
}

// Hapus data
if (isset($_GET['hapus'])) {
  $id = $_GET['hapus'];
  try {
    $stmt = $pdo->prepare("DELETE FROM tb_masyarakat WHERE id_user=?");
    $stmt->execute([$id]);
    echo "<script>alert('Data berhasil dihapus!'); window.location='admin.php';</script>";
  } catch (PDOException $e) {
    echo "<script>alert('Gagal menghapus data: " . $e->getMessage() . "');</script>";
  }
}
?>

<!-- Link Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<div class="container py-5">
  <h4 class="mb-4 text-primary">Forms / Masyarakat</h4>

  <div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Data Masyarakat</h5>
      <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-person-plus"></i> Tambah Masyarakat
      </button>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center">
          <thead class="table-light">
            <tr>
              <th>Nama Lengkap</th>
              <th>Username</th>
              <th>No. Telepon</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $query = $pdo->query("SELECT * FROM tb_masyarakat ORDER BY id_user DESC");
            while ($data = $query->fetch()) {
              echo "<tr>
                      <td>{$data['nama_lengkap']}</td>
                      <td>{$data['username']}</td>
                      <td>{$data['telp']}</td>
                      <td>
                        <a href='?edit={$data['id_user']}' class='btn btn-warning btn-sm'><i class='bi bi-pencil-square'></i></a>
                        <a href='?hapus={$data['id_user']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin ingin menghapus data ini?')\"><i class='bi bi-trash'></i></a>
                      </td>
                    </tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- MODAL TAMBAH -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="post" class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalTambahLabel">Form Tambah Masyarakat</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" name="nama_lengkap" placeholder="Masukkan nama lengkap" required>
          </div>
          <div class="col-md-6">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" name="username" placeholder="Masukkan username" required>
          </div>
          <div class="col-md-6">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" placeholder="Masukkan password" required>
          </div>
          <div class="col-md-6">
            <label for="telp" class="form-label">No. Telepon</label>
            <input type="text" class="form-control" name="telp" placeholder="Contoh: 08123456789" required>
          </div>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="submit" name="tambah" class="btn btn-success">
          <i class="bi bi-save"></i> Simpan
        </button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="bi bi-x-circle"></i> Batal
        </button>
      </div>
    </form>
  </div>
</div>

<?php
// MODAL EDIT (jika ada parameter edit di URL)
if (isset($_GET['edit'])) {
  $id_edit = $_GET['edit'];
  $stmt = $pdo->prepare("SELECT * FROM tb_masyarakat WHERE id_user = ?");
  $stmt->execute([$id_edit]);
  $dataEdit = $stmt->fetch();
  if ($dataEdit) {
?>
<!-- MODAL EDIT -->
<script>
  document.addEventListener("DOMContentLoaded", function () {
    var editModal = new bootstrap.Modal(document.getElementById('modalEdit'));
    editModal.show();
  });
</script>

<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="post" class="modal-content">
      <input type="hidden" name="id_user" value="<?= $dataEdit['id_user'] ?>">
      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title" id="modalEditLabel">Edit Data Masyarakat</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" name="nama_lengkap" value="<?= $dataEdit['nama_lengkap'] ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" name="username" value="<?= $dataEdit['username'] ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">No. Telepon</label>
            <input type="text" class="form-control" name="telp" value="<?= $dataEdit['telp'] ?>" required>
          </div>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="submit" name="edit" class="btn btn-warning text-white">
          <i class="bi bi-save"></i> Update
        </button>
        <a href="admin.php" class="btn btn-secondary">
          <i class="bi bi-x-circle"></i> Batal
        </a>
      </div>
    </form>
  </div>
</div>
<?php
  }
}
?>
