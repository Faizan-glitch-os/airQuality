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
        } else {
            $noCity = true;
        }
    }
}

//if filename is not empty, then aggregate the data
if (!empty($filename)) {
    $cityData = json_decode(file_get_contents('compress.bzip2://' . __DIR__ . '/data/' . $filename), true)['results'];
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

<?php endif; ?>


<?php
include_once __DIR__ . '/views/footer.inc.php';
?>