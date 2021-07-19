<html>
<head>
<?php require "partials/head.php"; ?>
<link rel="stylesheet" href="css/browse.css">
<script src="js/browse.js" defer></script>
</head>
<body>
<?php require "partials/header.php"; ?>
    <?php require "partials/author-filter.php"; ?>
    <section id="slogans">
        <ul></ul>
    </section>
    <section>
        <button class="loader" type="button" data-params>Load Page <span class="page">1</span></button>
    </section>
    <div id="templates">
        <figure>
            <blockquote>
                <p class="slogan"></p>
            </blockquote>
            <figcaption>
                <cite>
                    <span class="timestamp"></span><a class="user-name" target="_blank" href="#"></a>
                </cite>
            </figcaption>
        </figure>
    </div>
<?php require "partials/footer.php"; ?>
</body>
</html>
