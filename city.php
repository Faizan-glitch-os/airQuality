<?php
include_once __DIR__ . '/inc/functions.inc.php';
?>

<?php
$currentCity = null;
$noCity = true;
$filename = null;

//check if received city is not empty then set currentCity
if (!empty($_GET['city'])) {
    $currentCity = $_GET['city'];
}

//check if currenCity is not empty, then read from cities json and get the filename
if (!empty($currentCity)) {
    $cities = json_decode(file_get_contents(__DIR__ . '/data/index.json'), true);

    foreach ($cities as $city) {
        if ($city['city'] === $currentCity) {
            $noCity = false;
            $filename = $city['filename'];
            break;
        }
    }
}

//if filename is not empty, then aggregate the data
if (!empty($filename)) {
    $results = json_decode(file_get_contents('compress.bzip2://' . __DIR__ . '/data/' . $filename), true)['results'];

    $units = [
    'pm25' => null,
    'pm10' => null,
    ];

    foreach ($results as $result) {
        if (!empty($units['pm25'] && !empty($units['pm10']))) {
            break;
        }
        if ($result['parameter'] === 'pm25') {
            $units['pm25'] = $result['unit'];
        }
        if ($result['parameter'] === 'pm10') {
            $units['pm10'] = $result['unit'];
        }
    }

    $stats = [];

    foreach ($results as $result) {
        if ($result['parameter'] !== 'pm25' && $result['parameter'] !== 'pm10') {
            continue;
        }
        if ($result['value'] < 0) {
            continue;
        }

        $month = substr($result['date']['local'], 0, 7);

        if (!isset($stats[$month])) {
            $stats[$month] = [
                'pm25' => [],
                'pm10' => [],
            ];
        }

        $stats[$month][$result['parameter']][] = $result['value'];
    }
}

?>

<?php
include_once __DIR__ . '/views/header.inc.php';
?>

<!-- if currentCity is empty, then show error -->
<?php if (empty($currentCity)): ?>
    <p>No City selected</p>

<?php elseif ($noCity): ?>
    <p>Data is not availble for the current city: <strong><?php echo $currentCity; ?></strong></p>

<?php else: ?>
    <?php if (!empty($stats)): ?>
        <h1><?php echo $currentCity ?></h1>
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th>PM2.5</th>
                    <th>PM10</th>
                </tr>
            </thead>
            <?php foreach ($stats as $month => $measurments): ?>
                <tr>
                    <th><?php echo e($month); ?></th>
                    <td>
                        <?php echo round(e(array_sum($measurments['pm25']) / count($measurments['pm25'])), 2); ?> <?php echo $units['pm25'] ?>
                    </td>
                    <td>
                        <?php echo round(e(array_sum($measurments['pm10']) / count($measurments['pm10'])), 2); ?> <?php echo $units['pm10'] ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
<?php endif; ?>


<?php
include_once __DIR__ . '/views/footer.inc.php';
?>