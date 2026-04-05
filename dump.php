<?php
$login = $login ?? false;
$limit = $limit ?? false;

if (isset($_SESSION["login"]) && $_SESSION["login"] === "on") {
  // Check session expiry (30 minutes timeout)
  if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    $login = false;
  } else {
    $_SESSION['last_activity'] = time();
    $login = true;
  }
} else {
  $_SESSION['last_activity'] = time();
  $login = false;
}

$dumpDir = __DIR__ . '/dump/';
$dumpFiles = [];
if (is_dir($dumpDir)) {
	foreach (glob($dumpDir . '*.zip') as $file) {
		if (is_file($file)) {
			$dumpFiles[] = [
				'name' => basename($file),
				'path' => 'dump/' . basename($file),
				'size' => filesize($file),
				'mtime' => filemtime($file),
			];
		}
	}
	usort($dumpFiles, function ($a, $b) {
		return $b['mtime'] <=> $a['mtime'];
	});
}

function formatSize(int $bytes): string {
	if ($bytes >= 1073741824) {
		return number_format($bytes / 1073741824, 2) . ' GB';
	}
	if ($bytes >= 1048576) {
		return number_format($bytes / 1048576, 2) . ' MB';
	}
	if ($bytes >= 1024) {
		return number_format($bytes / 1024, 2) . ' KB';
	}
	return $bytes . ' B';
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta charset="UTF-8">
<title>ダンプファイル一覧</title>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="./travel_archive.css">
<script src="./dist/dump.js"></script>
</head>

<body>
  <?php if (!$login) { ?>
    <div class="container">
      <div class="menu-wrap">
      </div>

      <div class="input-row">
        <div class="download-btn">
          <button class="download-button" onClick="createDump()">
            <i class="material-icons">folder_zip</i> ダンプ生成
          </button>
        </div>
      </div>

      <section class="dump-list">
        <h1>ダンプファイル一覧</h1>
        <?php if (empty($dumpFiles)) { ?>
          <p>現在、ダンプファイルはありません。</p>
        <?php } else { ?>
          <table class="dump-table">
            <thead>
              <tr>
                <th>ファイル名</th>
                <th>更新日時</th>
                <th>サイズ</th>
                <th>ダウンロード</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($dumpFiles as $file) { ?>
                <tr>
                  <td><?php echo htmlspecialchars($file['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?php echo date('Y-m-d H:i:s', $file['mtime']); ?></td>
                  <td><?php echo htmlspecialchars(formatSize($file['size']), ENT_QUOTES, 'UTF-8'); ?></td>
                  <td>
                    <a href="<?php echo htmlspecialchars($file['path'], ENT_QUOTES, 'UTF-8'); ?>" download="<?php echo htmlspecialchars($file['name'], ENT_QUOTES, 'UTF-8'); ?>"> 
                      <i class="material-icons">download</i> ダウンロード
                    </a>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        <?php } ?>
      </section>

    <div>
      <dialog class="dialog" id="img-dialog">
        <div id="img-preview"></div>
        <div>
          <input type="button" value="閉じる" onClick="closeDialog()" data-backdrop="true"/>
        </div>
      </dialog>
      <div id="dialog-background"></div>
    </div>

    <script src="./dist/dump.js"></script>
  <?php } else { ?>
    <h1>閉鎖中</h1>
  <?php } ?>

</body>
</html>
