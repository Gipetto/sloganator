<html>
<head>
    <?php require "partials/head.php" ?>
    <script src="https://cdn.anychart.com/releases/v8/js/anychart-base.min.js"></script>
    <script src="https://cdn.anychart.com/releases/v8/js/anychart-tag-cloud.min.js"></script>
    <script src="js/word-cloud.js" defer></script>
</head>
<body>
<?php require "partials/header.php"; ?>
    <?php require "partials/author-filter.php"; ?>
    <section id="word-cloud">
        <div id="chart"></div>
    </section>
<?php require "partials/footer.php"; ?>
<script>
    const data = <?php echo json_encode($data) . PHP_EOL; ?>
</script>
</body>
</html>