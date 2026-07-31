<?php require_once __DIR__.'/layout_top.php'; ?>

<div class="am-card" style="max-width:520px;margin:0 auto;padding:1.5rem">
  <div style="font-weight:800;font-size:1rem;color:#fff;margin-bottom:1rem"><i class="fa-solid fa-broom" style="color:var(--red);margin-right:.4rem"></i>Kết quả dọn ảnh sản phẩm</div>
  <div style="font-size:.85rem;color:#ccc;line-height:2">
    <div>Tổng số file kiểm tra: <b style="color:#fff"><?= $total ?></b></div>
    <div>Số ảnh đã được nén lại (quá khổ): <b style="color:#22c55e"><?= $resized ?></b></div>
    <div>Dung lượng trước: <b style="color:#fff"><?= round($before/1024/1024, 1) ?> MB</b></div>
    <div>Dung lượng sau: <b style="color:#22c55e"><?= round($after/1024/1024, 1) ?> MB</b></div>
    <div>Đã tiết kiệm: <b style="color:#22c55e"><?= $before>0 ? round((1-$after/$before)*100) : 0 ?>%</b></div>
  </div>
  <a href="<?= APP_URL ?>/admin/optimize-uploads" class="btn-r" style="display:inline-flex;margin-top:1.25rem;text-decoration:none"><i class="fa-solid fa-rotate"></i> Chạy lại</a>
  <a href="<?= APP_URL ?>/admin" class="btn-g" style="display:inline-flex;margin-top:1.25rem;margin-left:.5rem;text-decoration:none">Về Dashboard</a>
</div>

<?php require_once __DIR__.'/layout_bottom.php'; ?>
