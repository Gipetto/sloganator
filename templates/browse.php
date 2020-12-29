<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="generator" content="Sloganator 2.0">
    <title>sloganator</title>
    <link rel="stylesheet" href="css/browse.css">
    <script src="js/browse.js" defer></script>
</head>
<body>
<div id="content">
    <header>
        <h1>Sloganator</h1>
        <p>Hello, <span class="current-user" data-id="<?php echo $userId; ?>"><?php echo $userName; ?></span></p>
    </header>
    <section id="slogans">
        <ul></ul>
    </section>
    <section>
        <button class="loader" type="button" data-page="1">Load Page <span class="page">1</span></button>
    </section>
    <footer>
        <p>&copy; <?php echo date('Y'); ?>, not by you.</p>
    </footer>
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
</div>
</body>
</html>
