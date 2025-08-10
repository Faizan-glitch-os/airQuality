<?php
include_once './views/header.inc.php';

include_once('./inc/functions.inc.php');
?>
<?php
$cities = json_decode(file_get_contents(__DIR__ . '/data/index.json'), true);
?>


<ul>
    <?php foreach ($cities as $city): ?>
        <a href="city.php? <?php echo http_build_query(['city' => $city['city']]) ?>">
            <li>
            <?php echo e($city['city']) ?>,
            <?php echo e($city['country']) ?>
           (<?php echo e($city['flag']) ?>)
            </li>
        </a>
       
    <?php endforeach; ?>
</ul>

<?php
include_once './views/footer.inc.php';
?>