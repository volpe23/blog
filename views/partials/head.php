<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>My Blog</title>
    <link rel="stylesheet" href="/output.css">
</head>

<body>
    <header class="w-full mx-auto">
        <nav class="py-6 border-gray-300 border-b shadow">
            <div class="flex justify-between items-center container mx-auto">
                <ul class="flex gap-6">
                    <?php
                    foreach ($navRoutes as $route => $options) {
                        if ($options["restriction"]): ?>
                            <li><a class="py-2 rounded font-medium text-red-900 
                            <?= parse_url($_SERVER["REQUEST_URI"])["path"] === $route ? "underline" : "" ?> 
                            hover:text-red-700 hover:underline" href="<?= $route ?>">
                                    <?= $options["name"] ?>
                                </a></li>
                    <?php endif;
                    } ?>
                </ul>
                <?php if (auth()->check()): ?>
                    <div class="flex gap-6 items-center">
                        <span><?= auth()->user()?->username ?></span>
                        <form action="/logout" method="POST">
                            <input class="py-2 rounded font-medium text-red-900 hover:text-red-700 hover:underline hover:cursor-pointer" type="submit" value="Logout">
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <main class="container mx-auto">